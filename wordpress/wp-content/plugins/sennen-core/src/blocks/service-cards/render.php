<?php
/**
 * Render exact service package cards matching app/services/page.tsx.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$count = $attributes['count'] ?? 3;
$services = get_posts( array(
	'post_type'      => 'sennen_service',
	'posts_per_page' => $count,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
) );

if ( empty( $services ) ) { return; }
?>

<section style="background-color: var(--color-surface); padding-top: var(--spacing-section-gap); padding-bottom: var(--spacing-section-gap); padding-left: var(--spacing-container-padding); padding-right: var(--spacing-container-padding);">
	<div class="max-w-7xl mx-auto w-full grid grid-cols-1 md:grid-cols-3 gap-8">
		<?php foreach ( $services as $service ) :
			$price     = get_post_meta( $service->ID, '_sennen_service_price', true );
			$duration  = get_post_meta( $service->ID, '_sennen_service_duration', true );
			$features  = get_post_meta( $service->ID, '_sennen_service_features', true ) ?: array();
			$desc      = wp_strip_all_tags( $service->post_content );
		?>
			<div class="rounded-3xl p-10 shadow-sm border flex flex-col relative group overflow-hidden transition-all duration-300 hover:shadow-md hover:-translate-y-2" style="background-color: var(--color-surface-container-lowest); border-color: rgba(194,201,187,0.3);">
				<div class="absolute top-0 left-0 w-full h-2 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="background: linear-gradient(to right, transparent, var(--color-primary-fixed), transparent);"></div>
				<h3 class="text-headline-md mb-2 relative z-10" style="color: var(--color-primary);"><?php echo esc_html( $service->post_title ); ?></h3>
				<div class="flex items-baseline gap-2 mb-6 relative z-10">
					<span class="text-headline-md" style="color: var(--color-secondary);"><?php echo esc_html( $price ); ?></span>
					<span class="text-label-caps tracking-widest uppercase" style="color: var(--color-on-surface-variant);">/ <?php echo esc_html( $duration ); ?></span>
				</div>
				<p class="text-body-md font-light mb-8 relative z-10 flex-grow" style="color: var(--color-on-surface);"><?php echo esc_html( $desc ); ?></p>
				<div class="h-px w-full mb-8" style="background-color: rgba(194,201,187,0.5);"></div>
				<ul class="space-y-4 mb-10 relative z-10 flex-grow">
					<?php foreach ( $features as $feature ) : ?>
						<li class="flex gap-3 text-body-md font-light" style="color: var(--color-on-surface-variant);">
							<span style="color: var(--color-primary);">•</span>
							<span><?php echo esc_html( $feature ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
				<a href="<?php echo esc_url( home_url( '/booking/' ) ); ?>" class="w-full py-4 text-center border-2 text-label-caps uppercase tracking-widest rounded-full transition-colors duration-300 relative z-10 block" style="border-color: var(--color-primary); color: var(--color-primary);" onmouseover="this.style.backgroundColor='var(--color-primary)';this.style.color='var(--color-on-primary)'" onmouseout="this.style.backgroundColor='transparent';this.style.color='var(--color-primary)'">
					Inquire Now
				</a>
			</div>
		<?php endforeach; ?>
	</div>
</section>
