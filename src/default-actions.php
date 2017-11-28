<?php

defined( 'ABSPATH' ) or exit;

$boxzilla = boxzilla();

// Register custom post type
add_action( 'init', function() use( $boxzilla ){
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
        'menu_icon' => $boxzilla->plugin->url( '/assets/img/menu-icon.png' ),
        'query_var' => false,
        'capability_type' => 'box',
        'capabilities' => array(
            'edit_post' => 'edit_box',
            'edit_posts' => 'edit_boxes',
            'edit_others_posts' => 'edit_other_boxes',
            'publish_posts' => 'publish_boxes',
            'read_post' => 'read_box',
            'read_private_posts' => 'read_private_box',
            'delete_posts' => 'delete_box',
        ),
    );

    register_post_type( 'boxzilla-box', $args );

    add_shortcode( 'boxzilla_link', 'boxzilla_get_link_html' );
});

add_action( 'admin_init', function() {
    $admins = get_role( 'administrator' );

    if( ! $admins->has_cap( 'edit_box' ) ) {
        $admins->add_cap('edit_box');
        $admins->add_cap('edit_boxes');
        $admins->add_cap('edit_other_boxes');
        $admins->add_cap('publish_boxes');
        $admins->add_cap('read_box');
        $admins->add_cap('read_private_box');
        $admins->add_cap('delete_box');
    }
});

function boxzilla_get_link_html( $args = array(), $content = '' ) {
    $valid_actions = array(
        'show',
        'toggle',
        'hide',
        'dismiss'
    );
    $box_id = empty( $args['box'] ) ? '' : absint( $args['box'] );
    $class_attr = empty( $args['class'] ) ? '' : esc_attr( $args['class'] );
    $action = empty( $args['action'] ) || ! in_array( $args['action'], $valid_actions ) ? 'show' : $args['action'];
    return sprintf( '<a href="javascript:Boxzilla.%s(%s)" class="%s">', $action, $box_id, $class_attr ) . $content . '</a>';
}
