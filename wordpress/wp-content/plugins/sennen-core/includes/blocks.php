<?php
/**
 * Custom Gutenberg blocks for Sennen Core.
 *
 * @package SennenCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Sennen block category.
 *
 * @param array $categories Existing block categories.
 * @return array
 */
function sennen_core_block_category( array $categories ): array {
	$categories[] = array(
		'slug'  => 'sennen-sections',
		'title' => __( 'Sennen Sections', 'sennen-core' ),
		'icon'  => null,
	);
	return $categories;
}
add_filter( 'block_categories_all', 'sennen_core_block_category', 10, 1 );

/**
 * Register all custom blocks.
 */
function sennen_core_register_blocks(): void {
	// Exact Home page blocks.
	register_block_type( SENNEN_CORE_PATH . 'src/blocks/home-hero' );
	register_block_type( SENNEN_CORE_PATH . 'src/blocks/home-philosophy' );
	register_block_type( SENNEN_CORE_PATH . 'src/blocks/home-testimonials-section' );

	// Exact About page blocks.
	register_block_type( SENNEN_CORE_PATH . 'src/blocks/about-hero' );
	register_block_type( SENNEN_CORE_PATH . 'src/blocks/about-journey-sections' );
	register_block_type( SENNEN_CORE_PATH . 'src/blocks/about-philosophy-cards' );
	register_block_type( SENNEN_CORE_PATH . 'src/blocks/about-gallery' );

	// Exact Services page blocks.
	register_block_type( SENNEN_CORE_PATH . 'src/blocks/texture-hero' );
	register_block_type( SENNEN_CORE_PATH . 'src/blocks/services-philosophy' );
	register_block_type( SENNEN_CORE_PATH . 'src/blocks/service-cards' );
	register_block_type( SENNEN_CORE_PATH . 'src/blocks/bespoke-cta' );

	// Exact Testimonials page blocks.
	register_block_type( SENNEN_CORE_PATH . 'src/blocks/testimonial-cards' );

	// Exact Booking page blocks.
	register_block_type( SENNEN_CORE_PATH . 'src/blocks/booking-contact-grid' );

	// Shared blocks.
	register_block_type( SENNEN_CORE_PATH . 'src/blocks/botanical-divider' );

	// Service list block.
	register_block_type( SENNEN_CORE_PATH . 'src/blocks/service-list' );

	// Testimonial list block.
	register_block_type( SENNEN_CORE_PATH . 'src/blocks/testimonial-list' );

	// Booking embed block.
	register_block_type( SENNEN_CORE_PATH . 'src/blocks/booking-embed' );

	// Contact form block.
	register_block_type( SENNEN_CORE_PATH . 'src/blocks/contact-form' );
}
add_action( 'init', 'sennen_core_register_blocks' );

/**
 * Enqueue block editor assets.
 */
function sennen_core_enqueue_block_editor_assets(): void {
	$script_path = SENNEN_CORE_PATH . 'assets/js/editor-blocks.js';

	wp_enqueue_script(
		'sennen-core-editor-blocks',
		SENNEN_CORE_URL . 'assets/js/editor-blocks.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n', 'wp-server-side-render' ),
		file_exists( $script_path ) ? (string) filemtime( $script_path ) : SENNEN_CORE_VERSION,
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'sennen_core_enqueue_block_editor_assets' );
