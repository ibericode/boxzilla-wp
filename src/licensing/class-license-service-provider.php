<?php

namespace Boxzilla\Licensing;

use Boxzilla\DI\Container,
	Boxzilla\DI\ServiceProviderInterface;

class LicenseServiceProvider implements ServiceProviderInterface {

	/**
	 * Registers all licensing related services
	 *
	 * @param Container $container
	 */
	public function register( Container $container ) {

		$container['license'] = function( $container ) {
			return new License( 'boxzilla_license' );
		};

		$container['license_api'] = function( $container ) {
			$api_url = 'https://platform.boxzillaplugin.com/api/v2';
			return new API( $api_url, $container['license'] );
		};

		$container['license_manager'] = function( $container ) {
			return new LicenseManager( $container['plugins'], $container['license_api'], $container['license'], $container['notices'] );
		};

		$container['update_manager'] = function( $container ) {
			return new UpdateManager( $container['plugins'], $container['license_api'], $container['license'] );
		};

		$container['license_poller'] = function( $container ) {
			return new Poller( $container['license_api'], $container['license'] );
		};
	}

}
