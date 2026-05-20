<?php
/**
 * Server-side render for sennen/testimonial-list block.
 *
 * @package SennenCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$columns     = $attributes['columns'] ?? 3;
$count       = $attributes['count'] ?? 3;
$show_avatar = $attributes['showAvatar'] ?? true;

$testimonials = get_posts(
	array(
		'post_type'      => 'sennen_testimonial',
		'posts_per_page' => intval( $count ),
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	)
);

if ( empty( $testimonials ) ) {
	if ( current_user_can( 'edit_posts' ) ) {
		return '<p class="has-text-color" style="color:#72796e">' . esc_html__( 'No testimonials found. Add testimonials in the admin menu → Testimonials.', 'sennen-core' ) . '</p>';
	}
	return '';
}

ob_start();
?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
	<div class="wp-block-columns" style="gap:var(--wp--preset--spacing--gutter)">
		<?php foreach ( $testimonials as $testimonial ) : ?>
			<div class="wp-block-column" style="flex-basis:auto">
				<div class="is-style-ambient-card" style="background-color:#ffffff;border-radius:4px;padding:var(--wp--preset--spacing--gutter);box-shadow:0 10px 30px -10px rgba(152,70,35,0.1);height:100%;display:flex;flex-direction:column">
					<p style="color:#42493e;font-size:var(--wp--preset--font-size--body-md);font-style:italic;line-height:1.7;margin-bottom:var(--wp--preset--spacing--gutter);flex-grow:1">
						"<?php echo esc_html( $testimonial->post_excerpt ?: wp_trim_words( $testimonial->post_content, 30 ) ); ?>"
					</p>

					<div style="display:flex;align-items:center;gap:0.75rem;margin-top:auto">
						<?php if ( $show_avatar && has_post_thumbnail( $testimonial->ID ) ) : ?>
							<?php echo get_the_post_thumbnail( $testimonial->ID, 'thumbnail', array( 'style' => 'width:44px;height:44px;border-radius:50%;object-fit:cover;flex-shrink:0' ) ); ?>
						<?php elseif ( $show_avatar ) : ?>
							<div style="width:44px;height:44px;border-radius:50%;background-color:#e5e2db;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-family:var(--wp--preset--font-family--serif);font-size:1.25rem;color:#72796e">
								<?php echo esc_html( strtoupper( substr( $testimonial->post_title, 0, 2 ) ) ); ?>
							</div>
						<?php endif; ?>
						<div>
							<p style="font-weight:600;font-size:var(--wp--preset--font-size--body-md);margin:0">
								<?php echo esc_html( $testimonial->post_title ); ?>
							</p>
							<?php
							$author_title = get_post_meta( $testimonial->ID, '_sennen_testimonial_author_title', true );
							if ( $author_title ) :
								?>
								<p style="color:#72796e;font-size:0.8125rem;margin:0">
									<?php echo esc_html( $author_title ); ?>
								</p>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
<?php
return ob_get_clean();
