<?php defined( 'ABSPATH' ) or exit; ?>
<div id="notice-notinymce" class="error" style="display: none;"><p><?php _e( 'For the best experience when styling your box, please use the default WordPress visual editor.', 'scroll-triggered-boxes' ); ?></p></div>

<table class="form-table">
	<?php do_action( 'stb_before_box_appearance_controls', $box, $opts ); ?>
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
			<input id="stb-width" name="stb[css][width]" id="stb-box-width" min="0" max="3200" type="number" step="1" value="<?php echo esc_attr($opts['css']['width']); ?>" />
			<p class="help"><?php _e( 'Width in px', 'scroll-triggered-boxes' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<td>
			<label class="stb-label" for="stb-border-color"><?php _e( 'Border color', 'scroll-triggered-boxes' ); ?></label>
			<input name="stb[css][border_color]" id="stb-border-color" type="text" class="stb-color-field" value="<?php echo esc_attr($opts['css']['border_color']); ?>" />
		</td>
		<td>
			<label class="stb-label" for="stb-border-width"><?php _e( 'Border width', 'scroll-triggered-boxes' ); ?></label>
			<input name="stb[css][border_width]" id="stb-border-width" type="number" min="0" max="25" step="1" value="<?php echo esc_attr($opts['css']['border_width']); ?>" />
			<p class="help"><?php _e( 'Width in px', 'scroll-triggered-boxes' ); ?></p>
		</td>
		<td>
			<label class="stb-label" for="stb-border-style"><?php _e( 'Border style', 'scroll-triggered-boxes' ); ?></label>
			<select name="stb[css][border_style]" id="stb-border-style">
				<option value="" <?php selected( $opts['css']['border_style'], '' ); ?>><?php _e( 'Default', 'stb-theme-pack' ); ?></option>
				<option value="solid" <?php selected( $opts['css']['border_style'], 'solid' ); ?>><?php _e( 'Solid', 'stb-theme-pack' ); ?></option>
				<option value="dashed" <?php selected( $opts['css']['border_style'], 'dashed' ); ?>><?php _e( 'Dashed', 'stb-theme-pack' ); ?></option>
				<option value="dotted" <?php selected( $opts['css']['border_style'], 'dotted' ); ?>><?php _e( 'Dotted', 'stb-theme-pack' ); ?></option>
				<option value="double" <?php selected( $opts['css']['border_style'], 'double' ); ?>><?php _e( 'Double', 'stb-theme-pack' ); ?></option>
			</select>
			<p class="help"><?php _e( 'Border style', 'scroll-triggered-boxes' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<td colspan="3">
			<label class="stb-label"><?php _e( 'Manual CSS', 'scroll-triggered-boxes' ); ?></label>
			<textarea id="stb-manual-css" name="stb[css][manual]" class="widefat" rows="5" placeholder=".stb-<?php echo $box->ID; ?> { ... }"><?php echo esc_textarea( $opts['css']['manual'] ); ?></textarea>
		</td>
	</tr>
	<?php do_action( 'stb_after_box_appearance_controls', $box, $opts ); ?>
</table>

<p><?php printf( __( '<a href="%s">Click here to reset all styling settings</a>.', 'scroll-triggered-boxes' ), 'javascript:STB_Admin.Designer.resetStyles();' ); ?></p>