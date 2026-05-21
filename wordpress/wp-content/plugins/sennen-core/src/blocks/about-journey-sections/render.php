<?php
/**
 * Render alternating text/image journey sections matching app/about/page.tsx.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sections = $attributes['sections'] ?? array();

if ( empty( $sections ) ) {
	$sections = array(
		array(
			'heading'  => 'The Awakening in Chiang Mai',
			'body'     => 'Years spent in the northern mountains of Thailand taught me the art of stillness. It wasn\'t about escaping reality, but rather plunging deeply into the present moment. Here, among the whispering bamboos and mindful monks, the foundation of my holistic approach was born.',
			'imageUrl' => '/images/journey.jpg',
		),
	);
}

foreach ( $sections as $index => $section ) :
	$heading    = $section['heading'] ?? '';
	$body       = $section['body'] ?? '';
	$image      = $section['imageUrl'] ?? '/images/journey.jpg';
	$image_src  = sennen_image_url( ltrim( $image, '/' ) );

	// Alternate image/text sides.
	$img_col   = ( $index % 2 === 1 ) ? 'md:col-start-8 md:col-span-5' : 'md:col-span-5 md:col-start-2';
	$text_col  = ( $index % 2 === 1 ) ? 'md:col-span-5 md:col-start-2' : 'md:col-span-5 md:col-start-8';
	?>

	<section class="max-w-7xl mx-auto w-full" style="padding-left: var(--spacing-container-padding); padding-right: var(--spacing-container-padding); margin-bottom: var(--spacing-section-gap);">
		<div class="grid grid-cols-1 md:grid-cols-12 items-center" style="gap: var(--spacing-gutter);">
			<div class="relative group <?php echo esc_attr( $img_col ); ?>">
				<div class="aspect-[3/4] relative organic-shape-1 overflow-hidden shadow-lg border-4" style="border-color: var(--color-surface-container-highest);">
					<img src="<?php echo esc_url( $image_src ); ?>" alt="<?php echo esc_attr( $heading ); ?>" class="sennen-img-fill object-cover group-hover:scale-105 transition-transform duration-[1200ms]" />
				</div>
				<div class="absolute -z-10 -bottom-10 -left-10 w-64 h-64 rounded-full opacity-50 blur-2xl" style="background-color: var(--color-surface-container-high);"></div>
			</div>
			<div class="space-y-8 mt-12 md:mt-0 <?php echo esc_attr( $text_col ); ?>">
				<h2 class="text-headline-lg leading-tight" style="color: var(--color-primary);"><?php echo esc_html( $heading ); ?></h2>
				<p class="text-body-md font-light leading-relaxed whitespace-pre-line" style="color: var(--color-on-surface-variant);"><?php echo esc_html( $body ); ?></p>
			</div>
		</div>
	</section>

<?php endforeach; ?>
