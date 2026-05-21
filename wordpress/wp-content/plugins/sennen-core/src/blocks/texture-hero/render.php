<?php
/**
 * Render a textured hero section matching the Next.js texture-hero pattern.
 * Used by Services, Testimonials, and Booking pages.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$label    = $attributes['label'] ?? '';
$heading  = $attributes['heading'] ?? '';
$subtitle = $attributes['subtitle'] ?? '';
?>

<section class="relative overflow-hidden" style="margin-top: -72px; padding-top: 150px; padding-bottom: var(--spacing-section-gap); padding-left: var(--spacing-container-padding); padding-right: var(--spacing-container-padding); background-color: var(--color-surface-container);">
	<!-- Background Texture -->
	<div class="absolute inset-0 bg-texture opacity-50 mix-blend-multiply"></div>
	<?php if ( $heading && (stripos($heading, 'Begin') !== false || stripos($heading, 'Book') !== false) ) : ?>
	<div class="absolute inset-0 bg-gradient-to-b from-transparent to-background/80"></div>
	<?php endif; ?>
	<div class="max-w-4xl mx-auto text-center relative z-10 space-y-6">
		<?php if ( $label ) : ?>
			<span class="text-label-caps tracking-widest uppercase block" style="color: var(--color-secondary);"><?php echo esc_html( $label ); ?></span>
		<?php endif; ?>
		<?php if ( $heading ) : ?>
			<h1 class="text-display-xl leading-tight" style="color: var(--color-primary);"><?php echo esc_html( $heading ); ?></h1>
		<?php endif; ?>
		<?php if ( $subtitle ) : ?>
			<p class="text-body-lg font-light mx-auto max-w-2xl" style="color: var(--color-on-surface-variant);"><?php echo esc_html( $subtitle ); ?></p>
		<?php endif; ?>
	</div>
</section>
