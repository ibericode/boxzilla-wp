<?php

namespace Boxzilla;

defined( 'ABSPATH' ) or exit;

/** @var Boxzilla $boxzilla */
$boxzilla = boxzilla();

// register services
$provider = new BoxzillaServiceProvider();
$provider->register( $boxzilla );

// load default filters
require __DIR__ . '/src/default-filters.php';

add_action( 'init', function() use( $boxzilla ){
    // Register custom post type
    $args = array(
        'public' => false,
        'labels'  =>  array(
            'name'               => __( 'Boxzilla', 'boxzilla' ),
            'singular_name'      => __( 'Box', 'boxzilla' ),
            'add_new'            => __( 'Add New', 'boxzilla' ),
            'add_new_item'       => __( 'Add New Box', 'boxzilla' ),
            'edit_item'          => __( 'Edit Box', 'boxzilla' ),
            'new_item'           => __( 'New Box', 'boxzilla' ),
            'all_items'          => __( 'All Boxes', 'boxzilla' ),
            'view_item'          => __( 'View Box', 'boxzilla' ),
            'search_items'       => __( 'Search Boxes', 'boxzilla' ),
            'not_found'          => __( 'No Boxes found', 'boxzilla' ),
            'not_found_in_trash' => __( 'No Boxes found in Trash', 'boxzilla' ),
            'parent_item_colon'  => '',
            'menu_name'          => __( 'Boxzilla', 'boxzilla' )
        ),
        'show_ui' => true,
        'menu_position' => '108.1337133',
        'menu_icon' => $boxzilla->plugin->url( '/assets/img/menu-icon.jpg' ),
        'query_var' => false
    );

    register_post_type( 'boxzilla-box', $args );
});

if( ! is_admin() ) {

    // PUBLIC
    add_action( 'template_redirect', function() use( $boxzilla ) {
        $boxzilla['box_loader']->init();
    });

} else {

    // ADMIN (and AJAX)
    $provider = new Licensing\LicenseServiceProvider();
    $provider->register( $boxzilla );

    if( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
        add_action('init', function() use( $boxzilla ) {
            $boxzilla['admin']->init();
        });
    } else {
        $boxzilla['filter.autocomplete']->add_hooks();
    }
    
}

