<?php
/**
 * Custom post types for Sennen Core.
 *
 * @package SennenCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register sennen_service post type.
 */
function sennen_core_register_post_types(): void {
	register_post_type(
		'sennen_service',
		array(
			'labels'              => array(
				'name'          => __( 'Services', 'sennen-core' ),
				'singular_name' => __( 'Service', 'sennen-core' ),
				'add_new_item'  => __( 'Add New Service', 'sennen-core' ),
				'edit_item'     => __( 'Edit Service', 'sennen-core' ),
				'view_item'     => __( 'View Service', 'sennen-core' ),
				'all_items'     => __( 'All Services', 'sennen-core' ),
				'search_items'  => __( 'Search Services', 'sennen-core' ),
			),
			'public'              => true,
			'has_archive'         => false,
			'rewrite'             => array( 'slug' => 'services' ),
			'show_in_rest'        => true,
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
			'menu_icon'           => 'dashicons-heart',
			'show_in_menu'        => true,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'template'            => array(),
			'template_lock'       => false,
		)
	);

	register_post_type(
		'sennen_testimonial',
		array(
			'labels'              => array(
				'name'          => __( 'Testimonials', 'sennen-core' ),
				'singular_name' => __( 'Testimonial', 'sennen-core' ),
				'add_new_item'  => __( 'Add New Testimonial', 'sennen-core' ),
				'edit_item'     => __( 'Edit Testimonial', 'sennen-core' ),
				'view_item'     => __( 'View Testimonial', 'sennen-core' ),
				'all_items'     => __( 'All Testimonials', 'sennen-core' ),
				'search_items'  => __( 'Search Testimonials', 'sennen-core' ),
			),
			'public'              => true,
			'has_archive'         => true,
			'rewrite'             => array( 'slug' => 'testimonials' ),
			'show_in_rest'        => true,
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
			'menu_icon'           => 'dashicons-format-quote',
			'show_in_menu'        => true,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'template'            => array(),
			'template_lock'       => false,
		)
	);
}
add_action( 'init', 'sennen_core_register_post_types' );
