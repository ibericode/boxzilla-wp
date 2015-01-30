<?php
if( ! defined( 'STB::VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
} ?>
<h2 class="no-top-margin"><?php _e( 'Scroll Triggered Box Options', 'scroll-triggered-boxes' ); ?></h2>

<h3 class="stb-title"><?php _e( 'Appearance', 'scroll-triggered-boxes' ); ?></h3>
<table class="form-table">
	<tr valign="top">
		<td>
			<label class="stb-label" for="stb-background-color"><?php _e( 'Background color', 'scroll-triggered-boxes' ); ?></label>
			<input id="stb-background-color" name="stb[css][background_color]" type="text" class="stb-color-field" value="<?php echo esc_attr($opts['css']['background_color']); ?>" />
		</td>
		<td>
			<label class="stb-label" for="stb-color"><?php _e( 'Text color', 'scroll-triggered-boxes' ); ?></label>
			<input id="stb-color" name="stb[css][color]" type="text" class="stb-color-field" value="<?php echo esc_attr($opts['css']['color']); ?>" />
		</td>
		<td>
			<label class="stb-label" for="stb-width"><?php _e( 'Box width', 'scroll-triggered-boxes' ); ?></label>
			<input id="stb-width" name="stb[css][width]" id="stb-box-width" type="number" min="0" max="1600" value="<?php echo esc_attr($opts['css']['width']); ?>" />
		</td>
	</tr>
	<tr valign="top">
		<td>
			<label class="stb-label" for="stb-border-color"><?php _e( 'Border color', 'scroll-triggered-boxes' ); ?></label>
			<input name="stb[css][border_color]" id="stb-border-color" type="text" class="stb-color-field" value="<?php echo esc_attr($opts['css']['border_color']); ?>" />
		</td>
		<td>
			<label class="stb-label" for="stb-border-width"><?php _e( 'Border width', 'scroll-triggered-boxes' ); ?></label>
			<input name="stb[css][border_width]" id="stb-border-width" type="number" min="0" max="25" value="<?php echo esc_attr($opts['css']['border_width']); ?>" />
		</td>
		<td></td>
	</tr>
</table>

<h3 class="stb-title"><?php _e( 'Display Options', 'scroll-triggered-boxes' ); ?></h3>
<table class="form-table">
	<?php 
	$key = 0;
	foreach($opts['rules'] as $rule) { ?>
		<tr valign="top" class="stb-rule-row">
			<th><label><?php _e( 'Show this box', 'scroll-triggered-boxes' ); ?></label></th>
			<td class="stb-sm">
				<select class="widefat stb-rule-condition" name="stb[rules][<?php echo $key; ?>][condition]">
					<optgroup label="<?php _e( 'Basic', 'scroll-triggered-boxes' ); ?>">
						<option value="everywhere" <?php selected($rule['condition'], 'everywhere')?>><?php _e( 'Everywhere', 'scroll-triggered-boxes' ); ?></option>
						<option value="is_post_type" <?php selected($rule['condition'], 'is_post_type'); ?>><?php _e( 'if Post Type is', 'scroll-triggered-boxes' ); ?></option>
						<option value="is_page" <?php selected($rule['condition'], 'is_page'); ?>><?php _e( 'if Page is', 'scroll-triggered-boxes' ); ?></option>
						<option value="is_not_page" <?php selected($rule['condition'], 'is_not_page'); ?>><?php _e( 'if Page is not', 'scroll-triggered-boxes' ); ?></option>
						<option value="is_single" <?php selected($rule['condition'], 'is_single'); ?>><?php _e( 'if Post is', 'scroll-triggered-boxes' ); ?></option>
					</optgroup>
					<optgroup label="<?php _e( 'Advanced', 'scroll-triggered-boxes' ); ?>">
						<option value="manual" <?php selected($rule['condition'], 'manual'); ?>><?php _e( 'Manual conditon', 'scroll-triggered-boxes' ); ?></option>
					</optgroup>
				</select>
			</td>
			<td>
				<input class="stb-rule-value widefat" name="stb[rules][<?php echo $key; ?>][value]" type="text" value="<?php echo esc_attr($rule['value']); ?>" placeholder="<?php _e( 'Leave empty for any or enter (comma-separated) names or ID\'s', 'scroll-triggered-boxes' ); ?>" <?php if( $rule['condition'] == 'everywhere' ) { echo 'style="display: none;"'; } ?> />
			</td>
			<td class="stb-xsm" width="1"><span class="stb-close stb-remove-rule">×</span></td>
		</tr>
	<?php $key++;
	} ?>
	<tr>
		<th></th>
		<td colspan="3"><button type="button" class="button stb-add-rule"><?php _e( 'Add rule', 'scroll-triggered-boxes' ); ?></button></td>
	</tr>
	<tr class="stb-manual-tip" style="display: none;">
		<td></td><td></td><td colspan="2"><p class="help"><?php printf( __( 'For using advanced (manual) rules, have a look at %sthe WordPress Conditional Tags Codex page%s.', 'scroll-triggered-boxes' ), '<a href="https://codex.wordpress.org/Conditional_Tags">', '</a>' ); ?></p></td>
	</tr>
	<tr valign="top">
		<th><label for="stb_position"><?php _e( 'Box Position', 'scroll-triggered-boxes' ); ?></label></th>
		<td>
			<select id="stb_position" name="stb[css][position]" class="widefat">
				<option value="top-left" <?php selected($opts['css']['position'], 'top-left'); ?>><?php _e( 'Top Left', 'scroll-triggered-boxes' ); ?></option>
				<option value="top-center" <?php selected($opts['css']['position'], 'top-center'); ?>><?php _e( 'Top Center', 'scroll-triggered-boxes' ); ?></option>
				<option value="top-right" <?php selected($opts['css']['position'], 'top-right'); ?>><?php _e( 'Top Right', 'scroll-triggered-boxes' ); ?></option>
				<option value="bottom-left" <?php selected($opts['css']['position'], 'bottom-left'); ?>><?php _e( 'Bottom Left', 'scroll-triggered-boxes' ); ?></option>
				<option value="bottom-center" <?php selected($opts['css']['position'], 'bottom-center'); ?>><?php _e( 'Bottom Center', 'scroll-triggered-boxes' ); ?></option>
				<option value="bottom-right" <?php selected($opts['css']['position'], 'bottom-right'); ?>><?php _e( 'Bottom Right', 'scroll-triggered-boxes' ); ?></option>
			</select>
		</td>
		<td colspan="2"></td>
	</tr>
	<tr valign="top">
		<th><label for="stb_trigger"><?php _e( 'Trigger Point', 'scroll-triggered-boxes' ); ?></label></th>
		<td class="stb-sm">
			<select id="stb_trigger" name="stb[trigger]" class="widefat">
				<optgroup label="Basic">
					<option value="percentage" <?php selected($opts['trigger'], 'percentage'); ?>>% <?php _e( 'of page height', 'scroll-triggered-boxes' ); ?></option>
				</optgroup>
				<optgroup label="Advanced">
					<option value="element" <?php selected($opts['trigger'], 'element'); ?>><?php _e( 'Element Selector', 'scroll-triggered-boxes' ); ?></option>
				</optgroup>
			</select>
		</td>
		<td>
			<input type="number" class="stb-trigger-percentage" name="stb[trigger_percentage]" min="0" max="100" value="<?php echo esc_attr($opts['trigger_percentage']); ?>" <?php if($opts['trigger'] != 'percentage') {  echo 'style="display: none;"'; } ?> />
			<input type="text" class="stb-trigger-element widefat" name="stb[trigger_element]" value="<?php echo esc_attr($opts['trigger_element']); ?>" placeholder="<?php _e('Example: #comments (element must exist or box won\'t be shown)', 'scroll-triggered-boxes'); ?>" <?php if($opts['trigger'] != 'element') { echo 'style="display: none;"'; } ?> />
		</td>
	</tr>
	<tr valign="top">
	<th><label><?php _e( 'Animation', 'scroll-triggered-boxes' ); ?></label></th>
		<td colspan="3">
			<label><input type="radio" name="stb[animation]" value="fade" <?php checked($opts['animation'], 'fade'); ?> /> <?php _e( 'Fade In', 'scroll-triggered-boxes' ); ?></label> &nbsp;
			<label><input type="radio" name="stb[animation]" value="slide" <?php checked($opts['animation'], 'slide'); ?> /> <?php _e( 'Slide In', 'scroll-triggered-boxes' ); ?></label>
			<p class="help"><?php _e( 'Which animation type should be used to show the box when triggered?', 'scroll-triggered-boxes' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th><label for="stb_cookie"><?php _e( 'Cookie expiration days', 'scroll-triggered-boxes' ); ?></label></th>
		<td colspan="3">
			<input type="number" id="stb_cookie" name="stb[cookie]" min="0" step="1" value="<?php echo esc_attr($opts['cookie']); ?>" />
			<p class="help"><?php _e( 'After closing the box, how many days should it stay hidden?', 'scroll-triggered-boxes' ); ?></p>
		</td>
		
	</tr>
	<tr valign="top">
		<th><label for="stb_hide_on_screen_size"><?php _e( 'Hide box on small screens?', 'scroll-triggered-boxes' ); ?></label></th>
		<td colspan="3">
			<p><?php printf( __( 'Hide on screens smaller than %s.', 'scroll-triggered-boxes' ), '<input type="number" min="0" name="stb[hide_on_screen_size]" value="' . esc_attr( $opts['hide_on_screen_size'] ) . '" placeholder="'. $opts['css']['width'] .'" style="max-width: 70px;" />px' ); ?></p>
			<p class="help"><?php _e( 'Set to <code>0</code> if you do not want to disable the box on small screens.', 'scroll-triggered-boxes' ); ?></p>
		</td>

	</tr>
	<tr valign="top">
		<th><label for="stb_auto_hide"><?php _e( 'Auto-hide?', 'scroll-triggered-boxes' ); ?></label></th>
		<td colspan="3">
			<label><input type="radio" id="stb_auto_hide_1" name="stb[auto_hide]" value="1" <?php checked($opts['auto_hide'], 1); ?> /> <?php _e( 'Yes' ); ?></label> &nbsp;
			<label><input type="radio" id="stb_auto_hide_0" name="stb[auto_hide]" value="0" <?php checked($opts['auto_hide'], 0); ?> /> <?php _e( 'No' ); ?></label> &nbsp;
			<p class="help"><?php _e( 'Hide box again when visitors scroll back up?', 'scroll-triggered-boxes' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th><label for="stb_test_mode"><?php _e( 'Enable test mode?', 'scroll-triggered-boxes' ); ?></label></th>
		<td colspan="3">
			<label><input type="radio" id="stb_test_mode_1" name="stb[test_mode]" value="1" <?php checked($opts['test_mode'], 1); ?> /> <?php _e( 'Yes' ); ?></label> &nbsp;
			<label><input type="radio" id="stb_test_mode_0" name="stb[test_mode]" value="0" <?php checked($opts['test_mode'], 0); ?> /> <?php _e( 'No' ); ?></label> &nbsp;
			<p class="help"><?php _e( 'If test mode is enabled, the box will show up regardless of whether a cookie has been set.', 'scroll-triggered-boxes' ); ?></p>
		</td>
	</tr>
</table>



<?php wp_nonce_field( 'stb_options', 'stb_options_nonce' ); ?>