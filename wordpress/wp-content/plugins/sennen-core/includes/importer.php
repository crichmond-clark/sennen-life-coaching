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
	$heading     = $data['heroHeading'] ?? 'Rooted in Grace';
	$subtitle    = $data['heroSubtitle'] ?? 'A sanctuary for spiritual alignment, mindful wellness, and the slow-living philosophy. Breathe deeply, you have arrived.';
	$cta         = $data['heroCtaText'] ?? 'Begin Your Journey';
	$philosophy  = $data['philosophyHeading'] ?? 'The Art of Slowing Down.';
	$body_blocks = $data['philosophyBody'] ?? array();

	return sprintf(
		'<!-- wp:group {"align":"full","className":"sennen-cover-hero sennen-home-hero","layout":{"type":"constrained"}} --><div class="wp-block-group alignfull sennen-cover-hero sennen-home-hero"><!-- wp:group {"className":"sennen-hero-content","layout":{"type":"constrained"}} --><div class="wp-block-group sennen-hero-content"><!-- wp:paragraph {"align":"center","className":"is-style-label-caps","style":{"color":{"text":"#2d5a27"}}} --><p class="has-text-align-center is-style-label-caps has-text-color" style="color:#2d5a27">Find Your Center</p><!-- /wp:paragraph --><!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontSize":"var:preset|font-size|display-xl"},"color":{"text":"#2d5a27"}}} --><h1 class="wp-block-heading has-text-align-center has-text-color" style="color:#2d5a27;font-size:var(--wp--preset--font-size--display-xl)">%s</h1><!-- /wp:heading --><!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"var:preset|font-size|body-lg","fontWeight":"300"},"color":{"text":"#42493e"}}} --><p class="has-text-align-center has-text-color" style="color:#42493e;font-size:var(--wp--preset--font-size--body-lg);font-weight:300">%s</p><!-- /wp:paragraph --><!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} --><div class="wp-block-buttons"><!-- wp:button {"className":"is-style-sennen-primary"} --><div class="wp-block-button is-style-sennen-primary"><a class="wp-block-button__link wp-element-button" href="/booking">%s</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:group --></div><!-- /wp:group -->%s<!-- wp:group {"align":"wide","className":"sennen-section sennen-philosophy-section","layout":{"type":"constrained"}} --><div class="wp-block-group alignwide sennen-section sennen-philosophy-section"><!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":"var:preset|spacing|section-gap"}}} --><div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"45%%"} --><div class="wp-block-column is-vertically-aligned-center" style="flex-basis:45%%"><!-- wp:heading {"level":2,"style":{"typography":{"fontSize":"var:preset|font-size|headline-lg"},"color":{"text":"#2d5a27"}}} --><h2 class="wp-block-heading has-text-color" style="color:#2d5a27;font-size:var(--wp--preset--font-size--headline-lg)">%s</h2><!-- /wp:heading -->%s<!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button {"className":"is-style-sennen-secondary"} --><div class="wp-block-button is-style-sennen-secondary"><a class="wp-block-button__link wp-element-button" href="/about">Our Story</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:column --><!-- wp:column {"verticalAlignment":"center","width":"55%%"} --><div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%%"><!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"is-style-organic-shape-1 sennen-hover-image"} --><figure class="wp-block-image size-large is-style-organic-shape-1 sennen-hover-image"><img src="/images/philosophy.jpg" alt="Woman in peaceful meditation in a natural setting"/></figure><!-- /wp:image --></div><!-- /wp:column --></div><!-- /wp:columns --></div><!-- /wp:group --><!-- wp:group {"align":"full","className":"sennen-section sennen-testimonials-shell","style":{"color":{"background":"#f6f2ea"}},"layout":{"type":"constrained"}} --><div class="wp-block-group alignfull sennen-section sennen-testimonials-shell has-background" style="background-color:#f6f2ea"><!-- wp:sennen/botanical-divider {"opacity":30} /--><!-- wp:paragraph {"align":"center","className":"is-style-label-caps","style":{"color":{"text":"#984623"}}} --><p class="has-text-align-center is-style-label-caps has-text-color" style="color:#984623">Testimonials</p><!-- /wp:paragraph --><!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"var:preset|font-size|headline-lg"},"color":{"text":"#2d5a27"}}} --><h2 class="wp-block-heading has-text-align-center has-text-color" style="color:#2d5a27;font-size:var(--wp--preset--font-size--headline-lg)">What Clients Say</h2><!-- /wp:heading --><!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"var:preset|font-size|body-lg","fontWeight":"300"},"color":{"text":"#42493e"}}} --><p class="has-text-align-center has-text-color" style="color:#42493e;font-size:var(--wp--preset--font-size--body-lg);font-weight:300">Stories from those who have walked this path with me.</p><!-- /wp:paragraph --><!-- wp:sennen/testimonial-list {"columns":3,"count":3} /--></div><!-- /wp:group -->',
		esc_html( $heading ),
		esc_html( $subtitle ),
		esc_html( $cta ),
		'<!-- wp:sennen/botanical-divider {"opacity":30} /-->',
		esc_html( $philosophy ),
		sennen_core_portable_text_to_blocks( $body_blocks )
	);
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
	$journeys = $data['journeySections'] ?? array();
	$cards    = $data['philosophyCards'] ?? array();

	$journey_markup = '';
	foreach ( $journeys as $section ) {
		$journey_markup .= sprintf(
			'<!-- wp:group {"align":"wide","className":"sennen-section sennen-journey-section","layout":{"type":"constrained"}} --><div class="wp-block-group alignwide sennen-section sennen-journey-section"><!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":"var:preset|spacing|section-gap"}}} --><div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center"} --><div class="wp-block-column is-vertically-aligned-center"><!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"is-style-organic-shape-1 sennen-hover-image"} --><figure class="wp-block-image size-large is-style-organic-shape-1 sennen-hover-image"><img src="/images/journey.jpg" alt="%s"/></figure><!-- /wp:image --></div><!-- /wp:column --><!-- wp:column {"verticalAlignment":"center"} --><div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading {"level":2,"style":{"typography":{"fontSize":"var:preset|font-size|headline-lg"},"color":{"text":"#2d5a27"}}} --><h2 class="wp-block-heading has-text-color" style="color:#2d5a27;font-size:var(--wp--preset--font-size--headline-lg)">%s</h2><!-- /wp:heading --><!-- wp:paragraph {"style":{"typography":{"fontSize":"var:preset|font-size|body-md","fontWeight":"300","lineHeight":"1.8"},"color":{"text":"#42493e"}}} --><p class="has-text-color" style="color:#42493e;font-size:var(--wp--preset--font-size--body-md);font-weight:300;line-height:1.8;white-space:pre-line">%s</p><!-- /wp:paragraph --></div><!-- /wp:column --></div><!-- /wp:columns --></div><!-- /wp:group -->',
			esc_attr( $section['heading'] ?? 'Meditation in nature' ),
			esc_html( $section['heading'] ?? 'The Awakening in Chiang Mai' ),
			esc_html( $section['body'] ?? '' )
		);
	}

	$cards_markup = '';
	foreach ( $cards as $index => $card ) {
		$cards_markup .= sprintf(
			'<!-- wp:column {"className":"%s"} --><div class="wp-block-column %s"><!-- wp:group {"className":"is-style-ambient-card sennen-philosophy-card","layout":{"type":"constrained"}} --><div class="wp-block-group is-style-ambient-card sennen-philosophy-card"><!-- wp:paragraph {"className":"sennen-card-icon","style":{"color":{"text":"#2d5a27"}}} --><p class="sennen-card-icon has-text-color" style="color:#2d5a27">%s</p><!-- /wp:paragraph --><!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"var:preset|font-size|headline-md"},"color":{"text":"#984623"}}} --><h3 class="wp-block-heading has-text-color" style="color:#984623;font-size:var(--wp--preset--font-size--headline-md)">%s</h3><!-- /wp:heading --><!-- wp:paragraph {"style":{"typography":{"fontSize":"var:preset|font-size|body-md","fontWeight":"300","lineHeight":"1.7"}}} --><p style="font-size:var(--wp--preset--font-size--body-md);font-weight:300;line-height:1.7">%s</p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:column -->',
			1 === $index ? 'sennen-card-lifted' : '',
			1 === $index ? 'sennen-card-lifted' : '',
			esc_html( sennen_core_icon_glyph( $card['iconName'] ?? 'Flower' ) ),
			esc_html( $card['title'] ?? '' ),
			esc_html( $card['body'] ?? '' )
		);
	}

	return sprintf(
		'<!-- wp:group {"align":"full","className":"sennen-cover-hero sennen-about-hero","layout":{"type":"constrained"}} --><div class="wp-block-group alignfull sennen-cover-hero sennen-about-hero"><!-- wp:group {"className":"sennen-hero-content","layout":{"type":"constrained"}} --><div class="wp-block-group sennen-hero-content"><!-- wp:paragraph {"align":"center","className":"is-style-label-caps","style":{"color":{"text":"#984623"}}} --><p class="has-text-align-center is-style-label-caps has-text-color" style="color:#984623">The Guide</p><!-- /wp:paragraph --><!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontSize":"var:preset|font-size|display-xl"},"color":{"text":"#2d5a27"}}} --><h1 class="wp-block-heading has-text-align-center has-text-color" style="color:#2d5a27;font-size:var(--wp--preset--font-size--display-xl)">%s</h1><!-- /wp:heading --><!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"var:preset|font-size|body-lg","fontWeight":"300","lineHeight":"1.7"},"color":{"text":"#42493e"}}} --><p class="has-text-align-center has-text-color" style="color:#42493e;font-size:var(--wp--preset--font-size--body-lg);font-weight:300;line-height:1.7">%s</p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:group -->%s<!-- wp:group {"align":"full","className":"sennen-section sennen-philosophy-cards-section","style":{"color":{"background":"#f6f2ea"}},"layout":{"type":"constrained"}} --><div class="wp-block-group alignfull sennen-section sennen-philosophy-cards-section has-background" style="background-color:#f6f2ea"><!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"var:preset|font-size|headline-lg"},"color":{"text":"#2d5a27"}}} --><h2 class="wp-block-heading has-text-align-center has-text-color" style="color:#2d5a27;font-size:var(--wp--preset--font-size--headline-lg)">My Philosophy</h2><!-- /wp:heading --><!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"var:preset|font-size|body-lg","fontWeight":"300"},"color":{"text":"#42493e"}}} --><p class="has-text-align-center has-text-color" style="color:#42493e;font-size:var(--wp--preset--font-size--body-lg);font-weight:300">Guiding principles for a soulful existence.</p><!-- /wp:paragraph --><!-- wp:columns {"style":{"spacing":{"blockGap":"var:preset|spacing|gutter"}}} --><div class="wp-block-columns">%s</div><!-- /wp:columns --></div><!-- /wp:group --><!-- wp:group {"align":"wide","className":"sennen-section sennen-gallery-section","layout":{"type":"constrained"}} --><div class="wp-block-group alignwide sennen-section sennen-gallery-section"><!-- wp:heading {"textAlign":"center","level":2,"style":{"color":{"text":"#2d5a27"}}} --><h2 class="wp-block-heading has-text-align-center has-text-color" style="color:#2d5a27">Fragments of the Journey</h2><!-- /wp:heading --><!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":"var:preset|spacing|gutter"}}} --><div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"width":"58%%"} --><div class="wp-block-column" style="flex-basis:58%%"><!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"is-style-organic-shape-2 sennen-hover-image"} --><figure class="wp-block-image size-large is-style-organic-shape-2 sennen-hover-image"><img src="/images/yoga-shala.jpg" alt="Peaceful yoga shala in nature"/></figure><!-- /wp:image --></div><!-- /wp:column --><!-- wp:column {"width":"42%%"} --><div class="wp-block-column" style="flex-basis:42%%"><!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"is-style-organic-shape-3 sennen-hover-image"} --><figure class="wp-block-image size-large is-style-organic-shape-3 sennen-hover-image"><img src="/images/ceramics.jpg" alt="Ceramics and incense"/></figure><!-- /wp:image --><!-- wp:image {"sizeSlug":"medium","linkDestination":"none","className":"sennen-round-image sennen-hover-image"} --><figure class="wp-block-image size-medium sennen-round-image sennen-hover-image"><img src="/images/meditation.jpg" alt="Connecting with nature"/></figure><!-- /wp:image --></div><!-- /wp:column --></div><!-- /wp:columns --></div><!-- /wp:group -->',
		esc_html( $heading ),
		esc_html( $subtitle ),
		$journey_markup,
		$cards_markup
	);
}

/**
 * Build Services page blocks.
 *
 * @return string
 */
function sennen_core_build_services_page_blocks(): string {
	return '<!-- wp:group {"align":"full","className":"sennen-texture-hero sennen-services-hero","layout":{"type":"constrained"}} --><div class="wp-block-group alignfull sennen-texture-hero sennen-services-hero"><!-- wp:paragraph {"align":"center","className":"is-style-label-caps","style":{"color":{"text":"#984623"}}} --><p class="has-text-align-center is-style-label-caps has-text-color" style="color:#984623">Offerings</p><!-- /wp:paragraph --><!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontSize":"var:preset|font-size|display-xl"},"color":{"text":"#2d5a27"}}} --><h1 class="wp-block-heading has-text-align-center has-text-color" style="color:#2d5a27;font-size:var(--wp--preset--font-size--display-xl)">Nourish Your Spirit</h1><!-- /wp:heading --><!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"var:preset|font-size|body-lg","fontWeight":"300"},"color":{"text":"#42493e"}}} --><p class="has-text-align-center has-text-color" style="color:#42493e;font-size:var(--wp--preset--font-size--body-lg);font-weight:300">Whether you need a gentle reset or a deep transformative journey, these offerings hold the space for your unwinding. Choose the path that calls to your current season.</p><!-- /wp:paragraph --></div><!-- /wp:group --><!-- wp:group {"align":"wide","className":"sennen-section","layout":{"type":"constrained"}} --><div class="wp-block-group alignwide sennen-section"><!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":"var:preset|spacing|section-gap"}}} --><div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column --><div class="wp-block-column"><!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"is-style-organic-shape-3 sennen-hover-image"} --><figure class="wp-block-image size-large is-style-organic-shape-3 sennen-hover-image"><img src="/images/tea-ritual.jpg" alt="Hands holding tea bowl in peaceful ritual"/></figure><!-- /wp:image --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":2,"style":{"color":{"text":"#2d5a27"}}} --><h2 class="wp-block-heading has-text-color" style="color:#2d5a27">Not fixing, just remembering.</h2><!-- /wp:heading --><!-- wp:paragraph {"style":{"typography":{"fontSize":"var:preset|font-size|body-md","fontWeight":"300","lineHeight":"1.8"},"color":{"text":"#42493e"}}} --><p class="has-text-color" style="color:#42493e;font-size:var(--wp--preset--font-size--body-md);font-weight:300;line-height:1.8">My approach does not assume you are broken. Instead, these sessions are designed to help you peel back the layers of conditioning, stress, and noise to remember the wholeness that already resides within you.</p><!-- /wp:paragraph --><!-- wp:paragraph {"style":{"typography":{"fontSize":"var:preset|font-size|body-md","fontWeight":"300","lineHeight":"1.8"},"color":{"text":"#42493e"}}} --><p class="has-text-color" style="color:#42493e;font-size:var(--wp--preset--font-size--body-md);font-weight:300;line-height:1.8">We move slowly, respecting the pace of your nervous system. Every offering is an invitation, never a demand.</p><!-- /wp:paragraph --></div><!-- /wp:column --></div><!-- /wp:columns --></div><!-- /wp:group --><!-- wp:group {"align":"full","className":"sennen-section","layout":{"type":"constrained"}} --><div class="wp-block-group alignfull sennen-section"><!-- wp:sennen/service-list {"columns":3,"count":3,"showFeatures":true,"buttonText":"Inquire Now","buttonUrl":"/booking"} /--></div><!-- /wp:group --><!-- wp:group {"align":"wide","className":"sennen-section sennen-bespoke-cta","layout":{"type":"constrained"}} --><div class="wp-block-group alignwide sennen-section sennen-bespoke-cta"><!-- wp:heading {"textAlign":"center","level":2,"style":{"color":{"text":"#2d5a27"}}} --><h2 class="wp-block-heading has-text-align-center has-text-color" style="color:#2d5a27">Need something bespoke?</h2><!-- /wp:heading --><!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"var:preset|font-size|body-lg","fontWeight":"300"},"color":{"text":"#42493e"}}} --><p class="has-text-align-center has-text-color" style="color:#42493e;font-size:var(--wp--preset--font-size--body-lg);font-weight:300">I occasionally take on bespoke retreats or group facilitation. If you have a specific vision, let\'s explore it together.</p><!-- /wp:paragraph --><!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} --><div class="wp-block-buttons"><!-- wp:button {"className":"is-style-sennen-secondary"} --><div class="wp-block-button is-style-sennen-secondary"><a class="wp-block-button__link wp-element-button" href="/booking">Send an Inquiry</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:group -->';
}

/**
 * Build Testimonials page blocks.
 *
 * @return string
 */
function sennen_core_build_testimonials_page_blocks(): string {
	return '<!-- wp:group {"align":"full","className":"sennen-texture-hero sennen-testimonials-hero","layout":{"type":"constrained"}} --><div class="wp-block-group alignfull sennen-texture-hero sennen-testimonials-hero"><!-- wp:paragraph {"align":"center","className":"is-style-label-caps","style":{"color":{"text":"#984623"}}} --><p class="has-text-align-center is-style-label-caps has-text-color" style="color:#984623">Stories of Transformation</p><!-- /wp:paragraph --><!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontSize":"var:preset|font-size|display-xl"},"color":{"text":"#2d5a27"}}} --><h1 class="wp-block-heading has-text-align-center has-text-color" style="color:#2d5a27;font-size:var(--wp--preset--font-size--display-xl)">What Clients Say</h1><!-- /wp:heading --><!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"var:preset|font-size|body-lg","fontWeight":"300"},"color":{"text":"#42493e"}}} --><p class="has-text-align-center has-text-color" style="color:#42493e;font-size:var(--wp--preset--font-size--body-lg);font-weight:300">Words from those who have walked this path with me. Each story is a testament to the power of presence, compassion, and intentional healing.</p><!-- /wp:paragraph --></div><!-- /wp:group --><!-- wp:group {"align":"wide","className":"sennen-section","layout":{"type":"constrained"}} --><div class="wp-block-group alignwide sennen-section"><!-- wp:sennen/testimonial-list {"columns":3,"count":9,"showAvatar":true} /--></div><!-- /wp:group -->';
}

/**
 * Build Booking page blocks.
 *
 * @param array $data Booking data.
 * @return string
 */
function sennen_core_build_booking_blocks( array $data ): string {
	$heading         = $data['heroHeading'] ?? 'Begin Your Journey';
	$subtitle        = $data['heroSubtitle'] ?? 'Take a deep breath. Inquire about a session below, or simply send a note to connect. I look forward to holding space for you.';
	$schedule_heading = $data['scheduleHeading'] ?? 'Schedule Your Session';
	$contact_heading  = $data['contactHeading'] ?? 'Or send a gentle note';
	$faq_heading      = $data['faqHeading'] ?? 'Good to know';
	$faqs             = $data['faqs'] ?? array();

	$faq_markup = '';
	foreach ( $faqs as $faq ) {
		$faq_markup .= sprintf(
			'<!-- wp:group {"className":"sennen-faq-item","layout":{"type":"constrained"}} --><div class="wp-block-group sennen-faq-item"><!-- wp:heading {"level":4,"style":{"typography":{"fontSize":"var:preset|font-size|body-md","fontWeight":"700"}}} --><h4 class="wp-block-heading" style="font-size:var(--wp--preset--font-size--body-md);font-weight:700">%s</h4><!-- /wp:heading --><!-- wp:paragraph {"style":{"typography":{"fontSize":"var:preset|font-size|body-md","fontWeight":"300"},"color":{"text":"#42493e"}}} --><p class="has-text-color" style="color:#42493e;font-size:var(--wp--preset--font-size--body-md);font-weight:300">%s</p><!-- /wp:paragraph --></div><!-- /wp:group -->',
			esc_html( $faq['question'] ?? '' ),
			esc_html( $faq['answer'] ?? '' )
		);
	}

	return sprintf(
		'<!-- wp:group {"align":"full","className":"sennen-texture-hero sennen-booking-hero","layout":{"type":"constrained"}} --><div class="wp-block-group alignfull sennen-texture-hero sennen-booking-hero"><!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontSize":"var:preset|font-size|display-xl"},"color":{"text":"#2d5a27"}}} --><h1 class="wp-block-heading has-text-align-center has-text-color" style="color:#2d5a27;font-size:var(--wp--preset--font-size--display-xl)">%s</h1><!-- /wp:heading --><!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"var:preset|font-size|body-lg","fontWeight":"300"},"color":{"text":"#42493e"}}} --><p class="has-text-align-center has-text-color" style="color:#42493e;font-size:var(--wp--preset--font-size--body-lg);font-weight:300">%s</p><!-- /wp:paragraph --></div><!-- /wp:group --><!-- wp:group {"align":"wide","className":"sennen-booking-grid","layout":{"type":"constrained"}} --><div class="wp-block-group alignwide sennen-booking-grid"><!-- wp:columns {"style":{"spacing":{"blockGap":"var:preset|spacing|section-gap"}}} --><div class="wp-block-columns"><!-- wp:column {"width":"50%%"} --><div class="wp-block-column" style="flex-basis:50%%"><!-- wp:group {"className":"is-style-ambient-card sennen-booking-card","layout":{"type":"constrained"}} --><div class="wp-block-group is-style-ambient-card sennen-booking-card"><!-- wp:heading {"level":2,"style":{"typography":{"fontSize":"var:preset|font-size|headline-md"},"color":{"text":"#2d5a27"}}} --><h2 class="wp-block-heading has-text-color" style="color:#2d5a27;font-size:var(--wp--preset--font-size--headline-md)">%s</h2><!-- /wp:heading --><!-- wp:sennen/booking-embed {"useGlobalUrl":true,"minHeight":650} /--></div><!-- /wp:group --></div><!-- /wp:column --><!-- wp:column {"width":"50%%"} --><div class="wp-block-column" style="flex-basis:50%%"><!-- wp:heading {"level":2,"style":{"typography":{"fontSize":"var:preset|font-size|headline-md"},"color":{"text":"#2d5a27"}}} --><h2 class="wp-block-heading has-text-color" style="color:#2d5a27;font-size:var(--wp--preset--font-size--headline-md)">%s</h2><!-- /wp:heading --><!-- wp:sennen/contact-form /--><!-- wp:group {"className":"sennen-faq-list","layout":{"type":"constrained"}} --><div class="wp-block-group sennen-faq-list"><!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"var:preset|font-size|headline-md"},"color":{"text":"#2d5a27"}}} --><h3 class="wp-block-heading has-text-color" style="color:#2d5a27;font-size:var(--wp--preset--font-size--headline-md)">%s</h3><!-- /wp:heading -->%s</div><!-- /wp:group --></div><!-- /wp:column --></div><!-- /wp:columns --></div><!-- /wp:group -->',
		esc_html( $heading ),
		esc_html( $subtitle ),
		esc_html( $schedule_heading ),
		esc_html( $contact_heading ),
		esc_html( $faq_heading ),
		$faq_markup
	);
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
