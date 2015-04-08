<?php
/*
Plugin Name: Scroll Triggered Boxes
Version: 2.0
Plugin URI: https://dannyvankooten.com/
Description: Call-To-Action Boxes that display after visitors scroll down far enough. Highly conversing, not so annoying!
Author: Danny van Kooten
Author URI: https://dannyvankooten.com/
Text Domain: scroll-triggered-boxes
Domain Path: /languages/
License: GPL v3

Scroll Triggered Boxes Plugin
Copyright (C) 2013-2014, Danny van Kooten, hi@dannyvankooten.com

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

if( version_compare( PHP_VERSION, '5.3', '>=' ) ) {

	// load autoloader & init plugin
	require dirname( __FILE__ ) . '/vendor/autoload.php';

	// we need this constant later on
	define( 'STB_PLUGIN_FILE', __FILE__ );

	// instantiate plugin class in The One True Global
	$GLOBALS['ScrollTriggeredBoxes'] = call_user_func( array( 'ScrollTriggeredBoxes\\Plugin', 'bootstrap' ) );

} else {

	// load php 5.2 fallback
	require dirname( __FILE__ ) . '/fallback.php';
	new STB_PHP_Fallback( 'Scroll Triggered Boxes', plugin_basename( __FILE__ ) );

}

