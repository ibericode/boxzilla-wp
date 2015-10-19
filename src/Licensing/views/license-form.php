<?php defined( 'ABSPATH' ) or exit; ?>

<h2><?php _e( 'License & Plugin Updates', 'scroll-triggered-boxes' ); ?></h2>

<?php if( ! $this->license->activated ) { ?>
	<div class="error inline">
		<p>
			<strong><?php _e( 'Warning! You are <u>not</u> receiving plugin updates for the following plugin(s):', 'scroll-triggered-boxes' ); ?></strong>
			<?php echo join( ', ', $this->extensions->map(function($p) { return $p->name(); }) ); ?>.
			To fix this, please activate your license.
		</p>
	</div>
<?php } ?>

<form method="post">
	<table class="form-table">
		<tr valign="top">
			<th><?php _e( 'License Key', 'scroll-triggered-boxes' ); ?></th>
			<td>
				<input size="40" name="stb_license_key" placeholder="<?php esc_attr_e( 'Enter your license key..', 'scroll-triggered-boxes' ); ?>" value="<?php echo esc_attr( $this->license->key ); ?>" <?php if( $this->license->activated ) { echo 'readonly'; } ?> />
				<input class="button" type="submit" name="action" value="<?php echo ( $this->license->activated ) ? 'deactivate' : 'activate'; ?>" />
				<p class="help">
					<?php echo sprintf( __( 'The license key received when purchasing your premium plan. <a href="%s">You can find it here</a>.', 'scroll-triggered-boxes' ), 'https://scrolltriggeredboxes.com/account' ); ?>
				</p>
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
		<input type="submit" class="button button-primary" name="action" value="<?php _e( 'Save Changes' ); ?>" />
	</p>

	<input type="hidden" name="stb_license_form" value="1" />
</form>