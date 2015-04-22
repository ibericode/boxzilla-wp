<?php defined( 'ABSPATH' ) or exit; ?>

<h2>License & Automatic Updates</h2>

<form method="post">
	<table class="form-table">
		<tr valign="top">
			<th><?php _e( 'License Key', 'scroll-triggered-boxes' ); ?></th>
			<td>
				<input class="regular-text" name="license_key" placeholder="<?php esc_attr_e( 'Enter your license key..', 'scroll-triggered-boxes' ); ?>" value="<?php echo esc_attr( $this->license->key ); ?>" />
				<p class="help"><?php _e( 'The license key received when purchasing your premium plan.', 'scroll-triggered-boxes' ); ?></p>
			</td>
		</tr>
		<?php foreach( $this->extensions as $plugin ) { ?>
			<tr valign="top">
				<th><?php _e( 'Receive updates for..', 'scroll-triggered-boxes' ); ?></th>
				<td>
					<label class="radio-label"><input type="checkbox" name="license_activations[]" value="<?php echo esc_attr( $plugin->id() ); ?>" <?php checked( $this->license->is_plugin_activated( $plugin ), true ) ?> /> &nbsp; <?php echo $plugin->name(); ?></label>
				</td>
			</tr>
		<?php } ?>
	</table>

	<?php submit_button(); ?>

	<input type="hidden" name="stb_license_form" value="1" />
</form>