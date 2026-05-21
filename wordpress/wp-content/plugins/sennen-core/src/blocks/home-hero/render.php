<?php
/**
 * Render the exact Home hero section matching app/page.tsx hero.
 *
 * @package Sennen_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$label    = $attributes['label'] ?? 'Find Your Center';
$heading  = $attributes['heading'] ?? 'Rooted in Grace';
$subtitle = $attributes['subtitle'] ?? 'A sanctuary for spiritual alignment, mindful wellness, and the slow-living philosophy. Breathe deeply, you have arrived.';
$cta_text = $attributes['ctaText'] ?? 'Begin Your Journey';
$cta_url  = $attributes['ctaUrl'] ?? '/booking/';
$image    = $attributes['imageUrl'] ?? '/images/hero.jpg';

// Resolve image URL.
$image_src = sennen_image_url( ltrim( $image, '/' ) );
?>

<section class="relative min-h-[90vh] flex items-center justify-center overflow-hidden" style="margin-top: -72px;">
	<!-- Background Image -->
	<div class="absolute inset-0 z-0">
		<img src="<?php echo esc_url( $image_src ); ?>" alt="" class="sennen-img-fill object-cover object-center" loading="eager" />
		<div class="absolute inset-0" style="background-color: rgba(229,226,219,0.3); backdrop-filter: blur(2px);"></div>
		<div class="absolute inset-0 bg-gradient-to-b from-transparent" style="--tw-gradient-via-position: 20%; --tw-gradient-to-position: 100%; --tw-gradient-to: var(--color-background);"></div>
	</div>

	<!-- Content -->
	<div class="relative z-10 text-center max-w-4xl mx-auto flex flex-col items-center mt-20" style="padding-left: var(--spacing-container-padding); padding-right: var(--spacing-container-padding); gap: var(--spacing-content-gap);">
		<div class="space-y-6">
			<span class="block text-label-caps tracking-[0.3em] uppercase" style="color: var(--color-primary);"><?php echo esc_html( $label ); ?></span>
			<h1 class="text-display-xl drop-shadow-sm leading-tight" style="color: var(--color-primary);"><?php echo esc_html( $heading ); ?></h1>
			<p class="text-body-lg font-light max-w-2xl mx-auto" style="color: var(--color-on-surface-variant);"><?php echo esc_html( $subtitle ); ?></p>
		</div>
		<a href="<?php echo esc_url( $cta_url ); ?>" class="inline-flex items-center justify-center px-8 py-4 text-label-caps uppercase tracking-widest rounded-full shadow-[0_8px_24px_0_rgba(152,70,35,0.25)] transition-all duration-300 hover:-translate-y-1" style="background-color: var(--color-secondary); color: var(--color-on-secondary);" onmouseover="this.style.backgroundColor='var(--color-tertiary)'" onmouseout="this.style.backgroundColor='var(--color-secondary)'">
			<?php echo esc_html( $cta_text ); ?>
		</a>

		<!-- Scroll Indicator -->
		<div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 animate-bounce" style="color: rgba(21,66,18,0.7);">
			<span class="text-[10px] uppercase font-semibold tracking-widest" style="font-family: var(--font-sans);">Scroll to explore</span>
			<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="m19 12-7 7-7-7"/></svg>
		</div>
	</div>
</section>
