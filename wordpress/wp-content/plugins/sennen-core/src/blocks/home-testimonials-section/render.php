<?php
/**
 * Render the exact Home testimonials section matching components/Testimonials.tsx.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$count = $attributes['count'] ?? 3;

$testimonials = get_posts( array(
	'post_type'      => 'sennen_testimonial',
	'posts_per_page' => $count,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
) );

if ( empty( $testimonials ) ) {
	return;
}

$botanical_up   = sennen_botanical_svg( 'up' );
$botanical_down = sennen_botanical_svg( 'down' );
?>

<section class="relative" style="padding-top: var(--spacing-section-gap); padding-bottom: var(--spacing-section-gap); padding-left: var(--spacing-container-padding); padding-right: var(--spacing-container-padding); background-color: var(--color-surface-container-low);">
	<!-- Botanical divider top -->
	<div class="w-full h-16 flex justify-center items-center opacity-30 mb-8">
		<?php echo $botanical_up; ?>
	</div>

	<div class="max-w-7xl mx-auto w-full">
		<div class="text-center mb-16 space-y-4">
			<?php echo sennen_label( 'Testimonials' ); ?>
			<h2 class="text-headline-lg" style="color: var(--color-primary);">What Clients Say</h2>
			<p class="text-body-lg font-light max-w-2xl mx-auto" style="color: var(--color-on-surface-variant);">Stories from those who have walked this path with me.</p>
		</div>

		<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
			<?php
			$index = 0;
			foreach ( $testimonials as $t ) :
				$quote        = get_post_meta( $t->ID, '_sennen_testimonial_quote', true ) ?: wp_trim_words( wp_strip_all_tags( $t->post_content ), 40 );
				$author_name  = $t->post_title;
				$author_title = get_post_meta( $t->ID, '_sennen_testimonial_author_title', true ) ?: '';
				$offset_class = 1 === $index ? 'md:-translate-y-4' : '';
			?>
				<div class="relative overflow-hidden group p-10 rounded-3xl shadow-sm border border-outline-variant/30 <?php echo esc_attr( $offset_class ); ?>" style="background-color: var(--color-surface-container-lowest);">
					<!-- Decorative quote mark -->
					<div class="absolute top-6 left-6 text-[80px] font-serif leading-none" style="color: rgba(152,70,35,0.1);">&ldquo;</div>

					<!-- Quote -->
					<p class="text-body-lg font-light leading-relaxed mb-8 relative z-10" style="color: var(--color-on-surface);">
						&ldquo;<?php echo esc_html( $quote ); ?>&rdquo;
					</p>

					<!-- Author -->
					<div class="flex items-center gap-4">
						<div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: var(--color-secondary-container);">
							<span class="text-body-md font-medium" style="color: var(--color-secondary);"><?php echo esc_html( substr( $author_name, 0, 1 ) ); ?></span>
						</div>
						<div>
							<p class="text-body-md font-medium" style="color: var(--color-on-surface);"><?php echo esc_html( $author_name ); ?></p>
							<?php if ( $author_title ) : ?>
								<p class="text-label-caps tracking-widest uppercase" style="color: var(--color-on-surface-variant);"><?php echo esc_html( $author_title ); ?></p>
							<?php endif; ?>
						</div>
					</div>

					<!-- Top accent bar -->
					<div class="absolute top-0 left-0 w-full h-1 opacity-0 group-hover:opacity-100 transition-opacity" style="background: linear-gradient(to right, rgba(21,66,18,0.2), var(--color-primary), rgba(21,66,18,0.2));"></div>
				</div>
			<?php
				$index++;
			endforeach;
			?>
		</div>
	</div>

	<!-- Botanical divider bottom -->
	<div class="w-full h-16 flex justify-center items-center opacity-30 mt-16">
		<?php echo $botanical_down; ?>
	</div>
</section>
