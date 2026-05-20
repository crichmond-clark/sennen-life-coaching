<?php
/**
 * Contact form REST endpoint for Sennen Core.
 *
 * @package SennenCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register contact form REST route.
 */
function sennen_core_register_contact_route(): void {
	register_rest_route(
		'sennen/v1',
		'/contact',
		array(
			'methods'             => 'POST',
			'callback'            => 'sennen_core_handle_contact',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'sennen_core_register_contact_route' );

/**
 * Handle contact form submission.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response|WP_Error
 */
function sennen_core_handle_contact( WP_REST_Request $request ) {
	// --- 1. Nonce check ---
	$nonce = $request->get_param( 'nonce' );
	if ( ! $nonce || ! wp_verify_nonce( $nonce, 'sennen_contact_nonce' ) ) {
		return new WP_REST_Response(
			array( 'success' => false, 'error' => __( 'Invalid nonce.', 'sennen-core' ) ),
			400
		);
	}

	// --- 2. Honeypot check ---
	$website = $request->get_param( 'website' );
	if ( ! empty( $website ) ) {
		// Silently pretend success for spam bots.
		return new WP_REST_Response( array( 'success' => true ), 200 );
	}

	// --- 3. Rate limit by IP (transient) ---
	$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
	$rate_key = 'sennen_contact_rate_' . md5( $ip );
	if ( get_transient( $rate_key ) ) {
		return new WP_REST_Response(
			array( 'success' => false, 'error' => __( 'Please wait before sending another message.', 'sennen-core' ) ),
			429
		);
	}
	set_transient( $rate_key, true, 30 ); // 30-second cooldown.

	// --- 4. Validate fields ---
	$name         = sanitize_text_field( wp_unslash( $request->get_param( 'name' ) ?? '' ) );
	$email        = sanitize_email( wp_unslash( $request->get_param( 'email' ) ?? '' ) );
	$inquiry_type = sanitize_text_field( wp_unslash( $request->get_param( 'inquiryType' ) ?? '' ) );
	$message      = sanitize_textarea_field( wp_unslash( $request->get_param( 'message' ) ?? '' ) );

	$errors = array();

	if ( empty( $name ) || strlen( $name ) < 2 || strlen( $name ) > 120 ) {
		$errors[] = __( 'Please enter your name.', 'sennen-core' );
	}

	if ( empty( $email ) || ! is_email( $email ) ) {
		$errors[] = __( 'Please enter a valid email.', 'sennen-core' );
	}

	$allowed_types = sennen_core_get_inquiry_types();
	if ( ! in_array( $inquiry_type, $allowed_types, true ) ) {
		$errors[] = __( 'Please select a valid inquiry type.', 'sennen-core' );
	}

	if ( empty( $message ) || strlen( $message ) < 10 || strlen( $message ) > 5000 ) {
		$errors[] = __( 'Please enter a message (10–5000 characters).', 'sennen-core' );
	}

	if ( ! empty( $errors ) ) {
		return new WP_REST_Response(
			array( 'success' => false, 'error' => implode( ' ', $errors ) ),
			400
		);
	}

	// --- 5. Send email ---
	$to      = get_option( 'sennen_contact_email', get_option( 'admin_email' ) );
	$subject = sprintf(
		/* translators: %1$s: inquiry type, %2$s: sender name */
		__( '[Sennen] %1$s from %2$s', 'sennen-core' ),
		$inquiry_type,
		$name
	);

	$body = sprintf(
		"Name: %s\nEmail: %s\nInquiry Type: %s\n\nMessage:\n%s",
		$name,
		$email,
		$inquiry_type,
		$message
	);

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		sprintf( 'From: %s <%s>', $name, $to ), // Send from the site email to avoid spoofing issues.
		sprintf( 'Reply-To: %s <%s>', $name, $email ),
	);

	$sent = wp_mail( $to, $subject, $body, $headers );

	if ( ! $sent ) {
		return new WP_REST_Response(
			array( 'success' => false, 'error' => __( 'Message could not be sent. Please try again.', 'sennen-core' ) ),
			500
		);
	}

	return new WP_REST_Response( array( 'success' => true ), 200 );
}

/**
 * Get allowed inquiry types.
 *
 * @return array<string>
 */
function sennen_core_get_inquiry_types(): array {
	$defaults = array(
		__( 'General Question', 'sennen-core' ),
		__( 'Bespoke Retreat', 'sennen-core' ),
		__( 'Virtual Session', 'sennen-core' ),
		__( 'In-Person Session', 'sennen-core' ),
	);

	return apply_filters( 'sennen_contact_inquiry_types', $defaults );
}
