<?php
/*
Plugin Name: Scroll Triggered Boxes
Version: 2.2.3
Plugin URI: https://scrolltriggeredboxes.com/#utm_source=wp-plugin&utm_medium=scroll-triggered-boxes&utm_campaign=plugins-page
Description: CTA boxes that show up at certain trigger points. Predecessor of the free <a href="https://boxzillaplugin.com/">Boxzilla Plugin</a>.
Author: ibericode
Author URI: https://ibericode.com/#utm_source=wp-plugin&utm_medium=scroll-triggered-boxes&utm_campaign=plugins-page
Text Domain: scroll-triggered-boxes
Domain Path: /languages/
License: GPL v3
GitHub Plugin URI: https://github.com/ibericode/scroll-triggered-boxes

Scroll Triggered Boxes Plugin
Copyright (C) 2013-2016, Danny van Kooten, hi@dannyvankooten.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * @return ScrollTriggeredBoxes\Plugin
 */
function scroll_triggered_boxes() {
	static $instance;

	if( is_null( $instance ) ) {

		$classname =  'ScrollTriggeredBoxes\\Plugin';
		$id = 0;
		$file = __FILE__;
		$dir = dirname( __FILE__ );
		$name = 'Scroll Triggered Boxes';
		$version = '2.2.1';

		$instance = new $classname(
			$id,
			$name,
			$version,
			$file,
			$dir
		);
	}

	return $instance;
}

// wrapper function to move out of global namespace
function __load_scroll_triggered_boxes() {

	// load autoloader & init plugin
	require dirname( __FILE__ ) . '/vendor/autoload.php';

	// fetch instance and store in global
	$GLOBALS['scroll_triggered_boxes'] = scroll_triggered_boxes();

	// register activation hook
	register_activation_hook( __FILE__, array( 'ScrollTriggeredBoxes\\Admin\\Installer', 'run' ) );
}

function __load_scroll_triggered_boxes_fallback() {
	// load php 5.2 fallback
	require dirname( __FILE__ ) . '/fallback.php';
	new STB_PHP_Fallback( 'Scroll Triggered Boxes', plugin_basename( __FILE__ ) );
}

if( version_compare( PHP_VERSION, '5.3', '>=' ) ) {
	__load_scroll_triggered_boxes();
} else {
	__load_scroll_triggered_boxes_fallback();
}
