<?php
/**
 * Render the exact Home philosophy section matching app/page.tsx.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading  = $attributes['heading'] ?? 'The Art of Slowing Down.';
$paras    = $attributes['bodyParagraphs'] ?? array();
$cta_text = $attributes['ctaText'] ?? 'Our Story';
$cta_url  = $attributes['ctaUrl'] ?? '/about/';
$image    = $attributes['imageUrl'] ?? '/images/philosophy.jpg';

if ( empty( $paras ) ) {
	$paras = array(
		'In a world that constantly demands more, we offer a space to simply be. Our philosophy is rooted in the earth, drawing inspiration from the tactile textures of nature and the gentle rhythm of the tides.',
		'Here, every breath is intentional, every movement is unhurried. We believe true luxury is found in connection—to oneself, to the environment, and to the present moment.',
	);
}

$image_src = sennen_image_url( ltrim( $image, '/' ) );
?>

<section class="flex-grow flex items-center" style="padding-top: var(--spacing-section-gap); padding-bottom: var(--spacing-section-gap); padding-left: var(--spacing-container-padding); padding-right: var(--spacing-container-padding);">
	<div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-12 items-center w-full" style="gap: var(--spacing-gutter);">
		<!-- Text Content -->
		<div class="md:col-span-5 md:col-start-2 space-y-8 z-10 relative">
			<h2 class="text-headline-lg" style="color: var(--color-primary);">
				<?php echo esc_html( $heading ); ?>
			</h2>
			<?php foreach ( $paras as $i => $text ) : ?>
				<p class="text-body-lg font-light leading-relaxed <?php echo $i > 0 ? 'text-body-md opacity-90' : ''; ?>" style="color: var(--color-on-surface-variant);">
					<?php echo esc_html( $text ); ?>
				</p>
			<?php endforeach; ?>
			<a href="<?php echo esc_url( $cta_url ); ?>" class="inline-flex items-center gap-2 text-label-caps uppercase tracking-widest transition-colors group mt-4" style="color: var(--color-secondary);" onmouseover="this.style.color='var(--color-tertiary)'" onmouseout="this.style.color='var(--color-secondary)'">
				<span><?php echo esc_html( $cta_text ); ?></span>
				<svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
			</a>
		</div>

		<!-- Image Container -->
		<div class="md:col-span-5 md:col-start-8 mt-12 md:mt-0 relative group">
			<div class="aspect-[4/5] relative organic-shape-1 overflow-hidden shadow-[0_20px_40px_-15px_rgba(45,90,39,0.15)] transform transition-transform duration-700 ease-out">
				<img src="<?php echo esc_url( $image_src ); ?>" alt="" class="sennen-img-fill object-cover object-center group-hover:scale-105 transition-transform duration-[1200ms]" />
				<div class="absolute inset-0 mix-blend-overlay" style="background-color: rgba(21,66,18,0.1);"></div>
			</div>
			<!-- Decorative Elements -->
			<div class="absolute -bottom-8 -left-8 w-32 h-32 rounded-full mix-blend-multiply blur-2xl opacity-60" style="background-color: var(--color-secondary-container);"></div>
			<div class="absolute -top-8 -right-8 w-40 h-40 rounded-full mix-blend-multiply blur-3xl opacity-40" style="background-color: var(--color-primary-fixed);"></div>
		</div>
	</div>
</section>
