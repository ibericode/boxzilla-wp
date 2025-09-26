<?php

/*
Plugin Name: Boxzilla
Version: 3.4.5
Plugin URI: https://www.boxzillaplugin.com/
Description: Call-To-Action Boxes that display after visitors scroll down far enough. Unobtrusive, but highly conversing!
Author: ibericode
Author URI: https://www.ibericode.com/
Text Domain: boxzilla
Domain Path: /languages/
License: GPL-3.0-or-later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Boxzilla Plugin
Copyright (C) 2013 Danny van Kooten

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>.
*/


// Exit if not loaded inside a WordPress context
defined('ABSPATH') or exit;

// Exit if PHP lower than 7.4
PHP_VERSION_ID >= 70400 or exit;

define('BOXZILLA_FILE', __FILE__);
define('BOXZILLA_DIR', __DIR__);
define('BOXZILLA_VERSION', '3.4.5');

require __DIR__ . '/autoload.php';
require __DIR__ . '/src/services.php';
require __DIR__ . '/src/licensing/services.php';

// register activation hook
register_activation_hook(__FILE__, [ 'Boxzilla\\Admin\\Installer', 'run' ]);

// Bootstrap plugin at later action hook
add_action(
    'plugins_loaded',
    function () {
        $boxzilla = boxzilla();

        // load default filters
        require __DIR__ . '/src/default-filters.php';
        require __DIR__ . '/src/default-actions.php';

        if (defined('DOING_AJAX') && DOING_AJAX) {
            $boxzilla['filter.autocomplete']->init();
            $boxzilla['admin.menu']->init();
        } elseif (defined('DOING_CRON') && DOING_CRON) {
            $boxzilla['license_poller']->init();
        } elseif (is_admin()) {
            $boxzilla['admin']->init();
            $boxzilla['admin.menu']->init();
        } else {
            add_action(
                'template_redirect',
                function () use ($boxzilla) {
                    $boxzilla['box_loader']->init();
                }
            );
        }

        // license manager
        if (is_admin() || ( defined('DOING_CRON') && DOING_CRON ) || ( defined('WP_CLI') && WP_CLI )) {
            $boxzilla['license_manager']->init();

            if (count($boxzilla->plugins) > 0) {
                $boxzilla['update_manager']->init();
            }
        }
    },
    90
);
