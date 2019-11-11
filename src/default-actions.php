<?php

defined( 'ABSPATH' ) or exit;

$boxzilla = boxzilla();

// Register custom post type
add_action(
	'init',
	function () use ( $boxzilla ) {
		$args = array(
			'public'          => false,
			'labels'          => array(
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
				'menu_name'          => __( 'Boxzilla', 'boxzilla' ),
			),
			'show_ui'         => true,
			'menu_position'   => '108.1337133',
			'menu_icon'       => 'data:image/svg+xml;base64,' . base64_encode( '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid meet" viewBox="0 0 16 16" width="16" height="16"><defs><path d="M0 0L16 0L16 16L0 16L0 16L0 0ZM6.72 13.59L6.72 12.47L7.71 13.28L8.94 13.59L10.17 13.28L11.17 12.46L11.85 11.25L12.1 9.76L11.85 8.27L11.17 7.05L10.17 6.23L8.94 5.93L7.71 6.23L6.72 7.04L6.72 2.27L4.16 2.27L4.16 13.59L4.16 13.59L6.72 13.59ZM9.93 9.03L10.06 9.76L9.93 10.49L9.56 11.08L9.01 11.48L8.35 11.63L7.68 11.48L7.13 11.08L6.76 10.49L6.72 10.26L6.72 9.25L6.76 9.03L7.13 8.43L7.68 8.03L8.35 7.88L9.01 8.03L9.56 8.43L9.56 8.43L9.93 9.03Z" id="c1kAiZqIlD"></path></defs><g><g><g><use xlink:href="#c1kAiZqIlD" opacity="1" fill="#a0a5aa" fill-opacity="1"></use></g></g></g></svg>' ),
			'query_var'       => false,
			'capability_type' => 'box',
			'capabilities'    => array(
				'edit_post'          => 'edit_box',
				'edit_posts'         => 'edit_boxes',
				'edit_others_posts'  => 'edit_other_boxes',
				'publish_posts'      => 'publish_boxes',
				'read_post'          => 'read_box',
				'read_private_posts' => 'read_private_box',
				'delete_posts'       => 'delete_box',
			),
		);

		register_post_type( 'boxzilla-box', $args );

		add_shortcode( 'boxzilla_link', 'boxzilla_get_link_html' );
	}
);

add_action(
	'admin_init',
	function () {
		$admins = get_role( 'administrator' );

		if ( ! $admins->has_cap( 'edit_box' ) ) {
			$admins->add_cap( 'edit_box' );
			$admins->add_cap( 'edit_boxes' );
			$admins->add_cap( 'edit_other_boxes' );
			$admins->add_cap( 'publish_boxes' );
			$admins->add_cap( 'read_box' );
			$admins->add_cap( 'read_private_box' );
			$admins->add_cap( 'delete_box' );
		}
	}
);

function boxzilla_get_link_html( $args = array(), $content = '' ) {
	$valid_actions = array(
		'show',
		'toggle',
		'hide',
		'dismiss',
	);
	$box_id        = empty( $args['box'] ) ? '' : absint( $args['box'] );
	$class_attr    = empty( $args['class'] ) ? '' : esc_attr( $args['class'] );
	$action        = empty( $args['action'] ) || ! in_array( $args['action'], $valid_actions, true ) ? 'show' : $args['action'];
	return sprintf( '<a href="javascript:Boxzilla.%s(%s)" class="%s">', $action, $box_id, $class_attr ) . $content . '</a>';
}
