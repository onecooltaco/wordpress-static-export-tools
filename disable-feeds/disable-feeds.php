<?php
/**
 * Plugin Name: Disable Feeds for Static Export
 * Plugin URI: https://github.com/onecooltaco/wordpress-static-export-tools
 * Description: Removes WordPress feed discovery links and returns a 404 response for feed requests while preparing a static export.
 * Version: 1.0.0
 * Author: Jeremy Leggat
 * Author URI: https://github.com/onecooltaco
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: disable-feeds-static-export
 *
 * @package WordPress_Static_Export_Tools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stop WordPress from rendering a dynamic feed.
 */
function onecooltaco_static_export_disable_feed() {
	wp_die(
		esc_html__( 'Feeds are not available on this site.', 'disable-feeds-static-export' ),
		esc_html__( 'Feed unavailable', 'disable-feeds-static-export' ),
		array( 'response' => 404 )
	);
}

$onecooltaco_static_export_feed_hooks = array(
	'do_feed',
	'do_feed_rdf',
	'do_feed_rss',
	'do_feed_rss2',
	'do_feed_atom',
	'do_feed_rss2_comments',
	'do_feed_atom_comments',
);

foreach ( $onecooltaco_static_export_feed_hooks as $onecooltaco_static_export_feed_hook ) {
	add_action( $onecooltaco_static_export_feed_hook, 'onecooltaco_static_export_disable_feed', 1 );
}

/**
 * Remove feed discovery links from front-end HTML.
 */
function onecooltaco_static_export_remove_feed_links() {
	remove_action( 'wp_head', 'feed_links', 2 );
	remove_action( 'wp_head', 'feed_links_extra', 3 );
}
add_action( 'init', 'onecooltaco_static_export_remove_feed_links', 99 );

/**
 * Remind administrators that feed requests are being blocked.
 */
function onecooltaco_static_export_disable_feeds_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	echo '<div class="notice notice-warning"><p>';
	echo esc_html__( 'Disable Feeds for Static Export is active. Remove it after validating the exported site.', 'disable-feeds-static-export' );
	echo '</p></div>';
}
add_action( 'admin_notices', 'onecooltaco_static_export_disable_feeds_admin_notice' );
add_action( 'network_admin_notices', 'onecooltaco_static_export_disable_feeds_admin_notice' );
