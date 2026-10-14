<?php
/**
 * Plugin Name: Static Export Cleanup
 * Plugin URI: https://github.com/onecooltaco/wordpress-static-export-tools
 * Description: Removes WordPress-only discovery metadata from front-end HTML before a static export without changing public page routes or content.
 * Version: 1.0.0
 * Author: Jeremy Leggat
 * Author URI: https://github.com/onecooltaco
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: static-export-cleanup
 *
 * @package WordPress_Static_Export_Tools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Remove discovery links and headers that point to dynamic WordPress services.
 *
 * The REST API itself stays enabled because WordPress and other plugins may need
 * it while the source site is being administered or rendered for export.
 */
function onecooltaco_static_export_remove_discovery_metadata() {
	remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
	remove_action( 'template_redirect', 'rest_output_link_header', 11 );

	// WordPress 6.9 added priority 4 while retaining priority 10 as a fallback.
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 4 );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );

	remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
	remove_action( 'template_redirect', 'wp_shortlink_header', 11 );

	remove_action( 'wp_head', 'feed_links', 2 );
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_generator' );
}
add_action( 'init', 'onecooltaco_static_export_remove_discovery_metadata', 99 );

// Prevent generator output outside wp_head(), including feeds.
add_filter( 'the_generator', '__return_empty_string' );

// Future-proof oEmbed removal if WordPress changes the wp_head priority again.
add_filter( 'oembed_discovery_links', '__return_empty_string' );

/**
 * Remove the XML-RPC discovery header without disabling XML-RPC on the source.
 *
 * @param string[] $headers Response headers keyed by header name.
 * @return string[]
 */
function onecooltaco_static_export_remove_pingback_header( $headers ) {
	unset( $headers['X-Pingback'] );

	return $headers;
}
add_filter( 'wp_headers', 'onecooltaco_static_export_remove_pingback_header' );

/**
 * Remind administrators that this temporary export helper is active.
 */
function onecooltaco_static_export_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	echo '<div class="notice notice-warning"><p>';
	echo esc_html__( 'Static Export Cleanup is active. Remove it after validating the exported site.', 'static-export-cleanup' );
	echo '</p></div>';
}
add_action( 'admin_notices', 'onecooltaco_static_export_admin_notice' );
add_action( 'network_admin_notices', 'onecooltaco_static_export_admin_notice' );
