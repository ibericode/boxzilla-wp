<?php
// prevent direct file access
defined( 'ABSPATH' ) or exit;

/** @var Boxzilla\Licensing\License $license */
?>

<h2><?php _e( 'License & Plugin Updates', 'boxzilla' ); ?></h2>

<?php if( ! $license->activated ) { ?>
	<div class="error inline">
		<p>
			<strong><?php _e( 'Warning! You are <u>not</u> receiving plugin updates for the following plugin(s):', 'boxzilla' ); ?></strong>
		</p>
		<ul class="ul-square">
			<li><?php echo join( '</li><li>', $this->extensions->map(function($p) { return $p->name(); }) ); ?></li>
		</ul>
		<p>
			To fix this, please activate your license using the form below.
		</p>
	</div>
<?php } ?>

<form method="post">
	<table class="form-table">
		<tr valign="top">
			<th><?php _e( 'License Key', 'boxzilla' ); ?></th>
			<td>
				<input size="40" name="boxzilla_license_key" placeholder="<?php esc_attr_e( 'Enter your license key..', 'boxzilla' ); ?>" value="<?php echo esc_attr( $this->license->key ); ?>" <?php if( $this->license->activated ) { echo 'readonly'; } ?> />
				<input class="button" type="submit" name="action" value="<?php echo ( $this->license->activated ) ? 'deactivate' : 'activate'; ?>" />
				<p class="help">
					<?php echo sprintf( __( 'The license key received when purchasing your premium Boxzilla plan. <a href="%s">You can find it here</a>.', 'boxzilla' ), 'https://platform.boxzillaplugin.com/' ); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th><?php _e( 'License Status', 'boxzilla' ); ?></th>
			<td>
				<?php
				if( $license->activated ) { ?>
					<p><span class="status positive"><?php _e( 'ACTIVE', 'boxzilla' ); ?></span> - <?php _e( 'you are receiving plugin updates', 'boxzilla' ); ?></p>
				<?php } else { ?>
					<p><span class="status negative"><?php _e( 'INACTIVE', 'boxzilla' ); ?></span> - <?php _e( 'you are <strong>not</strong> receiving plugin updates', 'boxzilla' ); ?></p>
				<?php } ?>
			</td>
		</tr>
	</table>




	<p>
		<input type="submit" class="button button-primary" name="action" value="<?php _e( 'Save Changes' ); ?>" />
	</p>

	<input type="hidden" name="boxzilla_license_form" value="1" />
</form>