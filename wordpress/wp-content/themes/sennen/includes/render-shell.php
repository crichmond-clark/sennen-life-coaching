<?php
/**
 * Exact shell renderers for nav and footer matching Next.js components.
 *
 * @package Sennen
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the exact Sennen navigation bar matching NavBar.tsx.
 */
function sennen_render_nav(): void {
	$brand_name    = get_bloginfo( 'name' ) ?: 'Sennen Life Coaching';
	$current_slug  = trim( parse_url( $_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH ), '/' );
	$booking_url   = home_url( '/booking/' );

	$nav_links = array(
		array( 'name' => 'Home',         'href' => home_url( '/' ) ),
		array( 'name' => 'About',        'href' => home_url( '/about/' ) ),
		array( 'name' => 'Services',     'href' => home_url( '/services/' ) ),
		array( 'name' => 'Testimonials', 'href' => home_url( '/testimonials/' ) ),
		array( 'name' => 'Booking',      'href' => $booking_url ),
	);

	// Map slugs to nav items for active state.
	$slug_map = array(
		''             => 'Home',
		'about'        => 'About',
		'services'     => 'Services',
		'testimonials' => 'Testimonials',
		'booking'      => 'Booking',
	);

	$active_name = $slug_map[ $current_slug ] ?? '';
	?>
	<nav class="sennen-nav sennen-nav--transparent" id="sennen-nav" aria-label="<?php esc_attr_e( 'Main navigation', 'sennen' ); ?>">
		<div class="sennen-nav-inner">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-headline-md tracking-tight z-10 relative hover:opacity-80 transition-opacity" style="color: var(--color-primary); text-decoration: none;">
				<?php echo esc_html( $brand_name ); ?>
			</a>

			<!-- Desktop Nav Links -->
			<div class="sennen-nav-links">
				<?php foreach ( $nav_links as $link ) : ?>
					<?php
					$is_active = $link['name'] === $active_name;
					$link_class = $is_active ? 'sennen-nav-link sennen-nav-link--active' : 'sennen-nav-link sennen-nav-link--inactive';
					?>
					<a href="<?php echo esc_url( $link['href'] ); ?>" class="<?php echo esc_attr( $link_class ); ?>">
						<?php echo esc_html( $link['name'] ); ?>
					</a>
				<?php endforeach; ?>
			</div>

			<a href="<?php echo esc_url( $booking_url ); ?>" class="sennen-nav-cta">
				Begin Your Journey
			</a>

			<!-- Mobile Menu Toggle -->
			<button class="sennen-nav-toggle" id="sennen-mobile-toggle" aria-label="<?php esc_attr_e( 'Toggle menu', 'sennen' ); ?>" aria-expanded="false">
				<svg id="sennen-icon-menu" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
				<svg id="sennen-icon-close" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
			</button>
		</div>

		<!-- Mobile Menu -->
		<div class="sennen-mobile-menu" id="sennen-mobile-menu">
			<?php foreach ( $nav_links as $link ) : ?>
				<?php
				$is_mobile_active = $link['name'] === $active_name;
				$mobile_class = $is_mobile_active ? 'font-bold' : '';
				$mobile_color = $is_mobile_active ? 'color: var(--color-secondary)' : 'color: var(--color-on-surface)';
				?>
				<a href="<?php echo esc_url( $link['href'] ); ?>" class="sennen-mobile-nav-link" style="<?php echo esc_attr( $mobile_color ); ?>">
					<?php echo esc_html( $link['name'] ); ?>
				</a>
			<?php endforeach; ?>
			<a href="<?php echo esc_url( $booking_url ); ?>" class="mt-4 flex items-center justify-center px-8 py-4 text-label-caps uppercase tracking-widest rounded-full" style="background-color: var(--color-primary); color: var(--color-on-primary); text-decoration: none;">
				Begin Your Journey
			</a>
		</div>
	</nav>
	<?php
}

/**
 * Render the exact Sennen footer matching Footer.tsx.
 */
function sennen_render_footer(): void {
	$brand_name = get_bloginfo( 'name' ) ?: 'Sennen Life Coaching';
	$tagline    = get_bloginfo( 'description' ) ?: 'Rooted in Grace';
	$year       = gmdate( 'Y' );

	$social_links = array(
		array( 'platform' => 'Instagram', 'url' => '#' ),
		array( 'platform' => 'TikTok',    'url' => '#' ),
		array( 'platform' => 'LinkedIn',  'url' => '#' ),
		array( 'platform' => 'Facebook',  'url' => '#' ),
		array( 'platform' => 'X',         'url' => '#' ),
	);
	?>
	<footer class="sennen-footer">
		<div class="sennen-footer-inner">
			<div class="flex flex-col items-center md:items-start gap-4 mb-4 md:mb-0">
				<span class="text-headline-md tracking-tight opacity-90 hover:opacity-100 transition-opacity" style="color: var(--color-primary);"><?php echo esc_html( $brand_name ); ?></span>
				<span class="text-body-md font-light" style="color: var(--color-on-surface-variant);">&copy; <?php echo esc_html( $year ); ?> <?php echo esc_html( $brand_name ); ?>. <?php echo esc_html( $tagline ); ?>.</span>
			</div>

			<div class="sennen-footer-socials">
				<?php foreach ( $social_links as $link ) : ?>
					<a href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $link['platform'] ); ?>">
						<?php echo sennen_get_social_svg( $link['platform'] ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</footer>
	<?php
}

/**
 * Get SVG markup for a social platform icon.
 */
function sennen_get_social_svg( string $platform ): string {
	$svgs = array(
		'Instagram' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>',
		'TikTok'    => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1 0-5.78 2.92 2.92 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.26z"/></svg>',
		'LinkedIn'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/></svg>',
		'Facebook'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>',
		'X'         => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
	);

	return $svgs[ $platform ] ?? '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>';
}
