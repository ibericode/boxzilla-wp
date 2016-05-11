<?php

defined( 'ABSPATH' ) or exit;

/** @var \Boxzilla\Box $box */
/** @var array $opts */
/** @var array $global_opts */

/** @var array $rule_options */
$rule_options = array(
	'' => __( "Select a condition", 'boxzilla' ),
	'everywhere' => __( 'everywhere', 'boxzilla' ),
	'is_page' => __( 'if page', 'boxzilla' ),
	'is_single' => __( 'if post', 'boxzilla' ),
	'is_post_in_category' => __( 'if post category', 'boxzilla' ),
	'is_post_type' => __( 'if post type', 'boxzilla' ),
	'is_url' => __( 'if URL', 'boxzilla' ),
	'is_referer' => __( 'if referer', 'boxzilla' ),
);

$box_positions = array(
	'bottom-left' => __( 'Bottom Left', 'boxzilla' ),
	'bottom-right' => __( 'Bottom Right', 'boxzilla' ),
	'center' => __( 'Center', 'boxzilla' ),
	'top-left' => __( 'Top Left', 'boxzilla' ),
	'top-right' => __( 'Top Right', 'boxzilla' ),
);

?>
<table class="form-table">
	<?php
	do_action( 'boxzilla_before_box_option_controls', $box, $opts );

	?>
	<tr>
		<th><?php _e( 'Load this box if', 'boxzilla' ); ?></th>
		<td>
			<label>
				<?php _e( 'Request matches', 'boxzilla' ); ?>
				<select name="boxzilla_box[rules_comparision]">
					<option value="any" <?php selected( $opts['rules_comparision'], 'any' ); ?>><?php _e( 'any', 'boxzilla' ); ?></option>
					<option value="all" <?php selected( $opts['rules_comparision'], 'all' ); ?>><?php _e( 'all', 'boxzilla' ); ?></option>
				</select>
				<?php _e( 'of the following conditions.', 'boxzilla' ); ?>
			</label>
		</td>
	</tr>
	<tbody id="boxzilla-box-rules">
	<?php
	$key = 0;
	foreach( $opts['rules'] as $rule ) { if( ! array_key_exists( 'condition', $rule ) ) { continue; } ?>
		<tr valign="top" class="boxzilla-rule-row boxzilla-rule-row-<?php echo $key; ?>">
			<th style="text-align: right; font-weight: normal;">
				<span class="boxzilla-close boxzilla-remove-rule"><span class="dashicons dashicons-dismiss"></span></span>
			</th>
			<td>
				<select class="boxzilla-rule-condition" name="boxzilla_box[rules][<?php echo $key; ?>][condition]">
					<?php foreach( $rule_options as $value => $label ) {
						printf( '<option value="%s" %s %s>%s</option>', $value, disabled( $value, '', false ), selected( $rule['condition'], $value ), $label );
					} ?>
				</select>

				<select class="boxzilla-rule-qualifier" name="boxzilla_box[rules][<?php echo $key; ?>][qualifier]">
					<option value="1" <?php selected( ! isset( $rule['qualifier'] ) || $rule['qualifier'] ); ?>><?php _e( 'is', 'boxzilla' ); ?></option>
					<option value="0" <?php selected( isset( $rule['qualifier'] ) && !$rule['qualifier'] ); ?>><?php _e( 'is not', 'boxzilla' ); ?></option>
				</select>

				<input class="boxzilla-rule-value regular-text" name="boxzilla_box[rules][<?php echo $key; ?>][value]" type="text" value="<?php echo esc_attr( $rule['value'] ); ?>" placeholder="<?php _e( 'Leave empty for any or enter (comma-separated) names or ID\'s', 'boxzilla' ); ?>" style="<?php if( in_array( $rule['condition'], array( '', 'everywhere' ) ) ) { echo 'display: none;'; } ?>" />
			</td>
		</tr>
	<?php $key++;
	} ?>
	</tbody>
	<tr>
		<th></th>
		<td><button type="button" class="button boxzilla-add-rule"><?php _e( 'Add rule', 'boxzilla' ); ?></button></td>
	</tr>
	<tr valign="top">
		<th><label for="boxzilla_position"><?php _e( 'Box Position', 'boxzilla' ); ?></label></th>
		<td>
			<select id="boxzilla_position" name="boxzilla_box[css][position]">
				<?php foreach( $box_positions as $value => $label ) {
					printf( '<option value="%s" %s>%s</option>', $value, selected( $opts['css']['position'], $value ), $label );
				} ?>
			</select>
		</td>
	</tr>
	<tr valign="top">
		<th><label><?php _e( 'Animation', 'boxzilla' ); ?></label></th>
		<td>
			<label><input type="radio" name="boxzilla_box[animation]" value="fade" <?php checked($opts['animation'], 'fade'); ?> /> <?php _e( 'Fade In', 'boxzilla' ); ?></label> &nbsp;
			<label><input type="radio" name="boxzilla_box[animation]" value="slide" <?php checked($opts['animation'], 'slide'); ?> /> <?php _e( 'Slide In', 'boxzilla' ); ?></label>
			<p class="help"><?php _e( 'Which animation type should be used to show the box when triggered?', 'boxzilla' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th><label for="boxzilla_trigger"><?php _e( 'Auto-show box?', 'boxzilla' ); ?></label></th>
		<td>
			<label><input type="radio" class="boxzilla-auto-show-trigger" name="boxzilla_box[trigger]" value="" <?php checked( $opts['trigger'], '' ); ?> /> <?php _e( 'Never', 'boxzilla' ); ?></label><br />
			<label><input type="radio" class="boxzilla-auto-show-trigger" name="boxzilla_box[trigger]" value="time_on_page" <?php checked( $opts['trigger'], 'time_on_page' ); ?> /> <?php printf( __( 'Yes, after %s seconds on the page.', 'boxzilla' ), '<input type="number" name="boxzilla_box[trigger_time_on_page]" min="0" value="' . esc_attr( $opts['trigger_time_on_page'] ) . '" />' ); ?></label><br />
			<label><input type="radio" class="boxzilla-auto-show-trigger" name="boxzilla_box[trigger]" value="percentage" <?php checked( $opts['trigger'], 'percentage' ); ?> /> <?php printf( __( 'Yes, when at %s of page height', 'boxzilla' ), '<input type="number" name="boxzilla_box[trigger_percentage]" min="0" max="100" value="' . esc_attr( $opts['trigger_percentage'] ) . '" />%' ); ?></label><br />
			<label><input type="radio" class="boxzilla-auto-show-trigger" name="boxzilla_box[trigger]" value="element" <?php checked( $opts['trigger'], 'element' ); ?> /> <?php printf( __( 'Yes, when at element %s', 'boxzilla' ), '<input type="text" name="boxzilla_box[trigger_element]" value="' . esc_attr( $opts['trigger_element'] ) . '" placeholder="' . __( 'Example: #comments', 'boxzilla') .'" />' ); ?></label><br />
			<?php do_action( 'boxzilla_output_auto_show_trigger_options', $opts ); ?>
		</td>
	</tr>
	<tbody class="boxzilla-trigger-options" style="display: <?php echo ( $opts['trigger'] === '' ) ? 'none' : 'table-row-group'; ?>;">
	<tr valign="top">
		<th><label for="boxzilla_cookie"><?php _e( 'Cookie expiration days', 'boxzilla' ); ?></label></th>
		<td>
			<input type="number" id="boxzilla_cookie" name="boxzilla_box[cookie]" min="0" step="1" value="<?php echo esc_attr($opts['cookie']); ?>" />
			<p class="help"><?php _e( 'After closing the box, how many days should it stay hidden?', 'boxzilla' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th><label for="boxzilla_hide_on_screen_size"><?php _e( 'Do not auto-show box on small screens?', 'boxzilla' ); ?></label></th>
		<td>
			<p><?php printf( __( 'Do not auto-show on screens smaller than %s.', 'boxzilla' ), '<input type="number" min="0" name="boxzilla_box[hide_on_screen_size]" value="' . esc_attr( $opts['hide_on_screen_size'] ) . '" style="max-width: 70px;" />px' ); ?></p>
			<p class="help"><?php _e( 'Leave empty if you <strong>do</strong> want to auto-show the box on small screens.', 'boxzilla' ); ?></p>
		</td>

	</tr>
	<tr valign="top">
		<th><label for="boxzilla_auto_hide"><?php _e( 'Auto-hide?', 'boxzilla' ); ?></label></th>
		<td>
			<label><input type="radio" name="boxzilla_box[auto_hide]" value="1" <?php checked( $opts['auto_hide'], 1 ); ?> /> <?php _e( 'Yes' ); ?></label> &nbsp;
			<label><input type="radio" name="boxzilla_box[auto_hide]" value="0" <?php checked( $opts['auto_hide'], 0 ); ?> /> <?php _e( 'No' ); ?></label> &nbsp;
			<p class="help"><?php _e( 'Hide box again when visitors scroll back up?', 'boxzilla' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th><label for="boxzilla_test_mode"><?php _e( 'Enable test mode?', 'boxzilla' ); ?></label></th>
		<td>
			<label><input type="radio" id="boxzilla_test_mode_1" name="boxzilla_global_settings[test_mode]" value="1" <?php checked( $global_opts['test_mode'], 1 ); ?> /> <?php _e( 'Yes' ); ?></label> &nbsp;
			<label><input type="radio" id="boxzilla_test_mode_0" name="boxzilla_global_settings[test_mode]" value="0" <?php checked( $global_opts['test_mode'], 0 ); ?> /> <?php _e( 'No' ); ?></label> &nbsp;
			<p class="help"><?php _e( 'If test mode is enabled, all boxes will show up regardless of whether a cookie has been set.', 'boxzilla' ); ?></p>
		</td>
	</tr>
	<?php do_action( 'boxzilla_after_box_option_controls', $box, $opts ); ?>
	</tbody>
</table>


<script type="text/html" id="tmpl-rule-row-template">
	<tr valign="top" class="boxzilla-rule-row boxzilla-rule-row-{{{data.key}}}">
		<th style="text-align: right; font-weight: normal;">
			<span class="boxzilla-close boxzilla-remove-rule"><span class="dashicons dashicons-dismiss"></span></span>
		</th>
		<td class="boxzilla-sm">
			<select class="boxzilla-rule-condition" name="boxzilla_box[rules][{{{data.key}}}][condition]">
				<?php foreach( $rule_options as $value => $label ) {
					printf( '<option value="%s" %s %s>%s</option>', $value, disabled( $value, '', false ), '', $label );
				} ?>
			</select>
			<select class="boxzilla-rule-qualifier" name="boxzilla_box[rules][{{{data.key}}}][qualifier]" style="display: none;" >
				<option><?php _e( 'is', 'boxzilla' ); ?></option>
				<option><?php _e( 'is not', 'boxzilla' ); ?></option>
			</select>

			<input class="boxzilla-rule-value regular-text" name="boxzilla_box[rules][{{{data.key}}}][value]" type="text" value="" placeholder="<?php _e( 'Leave empty for any or enter (comma-separated) names or ID\'s', 'boxzilla' ); ?>" style="display: none;" />
		</td>
	</tr>
</script>