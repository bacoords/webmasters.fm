<?php
/**
 * Load the child theme's front-end styles after Ollie's styles.
 *
 * @package Webmasters_Ollie
 */

add_action(
	'wp_enqueue_scripts',
	function () {
		$stylesheet = get_stylesheet_directory() . '/style.css';
		wp_enqueue_style(
			'webmasters-ollie',
			get_stylesheet_uri(),
			array( 'ollie' ),
			filemtime( $stylesheet )
		);
	},
	20
);

add_action(
	'after_setup_theme',
	function () {
		add_editor_style( 'style.css' );
	}
);
