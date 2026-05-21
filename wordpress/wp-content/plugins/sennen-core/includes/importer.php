<?php
/**
 * Content importer for Sennen Core.
 *
 * Provides WP-CLI commands and helper functions to import
 * content from the existing content/*.json files.
 *
 * Usage: wp sennen import --dry-run
 *
 * @package SennenCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register WP-CLI commands for content import.
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command( 'sennen import', 'sennen_core_import_command' );
}

/**
 * Import command callback.
 *
 * @param array $args       Positional arguments.
 * @param array $assoc_args Associative arguments.
 */
function sennen_core_import_command( array $args, array $assoc_args ): void {
	$dry_run = isset( $assoc_args['dry-run'] );

	WP_CLI::log( $dry_run ? 'Dry run mode — no data will be written.' : 'Starting import...' );

	sennen_core_import_site_settings( $dry_run );
	sennen_core_import_services( $dry_run );
	sennen_core_import_testimonials( $dry_run );
	$home_page_id = sennen_core_import_pages( $dry_run );

	if ( ! $dry_run && $home_page_id ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_page_id );
	}

	WP_CLI::success( 'Import complete.' );
}

/**
 * Return an absolute path to an imported content file.
 *
 * @param string $file_name File name inside /content.
 * @return string
 */
function sennen_core_content_file( string $file_name ): string {
	return ABSPATH . 'content/' . $file_name;
}

/**
 * Read JSON from the mounted /content directory.
 *
 * @param string $file_name File name inside /content.
 * @return array|null
 */
function sennen_core_read_json( string $file_name ): ?array {
	$file = sennen_core_content_file( $file_name );

	if ( ! file_exists( $file ) ) {
		WP_CLI::warning( "$file_name not found." );
		return null;
	}

	$data = json_decode( file_get_contents( $file ), true );
	if ( ! is_array( $data ) ) {
		WP_CLI::warning( "Could not parse $file_name." );
		return null;
	}

	return $data;
}

/**
 * Import site settings from content/site-settings.json.
 *
 * @param bool $dry_run Whether to perform a dry run.
 */
function sennen_core_import_site_settings( bool $dry_run ): void {
	$data = sennen_core_read_json( 'site-settings.json' );
	if ( ! $data ) {
		return;
	}

	if ( ! $dry_run ) {
		if ( ! empty( $data['bookingUrl'] ) ) {
			update_option( 'sennen_booking_url', esc_url_raw( $data['bookingUrl'] ) );
		}
		if ( ! empty( $data['contactEmail'] ) ) {
			update_option( 'sennen_contact_email', sanitize_email( $data['contactEmail'] ) );
		}
		update_option( 'blogname', sanitize_text_field( $data['brandName'] ?? 'Sennen Life Coaching' ) );
		update_option( 'blogdescription', sanitize_text_field( $data['tagline'] ?? '' ) );
	}

	WP_CLI::log( '  ✓ Site settings imported.' );
}

/**
 * Import pages from JSON files and static Next.js-equivalent layouts.
 *
 * @param bool $dry_run Whether to perform a dry run.
 * @return int|null Home page ID.
 */
function sennen_core_import_pages( bool $dry_run ): ?int {
	$home    = sennen_core_read_json( 'home.json' );
	$about   = sennen_core_read_json( 'about.json' );
	$booking = sennen_core_read_json( 'booking.json' );

	$pages = array(
		array(
			'title'   => __( 'Home', 'sennen-core' ),
			'slug'    => 'home',
			'content' => sennen_core_build_home_blocks( $home ?? array() ),
		),
		array(
			'title'   => __( 'About', 'sennen-core' ),
			'slug'    => 'about',
			'content' => sennen_core_build_about_blocks( $about ?? array() ),
		),
		array(
			'title'   => __( 'Services', 'sennen-core' ),
			'slug'    => 'services',
			'content' => sennen_core_build_services_page_blocks(),
		),
		array(
			'title'   => __( 'Testimonials', 'sennen-core' ),
			'slug'    => 'testimonials',
			'content' => sennen_core_build_testimonials_page_blocks(),
		),
		array(
			'title'   => __( 'Booking', 'sennen-core' ),
			'slug'    => 'booking',
			'content' => sennen_core_build_booking_blocks( $booking ?? array() ),
		),
	);

	$home_page_id = null;

	foreach ( $pages as $page ) {
		if ( $dry_run ) {
			WP_CLI::log( '  Would upsert page: ' . $page['title'] );
			continue;
		}

		$page_id = sennen_core_upsert_page( $page['title'], $page['slug'], $page['content'] );
		if ( 'home' === $page['slug'] ) {
			$home_page_id = $page_id;
		}

		// Lock page template to prevent accidental layout changes.
		if ( $page_id ) {
			update_post_meta( $page_id, '_wp_page_template', 'blank' );
			update_post_meta( $page_id, 'sennen_layout_locked', true );
		}

		WP_CLI::log( '  ✓ Upserted page: ' . $page['title'] );
	}

	return $home_page_id;
}

/**
 * Insert or update a WordPress page by slug.
 *
 * @param string $title   Page title.
 * @param string $slug    Page slug.
 * @param string $content Page block markup.
 * @return int|null
 */
function sennen_core_upsert_page( string $title, string $slug, string $content ): ?int {
	$existing = get_page_by_path( $slug, OBJECT, 'page' );

	$post_data = array(
		'post_title'   => sanitize_text_field( $title ),
		'post_name'    => sanitize_title( $slug ),
		'post_content' => $content,
		'post_status'  => 'publish',
		'post_type'    => 'page',
	);

	if ( $existing ) {
		$post_data['ID'] = $existing->ID;
		$result          = wp_update_post( $post_data, true );
	} else {
		$result = wp_insert_post( $post_data, true );
	}

	if ( is_wp_error( $result ) ) {
		WP_CLI::warning( 'Could not upsert page ' . $title . ': ' . $result->get_error_message() );
		return null;
	}

	return intval( $result );
}

/**
 * Find an existing imported post by slug first, then title.
 *
 * @param string $post_type Post type.
 * @param string $slug      Preferred slug.
 * @param string $title     Fallback title.
 * @return array
 */
function sennen_core_find_existing_post( string $post_type, string $slug, string $title ): array {
	$existing = get_posts(
		array(
			'name'           => sanitize_title( $slug ),
			'post_type'      => $post_type,
			'post_status'    => 'any',
			'posts_per_page' => 1,
		)
	);

	if ( $existing ) {
		return $existing;
	}

	return get_posts(
		array(
			'title'          => sanitize_text_field( $title ),
			'post_type'      => $post_type,
			'post_status'    => 'any',
			'posts_per_page' => 1,
		)
	);
}

/**
 * Delete duplicate imported posts with the same title after the canonical post is upserted.
 *
 * @param string $post_type    Post type.
 * @param int    $canonical_id Canonical post ID to keep.
 * @param string $title        Imported title.
 */
function sennen_core_delete_duplicate_posts( string $post_type, int $canonical_id, string $title ): void {
	$duplicates = get_posts(
		array(
			'title'          => sanitize_text_field( $title ),
			'post_type'      => $post_type,
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);

	foreach ( $duplicates as $duplicate_id ) {
		if ( intval( $duplicate_id ) === $canonical_id ) {
			continue;
		}
		wp_delete_post( intval( $duplicate_id ), true );
	}
}

/**
 * Import services from content/services.json.
 *
 * @param bool $dry_run Whether to perform a dry run.
 */
function sennen_core_import_services( bool $dry_run ): void {
	$services = sennen_core_read_json( 'services.json' );
	if ( ! $services ) {
		return;
	}

	foreach ( $services as $service ) {
		$title = $service['title'] ?? 'Untitled';
		$slug  = $service['slug']['current'] ?? sanitize_title( $title );

		if ( $dry_run ) {
			WP_CLI::log( '  Would upsert service: ' . $title );
			continue;
		}

		$existing = sennen_core_find_existing_post( 'sennen_service', sanitize_title( $slug ), $title );

		$post_data = array(
			'post_title'   => sanitize_text_field( $title ),
			'post_name'    => sanitize_title( $slug ),
			'post_content' => sanitize_textarea_field( $service['description'] ?? '' ),
			'post_excerpt' => sanitize_textarea_field( $service['description'] ?? '' ),
			'post_status'  => 'publish',
			'post_type'    => 'sennen_service',
			'menu_order'   => intval( $service['sortOrder'] ?? 0 ),
		);

		if ( $existing ) {
			$post_data['ID'] = $existing[0]->ID;
			$post_id         = wp_update_post( $post_data, true );
		} else {
			$post_id = wp_insert_post( $post_data, true );
		}

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, '_sennen_service_price', sanitize_text_field( $service['price'] ?? '' ) );
			update_post_meta( $post_id, '_sennen_service_duration', sanitize_text_field( $service['duration'] ?? '' ) );
			if ( ! empty( $service['features'] ) ) {
				update_post_meta( $post_id, '_sennen_service_features', array_map( 'sanitize_text_field', $service['features'] ) );
			}
			update_post_meta( $post_id, '_sennen_service_sort_order', intval( $service['sortOrder'] ?? 0 ) );
			sennen_core_delete_duplicate_posts( 'sennen_service', intval( $post_id ), $title );
		}

		WP_CLI::log( '  ✓ Upserted service: ' . $title );
	}
}

/**
 * Import testimonials from content/testimonials.json.
 *
 * @param bool $dry_run Whether to perform a dry run.
 */
function sennen_core_import_testimonials( bool $dry_run ): void {
	$testimonials = sennen_core_read_json( 'testimonials.json' );
	if ( ! $testimonials ) {
		return;
	}

	foreach ( $testimonials as $testimonial ) {
		$title = $testimonial['authorName'] ?? 'Testimonial';
		$slug  = sanitize_title( $title );

		if ( $dry_run ) {
			WP_CLI::log( '  Would upsert testimonial: ' . $title );
			continue;
		}

		$existing = sennen_core_find_existing_post( 'sennen_testimonial', $slug, $title );

		$post_data = array(
			'post_title'   => sanitize_text_field( $title ),
			'post_name'    => $slug,
			'post_content' => sanitize_textarea_field( $testimonial['quote'] ?? '' ),
			'post_excerpt' => sanitize_textarea_field( $testimonial['quote'] ?? '' ),
			'post_status'  => 'publish',
			'post_type'    => 'sennen_testimonial',
		);

		if ( $existing ) {
			$post_data['ID'] = $existing[0]->ID;
			$post_id         = wp_update_post( $post_data, true );
		} else {
			$post_id = wp_insert_post( $post_data, true );
		}

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, '_sennen_testimonial_author_title', sanitize_text_field( $testimonial['authorTitle'] ?? '' ) );
			sennen_core_delete_duplicate_posts( 'sennen_testimonial', intval( $post_id ), $title );
		}

		WP_CLI::log( '  ✓ Upserted testimonial: ' . $title );
	}
}

/**
 * Convert Sanity portable-text style blocks into paragraph blocks.
 *
 * @param array $portable_blocks Portable text blocks.
 * @return string
 */
function sennen_core_portable_text_to_blocks( array $portable_blocks ): string {
	$markup = '';

	foreach ( $portable_blocks as $block ) {
		$text = '';
		foreach ( $block['children'] ?? array() as $child ) {
			$text .= $child['text'] ?? '';
		}

		if ( '' === trim( $text ) ) {
			continue;
		}

		$markup .= sprintf(
			'<!-- wp:paragraph {"style":{"typography":{"fontSize":"var:preset|font-size|body-lg","fontStyle":"normal","fontWeight":"300","lineHeight":"1.7"},"color":{"text":"#42493e"}}} --><p class="has-text-color" style="color:#42493e;font-size:var(--wp--preset--font-size--body-lg);font-style:normal;font-weight:300;line-height:1.7">%s</p><!-- /wp:paragraph -->',
			esc_html( $text )
		);
	}

	return $markup;
}

/**
 * Build Home page blocks.
 *
 * @param array $data Home data.
 * @return string
 */
function sennen_core_build_home_blocks( array $data ): string {
	$heading  = $data['heroHeading'] ?? 'Rooted in Grace';
	$subtitle = $data['heroSubtitle'] ?? 'A sanctuary for spiritual alignment, mindful wellness, and the slow-living philosophy. Breathe deeply, you have arrived.';
	$cta      = $data['heroCtaText'] ?? 'Begin Your Journey';

	$philosophy_heading = $data['philosophyHeading'] ?? 'The Art of Slowing Down.';
	$body_blocks        = $data['philosophyBody'] ?? array();

	// Extract philosophy paragraphs from portable text.
	$philosophy_paras = array();
	foreach ( $body_blocks as $block ) {
		$text = '';
		foreach ( $block['children'] ?? array() as $child ) {
			$text .= $child['text'] ?? '';
		}
		if ( '' !== trim( $text ) ) {
			$philosophy_paras[] = $text;
		}
	}
	$philosophy_json = wp_json_encode( $philosophy_paras );

	return '<!-- wp:sennen/home-hero {"heading":' . wp_json_encode( $heading ) . ',"subtitle":' . wp_json_encode( $subtitle ) . ',"ctaText":' . wp_json_encode( $cta ) . '} /-->' . "\n" .
		'<!-- wp:sennen/botanical-divider {"opacity":30} /-->' . "\n" .
		'<!-- wp:sennen/home-philosophy {"heading":' . wp_json_encode( $philosophy_heading ) . ',"bodyParagraphs":' . $philosophy_json . '} /-->' . "\n" .
		'<!-- wp:sennen/home-testimonials-section {"count":3} /-->';
}

/**
 * Build About page blocks.
 *
 * @param array $data About data.
 * @return string
 */
function sennen_core_build_about_blocks( array $data ): string {
	$heading  = $data['heroHeading'] ?? 'Rooted in Grace, Wandering in Spirit';
	$subtitle = $data['heroSubtitle'] ?? 'My journey began not with a destination, but with a profound desire to listen.';

	// Build journey sections.
	$journeys = array();
	foreach ( $data['journeySections'] ?? array() as $section ) {
		$journeys[] = array(
			'heading'  => $section['heading'] ?? '',
			'body'     => $section['body'] ?? '',
			'imageUrl' => '/images/journey.jpg',
		);
	}
	$journeys_json = wp_json_encode( $journeys );

	// Build philosophy cards.
	$cards = array();
	foreach ( $data['philosophyCards'] ?? array() as $card ) {
		$cards[] = array(
			'title'    => $card['title'] ?? '',
			'body'     => $card['body'] ?? '',
			'iconName' => $card['iconName'] ?? 'Flower',
		);
	}
	$cards_json = wp_json_encode( $cards );

	return '<!-- wp:sennen/about-hero {"heading":' . wp_json_encode( $heading ) . ',"subtitle":' . wp_json_encode( $subtitle ) . '} /-->' . "\n" .
		'<!-- wp:sennen/about-journey-sections {"sections":' . $journeys_json . '} /-->' . "\n" .
		'<!-- wp:sennen/about-philosophy-cards {"cards":' . $cards_json . '} /-->' . "\n" .
		'<!-- wp:sennen/about-gallery /-->';
}

/**
 * Build Services page blocks.
 *
 * @return string
 */
function sennen_core_build_services_page_blocks(): string {
	return '<!-- wp:sennen/texture-hero {"label":"Offerings","heading":"Nourish Your Spirit","subtitle":"Whether you need a gentle reset or a deep transformative journey, these offerings hold the space for your unwinding. Choose the path that calls to your current season."} /-->' . "\n" .
		'<!-- wp:sennen/services-philosophy /-->' . "\n" .
		'<!-- wp:sennen/service-cards {"count":3} /-->' . "\n" .
		'<!-- wp:sennen/bespoke-cta /-->';
}

/**
 * Build Testimonials page blocks.
 *
 * @return string
 */
function sennen_core_build_testimonials_page_blocks(): string {
	return '<!-- wp:sennen/texture-hero {"label":"Stories of Transformation","heading":"What Clients Say","subtitle":"Words from those who have walked this path with me. Each story is a testament to the power of presence, compassion, and intentional healing."} /-->' . "\n" .
		'<!-- wp:sennen/testimonial-cards {"count":9} /-->';
}

/**
 * Build Booking page blocks.
 *
 * @param array $data Booking data.
 * @return string
 */
function sennen_core_build_booking_blocks( array $data ): string {
	$heading  = $data['heroHeading'] ?? 'Begin Your Journey';
	$subtitle = $data['heroSubtitle'] ?? 'Take a deep breath. Inquire about a session below, or simply send a note to connect. I look forward to holding space for you.';
	$schedule = $data['scheduleHeading'] ?? 'Schedule Your Session';
	$contact  = $data['contactHeading'] ?? 'Or send a gentle note';
	$faq_head = $data['faqHeading'] ?? 'Good to know';
	$faqs     = $data['faqs'] ?? array();

	if ( empty( $faqs ) ) {
		$faqs = array(
			array( 'question' => 'Where do sessions take place?', 'answer' => 'In-person sessions are held at my private shala in Ubud. Virtual sessions take place via Zoom.' ),
			array( 'question' => 'What is your cancellation policy?', 'answer' => 'I ask for 48 hours notice for a full refund, honoring both your time and mine.' ),
		);
	}

	$faqs_json = wp_json_encode( $faqs );

	return '<!-- wp:sennen/texture-hero {"heading":' . wp_json_encode( $heading ) . ',"subtitle":' . wp_json_encode( $subtitle ) . '} /-->' . "\n" .
		'<!-- wp:sennen/booking-contact-grid {"scheduleHeading":' . wp_json_encode( $schedule ) . ',"contactHeading":' . wp_json_encode( $contact ) . ',"faqHeading":' . wp_json_encode( $faq_head ) . ',"faqs":' . $faqs_json . '} /-->';
}

/**
 * Return a simple icon glyph for imported philosophy cards.
 *
 * @param string $icon_name Icon name from Sanity.
 * @return string
 */
function sennen_core_icon_glyph( string $icon_name ): string {
	return match ( $icon_name ) {
		'Droplet' => '◌',
		'Leaf'    => '☘',
		'Sun'     => '☼',
		'Heart'   => '♡',
		default   => '✺',
	};
}
