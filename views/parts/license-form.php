<?php defined( 'ABSPATH' ) or exit; ?>

<h2><?php _e( 'License & Plugin Updates', 'scroll-triggered-boxes' ); ?></h2>

<form method="post">
	<table class="form-table">
		<tr valign="top">
			<th><?php _e( 'License Key', 'scroll-triggered-boxes' ); ?></th>
			<td>
				<input class="regular-text" name="license_key" placeholder="<?php esc_attr_e( 'Enter your license key..', 'scroll-triggered-boxes' ); ?>" value="<?php echo esc_attr( $this->license->key ); ?>" <?php if( $this->license->activated ) { echo 'readonly'; } ?> />
				<input class="button" type="submit" name="action" value="<?php echo ( $this->license->activated ) ? 'deactivate' : 'activate'; ?>" />
				<p class="help"><?php _e( 'The license key received when purchasing your premium plan.', 'scroll-triggered-boxes' ); ?></p>
			</td>
		</tr>
		<tr valign="top">
			<th><?php _e( 'License Status', 'scroll-triggered-boxes' ); ?></th>
			<td>
				<?php
				if( $this->license->activated ) { ?>
					<p><span class="status positive"><?php _e( 'ACTIVE', 'scroll-triggered-boxes' ); ?></span> - <?php _e( 'you are receiving plugin updates', 'scroll-triggered-boxes' ); ?></p>
				<?php } else { ?>
					<p><span class="status negative"><?php _e( 'INACTIVE', 'scroll-triggered-boxes' ); ?></span> - <?php _e( 'you are <strong>not</strong> receiving plugin updates', 'scroll-triggered-boxes' ); ?></p>
				<?php } ?>
			</td>
		</tr>
	</table>

	<p>
		<?php if( $this->license->activated ) { ?>
			<strong><?php _e( 'Great! You are receiving plugin updates for the following plugins:', 'scroll-triggered-boxes' ); ?></strong>
		<?php } else { ?>
			<strong><?php _e( 'Warning! You are <u>not</u> receiving plugin updates for the following plugins:', 'scroll-triggered-boxes' ); ?></strong>
		<?php } ?>
		<?php echo join( ', ', $this->extensions->map(function($p) { return $p->name(); }) ); ?>
	</p>

	<input type="submit" class="button button-primary" name="action" value="<?php _e( 'Save Changes' ); ?>" />

	<input type="hidden" name="stb_license_form" value="1" />
</form>