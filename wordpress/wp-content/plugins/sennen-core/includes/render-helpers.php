<?php
/**
 * Shared render helpers for Sennen section blocks.
 *
 * @package Sennen_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get an image URL relative to the theme images directory.
 * Falls back to /images/ at the WordPress root for mounted content images.
 */
function sennen_image_url( string $filename ): string {
	// Check theme images first.
	$theme_path = get_template_directory() . '/assets/images/' . $filename;
	if ( file_exists( $theme_path ) ) {
		return get_template_directory_uri() . '/assets/images/' . $filename;
	}
	// Fall back to content images mount.
	return home_url( '/images/' . $filename );
}

/**
 * Render the botanical SVG divider.
 */
function sennen_botanical_svg( string $variant = 'up', int $opacity = 30 ): string {
	if ( 'down' === $variant ) {
		return '<svg fill="none" height="24" viewBox="0 0 120 24" width="120" xmlns="http://www.w3.org/2000/svg" style="color: var(--color-primary);"><path d="M10 12C30 12 40 22 60 22C80 22 90 12 110 12" stroke="currentColor" stroke-linecap="round" stroke-width="1"></path><circle cx="60" cy="22" fill="currentColor" r="2"></circle><path d="M55 17C55 17 58 20 60 22C62 20 65 17 65 17" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1"></path></svg>';
	}
	return '<svg fill="none" height="24" viewBox="0 0 120 24" width="120" xmlns="http://www.w3.org/2000/svg" style="color: var(--color-primary);"><path d="M10 12C30 12 40 2 60 2C80 2 90 12 110 12" stroke="currentColor" stroke-linecap="round" stroke-width="1"></path><circle cx="60" cy="2" fill="currentColor" r="2"></circle><path d="M55 7C55 7 58 4 60 2C62 4 65 7 65 7" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1"></path></svg>';
}

/**
 * Render a label tag (uppercase small text).
 */
function sennen_label( string $text, string $color = 'var(--color-secondary)' ): string {
	return '<span class="text-label-caps tracking-widest uppercase" style="color: ' . esc_attr( $color ) . ';">' . esc_html( $text ) . '</span>';
}

/**
 * Extract plain text from Portable Text blocks.
 */
function sennen_extract_portable_text( array $blocks ): string {
	$texts = array();
	foreach ( $blocks as $block ) {
		if ( isset( $block['children'] ) && is_array( $block['children'] ) ) {
			foreach ( $block['children'] as $child ) {
				if ( isset( $child['text'] ) ) {
					$texts[] = $child['text'];
				}
			}
		}
	}
	return implode( '', $texts );
}

/**
 * Get a Sennen site setting from options table.
 */
function sennen_get_setting( string $key, $fallback = null ) {
	$settings = get_option( 'sennen_core_settings', array() );
	return $settings[ $key ] ?? $fallback;
}

/**
 * Get the site setting booking URL.
 */
function sennen_get_booking_url(): string {
	return sennen_get_setting( 'booking_url', '' );
}

/**
 * Get the site setting contact email.
 */
function sennen_get_contact_email(): string {
	return sennen_get_setting( 'contact_email', '' );
}
