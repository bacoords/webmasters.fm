<?php
/**
 * Plugin Name: WM Simple Stats API
 * Plugin URI: https://www.briancoords.com
 * Description: A simple plugin to provide Seriously Simple podcast stats via Rest API.
 * Version: 1.0
 * Author: Brian Coords
 * Author URI: https://www.briancoords.com
 * License: GPL2
 */

namespace WMSimpleStatsAPI;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Register our REST routes.
 */
function register_our_rest_routes() {
	register_rest_route(
		'wm-simple-stats/v1',
		'/stats',
		array(
			'methods'             => 'GET',
			'callback'            => __NAMESPACE__ . '\wm_simple_stats_get_stats',
			'permission_callback' => function () {
				return current_user_can( 'manage_options' );
			},
		)
	);
}
add_action( 'rest_api_init', __NAMESPACE__ . '\register_our_rest_routes' );




/**
 * Get the stats data.
 *
 * @return WP_REST_Response The response containing the stats data.
 */
function wm_simple_stats_get_stats() {

	// Example data, replace with your own logic
	$data = fetch_all_episodes_stats();

	return new \WP_REST_Response( $data, 200 );
}



/**
 * Fetch all episodes stats.
 *
 * Forked from Seriously Simple Stats plugin.
 *
 * @return array
 */
function fetch_all_episodes_stats() {

	$all_episodes_stats = array();

	global $wpdb;

	$dates = array(
		intval( current_time( 'm' ) ) => current_time( 'F' ),
		intval( date( 'm', strtotime( current_time( 'Y-m-d' ) . 'FIRST DAY OF -1 MONTH' ) ) ) => date( 'F', strtotime( current_time( 'Y-m-d' ) . 'FIRST DAY OF -1 MONTH' ) ),
		intval( date( 'm', strtotime( current_time( 'Y-m-d' ) . 'FIRST DAY OF -2 MONTH' ) ) ) => date( 'F', strtotime( current_time( 'Y-m-d' ) . 'FIRST DAY OF -2 MONTH' ) ),
	);

	$sql         = "SELECT COUNT(id) AS listens, post_id FROM {$wpdb->prefix}ssp_stats GROUP BY post_id";
	$total_stats = $wpdb->get_results( $sql );

	$all_episodes_lifetime_stats = array();
	foreach ( $total_stats as $total_stat ) {
		$all_episodes_lifetime_stats[ $total_stat->post_id ] = intval( $total_stat->listens );
	}

	if ( ! is_array( $total_stats ) ) {
		return $all_episodes_stats;
	}

	$last_months_stats = array();

	foreach ( $dates as $month_number => $month_name ) {
		$month_formatted = sprintf( '%02d', $month_number );

		$year = date( 'Y' );
		if ( $month_number == 12 ) {
			$year = date( 'Y', strtotime( current_time( 'Y-m-d' ) . 'FIRST DAY OF -1 YEAR' ) );
		}

		$start_month_template = sprintf( '%s-%%s-01 00:00:00', $year );
		$end_month_template   = sprintf( '%s-%%s-%s 23:59:59', $year, date( 't' ) );

		$month_start = strtotime( sprintf( $start_month_template, $month_formatted ) );
		$month_end   = strtotime( sprintf( $end_month_template, $month_formatted ) );

		$month_sql = $wpdb->prepare( "SELECT COUNT(id) as `listens`, `post_id` FROM `{$wpdb->prefix}ssp_stats` WHERE `date` >= %d AND `date` <= %d GROUP BY post_id", $month_start, $month_end );

		$month_stats = $wpdb->get_results( $month_sql );

		if ( ! is_array( $month_stats ) ) {
			continue;
		}

		foreach ( $month_stats as $episode_data ) {
			$last_months_stats[ $month_name ][ $episode_data->post_id ] = intval( $episode_data->listens );
		}
	}

	foreach ( $total_stats as $episode_data ) {
		$post = get_post( intval( $episode_data->post_id ) );
		if ( ! $post ) {
			continue;
		}
		$episode_stats = array(
			'id'             => $post->ID,
			'episode_name'   => $post->post_title,
			'date'           => date( 'Y-m-d', strtotime( $post->post_date ) ),
			'slug'           => admin_url( 'post.php?post=' . $post->ID . '&action=edit' ),
			'listens'        => $all_episodes_lifetime_stats[ $post->ID ],
			'formatted_date' => date_i18n( get_option( 'date_format' ), strtotime( $post->post_date ) ),
		);

		foreach ( $dates as $month_name ) {
			$episode_stats[ $month_name ] = isset( $last_months_stats[ $month_name ][ $post->ID ] ) ? $last_months_stats[ $month_name ][ $post->ID ] : 0;
		}

		$all_episodes_stats[] = $episode_stats;
	}

	return apply_filters( 'ssp_stats_three_months_all_episodes', $all_episodes_stats );
}
