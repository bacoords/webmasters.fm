<?php
/**
 * Plugin Name: BC Plugin Stats
 * Plugin URI: https://www.briancoords.com
 * Description: A plugin to display various statistics.
 * Version: 1.0
 * Author: Brian Coords
 * Author URI: https://www.briancoords.com
 * License: GPL2
 *
 * @package BCPluginStats
 */

namespace BCPluginStats;

use WP_REST_Request;
use WP_REST_Response;

/**
 * Create the podcast analytics table.
 *
 * @return void
 */
function create_podcast_analytics_table() {
	global $wpdb;
	$table_name      = $wpdb->prefix . 'bc_podcast_analytics';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
        ip_address VARCHAR(100) NOT NULL,
        user_agent TEXT NOT NULL,
        referer TEXT,
        file_path VARCHAR(255) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\create_podcast_analytics_table' );



/**
 * Register the REST route.
 *
 * @return void
 */
function register_our_rest_routes() {
	register_rest_route(
		'wm/v1',
		'/log',
		array(
			'methods'             => 'POST',
			'callback'            => __NAMESPACE__ . '\log_podcast_download',
			'permission_callback' => function () {
				return current_user_can( 'manage_options' );
			},
		)
	);
}
add_action( 'rest_api_init', __NAMESPACE__ . '\register_our_rest_routes' );



/**
 * Log a podcast download.
 *
 * @param WP_REST_Request $request The REST request.
 * @return WP_REST_Response
 */
function log_podcast_download( WP_REST_Request $request ) {
	global $wpdb;

	// Get request data.
	$ip_address = $request->get_param( 'ip' );
	$user_agent = $request->get_param( 'user_agent' );
	$referer    = $request->get_param( 'referer' );
	$file_path  = $request->get_param( 'file_path' );

	// Insert data into the database.
	$table_name = $wpdb->prefix . 'bc_podcast_analytics';
	$wpdb->insert(
		$table_name,
		array(
			'ip_address' => $ip_address,
			'user_agent' => $user_agent,
			'referer'    => $referer,
			'file_path'  => $file_path,
		)
	);

	return new WP_REST_Response( array( 'success' => true ), 200 );
}


/**
 * Add an admin menu page.
 *
 * @return void
 */
function add_our_admin_menu_page() {
	add_management_page(
		'Podcast Analytics',
		'Podcast Analytics',
		'manage_options',
		'podcast-analytics',
		__NAMESPACE__ . '\display_podcast_analytics'
	);
}
add_action( 'admin_menu', __NAMESPACE__ . '\add_our_admin_menu_page' );



/**
 * Display the podcast analytics.
 *
 * @return void
 */
function display_podcast_analytics() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'bc_podcast_analytics';

	$results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY timestamp DESC LIMIT 500" );

	echo '<h1>Podcast Analytics</h1>';
	echo '<table class="widefat">
            <tr>
                <th>Timestamp</th>
                <th>IP Address</th>
                <th>User Agent</th>
                <th>Referer</th>
                <th>File Path</th>
            </tr>';

	foreach ( $results as $row ) {
		echo '<tr>';
		echo '<td>' . esc_html( $row->timestamp ) . '</td>';
		echo '<td>' . esc_html( $row->ip_address ) . '</td>';
		echo '<td>' . esc_html( $row->user_agent ) . '</td>';
		echo '<td>' . esc_html( $row->referer ) . '</td>';
		echo '<td>' . esc_html( $row->file_path ) . '</td>';
		echo '</tr>';
	}

	echo '</table>';
}
