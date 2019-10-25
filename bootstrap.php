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
        $boxzilla['admin.menu']->init();
    });

    $bootstrapper->ajax(function() use( $boxzilla ) {
        $boxzilla['filter.autocomplete']->init();
        $boxzilla['admin.menu']->init();
    });

    $bootstrapper->front(function() use( $boxzilla ) {
        add_action( 'template_redirect', function() use( $boxzilla ) {
            $boxzilla['box_loader']->init();
        });
    });

    $bootstrapper->cron(function() use( $boxzilla ) {
        $boxzilla['license_poller']->init();
    });

    // license manager
    if( is_admin() || (  defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
        $boxzilla['license_manager']->init();
       
        if( count( $boxzilla->plugins ) > 0 ) {
            $boxzilla['update_manager']->init();
        }
    }

    $bootstrapper->run();
}, 90 );
