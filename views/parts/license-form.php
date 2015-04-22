<?php defined( 'ABSPATH' ) or exit; ?>

<tr valign="top">
	<th><?php _e( 'License Key', 'scroll-triggered-boxes' ); ?></th>
	<td>
		<input class="regular-text" name="license_key" placeholder="<?php esc_attr_e( 'Enter your license key..', 'scroll-triggered-boxes' ); ?>" value="<?php echo esc_attr( $this->license->key ); ?>" />
		<p class="help"><?php _e( 'The license key received when purchasing your premium plan.', 'scroll-triggered-boxes' ); ?></p>
	</td>
</tr>
<tr valign="top">
	<th><?php _e( 'License Status', 'scroll-triggered-boxes' ); ?></th>
	<td>
		<?php if( $this->license->activated ) { ?>
			<p>
				<span class="status positive"><?php _e( 'Active', 'scroll-triggered-boxes' ); ?></span> -
				<?php _e( 'your license is active, you are receiving plugin updates.' ); ?>
			</p>
		<?php } else { ?>
			<p>
				<span class="status negative"><?php _e( 'Inactive', 'scroll-triggered-boxes' ); ?></span> -
				<?php _e( 'your license is not active, you are <strong>not</strong> receiving plugin updates.' ); ?>
			</p>
		<?php } ?>
	</td>
</tr>

<input type="hidden" name="stb_license_form" value="1" />