<?php

namespace Boxzilla;

use Boxzilla\Admin\Admin;
use Boxzilla\Admin\Menu;
use Boxzilla\Admin\Notices;

defined( 'ABSPATH' ) or exit;

$boxzilla = boxzilla();

$boxzilla['admin'] = function ( $boxzilla ) {
	return new Admin( $boxzilla->plugin, $boxzilla );
};

$boxzilla['admin.menu'] = function() {
	return new Menu();
};

$boxzilla['bootstrapper'] = function() {
	return new Bootstrapper();
};

$boxzilla['box_loader'] = function ( $boxzilla ) {
	return new BoxLoader( $boxzilla->plugin, $boxzilla->options );
};

$boxzilla['filter.autocomplete'] = function () {
	return new Filter\Autocomplete();
};

$boxzilla['notices'] = function () {
	return new Notices();
};

$boxzilla['options'] = function () {
	$defaults = array(
		'test_mode' => 0,
	);

	$options = (array) get_option( 'boxzilla_settings', $defaults );
	$options = array_merge( $defaults, $options );
	return $options;
};

$boxzilla['plugin'] = new Plugin(
	'boxzilla',
	'Boxzilla',
	BOXZILLA_VERSION,
	BOXZILLA_FILE,
	dirname( BOXZILLA_FILE )
);

$boxzilla['plugins'] = function () {
	$raw = (array) apply_filters( 'boxzilla_extensions', array() );

	$plugins = array();
	foreach ( $raw as $p ) {
		$plugins[ $p->id() ] = $p;
	}
	return $plugins;
};
