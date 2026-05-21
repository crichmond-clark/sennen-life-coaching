<?php
/**
 * Render the exact About gallery section matching app/about/page.tsx.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$yoga      = sennen_image_url( 'yoga-shala.jpg' );
$ceramics  = sennen_image_url( 'ceramics.jpg' );
$meditation = sennen_image_url( 'meditation.jpg' );
?>

<section class="max-w-7xl mx-auto w-full" style="padding-left: var(--spacing-container-padding); padding-right: var(--spacing-container-padding); margin-bottom: var(--spacing-section-gap);">
	<h2 class="text-headline-md text-center mb-16" style="color: var(--color-primary);">Fragments of the Journey</h2>
	<div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
		<div class="md:col-span-7">
			<div class="aspect-[4/3] organic-shape-2 overflow-hidden shadow-md group relative">
				<img src="<?php echo esc_url( $yoga ); ?>" alt="Peaceful yoga shala in nature" class="sennen-img-fill object-cover scale-105 group-hover:scale-100 transition-transform duration-[2000ms] ease-out" />
			</div>
		</div>
		<div class="md:col-span-5 flex flex-col gap-8">
			<div class="aspect-[3/2] organic-shape-3 overflow-hidden shadow-md w-4/5 ml-auto group relative">
				<img src="<?php echo esc_url( $ceramics ); ?>" alt="Ceramics and incense" class="sennen-img-fill object-cover scale-105 group-hover:scale-100 transition-transform duration-[2000ms] ease-out" />
			</div>
			<div class="aspect-square rounded-full overflow-hidden shadow-md w-3/5 mr-auto border-8 relative group" style="border-color: var(--color-surface-container);">
				<img src="<?php echo esc_url( $meditation ); ?>" alt="Connecting with nature" class="sennen-img-fill object-cover scale-105 group-hover:scale-100 transition-transform duration-[2000ms] ease-out" />
			</div>
		</div>
	</div>
</section>
