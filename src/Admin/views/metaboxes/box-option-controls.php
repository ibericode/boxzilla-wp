<?php defined( 'ABSPATH' ) or exit; ?>
<table class="form-table">
	<?php
	do_action( 'stb_before_box_option_controls', $box, $opts );

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
				<option value="bottom-left" <?php selected($opts['css']['position'], 'bottom-left'); ?>><?php _e( 'Bottom Left', 'scroll-triggered-boxes' ); ?></option>
				<option value="bottom-right" <?php selected($opts['css']['position'], 'bottom-right'); ?>><?php _e( 'Bottom Right', 'scroll-triggered-boxes' ); ?></option>
				<option value="center" <?php selected($opts['css']['position'], 'center'); ?>><?php _e( 'Center', 'scroll-triggered-boxes' ); ?></option>
				<option value="top-left" <?php selected($opts['css']['position'], 'top-left'); ?>><?php _e( 'Top Left', 'scroll-triggered-boxes' ); ?></option>
				<option value="top-right" <?php selected($opts['css']['position'], 'top-right'); ?>><?php _e( 'Top Right', 'scroll-triggered-boxes' ); ?></option>

			</select>
		</td>
		<td colspan="2"></td>
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
		<th><label for="stb_trigger"><?php _e( 'Auto-show box?', 'scroll-triggered-boxes' ); ?></label></th>
		<td colspan="3">
			<label><input type="radio" class="stb-auto-show-trigger" name="stb[trigger]" value="" <?php checked( $opts['trigger'], '' ); ?> /> <?php _e( 'Never', 'scroll-triggered-boxes' ); ?></label><br />
			<label><input type="radio" class="stb-auto-show-trigger" name="stb[trigger]" value="percentage" <?php checked( $opts['trigger'], 'percentage' ); ?> /> <?php printf( __( 'Yes, when at %s of page height', 'scroll-triggered-boxes' ), '<input type="number" name="stb[trigger_percentage]" min="0" max="100" value="' . esc_attr( $opts['trigger_percentage'] ) . '" />%' ); ?></label><br />
			<label><input type="radio" class="stb-auto-show-trigger" name="stb[trigger]" value="element" <?php checked( $opts['trigger'], 'element' ); ?> /> <?php printf( __( 'Yes, when at element %s', 'scroll-triggered-boxes' ), '<input type="text" name="stb[trigger_element]" value="' . esc_attr( $opts['trigger_element'] ) . '" placeholder="' . __( 'Example: #comments', 'scroll-triggered-boxes') .'" />' ); ?></label>
		</td>
	</tr>
	<tbody class="stb-trigger-options" style="display: <?php echo ( $opts['trigger'] === '' ) ? 'none' : 'table-row-group'; ?>;">
	<tr valign="top">
		<th><label for="stb_cookie"><?php _e( 'Cookie expiration days', 'scroll-triggered-boxes' ); ?></label></th>
		<td colspan="3">
			<input type="number" id="stb_cookie" name="stb[cookie]" min="0" step="1" value="<?php echo esc_attr($opts['cookie']); ?>" />
			<p class="help"><?php _e( 'After closing the box, how many days should it stay hidden?', 'scroll-triggered-boxes' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th><label for="stb_hide_on_screen_size"><?php _e( 'Do not auto-show box on small screens?', 'scroll-triggered-boxes' ); ?></label></th>
		<td colspan="3">
			<p><?php printf( __( 'Do not auto-show on screens smaller than %s.', 'scroll-triggered-boxes' ), '<input type="number" min="0" name="stb[hide_on_screen_size]" value="' . esc_attr( $opts['hide_on_screen_size'] ) . '" placeholder="'. esc_attr( $opts['css']['width'] ) .'" style="max-width: 70px;" />px' ); ?></p>
			<p class="help"><?php _e( 'Set to <code>0</code> if you <strong>do</strong> want to auto-show the box on small screens.', 'scroll-triggered-boxes' ); ?></p>
		</td>

	</tr>
	<tr valign="top">
		<th><label for="stb_auto_hide"><?php _e( 'Auto-hide?', 'scroll-triggered-boxes' ); ?></label></th>
		<td colspan="3">
			<label><input type="radio" name="stb[auto_hide]" value="1" <?php checked( $opts['auto_hide'], 1 ); ?> /> <?php _e( 'Yes' ); ?></label> &nbsp;
			<label><input type="radio" name="stb[auto_hide]" value="0" <?php checked( $opts['auto_hide'], 0 ); ?> /> <?php _e( 'No' ); ?></label> &nbsp;
			<p class="help"><?php _e( 'Hide box again when visitors scroll back up?', 'scroll-triggered-boxes' ); ?></p>
		</td>
	</tr>
	<?php do_action( 'stb_after_box_option_controls', $box, $opts ); ?>
	</tbody>
</table>