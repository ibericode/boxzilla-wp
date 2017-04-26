<?php

class Boxzilla_PHP_Fallback {

	/**
	 * @var string
	 */
	private $plugin_name = '';

	/**
	 * @var string
	 */
	private $plugin_file = '';

	/**
	 * @param $plugin_name
	 * @param $plugin_file
	 */
	public function __construct( $plugin_name, $plugin_file ) {

		$this->plugin_name = $plugin_name;
		$this->plugin_file = $plugin_file;

		// deactivate plugin straight away
		add_action( 'admin_init', array( $this, 'deactivate_self' ) );
	}

	/**
	 * @return bool
	 */
	public function deactivate_self() {
		if( ! current_user_can( 'activate_plugins' ) ) {
			return false;
		}

		// deactivate self
		deactivate_plugins( $this->plugin_file );

		// get rid of "Plugin activated" notice
		if( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		// show notice to user
		add_action( 'admin_notices', array( $this, 'show_notice' ) );

		return true;
	}

	/**
	 * @return void
	 */
	public function show_notice() {
		?>
		<div class="updated">
			<p><?php printf( '<strong>%s</strong> did not activate because it requires <strong>PHP v5.3</strong> or higher, while your server is running <strong>PHP v%s</strong>.', $this->plugin_name, PHP_VERSION ); ?>
			<p><?php printf( '<a href="%s">Updating your PHP version</a> makes your site faster, more secure and should be easy for your host.', 'http://www.wpupdatephp.com/update/#utm_source=wp-plugin&utm_medium=boxzillas&utm_campaign=activation-notice' ); ?></p>
		</div>
		<?php
	}

}
