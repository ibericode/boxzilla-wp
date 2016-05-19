<?php

namespace Boxzilla;

defined( 'ABSPATH' ) or exit;

/** @var Boxzilla $boxzilla */
$boxzilla = boxzilla();

// register services
$provider = new BoxzillaServiceProvider();
$provider->register( $boxzilla );
$provider = new Licensing\LicenseServiceProvider();
$provider->register( $boxzilla );

// Rest of bootstrapping runs at plugins_loaded:90
add_action( 'plugins_loaded', function() use( $boxzilla ) {

    // load default filters
    require __DIR__ . '/src/default-filters.php';
    require __DIR__ . '/src/default-actions.php';

    // load secction specific code
    $section = boxzilla_get_section();
    switch( $section ) {
        case 'admin':
            $boxzilla['admin']->init();
            add_action( 'admin_init', array( $boxzilla['license_manager'], 'init' ) );
            $boxzilla['update_manager']->add_hooks();
            break;

        case 'ajax':
            // AJAX
            $boxzilla['filter.autocomplete']->add_hooks();
            break;

        case 'public':
            add_action( 'template_redirect', function() use( $boxzilla ) {
                $boxzilla['box_loader']->init();
            });
            break;

        case 'cron':
            $boxzilla['license_poller']->hook();
            break;
    }
}, 90 );
