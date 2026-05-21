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
	sennen_core_import_pages( $dry_run );
	sennen_core_import_services( $dry_run );
	sennen_core_import_testimonials( $dry_run );

	WP_CLI::success( 'Import complete.' );
}

/**
 * Import site settings from content/site-settings.json.
 *
 * @param bool $dry_run Whether to perform a dry run.
 */
function sennen_core_import_site_settings( bool $dry_run ): void {
	$file = ABSPATH . 'content/site-settings.json';

	if ( ! file_exists( $file ) ) {
		WP_CLI::warning( 'site-settings.json not found.' );
		return;
	}

	$data = json_decode( file_get_contents( $file ), true );
	if ( ! $data ) {
		WP_CLI::warning( 'Could not parse site-settings.json.' );
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
 * Import pages from content JSON files.
 *
 * @param bool $dry_run Whether to perform a dry run.
 */
function sennen_core_import_pages( bool $dry_run ): void {
	$pages = array(
		'home.json'    => __( 'Home', 'sennen-core' ),
		'about.json'   => __( 'About', 'sennen-core' ),
		'booking.json' => __( 'Booking', 'sennen-core' ),
	);

	foreach ( $pages as $file_name => $default_title ) {
		$file = ABSPATH . 'content/' . $file_name;

		if ( ! file_exists( $file ) ) {
			WP_CLI::warning( "$file_name not found." );
			continue;
		}

		$data = json_decode( file_get_contents( $file ), true );
		if ( ! $data ) {
			WP_CLI::warning( "Could not parse $file_name." );
			continue;
		}

		if ( $dry_run ) {
			WP_CLI::log( "  Would create page: $default_title" );
			continue;
		}

		// Build block markup from the JSON content.
		$content = sennen_core_build_page_blocks( $data );
		if ( empty( $content ) ) {
			continue;
		}

		$post_data = array(
			'post_title'   => $default_title,
			'post_content' => $content,
			'post_status'  => 'publish',
			'post_type'    => 'page',
		);

		wp_insert_post( $post_data );
		WP_CLI::log( "  ✓ Created page: $default_title" );
	}
}

/**
 * Import services from content/services.json.
 *
 * @param bool $dry_run Whether to perform a dry run.
 */
function sennen_core_import_services( bool $dry_run ): void {
	$file = ABSPATH . 'content/services.json';

	if ( ! file_exists( $file ) ) {
		WP_CLI::warning( 'services.json not found.' );
		return;
	}

	$services = json_decode( file_get_contents( $file ), true );
	if ( ! $services ) {
		WP_CLI::warning( 'Could not parse services.json.' );
		return;
	}

	foreach ( $services as $service ) {
		if ( $dry_run ) {
			WP_CLI::log( '  Would create service: ' . ( $service['title'] ?? 'Untitled' ) );
			continue;
		}

		$post_id = wp_insert_post(
			array(
				'post_title'   => sanitize_text_field( $service['title'] ?? '' ),
				'post_content' => sanitize_textarea_field( $service['description'] ?? '' ),
				'post_excerpt' => sanitize_textarea_field( $service['description'] ?? '' ),
				'post_status'  => 'publish',
				'post_type'    => 'sennen_service',
				'menu_order'   => intval( $service['sortOrder'] ?? 0 ),
			)
		);

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, '_sennen_service_price', sanitize_text_field( $service['price'] ?? '' ) );
			update_post_meta( $post_id, '_sennen_service_duration', sanitize_text_field( $service['duration'] ?? '' ) );
			if ( ! empty( $service['features'] ) ) {
				update_post_meta( $post_id, '_sennen_service_features', array_map( 'sanitize_text_field', $service['features'] ) );
			}
			update_post_meta( $post_id, '_sennen_service_sort_order', intval( $service['sortOrder'] ?? 0 ) );
		}

		WP_CLI::log( '  ✓ Created service: ' . ( $service['title'] ?? 'Untitled' ) );
	}
}

/**
 * Import testimonials from content/testimonials.json.
 *
 * @param bool $dry_run Whether to perform a dry run.
 */
function sennen_core_import_testimonials( bool $dry_run ): void {
	$file = ABSPATH . 'content/testimonials.json';

	if ( ! file_exists( $file ) ) {
		WP_CLI::warning( 'testimonials.json not found.' );
		return;
	}

	$testimonials = json_decode( file_get_contents( $file ), true );
	if ( ! $testimonials ) {
		WP_CLI::warning( 'Could not parse testimonials.json.' );
		return;
	}

	foreach ( $testimonials as $testimonial ) {
		if ( $dry_run ) {
			WP_CLI::log( '  Would create testimonial: ' . ( $testimonial['authorName'] ?? 'Unknown' ) );
			continue;
		}

		$post_id = wp_insert_post(
			array(
				'post_title'   => sanitize_text_field( $testimonial['authorName'] ?? 'Testimonial' ),
				'post_content' => sanitize_textarea_field( $testimonial['quote'] ?? '' ),
				'post_excerpt' => sanitize_textarea_field( $testimonial['quote'] ?? '' ),
				'post_status'  => 'publish',
				'post_type'    => 'sennen_testimonial',
			)
		);

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, '_sennen_testimonial_author_title', sanitize_text_field( $testimonial['authorTitle'] ?? '' ) );
		}

		WP_CLI::log( '  ✓ Created testimonial: ' . ( $testimonial['authorName'] ?? 'Unknown' ) );
	}
}

/**
 * Build Gutenberg block markup from page JSON data.
 *
 * @param array $data Page content data.
 * @return string Block HTML.
 */
function sennen_core_build_page_blocks( array $data ): string {
	$blocks = '';

	// Hero heading.
	if ( ! empty( $data['heroHeading'] ) ) {
		$blocks .= sprintf(
			'<!-- wp:heading {"level":1,"style":{"typography":{"fontSize":"var(--wp--preset--font-size--display-xl)"},"spacing":{"margin":{"bottom":"var:preset|spacing|gutter"}}}} --><h1 class="wp-block-heading" style="font-size:var(--wp--preset--font-size--display-xl);margin-bottom:var(--wp--preset--spacing--gutter)">%s</h1><!-- /wp:heading -->',
			esc_html( $data['heroHeading'] )
		);
	}

	// Hero subtitle.
	if ( ! empty( $data['heroSubtitle'] ) ) {
		$blocks .= sprintf(
			'<!-- wp:paragraph {"style":{"typography":{"fontSize":"var(--wp--preset--font-size--body-lg)"},"spacing":{"margin":{"bottom":"var:preset|spacing|content-gap"}}}} --><p style="font-size:var(--wp--preset--font-size--body-lg);margin-bottom:var(--wp--preset--spacing--content-gap)">%s</p><!-- /wp:paragraph -->',
			esc_html( $data['heroSubtitle'] )
		);
	}

	// Hero CTA.
	if ( ! empty( $data['heroCtaText'] ) ) {
		$blocks .= sprintf(
			'<!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button {"className":"is-style-sennen-primary"} --><div class="wp-block-button is-style-sennen-primary"><a class="wp-block-button__link wp-element-button" href="/booking">%s</a></div><!-- /wp:button --></div><!-- /wp:buttons -->',
			esc_html( $data['heroCtaText'] )
		);
	}

	return $blocks;
}
