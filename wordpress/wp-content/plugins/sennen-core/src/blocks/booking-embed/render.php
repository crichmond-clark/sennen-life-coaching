<?php
/**
 * Server-side render for sennen/booking-embed block.
 *
 * @package SennenCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$use_global_url = $attributes['useGlobalUrl'] ?? true;
$booking_url    = $attributes['bookingUrl'] ?? '';
$min_height     = intval( $attributes['minHeight'] ?? 700 );

// Resolve the booking URL.
if ( $use_global_url ) {
	$booking_url = get_option( 'sennen_booking_url', '' );
}

if ( empty( $booking_url ) ) {
	echo sprintf(
		'<div %s><div class="sennen-booking-placeholder"><div class="sennen-booking-placeholder-icon" aria-hidden="true">☉</div><p style="margin:0 0 0.5rem">%s</p><p class="is-style-label-caps" style="margin:0">%s</p></div></div>',
		wp_kses_data( get_block_wrapper_attributes() ),
		esc_html__( 'Calendly booking widget will appear here once configured.', 'sennen-core' ),
		esc_html__( 'Set your booking URL in Settings → Sennen', 'sennen-core' )
	);
	return;
}

// Sanitize the URL and only allow Calendly/TidyCal domains by default.
$allowed_hosts = apply_filters( 'sennen_booking_allowed_hosts', array( 'calendly.com', 'tidycal.com' ) );
$parsed        = wp_parse_url( $booking_url );

if ( ! $parsed || ! in_array( $parsed['host'] ?? '', $allowed_hosts, true ) ) {
	if ( current_user_can( 'manage_options' ) ) {
		echo sprintf(
			'<div %s><div style="background:#ffdad6;border:2px solid #ba1a1a;border-radius:4px;padding:var(--wp--preset--spacing--gutter);text-align:center"><p style="margin:0;color:#93000a">%s</p></div></div>',
			wp_kses_data( get_block_wrapper_attributes() ),
			esc_html__( 'Invalid booking URL. Only Calendly and TidyCal domains are allowed.', 'sennen-core' )
		);
	}
	return;
}

// Enqueue Calendly widget script once.
if ( ! wp_script_is( 'calendly-widget', 'enqueued' ) && str_contains( $booking_url, 'calendly.com' ) ) {
	wp_enqueue_script(
		'calendly-widget',
		'https://assets.calendly.com/assets/external/widget.js',
		array(),
		null,
		true
	);
}

$escaped_url  = esc_url( $booking_url );
$escaped_html = esc_js( $booking_url );

ob_start();
?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
	<div
		class="calendly-inline-widget"
		data-url="<?php echo esc_attr( $escaped_url ); ?>"
		style="min-width:320px;height:<?php echo intval( $min_height ); ?>px"
	></div>
	<script type="text/javascript">
		(function() {
			var s = document.createElement('script');
			s.type = 'text/javascript';
			s.async = true;
			s.src = 'https://assets.calendly.com/assets/external/widget.js';
			var x = document.getElementsByTagName('script')[0];
			x.parentNode.insertBefore(s, x);
		})();
	</script>
</div>
<?php
echo ob_get_clean();
