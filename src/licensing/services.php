<?php

namespace Boxzilla\Licensing;

defined( 'ABSPATH' ) or exit;

$boxzilla = boxzilla();

$boxzilla['license'] = function ( $boxzilla ) {
	return new License( 'boxzilla_license' );
};

$boxzilla['license_api'] = function ( $boxzilla ) {
	$api_url = 'https://my.boxzillaplugin.com/api/v2';
	return new API( $api_url, $boxzilla['license'] );
};

$boxzilla['license_manager'] = function ( $boxzilla ) {
	return new LicenseManager( $boxzilla['plugins'], $boxzilla['license_api'], $boxzilla['license'] );
};

$boxzilla['update_manager'] = function ( $boxzilla ) {
	return new UpdateManager( $boxzilla['plugins'], $boxzilla['license_api'], $boxzilla['license'] );
};

$boxzilla['license_poller'] = function ( $boxzilla ) {
	return new Poller( $boxzilla['license_api'], $boxzilla['license'] );
};
