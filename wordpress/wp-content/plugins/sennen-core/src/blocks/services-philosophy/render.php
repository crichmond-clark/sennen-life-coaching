<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$tea = sennen_image_url( 'tea-ritual.jpg' );
?>
<section class="max-w-7xl mx-auto w-full" style="padding-top: var(--spacing-section-gap); padding-bottom: var(--spacing-section-gap); padding-left: var(--spacing-container-padding); padding-right: var(--spacing-container-padding);">
	<div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
		<div class="aspect-square relative organic-shape-3 overflow-hidden shadow-lg group">
			<img src="<?php echo esc_url( $tea ); ?>" alt="Hands holding tea bowl in peaceful ritual" class="sennen-img-fill object-cover scale-105 group-hover:scale-100 transition-transform duration-[2000ms] ease-out" />
		</div>
		<div class="space-y-6">
			<h2 class="text-headline-md" style="color: var(--color-primary);">Not fixing, just remembering.</h2>
			<p class="text-body-md font-light leading-relaxed" style="color: var(--color-on-surface-variant);">My approach does not assume you are broken. Instead, these sessions are designed to help you peel back the layers of conditioning, stress, and noise to remember the wholeness that already resides within you.</p>
			<p class="text-body-md font-light leading-relaxed" style="color: var(--color-on-surface-variant);">We move slowly, respecting the pace of your nervous system. Every offering is an invitation, never a demand.</p>
		</div>
	</div>
</section>
