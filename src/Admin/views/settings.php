<?php defined( 'ABSPATH' ) or exit; ?>
<div class="wrap" id="stb-admin" class="stb-settings">

	<div class="stb-row">
		<div class="stb-col-two-third">

			<h2><?php _e( 'Settings', 'scroll-triggered-boxes' ); ?></h2>

			<?php do_action( 'stb_before_settings' ); ?>

			<form action="<?php echo admin_url( 'options.php' ); ?>" method="post">

				<?php settings_fields( 'stb_settings' ); ?>

				<table class="form-table">

					<?php do_action( 'stb_before_settings_rows' ); ?>

					<tr valign="top">
						<th><label for="stb_test_mode"><?php _e( 'Enable test mode?', 'scroll-triggered-boxes' ); ?></label></th>
						<td>
							<label><input type="radio" id="stb_test_mode_1" name="stb_settings[test_mode]" value="1" <?php checked( $opts['test_mode'], 1 ); ?> /> <?php _e( 'Yes' ); ?></label> &nbsp;
							<label><input type="radio" id="stb_test_mode_0" name="stb_settings[test_mode]" value="0" <?php checked( $opts['test_mode'], 0 ); ?> /> <?php _e( 'No' ); ?></label> &nbsp;
							<p class="help"><?php _e( 'If test mode is enabled, all boxes will show up regardless of whether a cookie has been set.', 'scroll-triggered-boxes' ); ?></p>
						</td>
					</tr>

					<?php do_action( 'stb_after_settings_rows' ); ?>
				</table>

				<?php submit_button(); ?>
			</form>

			<?php do_action( 'stb_after_settings' ); ?>
		</div>

		<div class="stb-sidebar stb-col-one-third">

			<!-- Begin MailChimp Signup Form -->
			<div class="stb-box">
				<h3>Subscribe to our mailing list</h3>
				<?php include __DIR__ . '/metaboxes/email-optin.php'; ?>
			</div>
			<!--End mc_embed_signup-->

			<div class="stb-box">
				<h3>Looking for help?</h3>
				<?php include __DIR__ . '/metaboxes/need-help.php'; ?>
			</div>

		</div>
	</div>

	<br style="clear: both;" />

</div>