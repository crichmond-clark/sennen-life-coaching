<?php
/**
 * Sennen Life Coaching theme setup.
 *
 * @package Sennen
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load shell renderers (nav + footer).
require_once get_template_directory() . '/includes/render-shell.php';

/**
 * Replace block header/footer template parts with exact Sennen renderers.
 */
function sennen_render_template_part( string $block_content, array $block ): string {
	if ( ! isset( $block['attrs']['slug'] ) ) {
		return $block_content;
	}

	if ( 'header' === $block['attrs']['slug'] ) {
		ob_start();
		sennen_render_nav();
		return ob_get_clean();
	}

	if ( 'footer' === $block['attrs']['slug'] ) {
		ob_start();
		sennen_render_footer();
		return ob_get_clean();
	}

	return $block_content;
}
add_filter( 'render_block_core/template-part', 'sennen_render_template_part', 10, 2 );

/**
 * Add parity body classes matching the Next.js layout shell.
 */
function sennen_body_classes( array $classes ): array {
	$classes[] = 'bg-background';
	$classes[] = 'text-on-background';
	$classes[] = 'text-body-md';
	$classes[] = 'antialiased';
	$classes[] = 'overflow-x-hidden';
	$classes[] = 'min-h-screen';
	$classes[] = 'flex';
	$classes[] = 'flex-col';
	return $classes;
}
add_filter( 'body_class', 'sennen_body_classes' );

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

	// Parity CSS — compiled from the same Tailwind design system as Next.js.
	if ( file_exists( get_template_directory() . '/assets/css/sennen-parity.css' ) ) {
		wp_enqueue_style(
			'sennen-parity',
			get_template_directory_uri() . '/assets/css/sennen-parity.css',
			array(),
			$theme_version
		);
	}

	// Frontend CSS — WordPress-specific overrides (kept minimal).
	wp_enqueue_style(
		'sennen-frontend',
		get_template_directory_uri() . '/assets/css/frontend.css',
		array( 'sennen-parity' ),
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

	// Frontend JS — nav scroll state, mobile menu, contact form, booking embed.
	wp_enqueue_script(
		'sennen-frontend',
		get_template_directory_uri() . '/assets/js/sennen-frontend.js',
		array(),
		$theme_version,
		true
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

/**
 * Output basic Open Graph meta tags.
 */
function sennen_opengraph_meta(): void {
	if ( is_singular() ) {
		global $post;
		$excerpt = has_excerpt( $post->ID )
			? wp_strip_all_tags( get_the_excerpt( $post->ID ) )
			: wp_trim_words( wp_strip_all_tags( $post->post_content ), 30 );

		echo '<meta property="og:title" content="' . esc_attr( get_the_title() ) . '">' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( $excerpt ) . '">' . "\n";
		echo '<meta property="og:url" content="' . esc_url( get_permalink() ) . '">' . "\n";

		if ( has_post_thumbnail( $post->ID ) ) {
			echo '<meta property="og:image" content="' . esc_url( get_the_post_thumbnail_url( $post->ID, 'large' ) ) . '">' . "\n";
		}
	} else {
		echo '<meta property="og:title" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( get_bloginfo( 'description' ) ) . '">' . "\n";
		echo '<meta property="og:url" content="' . esc_url( home_url() ) . '">' . "\n";
	}

	echo '<meta property="og:type" content="website">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
}
add_action( 'wp_head', 'sennen_opengraph_meta' );

/**
 * Add skip-to-content link for keyboard accessibility.
 */
function sennen_skip_to_content(): void {
	echo '<a class="skip-link screen-reader-text" href="#main-content">' . esc_html__( 'Skip to content', 'sennen' ) . '</a>';
}
add_action( 'wp_body_open', 'sennen_skip_to_content' );

/**
 * Remove WP block library CSS on pages with no blocks that need it.
 * Keeps it for admin/editor and pages that may have dynamic content.
 */
function sennen_optimize_block_styles(): void {
	// Always keep block styles — removing them breaks the theme.
	// Instead, we ensure we only enqueue what's needed above.
}

/**
 * Ensure WordPress core sitemap is enabled.
 */
function sennen_ensure_sitemap(): void {
	if ( ! get_option( 'blog_public' ) ) {
		update_option( 'blog_public', 1 );
	}
}
add_action( 'after_switch_theme', 'sennen_ensure_sitemap' );
