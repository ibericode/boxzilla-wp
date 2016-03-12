<?php

defined( 'ABSPATH' ) or exit;

/** @var \ScrollTriggeredBoxes\Box $box */
/** @var array $opts */
/** @var array $global_opts */

/** @var array $rule_options */
$rule_options = array(
	'' => __( "Select a condition", 'scroll-triggered-boxes' ),
	'everywhere' => __( 'everywhere', 'scroll-triggered-boxes' ),
	'is_page' => __( 'if page is', 'scroll-triggered-boxes' ),
	'is_single' => __( 'if post is', 'scroll-triggered-boxes' ),
	'is_post_in_category' => __( 'if is post in category', 'scroll-triggered-boxes' ),
	'is_post_type' => __( 'if post type is', 'scroll-triggered-boxes' ),
	'is_url' => __( 'if URL is', 'scroll-triggered-boxes' ),
	'is_referer' => __( 'if referer is', 'scroll-triggered-boxes' ),
);

$box_positions = array(
	'bottom-left' => __( 'Bottom Left', 'scroll-triggered-boxes' ),
	'bottom-right' => __( 'Bottom Right', 'scroll-triggered-boxes' ),
	'center' => __( 'Center', 'scroll-triggered-boxes' ),
	'top-left' => __( 'Top Left', 'scroll-triggered-boxes' ),
	'top-right' => __( 'Top Right', 'scroll-triggered-boxes' ),
);

?>
<table class="form-table">
	<?php
	do_action( 'stb_before_box_option_controls', $box, $opts );

	?>
	<tr>
		<th><?php _e( 'Load this box if', 'scroll-triggered-boxes' ); ?></th>
		<td colspan="3">
			<label>
				<?php _e( 'Request matches', 'scroll-triggered-boxes' ); ?>
				<select name="stb[rules_comparision]">
					<option value="any" <?php selected( $opts['rules_comparision'], 'any' ); ?>><?php _e( 'any', 'scroll-triggered-boxes' ); ?></option>
					<option value="all" <?php selected( $opts['rules_comparision'], 'all' ); ?>><?php _e( 'all', 'scroll-triggered-boxes' ); ?></option>
				</select>
				<?php _e( 'of the following conditions.', 'scroll-triggered-boxes' ); ?>
			</label>
		</td>
	</tr>
	<tbody id="stb-box-rules">
	<?php
	$key = 0;
	foreach( $opts['rules'] as $rule ) { if( ! array_key_exists( 'condition', $rule ) ) { continue; } ?>
		<tr valign="top" class="stb-rule-row stb-rule-row-<?php echo $key; ?>">
			<th style="text-align: right; font-weight: normal;">
				<span class="stb-close stb-remove-rule"><span class="dashicons dashicons-dismiss"></span></span>
			</th>
			<td class="stb-sm">
				<select class="widefat stb-rule-condition" name="stb[rules][<?php echo $key; ?>][condition]">
					<optgroup label="<?php _e( 'Basic', 'scroll-triggered-boxes' ); ?>">
						<?php foreach( $rule_options as $value => $label ) {
							printf( '<option value="%s" %s>%s</option>', $value, selected( $rule['condition'], $value ), $label );
						} ?>
					</optgroup>
					<optgroup label="<?php _e( 'Advanced', 'scroll-triggered-boxes' ); ?>">
						<option value="manual" <?php selected( $rule['condition'], 'manual' ); ?>><?php _e( 'manual conditon', 'scroll-triggered-boxes' ); ?></option>
					</optgroup>
				</select>
			</td>
			<td colspan="2">
				<input class="stb-rule-value widefat" name="stb[rules][<?php echo $key; ?>][value]" type="text" value="<?php echo esc_attr( $rule['value'] ); ?>" placeholder="<?php _e( 'Leave empty for any or enter (comma-separated) names or ID\'s', 'scroll-triggered-boxes' ); ?>" style="<?php if( in_array( $rule['condition'], array( '', 'everywhere' ) ) ) { echo 'display: none;'; } ?>" />
			</td>
		</tr>
	<?php $key++;
	} ?>
	</tbody>
	<tr>
		<th></th>
		<td colspan="3"><button type="button" class="button stb-add-rule"><?php _e( 'Add rule', 'scroll-triggered-boxes' ); ?></button></td>
	</tr>
	<tr class="stb-manual-hint" style="display: none;">
		<td></td><td></td><td colspan="2"><p class="help"><?php printf( __( 'For using advanced (manual) rules, have a look at %sthe WordPress Conditional Tags Codex page%s.', 'scroll-triggered-boxes' ), '<a href="https://codex.wordpress.org/Conditional_Tags">', '</a>' ); ?></p></td>
	</tr>
	<tr valign="top">
		<th><label for="stb_position"><?php _e( 'Box Position', 'scroll-triggered-boxes' ); ?></label></th>
		<td>
			<select id="stb_position" name="stb[css][position]" class="widefat">
				<?php foreach( $box_positions as $value => $label ) {
					printf( '<option value="%s" %s>%s</option>', $value, selected( $opts['css']['position'], $value ), $label );
				} ?>
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
			<?php // todo: build the actual logic behind this ?>
			<label><input type="radio" class="stb-auto-show-trigger" name="stb[trigger]" value="" <?php checked( $opts['trigger'], '' ); ?> /> <?php _e( 'Never', 'scroll-triggered-boxes' ); ?></label><br />
			<label><input type="radio" class="stb-auto-show-trigger" name="stb[trigger]" value="instant" <?php checked( $opts['trigger'], 'instant' ); ?> /> <?php print __( 'Yes, immediately after loading the page.', 'scroll-triggered-boxes' ); ?><input type="number" style="visibility: hidden;" /></label><br />
			<label><input type="radio" class="stb-auto-show-trigger" name="stb[trigger]" value="percentage" <?php checked( $opts['trigger'], 'percentage' ); ?> /> <?php printf( __( 'Yes, when at %s of page height', 'scroll-triggered-boxes' ), '<input type="number" name="stb[trigger_percentage]" min="0" max="100" value="' . esc_attr( $opts['trigger_percentage'] ) . '" />%' ); ?></label><br />
			<label><input type="radio" class="stb-auto-show-trigger" name="stb[trigger]" value="element" <?php checked( $opts['trigger'], 'element' ); ?> /> <?php printf( __( 'Yes, when at element %s', 'scroll-triggered-boxes' ), '<input type="text" name="stb[trigger_element]" value="' . esc_attr( $opts['trigger_element'] ) . '" placeholder="' . __( 'Example: #comments', 'scroll-triggered-boxes') .'" />' ); ?></label><br />
			<?php do_action( 'stb_output_auto_show_trigger_options', $opts ); ?>
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
	<tr valign="top">
		<th><label for="stb_test_mode"><?php _e( 'Enable test mode?', 'scroll-triggered-boxes' ); ?></label></th>
		<td>
			<label><input type="radio" id="stb_test_mode_1" name="stb_global_settings[test_mode]" value="1" <?php checked( $global_opts['test_mode'], 1 ); ?> /> <?php _e( 'Yes' ); ?></label> &nbsp;
			<label><input type="radio" id="stb_test_mode_0" name="stb_global_settings[test_mode]" value="0" <?php checked( $global_opts['test_mode'], 0 ); ?> /> <?php _e( 'No' ); ?></label> &nbsp;
			<p class="help"><?php _e( 'If test mode is enabled, all boxes will show up regardless of whether a cookie has been set.', 'scroll-triggered-boxes' ); ?></p>
		</td>
	</tr>
	<?php do_action( 'stb_after_box_option_controls', $box, $opts ); ?>
	</tbody>
</table>


<script type="text/html" id="tmpl-rule-row-template">
	<tr valign="top" class="stb-rule-row stb-rule-row-{{{data.key}}}">
		<th style="text-align: right; font-weight: normal;">
			<span class="stb-close stb-remove-rule"><span class="dashicons dashicons-dismiss"></span></span>
		</th>
		<td class="stb-sm">
			<select class="widefat stb-rule-condition" name="stb[rules][{{{data.key}}}][condition]">
				<optgroup label="<?php _e( 'Basic', 'scroll-triggered-boxes' ); ?>">
					<?php foreach( $rule_options as $value => $label ) {
						printf( '<option value="%s" %s>%s</option>', $value, '', $label );
					} ?>
				</optgroup>
				<optgroup label="<?php _e( 'Advanced', 'scroll-triggered-boxes' ); ?>">
					<option value="manual"><?php _e( 'manual conditon', 'scroll-triggered-boxes' ); ?></option>
				</optgroup>
			</select>
		</td>
		<td colspan="2">
			<input class="stb-rule-value widefat" name="stb[rules][{{{data.key}}}][value]" type="text" value="" placeholder="<?php _e( 'Leave empty for any or enter (comma-separated) names or ID\'s', 'scroll-triggered-boxes' ); ?>" style="display: none;" />
		</td>
	</tr>
</script>