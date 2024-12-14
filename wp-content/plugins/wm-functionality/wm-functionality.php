<?php
/**
 * Plugin Name: WM Functionality
 * Plugin URI: https://www.briancoords.com
 * Description: Custom functionality for the WM site.
 * Version: 1.0.0
 * Author: Brian Coords
 * Author URI: https://www.briancoords.com
 * License: GPL2
 *
 * @package WM_Functionality
 */

// Your custom functionality code goes here.

namespace WMFunctionality;

define( 'WM_FUNCTIONALITY_PATH', plugin_dir_path( __FILE__ ) );
define( 'WM_FUNCTIONALITY_URL', plugin_dir_url( __FILE__ ) );
define( 'WM_FUNCTIONALITY_VERSION', '1.0.0' );


/**
 * Change the speakers label to "Guests"
 *
 * @return string
 */
function ssp_speakers_plural_label_custom() {
	return 'Guests';
}
add_filter( 'ssp_speakers_plural_label', __NAMESPACE__ . '\ssp_speakers_plural_label_custom' );



/**
 * Change the speaker label to "Guest"
 *
 * @return string
 */
function ssp_speakers_single_label_custom() {
	return 'Guest';
}
add_filter( 'ssp_speakers_single_label', __NAMESPACE__ . '\ssp_speakers_single_label_custom' );



/**
 * Enqueue the transcript-block CSS
 */
function enqueue_transcript_block_css() {
	wp_enqueue_block_style(
		'create-block/castos-transcript',
		array(
			'handle' => 'wm-castos-transcript-block',
			'src'    => WM_FUNCTIONALITY_URL . 'block-castos-transcript.css',
			'ver'    => WM_FUNCTIONALITY_VERSION,
			'path'   => WM_FUNCTIONALITY_PATH . 'block-castos-transcript.css',
		)
	);
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\enqueue_transcript_block_css' );



/**
 * Register the build/term-image block
 */
function register_term_image_block() {
	register_block_type( WM_FUNCTIONALITY_PATH . 'build/term-image' );
}
add_action( 'init', __NAMESPACE__ . '\register_term_image_block' );



/**
 * Register the term image field in the REST API
 */
function add_term_image_to_api() {

	register_rest_field(
		'speaker',
		'image',
		array(
			'get_callback' => function ( $term ) {

				$image_id = get_term_meta( $term['id'], 'image', true );
				if ( ! $image_id ) {
					return array();
				}

				$image = wp_get_attachment_image_src( $image_id, 'full' );
				if ( ! $image ) {
					return array();
				}

				return array(
					'id'  => $image_id,
					'url' => $image[0],
				);
			},
			'schema'       => array(
				'description' => esc_html__( 'Image for the term.' ),
				'type'        => 'object',
				'context'     => array( 'view', 'edit' ),
				'properties'  => array(
					'id'  => array(
						'description' => esc_html__( 'Image ID.' ),
						'type'        => 'integer',
						'context'     => array( 'view', 'edit' ),
						'readonly'    => true,
					),
					'url' => array(
						'description' => esc_html__( 'Image URL.' ),
						'type'        => 'string',
						'context'     => array( 'view', 'edit' ),
						'format'      => 'uri',
						'readonly'    => true,
					),
				),
			),
		)
	);
}
add_filter( 'init', __NAMESPACE__ . '\add_term_image_to_api' );
add_filter( 'rest_api_init', __NAMESPACE__ . '\add_term_image_to_api' );
