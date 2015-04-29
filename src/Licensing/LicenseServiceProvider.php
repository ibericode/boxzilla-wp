<?php

namespace ScrollTriggeredBoxes\Licensing;

use ScrollTriggeredBoxes\DI\Container,
	ScrollTriggeredBoxes\DI\ServiceProviderInterface;

class LicenseServiceProvider implements ServiceProviderInterface {

	/**
	 * Registers all licensing related services
	 *
	 * @param Container $container
	 */
	public function register( Container $container ) {

		$container['api_url'] = function( $container ) {
			return 'https://scrolltriggeredboxes.com/api/v1';
		};

		$container['license'] = function( $container ) {
			return new License( 'stb_license' );
		};

		$container['api'] = function( $container ) {
			return new API( $container['api_url'], $container['notices'] );
		};

		$container['license_manager'] = function( $container ) {
			return new LicenseManager( $container['plugins'], $container['api'], $container['license'] );
		};

		$container['update_manager'] = function( $container ) {
			return new UpdateManager( $container['plugins'], $container['api'], $container['license'] );
		};

		$container['api_authenticator'] = function( $container ) {
			return new Authenticator( $container['api_url'], $container['license'] );
		};

	}

}