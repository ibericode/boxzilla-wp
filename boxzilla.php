<?php
/*
Plugin Name: Boxzilla
Version: 3.3.1
Plugin URI: https://www.boxzillaplugin.com/
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

define( 'BOXZILLA_FILE', __FILE__ );
define( 'BOXZILLA_VERSION', '3.3.1' );

require __DIR__ . '/autoload.php';
require __DIR__ . '/src/services.php';
require __DIR__ . '/src/licensing/services.php';

// register activation hook
register_activation_hook( __FILE__, array( 'Boxzilla\\Admin\\Installer', 'run' ) );

// Bootstrap plugin at later action hook
add_action(
	'plugins_loaded',
	function() {
		$boxzilla = boxzilla();

		// load default filters
		require __DIR__ . '/src/default-filters.php';
		require __DIR__ . '/src/default-actions.php';

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$boxzilla['filter.autocomplete']->init();
			$boxzilla['admin.menu']->init();
		} elseif ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			$boxzilla['license_poller']->init();
		} elseif ( is_admin() ) {
			$boxzilla['admin']->init();
			$boxzilla['admin.menu']->init();
		} else {
			add_action(
				'template_redirect',
				function() use ( $boxzilla ) {
					$boxzilla['box_loader']->init();
				}
			);
		}

		// license manager
		if ( is_admin() || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			$boxzilla['license_manager']->init();

			if ( count( $boxzilla->plugins ) > 0 ) {
				$boxzilla['update_manager']->init();
			}
		}

		// for legacy reasons: Boxzilla Theme Pack & Boxzilla WooCommerce used this
		// we will be removing this in future versions
		$boxzilla['bootstrapper']->run();
	},
	90
);
