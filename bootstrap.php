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

    $bootstrapper = $boxzilla->bootstrapper;

    $bootstrapper->admin(function() use( $boxzilla ){
        $boxzilla['admin']->init();

        if( count( $boxzilla->plugins ) > 0 ) {
            $boxzilla['license_manager']->hook();
            $boxzilla['update_manager']->hook();
        }
    });

    $bootstrapper->ajax(function() use( $boxzilla ) {
        $boxzilla['filter.autocomplete']->add_hooks();
    });

    $bootstrapper->front(function() use( $boxzilla ) {
        add_action( 'template_redirect', function() use( $boxzilla ) {
            $boxzilla['box_loader']->init();
        });
    });

    $bootstrapper->cron(function() use( $boxzilla ) {
        $boxzilla['license_poller']->hook();
    });

    $bootstrapper->run();
}, 90 );
