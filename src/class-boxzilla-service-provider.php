<?php

namespace Boxzilla;

use Boxzilla\Admin\Admin;
use Boxzilla\Admin\Notices;
use Boxzilla\DI\Container;
use Boxzilla\DI\ServiceProviderInterface;

class BoxzillaServiceProvider implements ServiceProviderInterface {

	/**
	 * Registers services on the given container.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @param Container $container An Container instance
	 */
	public function register( Container $container ) {

		$container['admin'] = function( $container ) {
			return new Admin( $container->plugin, $container );
		};

		$container['bootstrapper'] = new Bootstrapper();

		$container['box_loader'] = function( $container ) {
			return new BoxLoader( $container->plugin, $container->options );
		};

		$container['filter.autocomplete'] = function( $container ) {
			return new Filter\Autocomplete();
		};

		$container['notices'] = function( $container ) {
			return new Notices();
		};

		$container['options'] = function( $container ) {
			$defaults = array(
				'test_mode' => 0
			);

			$options = (array) get_option( 'boxzilla_settings', $defaults );
			$options = array_merge( $defaults, $options );
			return $options;
		};

		$container['plugin'] = new Plugin(
			'boxzilla',
			'Boxzilla',
			BOXZILLA_VERSION,
			BOXZILLA_FILE,
			dirname( BOXZILLA_FILE )
		);

		$container['plugins'] = function( $container ) {
			$plugins = (array) apply_filters( 'boxzilla_extensions', array() );
			return new Collection( $plugins );
		};






	}
}