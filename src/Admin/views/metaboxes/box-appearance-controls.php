<?php defined( 'ABSPATH' ) or exit; ?>
<div id="notice-notinymce" class="error" style="display: none;"><p><?php _e( 'For the best experience when styling your box, please use the default WordPress visual editor.', 'boxzilla' ); ?></p></div>

<table class="form-table">
	<?php do_action( 'boxzilla_before_box_appearance_controls', $box, $opts ); ?>
	<tr valign="top">
		<td>
			<label class="boxzilla-label" for="boxzilla-background-color"><?php _e( 'Background color', 'boxzilla' ); ?></label>
			<input id="boxzilla-background-color" name="boxzilla_box[css][background_color]" type="text" class="boxzilla-color-field" value="<?php echo esc_attr($opts['css']['background_color']); ?>" />
		</td>
		<td>
			<label class="boxzilla-label" for="boxzilla-color"><?php _e( 'Text color', 'boxzilla' ); ?></label>
			<input id="boxzilla-color" name="boxzilla_box[css][color]" type="text" class="boxzilla-color-field" value="<?php echo esc_attr($opts['css']['color']); ?>" />
		</td>
		<td>
			<label class="boxzilla-label" for="boxzilla-width"><?php _e( 'Box width', 'boxzilla' ); ?></label>
			<input id="boxzilla-width" name="boxzilla_box[css][width]" id="boxzilla-box-width" min="0" max="3200" type="number" step="1" value="<?php echo esc_attr($opts['css']['width']); ?>" />
			<p class="help"><?php _e( 'Width in px', 'boxzilla' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<td>
			<label class="boxzilla-label" for="boxzilla-border-color"><?php _e( 'Border color', 'boxzilla' ); ?></label>
			<input name="boxzilla_box[css][border_color]" id="boxzilla-border-color" type="text" class="boxzilla-color-field" value="<?php echo esc_attr($opts['css']['border_color']); ?>" />
		</td>
		<td>
			<label class="boxzilla-label" for="boxzilla-border-width"><?php _e( 'Border width', 'boxzilla' ); ?></label>
			<input name="boxzilla_box[css][border_width]" id="boxzilla-border-width" type="number" min="0" max="25" step="1" value="<?php echo esc_attr($opts['css']['border_width']); ?>" />
			<p class="help"><?php _e( 'Width in px', 'boxzilla' ); ?></p>
		</td>
		<td>
			<label class="boxzilla-label" for="boxzilla-border-style"><?php _e( 'Border style', 'boxzilla' ); ?></label>
			<select name="boxzilla_box[css][border_style]" id="boxzilla-border-style">
				<option value="" <?php selected( $opts['css']['border_style'], '' ); ?>><?php _e( 'Default', 'boxzilla-theme-pack' ); ?></option>
				<option value="solid" <?php selected( $opts['css']['border_style'], 'solid' ); ?>><?php _e( 'Solid', 'boxzilla-theme-pack' ); ?></option>
				<option value="dashed" <?php selected( $opts['css']['border_style'], 'dashed' ); ?>><?php _e( 'Dashed', 'boxzilla-theme-pack' ); ?></option>
				<option value="dotted" <?php selected( $opts['css']['border_style'], 'dotted' ); ?>><?php _e( 'Dotted', 'boxzilla-theme-pack' ); ?></option>
				<option value="double" <?php selected( $opts['css']['border_style'], 'double' ); ?>><?php _e( 'Double', 'boxzilla-theme-pack' ); ?></option>
			</select>
			<p class="help"><?php _e( 'Border style', 'boxzilla' ); ?></p>
		</td>
	</tr>
	<?php do_action( 'boxzilla_after_box_appearance_controls', $box, $opts ); ?>
</table>

<p><?php printf( __( '<a href="%s">Click here to reset all styling settings</a>.', 'boxzilla' ), 'javascript:Boxzilla_Admin.Designer.resetStyles();' ); ?></p>