<?php
/**
 * Post meta registration for Sennen Core.
 *
 * @package SennenCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register meta fields for custom post types.
 */
function sennen_core_register_meta(): void {
	// Service meta.
	$service_meta = array(
		'_sennen_service_price'      => 'string',
		'_sennen_service_duration'   => 'string',
		'_sennen_service_sort_order' => 'integer',
	);

	foreach ( $service_meta as $key => $type ) {
		register_post_meta(
			'sennen_service',
			$key,
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => $type,
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	// Service features (array meta with schema).
	register_post_meta(
		'sennen_service',
		'_sennen_service_features',
		array(
			'show_in_rest'  => array(
				'schema' => array(
					'items' => array(
						'type' => 'string',
					),
				),
			),
			'single'        => true,
			'type'          => 'array',
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	// Testimonial meta.
	$testimonial_meta = array(
		'_sennen_testimonial_author_title' => 'string',
		'_sennen_testimonial_sort_order'   => 'integer',
	);

	foreach ( $testimonial_meta as $key => $type ) {
		register_post_meta(
			'sennen_testimonial',
			$key,
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => $type,
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'sennen_core_register_meta' );
