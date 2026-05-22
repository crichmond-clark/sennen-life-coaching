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

	// Editor iframe styles. These must be registered during theme setup,
	// not wp_enqueue_scripts, otherwise the block editor never receives them.
	add_editor_style( 'assets/css/sennen-parity.css' );
	add_editor_style( 'assets/css/fonts.css' );
	add_editor_style( 'assets/css/editor.css' );

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
 * Enqueue parity assets in the block editor chrome as well as the iframe.
 * add_editor_style() handles the canvas; this makes previews/tooling consistent.
 */
function sennen_enqueue_block_editor_assets(): void {
	$theme_version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'sennen-editor-parity',
		get_template_directory_uri() . '/assets/css/sennen-parity.css',
		array(),
		$theme_version
	);

	wp_enqueue_style(
		'sennen-editor-fonts',
		get_template_directory_uri() . '/assets/css/fonts.css',
		array(),
		$theme_version
	);

	wp_enqueue_style(
		'sennen-editor-overrides',
		get_template_directory_uri() . '/assets/css/editor.css',
		array( 'sennen-editor-parity', 'sennen-editor-fonts' ),
		$theme_version
	);
}
add_action( 'enqueue_block_editor_assets', 'sennen_enqueue_block_editor_assets' );

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
 * Get a concise, stable SEO description for the current request.
 */
function sennen_get_seo_description(): string {
	$default = __( 'A sanctuary for spiritual alignment, mindful wellness, and the slow-living philosophy. Breathe deeply, you have arrived.', 'sennen' );

	if ( is_singular() ) {
		$post = get_queried_object();

		if ( $post instanceof WP_Post ) {
			if ( has_excerpt( $post->ID ) ) {
				return wp_strip_all_tags( get_the_excerpt( $post->ID ) );
			}

			$content = trim( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ) );
			if ( '' !== $content ) {
				return wp_trim_words( $content, 30, '' );
			}
		}
	}

	$slug_descriptions = array(
		'about'        => __( "Learn about Sennen's journey from corporate life to spiritual coaching. Discover the philosophy behind Rooted in Grace.", 'sennen' ),
		'services'     => __( 'Explore coaching offerings from grounding sessions to deep transformative mentorship. Find the path that calls to your current season.', 'sennen' ),
		'testimonials' => __( 'Hear from clients who have walked the path of transformation and healing.', 'sennen' ),
		'booking'      => __( 'Schedule a coaching session or send an inquiry. In-person sessions in Ubud and virtual sessions via Zoom available.', 'sennen' ),
	);

	if ( is_front_page() || is_home() ) {
		return $default;
	}

	if ( is_page() ) {
		$slug = get_post_field( 'post_name', get_queried_object_id() );
		if ( isset( $slug_descriptions[ $slug ] ) ) {
			return $slug_descriptions[ $slug ];
		}
	}

	return get_bloginfo( 'description' ) ?: $default;
}

/**
 * Get the preferred URL for the current request.
 */
function sennen_get_canonical_url(): string {
	if ( is_singular() ) {
		return get_permalink();
	}

	if ( is_front_page() || is_home() ) {
		return home_url( '/' );
	}

	if ( is_post_type_archive() ) {
		$archive_url = get_post_type_archive_link( get_query_var( 'post_type' ) );
		if ( $archive_url ) {
			return $archive_url;
		}
	}

	return home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) );
}

/**
 * Output page metadata, social metadata, and site-level JSON-LD.
 */
function sennen_seo_meta(): void {
	$title       = wp_get_document_title();
	$description = sennen_get_seo_description();
	$url         = sennen_get_canonical_url();
	$site_name   = get_bloginfo( 'name' ) ?: 'Sennen Life Coaching';
	$tagline     = get_bloginfo( 'description' ) ?: 'Rooted in Grace';
	$image       = get_template_directory_uri() . '/assets/images/og-image.jpg';

	if ( is_singular() ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post && has_post_thumbnail( $post->ID ) ) {
			$image = get_the_post_thumbnail_url( $post->ID, 'large' );
		}
	}

	echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	echo '<meta property="og:type" content="' . esc_attr( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '">' . "\n";
	echo '<meta property="og:locale" content="en_US">' . "\n";
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";

	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
		echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
	}

	$schema = array(
		array(
			'@context'    => 'https://schema.org',
			'@type'       => 'ProfessionalService',
			'@id'         => home_url( '/#organization' ),
			'name'        => $site_name,
			'url'         => home_url( '/' ),
			'slogan'      => $tagline,
			'description' => $description,
			'areaServed'  => array( 'Ubud', 'Online' ),
			'serviceType' => array( 'Life coaching', 'Spiritual coaching', 'Mindful wellness coaching' ),
		),
		array(
			'@context'  => 'https://schema.org',
			'@type'     => 'WebSite',
			'@id'       => home_url( '/#website' ),
			'name'      => $site_name,
			'url'       => home_url( '/' ),
			'publisher' => array(
				'@id' => home_url( '/#organization' ),
			),
		),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'sennen_seo_meta' );

/**
 * Keep low-value internal WordPress surfaces out of the index.
 */
function sennen_robots_directives( array $robots ): array {
	if ( is_search() || is_404() ) {
		$robots['noindex'] = true;
	}

	return $robots;
}
add_filter( 'wp_robots', 'sennen_robots_directives' );

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
