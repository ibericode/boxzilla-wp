<?php

require __DIR__ . '/src/functions.php';

spl_autoload_register(function ($class) {
	static $classmap = [
		'Boxzilla\\Admin\\Admin' => __DIR__ . '/src/admin/class-admin.php',
	    'Boxzilla\\Admin\\Installer' => __DIR__ . '/src/admin/class-installer.php',
	    'Boxzilla\\Admin\\Menu' => __DIR__ . '/src/admin/class-menu.php',
	    'Boxzilla\\Admin\\Migrations' => __DIR__ . '/src/admin/class-migrations.php',
	    'Boxzilla\\Admin\\Notices' => __DIR__ . '/src/admin/class-notices.php',
	    'Boxzilla\\Admin\\ReviewNotice' => __DIR__ . '/src/admin/class-review-notice.php',
	    'Boxzilla\\Box' => __DIR__ . '/src/class-box.php',
	    'Boxzilla\\BoxLoader' => __DIR__ . '/src/class-loader.php',
	    'Boxzilla\\Boxzilla' => __DIR__ . '/src/class-boxzilla.php',
	    'Boxzilla\\DI\\Container' => __DIR__ . '/src/di/class-container.php',
	    'Boxzilla\\DI\\ContainerWithPropertyAccess' => __DIR__ . '/src/di/class-container-with-property-access.php',
	    'Boxzilla\\Filter\\Autocomplete' => __DIR__ . '/src/admin/class-autocomplete.php',
	    'Boxzilla\\Licensing\\API' => __DIR__ . '/src/licensing/class-api.php',
	    'Boxzilla\\Licensing\\API_Exception' => __DIR__ . '/src/licensing/class-api-exception.php',
	    'Boxzilla\\Licensing\\License' => __DIR__ . '/src/licensing/class-license.php',
	    'Boxzilla\\Licensing\\LicenseManager' => __DIR__ . '/src/licensing/class-license-manager.php',
	    'Boxzilla\\Licensing\\Poller' => __DIR__ . '/src/licensing/class-poller.php',
	    'Boxzilla\\Licensing\\UpdateManager' => __DIR__ . '/src/licensing/class-update-manager.php',
	    'Boxzilla\\Plugin' => __DIR__ . '/src/class-plugin.php',
	];

	if (isset($classmap[$class])) {
		require $classmap[$class];
	}
});
