<?php
/*
Plugin Name: Boxzilla
Version: 3.2.27
Plugin URI: https://boxzillaplugin.com/#utm_source=wp-plugin&utm_medium=boxzilla&utm_campaign=plugins-page
Description: Call-To-Action Boxes that display after visitors scroll down far enough. Unobtrusive, but highly conversing!
Author: ibericode
Author URI: https://www.ibericode.com/
Text Domain: boxzilla
Domain Path: /languages/
License: GPL v2

Boxzilla Plugin
Copyright (C) 2013 - 2024, Danny van Kooten, hi@dannyvankooten.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

defined('ABSPATH') or exit;

/**
 * @ignore
 * @internal
 */
function _load_boxzilla() {

	define( 'BOXZILLA_FILE', __FILE__ );
	define( 'BOXZILLA_VERSION', '3.2.27' );

	require __DIR__ . '/bootstrap.php';
}

// bail if not on PHP 5.3 or later
if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {
	require dirname( __FILE__ ) . '/src/class-php-fallback.php';
	new Boxzilla_PHP_Fallback( 'Boxzilla', plugin_basename( __FILE__ ) );
	return;
}

// load autoloader but only if not loaded already (for compat with sitewide autoloader)
if ( ! function_exists( 'boxzilla' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

// register activation hook
register_activation_hook( __FILE__, array( 'Boxzilla\\Admin\\Installer', 'run' ) );

// hook into plugins_loaded for boostrapping
add_action( 'plugins_loaded', '_load_boxzilla', 8 );



