<?php
/**
 * Plugin Name: WM Functionality
 * Plugin URI: https://www.briancoords.com
 * Description: Custom functionality for the WM site.
 * Version: 1.0.0
 * Author: Brian Coords
 * Author URI: https://www.briancoords.com
 * License: GPL2
 */

// Your custom functionality code goes here.

namespace WMFunctionality;

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
