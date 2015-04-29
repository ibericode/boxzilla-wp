<?php

namespace ScrollTriggeredBoxes\Admin;

use ScrollTriggeredBoxes\DI\Container;
use ScrollTriggeredBoxes\DI\ServiceProviderInterface;

class AdminServiceProvider implements ServiceProviderInterface {

	/**
	 * Registers services on the given container.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @param Container $container An Container instance
	 */
	public function register( Container $container ) {
		$container['notices'] = function($container) {
			return new Notices();
		};
	}
}