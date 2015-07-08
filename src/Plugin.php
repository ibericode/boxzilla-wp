<?php

namespace ScrollTriggeredBoxes;

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
				'name'               => __( 'Scroll Triggered Boxes', 'scroll-triggered-boxes' ),
				'singular_name'      => __( 'Scroll Triggered Box', 'scroll-triggered-boxes' ),
				'add_new'            => __( 'Add New', 'scroll-triggered-boxes' ),
				'add_new_item'       => __( 'Add New Box', 'scroll-triggered-boxes' ),
				'edit_item'          => __( 'Edit Box', 'scroll-triggered-boxes' ),
				'new_item'           => __( 'New Box', 'scroll-triggered-boxes' ),
				'all_items'          => __( 'All Boxes', 'scroll-triggered-boxes' ),
				'view_item'          => __( 'View Box', 'scroll-triggered-boxes' ),
				'search_items'       => __( 'Search Boxes', 'scroll-triggered-boxes' ),
				'not_found'          => __( 'No Boxes found', 'scroll-triggered-boxes' ),
				'not_found_in_trash' => __( 'No Boxes found in Trash', 'scroll-triggered-boxes' ),
				'parent_item_colon'  => '',
				'menu_name'          => __( 'Scroll Triggered Boxes', 'scroll-triggered-boxes' )
			),
			'show_ui' => true,
			'menu_position' => '108.1337133',
			'menu_icon' => $this->url( '/assets/img/menu-icon.png' ),
			'query_var' => false
		);

		register_post_type( 'scroll-triggered-box', $args );
	}

}