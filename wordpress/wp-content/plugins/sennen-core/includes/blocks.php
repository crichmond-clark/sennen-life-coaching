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
	// Service list block.
	register_block_type( SENNEN_CORE_PATH . 'src/blocks/service-list' );

	// Testimonial list block.
	register_block_type( SENNEN_CORE_PATH . 'src/blocks/testimonial-list' );

	// Botanical divider block.
	register_block_type( SENNEN_CORE_PATH . 'src/blocks/botanical-divider' );
}
add_action( 'init', 'sennen_core_register_blocks' );

/**
 * Enqueue block editor assets.
 */
function sennen_core_enqueue_block_editor_assets(): void {
	wp_enqueue_script(
		'sennen-core-blocks',
		SENNEN_CORE_URL . 'build/blocks.js',
		array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n' ),
		SENNEN_CORE_VERSION,
		true
	);
}
// Editor JS enqueued conditionally when build/blocks.js exists.
// add_action( 'enqueue_block_editor_assets', 'sennen_core_enqueue_block_editor_assets' );
