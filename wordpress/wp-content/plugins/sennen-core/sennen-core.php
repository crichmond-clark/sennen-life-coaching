<?php
/**
 * Plugin Name: Sennen Core
 * Plugin URI:  https://sennenlifecoaching.com
 * Description: Custom post types, blocks, contact form, and settings for Sennen Life Coaching.
 * Version:     1.0.0
 * Author:      Sennen
 * Author URI:  https://sennenlifecoaching.com
 * License:     Proprietary
 * Text Domain: sennen-core
 * Requires PHP: 8.2
 * Requires at least: 6.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SENNEN_CORE_VERSION', '1.0.0' );
define( 'SENNEN_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'SENNEN_CORE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Load plugin includes.
 */
require_once SENNEN_CORE_PATH . 'includes/post-types.php';
require_once SENNEN_CORE_PATH . 'includes/meta.php';
require_once SENNEN_CORE_PATH . 'includes/settings.php';
require_once SENNEN_CORE_PATH . 'includes/contact.php';
require_once SENNEN_CORE_PATH . 'includes/importer.php';

/**
 * Plugin activation.
 */
function sennen_core_activate(): void {
	sennen_core_register_post_types();
	sennen_core_register_meta();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'sennen_core_activate' );

/**
 * Plugin deactivation.
 */
function sennen_core_deactivate(): void {
	unregister_post_type( 'sennen_service' );
	unregister_post_type( 'sennen_testimonial' );
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'sennen_core_deactivate' );
