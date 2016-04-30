<?php
/*
Plugin Name: Boxzilla
Version: 2.3
Plugin URI: https://boxzillaplugin.com/#utm_source=wp-plugin&utm_medium=boxzilla&utm_campaign=plugins-page
Description: Call-To-Action Boxes that display after visitors scroll down far enough. Unobtrusive, but highly conversing!
Author: ibericode
Author URI: https://ibericode.com/#utm_source=wp-plugin&utm_medium=boxzilla&utm_campaign=plugins-page
Text Domain: boxzilla
Domain Path: /languages/
License: GPL v3

Boxzilla Plugin
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
 * @ignore
 * @internal
 */
function __load_boxzilla() {

	// Load PHP 5.2 fallback
	if( version_compare( PHP_VERSION, '5.3', '<' ) ) {
		require dirname( __FILE__ ) . '/fallback.php';
		new STB_PHP_Fallback( 'Boxzilla', plugin_basename( __FILE__ ) );
		return;
	}

	define( 'BOXZILLA_FILE', __FILE__ );
	define( 'BOXZILLA_VERSION', '2.3' );

	require __DIR__ . '/bootstrap.php';
}


// load autoloader but only if not loaded already (for compat with sitewide autoloader)
if( ! function_exists( 'boxzilla' ) ) {
	require dirname( __FILE__ ) . '/vendor/autoload.php';
}

// register activation hook
register_activation_hook( __FILE__, array( 'Boxzilla\\Admin\\Installer', 'run' ) );

add_action( 'plugins_loaded', '__load_boxzilla', 8 );
