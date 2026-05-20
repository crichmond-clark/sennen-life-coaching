<?php
/**
 * Server-side render for sennen/botanical-divider block.
 *
 * @package SennenCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$opacity = isset( $attributes['opacity'] ) ? intval( $attributes['opacity'] ) / 100 : 0.3;

ob_start();
?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
	<div style="display:flex;justify-content:center;padding:24px 0;opacity:<?php echo esc_attr( $opacity ); ?>">
		<svg width="80" height="40" viewBox="0 0 80 40" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
			<path d="M40 0 C40 22 58 40 80 40 L0 40 C22 40 40 22 40 0Z" fill="currentColor" style="color:#2d5a27"/>
			<path d="M40 4 C40 20 54 36 72 36 L8 36 C26 36 40 20 40 4" fill="currentColor" style="color:#bcf0ae"/>
			<circle cx="40" cy="12" r="2" fill="currentColor" style="color:#154212"/>
		</svg>
	</div>
</div>
<?php
return ob_get_clean();
