<?php
/**
 * Plugin settings page for Sennen Core.
 *
 * @package SennenCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register settings.
 */
function sennen_core_register_settings(): void {
	register_setting(
		'sennen_settings_group',
		'sennen_booking_url',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'esc_url_raw',
			'default'           => '',
			'show_in_rest'      => true,
		)
	);

	register_setting(
		'sennen_settings_group',
		'sennen_contact_email',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_email',
			'default'           => get_option( 'admin_email' ),
			'show_in_rest'      => true,
		)
	);
}
add_action( 'init', 'sennen_core_register_settings' );

/**
 * Add settings page to admin menu.
 */
function sennen_core_add_settings_page(): void {
	add_options_page(
		__( 'Sennen Settings', 'sennen-core' ),
		__( 'Sennen', 'sennen-core' ),
		'manage_options',
		'sennen-settings',
		'sennen_core_render_settings_page'
	);
}
add_action( 'admin_menu', 'sennen_core_add_settings_page' );

/**
 * Render the settings page.
 */
function sennen_core_render_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$booking_url   = get_option( 'sennen_booking_url', '' );
	$contact_email = get_option( 'sennen_contact_email', get_option( 'admin_email' ) );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Sennen Life Coaching Settings', 'sennen-core' ); ?></h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'sennen_settings_group' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="sennen_booking_url"><?php esc_html_e( 'Booking URL (Calendly)', 'sennen-core' ); ?></label>
					</th>
					<td>
						<input
							type="url"
							id="sennen_booking_url"
							name="sennen_booking_url"
							value="<?php echo esc_attr( $booking_url ); ?>"
							class="regular-text"
						>
						<p class="description">
							<?php esc_html_e( 'Your Calendly scheduling link, e.g. https://calendly.com/your-name', 'sennen-core' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sennen_contact_email"><?php esc_html_e( 'Contact Form Email', 'sennen-core' ); ?></label>
					</th>
					<td>
						<input
							type="email"
							id="sennen_contact_email"
							name="sennen_contact_email"
							value="<?php echo esc_attr( $contact_email ); ?>"
							class="regular-text"
						>
						<p class="description">
							<?php esc_html_e( 'Where contact form submissions are sent.', 'sennen-core' ); ?>
						</p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
