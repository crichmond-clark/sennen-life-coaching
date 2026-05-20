<?php
/**
 * Server-side render for sennen/service-list block.
 *
 * @package SennenCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$columns      = $attributes['columns'] ?? 3;
$count        = $attributes['count'] ?? 3;
$button_text  = $attributes['buttonText'] ?? __( 'Inquire Now', 'sennen-core' );
$button_url   = $attributes['buttonUrl'] ?? '/booking';
$show_feature = $attributes['showFeatures'] ?? true;

$services = get_posts(
	array(
		'post_type'      => 'sennen_service',
		'posts_per_page' => intval( $count ),
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'meta_key'       => '_sennen_service_sort_order',
	)
);

if ( empty( $services ) ) {
	if ( current_user_can( 'edit_posts' ) ) {
		return '<p class="has-text-color" style="color:#72796e">' . esc_html__( 'No services found. Add services in the admin menu → Services.', 'sennen-core' ) . '</p>';
	}
	return '';
}

ob_start();
?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
	<div class="wp-block-columns" style="gap:var(--wp--preset--spacing--gutter);columns:<?php echo intval( $columns ); ?>">
		<?php foreach ( $services as $service ) : ?>
			<div class="wp-block-column" style="flex-basis:auto">
				<div class="is-style-ambient-card" style="background-color:#ffffff;border-radius:4px;padding:var(--wp--preset--spacing--gutter);box-shadow:0 10px 30px -10px rgba(152,70,35,0.1);height:100%">
					<?php if ( has_post_thumbnail( $service->ID ) ) : ?>
						<figure style="margin:0 calc(-1 * var(--wp--preset--spacing--gutter));margin-top:calc(-1 * var(--wp--preset--spacing--gutter));margin-bottom:var(--wp--preset--spacing--gutter)">
							<?php echo get_the_post_thumbnail( $service->ID, 'medium_large', array( 'style' => 'width:100%;height:200px;object-fit:cover;border-radius:4px 4px 0 0' ) ); ?>
						</figure>
					<?php endif; ?>

					<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:var(--wp--preset--spacing--base)">
						<h3 style="font-family:var(--wp--preset--font-family--serif);font-size:1.25rem;font-weight:600;margin:0">
							<?php echo esc_html( $service->post_title ); ?>
						</h3>
						<?php
						$price = get_post_meta( $service->ID, '_sennen_service_price', true );
						if ( $price ) :
							?>
							<span style="font-family:var(--wp--preset--font-family--sans);font-size:1.125rem;font-weight:600;color:var(--wp--preset--color--secondary);white-space:nowrap;margin-left:1rem">
								<?php echo esc_html( $price ); ?>
							</span>
						<?php endif; ?>
					</div>

					<?php
					$duration = get_post_meta( $service->ID, '_sennen_service_duration', true );
					if ( $duration ) :
						?>
						<p class="is-style-label-caps" style="margin-bottom:var(--wp--preset--spacing--base)">
							<?php echo esc_html( $duration ); ?>
						</p>
					<?php endif; ?>

					<p style="color:#42493e;font-size:var(--wp--preset--font-size--body-md);line-height:1.6;margin-bottom:var(--wp--preset--spacing--base)">
						<?php echo esc_html( $service->post_excerpt ?: wp_trim_words( $service->post_content, 25 ) ); ?>
					</p>

					<?php if ( $show_feature ) : ?>
						<?php
						$features = get_post_meta( $service->ID, '_sennen_service_features', true );
						if ( ! empty( $features ) && is_array( $features ) ) :
							?>
							<ul style="list-style:none;padding:0;margin:0 0 var(--wp--preset--spacing--gutter);color:#42493e;font-size:var(--wp--preset--font-size--body-md)">
								<?php foreach ( $features as $feature ) : ?>
									<li style="padding:0.25rem 0">✓ <?php echo esc_html( $feature ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					<?php endif; ?>

					<div class="wp-block-button is-style-sennen-secondary" style="margin-top:auto">
						<a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $button_url ); ?>">
							<?php echo esc_html( $button_text ); ?>
						</a>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
<?php
return ob_get_clean();
