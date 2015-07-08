<?php

namespace ScrollTriggeredBoxes;

use ScrollTriggeredBoxes\Admin\Admin;
use ScrollTriggeredBoxes\DI\Container;
use ScrollTriggeredBoxes\DI\ServiceProviderInterface;
use ScrollTriggeredBoxes\Filter\Autocomplete;

class PluginServiceProvider implements ServiceProviderInterface {

	/**
	 * Registers services on the given container.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @param Container $container An Container instance
	 */
	public function register( Container $container ) {
		$container['options'] = function( $app ) {
			$defaults = array(
				'test_mode' => 0
			);

			$options = (array) get_option( 'stb_settings', $defaults );
			$options = array_merge( $defaults, $options );
			return $options;
		};

		$container['plugins'] = function( $app ) {
			$plugins = (array) apply_filters( 'stb_extensions', array() );
			return new Collection( $plugins );
		};

		$container['box_loader'] = function( $app ) {
			return new BoxLoader( $app );
		};

		$container['admin'] = function( $app ) {
			return new Admin( $app );
		};

		$container['filter.autocomplete'] = function( $app ) {
			return new Filter\Autocomplete();
		};
	}
}