<?php
/**
 * Sennen Life Coaching theme setup.
 *
 * @package Sennen
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme setup.
 */
function sennen_setup(): void {
	// Block theme supports.
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-units', array( 'px', 'rem', 'em', 'vh', 'vw', '%' ) );
}
add_action( 'after_setup_theme', 'sennen_setup' );

/**
 * Enqueue theme assets.
 */
function sennen_enqueue_assets(): void {
	$theme_version = wp_get_theme()->get( 'Version' );

	// Frontend styles (additive to theme.json).
	wp_enqueue_style(
		'sennen-frontend',
		get_template_directory_uri() . '/assets/css/frontend.css',
		array(),
		$theme_version
	);

	// Editor styles.
	add_editor_style( 'assets/css/editor.css' );

	// Self-hosted fonts.
	wp_enqueue_style(
		'sennen-fonts',
		get_template_directory_uri() . '/assets/css/fonts.css',
		array(),
		$theme_version
	);
}
add_action( 'wp_enqueue_scripts', 'sennen_enqueue_assets' );

/**
 * Register block pattern categories.
 */
function sennen_register_pattern_categories(): void {
	if ( ! function_exists( 'register_block_pattern_category' ) ) {
		return;
	}

	register_block_pattern_category(
		'sennen-sections',
		array( 'label' => __( 'Sennen Sections', 'sennen' ) )
	);
}
add_action( 'init', 'sennen_register_pattern_categories' );

/**
 * Register custom block styles for core blocks.
 */
function sennen_register_block_styles(): void {
	register_block_style(
		'core/image',
		array(
			'name'  => 'organic-shape-1',
			'label' => __( 'Organic Shape 1', 'sennen' ),
		)
	);
	register_block_style(
		'core/image',
		array(
			'name'  => 'organic-shape-2',
			'label' => __( 'Organic Shape 2', 'sennen' ),
		)
	);
	register_block_style(
		'core/image',
		array(
			'name'  => 'organic-shape-3',
			'label' => __( 'Organic Shape 3', 'sennen' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'ambient-card',
			'label' => __( 'Ambient Card', 'sennen' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'textured-surface',
			'label' => __( 'Textured Surface', 'sennen' ),
		)
	);
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'label-caps',
			'label' => __( 'Label Caps', 'sennen' ),
		)
	);
	register_block_style(
		'core/button',
		array(
			'name'  => 'sennen-primary',
			'label' => __( 'Sennen Primary', 'sennen' ),
		)
	);
	register_block_style(
		'core/button',
		array(
			'name'  => 'sennen-secondary',
			'label' => __( 'Sennen Secondary', 'sennen' ),
		)
	);
}
add_action( 'init', 'sennen_register_block_styles' );

/**
 * Disable custom font sizes and enforce theme.json size scale.
 */
function sennen_disable_custom_font_sizes(): void {
	add_theme_support( 'editor-font-sizes', array() );
	add_theme_support( 'disable-custom-font-sizes' );
}
add_action( 'after_setup_theme', 'sennen_disable_custom_font_sizes' );

/**
 * Disable custom colors and enforce theme.json palette.
 */
function sennen_disable_custom_colors(): void {
	add_theme_support( 'disable-custom-colors' );
}
add_action( 'after_setup_theme', 'sennen_disable_custom_colors' );
