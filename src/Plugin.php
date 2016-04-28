<?php

namespace Boxzilla;

final class Plugin extends PluginBase {


	/**
	 * Register services in the Service Container
	 */
	protected function register_services() {
		$provider = new PluginServiceProvider();
		$provider->register( $this );
	}

	/**
	 * Start loading classes on `plugins_loaded`, priority 20.
	 */
	public function load() {
		$container = $this;

		add_action( 'init', array( $this, 'register_post_type' ) );

		if( ! is_admin() ) {
			add_action( 'template_redirect', function() use( $container ) {
				$container['box_loader']->init();
			});
		} elseif( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			add_action('init', function() use( $container ) {
				$container['admin']->init();
			});
		} else {
			$container['filter.autocomplete']->add_hooks();
		}
	}

	/**
	 * Register the box post type
	 */
	public function register_post_type() {
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
			'menu_icon' => $this->url( '/assets/img/menu-icon.jpg' ),
			'query_var' => false
		);

		register_post_type( 'boxzilla-box', $args );
	}

}