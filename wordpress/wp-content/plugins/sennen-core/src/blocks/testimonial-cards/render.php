<?php
/**
 * Render exact testimonial cards matching app/testimonials/page.tsx.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$count = $attributes['count'] ?? 9;
$testimonials = get_posts( array(
	'post_type'      => 'sennen_testimonial',
	'posts_per_page' => $count,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
) );

if ( empty( $testimonials ) ) { return; }
?>

<section class="max-w-7xl mx-auto w-full" style="padding-top: var(--spacing-section-gap); padding-bottom: var(--spacing-section-gap); padding-left: var(--spacing-container-padding); padding-right: var(--spacing-container-padding);">
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
		<?php foreach ( $testimonials as $t ) :
			$quote        = get_post_meta( $t->ID, '_sennen_testimonial_quote', true ) ?: wp_trim_words( wp_strip_all_tags( $t->post_content ), 40 );
			$author_name  = $t->post_title;
			$author_title = get_post_meta( $t->ID, '_sennen_testimonial_author_title', true ) ?: '';
		?>
			<div class="p-10 rounded-3xl shadow-sm border relative overflow-hidden group" style="background-color: var(--color-surface-container-lowest); border-color: rgba(194,201,187,0.3);">
				<div class="absolute top-6 left-6 text-[80px] font-serif leading-none" style="color: rgba(152,70,35,0.1);">&ldquo;</div>
				<p class="text-body-lg font-light leading-relaxed mb-8 relative z-10" style="color: var(--color-on-surface);">&ldquo;<?php echo esc_html( $quote ); ?>&rdquo;</p>
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
				<div class="absolute top-0 left-0 w-full h-1 opacity-0 group-hover:opacity-100 transition-opacity" style="background: linear-gradient(to right, rgba(21,66,18,0.2), var(--color-primary), rgba(21,66,18,0.2));"></div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
