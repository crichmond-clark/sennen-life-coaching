<?php
/**
 * Render the exact About hero section matching app/about/page.tsx.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$label    = $attributes['label'] ?? 'The Guide';
$heading  = $attributes['heading'] ?? 'Rooted in Grace, Wandering in Spirit';
$subtitle = $attributes['subtitle'] ?? 'My journey began not with a destination, but with a profound desire to listen.';
$image    = $attributes['imageUrl'] ?? '/images/about-hero.jpg';
$image_src = sennen_image_url( ltrim( $image, '/' ) );
?>

<section class="relative w-full min-h-[85vh] flex items-center justify-center mb-section-gap" style="margin-top: -72px; padding-left: var(--spacing-container-padding); padding-right: var(--spacing-container-padding);">
	<div class="absolute inset-0 z-0">
		<img src="<?php echo esc_url( $image_src ); ?>" alt="" class="sennen-img-fill object-cover opacity-80" loading="eager" />
		<div class="absolute inset-0 bg-gradient-to-t from-background" style="--tw-gradient-via-position: 40%; --tw-gradient-from: transparent 0%; --tw-gradient-to: var(--color-background) 100%;"></div>
	</div>
	<div class="relative z-10 max-w-4xl mx-auto text-center mt-20">
		<span class="text-label-caps mb-4 block tracking-widest uppercase" style="color: var(--color-secondary);"><?php echo esc_html( $label ); ?></span>
		<h1 class="text-display-xl mb-6" style="color: var(--color-primary);"><?php echo esc_html( $heading ); ?></h1>
		<p class="text-body-lg font-light max-w-2xl mx-auto leading-relaxed" style="color: var(--color-on-surface-variant);"><?php echo esc_html( $subtitle ); ?></p>
	</div>
</section>
