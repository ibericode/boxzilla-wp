<?php
/*
Plugin Name: Scroll Triggered Boxes
Version: 2.0.2
Plugin URI: https://scrolltriggeredboxes.com/#utm_source=wp-plugin&utm_medium=scroll-triggered-boxes&utm_campaign=plugins-page
Description: Call-To-Action Boxes that display after visitors scroll down far enough. Unobtrusive, but highly conversing!
Author: ibericode
Author URI: http://ibericode.com/
Text Domain: scroll-triggered-boxes
Domain Path: /languages/
License: GPL v3
GitHub Plugin URI: https://github.com/ibericode/scroll-triggered-boxes

Scroll Triggered Boxes Plugin
Copyright (C) 2013-2015, Danny van Kooten, hi@dannyvankooten.com

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

// wrapper function to move out of global namespace
function __load_scroll_triggered_boxes() {
	// load autoloader & init plugin
	require dirname( __FILE__ ) . '/vendor/autoload.php';

	// we need this constant later on
	$id = 0;
	$file = __FILE__;
	$dir = dirname( __FILE__ );
	$name = 'Scroll Triggered Boxes';
	$version = '2.0.2';

	$reflect  = new ReflectionClass( 'ScrollTriggeredBoxes\\Plugin' );
	$GLOBALS['stb'] = $reflect->newInstanceArgs( array(
			$id,
			$name,
			$version,
			$file,
			$dir
		)
	);
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