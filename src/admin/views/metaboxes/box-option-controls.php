<?php

defined('ABSPATH') or exit;

/** @var \Boxzilla\Box $box */
/** @var array $opts */
/** @var array $global_opts */

/** @var array $rule_options */
$rule_options = [
    'everywhere'          => esc_html__('everywhere', 'boxzilla'),
    'is_page'             => esc_html__('if page', 'boxzilla'),
    'is_single'           => esc_html__('if post', 'boxzilla'),
    'is_post_with_tag'    => esc_html__('if post tag', 'boxzilla'),
    'is_post_in_category' => esc_html__('if post category', 'boxzilla'),
    'is_post_type'        => esc_html__('if post type', 'boxzilla'),
    'is_url'              => esc_html__('if URL', 'boxzilla'),
    'is_referer'          => esc_html__('if referer', 'boxzilla'),
    'is_user_logged_in'   => esc_html__('if user', 'boxzilla'),
];

$rule_options = apply_filters('boxzilla_rules_options', $rule_options);
?>
<table class="form-table">
    <?php
    do_action('boxzilla_before_box_option_controls', $box, $opts);
    ?>
    <tr>
        <th><?php esc_html_e('Load this box if', 'boxzilla'); ?></th>
        <td>
            <label>
                <?php esc_html_e('Request matches', 'boxzilla'); ?>
                <select name="boxzilla_box[rules_comparision]" id="boxzilla-rule-comparison">
                    <option value="any" <?php selected($opts['rules_comparision'], 'any'); ?>><?php esc_html_e('any', 'boxzilla'); ?></option>
                    <option value="all" <?php selected($opts['rules_comparision'], 'all'); ?>><?php esc_html_e('all', 'boxzilla'); ?></option>
                </select>
                <?php esc_html_e('of the following conditions.', 'boxzilla'); ?>
            </label>
        </td>
    </tr>
    <tbody id="boxzilla-box-rules">
    <?php
    $key = 0;
    foreach ($opts['rules'] as $rule) {
        // skip invalid looking rules
        if (! isset($rule['condition'])) {
            continue;
        }

        // output row showing "and" or "or" between rules
        if ($key > 0) {
            $or   = esc_html__('or', 'boxzilla');
            $and  = esc_html__('and', 'boxzilla');
            $text = $opts['rules_comparision'] === 'any' ? $or : $and;

            echo '<tr>';
            echo '<th class="boxzilla-no-vpadding"></th>';
            echo '<td class="boxzilla-no-vpadding"><span class="boxzilla-andor boxzilla-muted">', $text, '</span></td>';
            echo '</tr>';
        }
        ?>
        <tr valign="top" class="boxzilla-rule-row boxzilla-rule-row-<?php echo $key; ?>">
            <th style="text-align: right; font-weight: normal;">
                <span class="boxzilla-close boxzilla-remove-rule" title="<?php esc_attr_e('Remove rule', 'boxzilla'); ?>"><span class="dashicons dashicons-dismiss"></span></span>
            </th>
            <td>
                <select class="boxzilla-rule-condition" name="boxzilla_box[rules][<?php echo $key; ?>][condition]">
                    <?php
                    foreach ($rule_options as $value => $label) {
                        $selected = selected($rule['condition'], $value, false);
                        echo "<option value=\"{$value}\" {$selected}>{$label}</option>";
                    }
                    ?>
                </select>

                <select class="boxzilla-rule-qualifier" name="boxzilla_box[rules][<?php echo $key; ?>][qualifier]" style="min-width: 135px;">
                    <option value="1" <?php selected(! isset($rule['qualifier']) || $rule['qualifier']); ?>><?php esc_html_e('is', 'boxzilla'); ?></option>
                    <option value="0" <?php selected(isset($rule['qualifier']) && ! $rule['qualifier']); ?>><?php esc_html_e('is not', 'boxzilla'); ?></option>
                    <option value="contains" <?php selected(isset($rule['qualifier']) && $rule['qualifier'] === 'contains'); ?> style="display: none;"><?php esc_html_e('contains', 'boxzilla'); ?>
                    <option value="not_contains" <?php selected(isset($rule['qualifier']) && $rule['qualifier'] === 'not_contains'); ?> style="display: none;"><?php esc_html_e('does not contain', 'boxzilla'); ?></option>
                </select>

                <input class="boxzilla-rule-value regular-text" name="boxzilla_box[rules][<?php echo $key; ?>][value]" type="text" value="<?php echo esc_attr($rule['value']); ?>" placeholder="<?php esc_attr_e('Leave empty for any or enter (comma-separated) names or ID\'s', 'boxzilla'); ?>" <?php echo in_array($rule['condition'], [ '', 'everywhere' ], true) ? 'style="display: none;"' : ''; ?> />
            </td>
        </tr>
        <?php
        $key++;
    }
    ?>
    </tbody>
    <tr>
        <th></th>
        <td><button type="button" class="button boxzilla-add-rule"><?php esc_html_e('Add another rule', 'boxzilla'); ?></button></td>
    </tr>
    <tr valign="top">
        <th><label for="boxzilla_position"><?php esc_html_e('Box Position', 'boxzilla'); ?></label></th>
        <td>
            <table class="window-positions">
                <tr>
                    <td>
                        <?php
                        $value = 'top-left';
                        $label = esc_html__('Top Left', 'boxzilla');
                        printf('<label><input type="radio" name="boxzilla_box[css][position]" value="%s" %s> &nbsp; %s</label>', $value, checked($opts['css']['position'], $value, false), $label);
                        ?>
                    </td>
                    <td></td>
                    <td>
                        <?php
                        $value = 'top-right';
                        $label = esc_html__('Top Right', 'boxzilla');
                        printf('<label><input type="radio" name="boxzilla_box[css][position]" value="%s" %s> &nbsp; %s</label>', $value, checked($opts['css']['position'], $value, false), $label);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <?php
                        $value = 'center';
                        $label = esc_html__('Center', 'boxzilla');
                        printf('<label><input type="radio" name="boxzilla_box[css][position]" value="%s" %s> &nbsp; %s</label>', $value, checked($opts['css']['position'], $value, false), $label);
                        ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <?php
                        $value = 'bottom-left';
                        $label = esc_html__('Bottom Left', 'boxzilla');
                        printf('<label><input type="radio" name="boxzilla_box[css][position]" value="%s" %s> &nbsp; %s</label>', $value, checked($opts['css']['position'], $value, false), $label);
                        ?>
                    </td>
                    <td></td>
                    <td>
                        <?php
                        $value = 'bottom-right';
                        $label = esc_html__('Bottom Right', 'boxzilla');
                        printf('<label><input type="radio" name="boxzilla_box[css][position]" value="%s" %s> &nbsp; %s</label>', $value, checked($opts['css']['position'], $value, false), $label);
                        ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr valign="top">
        <th><label><?php esc_html_e('Animation', 'boxzilla'); ?></label></th>
        <td>
            <label><input type="radio" name="boxzilla_box[animation]" value="fade" <?php checked($opts['animation'], 'fade'); ?> /> <?php esc_html_e('Fade In', 'boxzilla'); ?></label> &nbsp;
            <label><input type="radio" name="boxzilla_box[animation]" value="slide" <?php checked($opts['animation'], 'slide'); ?> /> <?php esc_html_e('Slide In', 'boxzilla'); ?></label>
            <p class="help"><?php esc_html_e('Which animation type should be used to show the box when triggered?', 'boxzilla'); ?></p>
        </td>
    </tr>
    <tr valign="top">
        <th><label for="boxzilla_trigger"><?php esc_html_e('Auto-show box?', 'boxzilla'); ?></label></th>
        <td>
            <label><input type="radio" class="boxzilla-auto-show-trigger" name="boxzilla_box[trigger]" value="" <?php checked($opts['trigger'], ''); ?> /> <?php esc_html_e('Never', 'boxzilla'); ?></label><br />
            <label><input type="radio" class="boxzilla-auto-show-trigger" name="boxzilla_box[trigger]" value="time_on_page" <?php checked($opts['trigger'], 'time_on_page'); ?> /> <?php printf(esc_html__('Yes, after %s seconds on the page.', 'boxzilla'), '<input type="number" name="boxzilla_box[trigger_time_on_page]" min="0" value="' . esc_attr($opts['trigger_time_on_page']) . '" />'); ?></label><br />
            <label><input type="radio" class="boxzilla-auto-show-trigger" name="boxzilla_box[trigger]" value="percentage" <?php checked($opts['trigger'], 'percentage'); ?> /> <?php printf(esc_html__('Yes, when at %s of page height', 'boxzilla'), '<input type="number" name="boxzilla_box[trigger_percentage]" min="0" max="100" value="' . esc_attr($opts['trigger_percentage']) . '" />%'); ?></label><br />
            <label><input type="radio" class="boxzilla-auto-show-trigger" name="boxzilla_box[trigger]" value="element" <?php checked($opts['trigger'], 'element'); ?> /> <?php printf(esc_html__('Yes, when at element %s', 'boxzilla'), '<input type="text" name="boxzilla_box[trigger_element]" value="' . esc_attr($opts['trigger_element']) . '" placeholder="' . esc_attr__('Example: #comments', 'boxzilla') . '" />'); ?></label><br />
            <?php do_action('boxzilla_output_auto_show_trigger_options', $opts); ?>
        </td>
    </tr>
    <tbody class="boxzilla-trigger-options" style="display: <?php echo ( $opts['trigger'] === '' ) ? 'none' : 'table-row-group'; ?>;">
    <tr valign="top">
        <th><label for="boxzilla_cookie"><?php esc_html_e('Cookie expiration', 'boxzilla'); ?></label></th>
        <td>
            <div style="display: inline-block; margin-right: 20px;">
                <label for="boxzilla_cookie_triggered" style="font-weight: bold; display: block;"><?php esc_html_e('Triggered', 'boxzilla'); ?></label>
                <input type="number" id="boxzilla_cookie_triggered" name="boxzilla_box[cookie][triggered]" min="0" step="1" value="<?php echo esc_attr($opts['cookie']['triggered']); ?>" />
                <small><?php esc_html_e('hours', 'boxzilla'); ?></small>
            </div>
            <div style="display: inline-block;">
                <label for="boxzilla_cookie_dismissed" style="font-weight: bold; display: block;"><?php esc_html_e('Dismissed', 'boxzilla'); ?></label>
                <input type="number" id="boxzilla_cookie_dismissed" name="boxzilla_box[cookie][dismissed]" min="0" step="1" value="<?php echo esc_attr($opts['cookie']['dismissed']); ?>" />
                <small><?php esc_html_e('hours', 'boxzilla'); ?></small>
            </div>
            <br />

            <p class="help"><?php esc_html_e('After this box is triggered or dismissed, how many hours should it stay hidden?', 'boxzilla'); ?></p>
        </td>
    </tr>
    <tr valign="top">
        <th><label><?php esc_html_e('Screen width', 'boxzilla'); ?></label></th>
        <td>
            <?php
            $condition_type   = $opts['screen_size_condition']['condition'];
            $condition_value  = $opts['screen_size_condition']['value'];
            $condition_select = '<select name="boxzilla_box[screen_size_condition][condition]"><option value="larger" ' . ( $condition_type === 'larger' ? 'selected' : '' ) . '>' . esc_html__('larger', 'boxzilla') . '</option><option value="smaller" ' . ( $condition_type === 'smaller' ? 'selected' : '' ) . '>' . esc_html__('smaller', 'boxzilla') . '</option></select>';
            ?>
            <p><?php printf(esc_html__('Only show on screens %1$s than %2$s.', 'boxzilla'), $condition_select, '<input type="number" min="0" name="boxzilla_box[screen_size_condition][value]" value="' . esc_attr($condition_value) . '" style="max-width: 70px;" />px'); ?></p>
            <p class="help"><?php esc_html_e('Leave empty if you want to show the box on all screen sizes.', 'boxzilla'); ?></p>
        </td>

    </tr>
    <?php if (in_array($opts['trigger'], [ 'element', 'percentage' ])) { ?>
    <tr valign="top">
        <th><label for="boxzilla_auto_hide"><?php esc_html_e('Auto-hide?', 'boxzilla'); ?></label></th>
        <td>
            <label><input type="radio" name="boxzilla_box[auto_hide]" value="1" <?php checked($opts['auto_hide'], 1); ?> /> <?php esc_html_e('Yes', 'boxzilla'); ?></label> &nbsp;
            <label><input type="radio" name="boxzilla_box[auto_hide]" value="0" <?php checked($opts['auto_hide'], 0); ?> /> <?php esc_html_e('No', 'boxzilla'); ?></label> &nbsp;
            <p class="help"><?php esc_html_e('Hide box again when visitors scroll back up?', 'boxzilla'); ?></p>
        </td>
    </tr>
    <?php } // end if ?>
    </tbody>
    <tbody>
    <tr valign="top">
        <th><label for="boxzilla_closable"><?php esc_html_e('Show close icon?', 'boxzilla'); ?></label></th>
        <td>
            <label><input type="radio" id="boxzilla_closable_1" name="boxzilla_box[show_close_icon]" value="1" <?php checked($opts['show_close_icon'], 1); ?> /> <?php esc_html_e('Yes', 'boxzilla'); ?></label> &nbsp;
            <label><input type="radio" id="boxzilla_closable_0" name="boxzilla_box[show_close_icon]" value="0" <?php checked($opts['show_close_icon'], 0); ?> /> <?php esc_html_e('No', 'boxzilla'); ?></label> &nbsp;
            <p class="help">
                <?php esc_html_e('If you decide to hide the close icon, make sure to offer an alternative way to close the box.', 'boxzilla'); ?><br />
                <?php esc_html_e('Example: ', 'boxzilla'); ?> <code>[boxzilla-close]No, thanks![/boxzilla-close]</code>
            </p>
        </td>
    </tr>
    <tr valign="top">
        <th><label for="boxzilla_test_mode"><?php esc_html_e('Enable test mode?', 'boxzilla'); ?></label></th>
        <td>
            <label><input type="radio" id="boxzilla_test_mode_1" name="boxzilla_global_settings[test_mode]" value="1" <?php checked($global_opts['test_mode'], 1); ?> /> <?php esc_html_e('Yes', 'boxzilla'); ?></label> &nbsp;
            <label><input type="radio" id="boxzilla_test_mode_0" name="boxzilla_global_settings[test_mode]" value="0" <?php checked($global_opts['test_mode'], 0); ?> /> <?php esc_html_e('No', 'boxzilla'); ?></label> &nbsp;
            <p class="help"><?php esc_html_e('If test mode is enabled, all boxes will show up regardless of whether a cookie has been set.', 'boxzilla'); ?></p>
        </td>
    </tr>
    <?php
    do_action('boxzilla_after_box_option_controls', $box, $opts);
    ?>
    </tbody>
</table>


<script type="text/html" id="tmpl-rule-row-template">
    <tr>
        <th class="boxzilla-no-vpadding"></th>
        <td class="boxzilla-no-vpadding"><span class="boxzilla-andor boxzilla-muted">{{data.andor}}</span></td>
    </tr>
    <tr valign="top" class="boxzilla-rule-row boxzilla-rule-row-{{{data.key}}}">
        <th style="text-align: right; font-weight: normal;">
            <span class="boxzilla-close boxzilla-remove-rule" title="<?php esc_attr_e('Remove rule', 'boxzilla'); ?>"><span class="dashicons dashicons-dismiss"></span></span>
        </th>
        <td class="boxzilla-sm">
            <select class="boxzilla-rule-condition" name="boxzilla_box[rules][{{{data.key}}}][condition]">
                <?php
                foreach ($rule_options as $value => $label) {
                    $disabled = disabled($value, '', false);
                    echo "<option value=\"{$value}\" {$disabled}>{$label}</option>";
                }
                ?>
            </select>
            <select class="boxzilla-rule-qualifier" name="boxzilla_box[rules][{{{data.key}}}][qualifier]" style="display: none; min-width: 135px;" >
                <option value="1" selected><?php esc_html_e('is', 'boxzilla'); ?></option>
                <option value="0"><?php esc_html_e('is not', 'boxzilla'); ?></option>
                <option value="contains" style="display: none;"><?php esc_html_e('contains', 'boxzilla'); ?></option>
                <option value="not_contains" style="display: none;"><?php esc_html_e('does not contain', 'boxzilla'); ?></option>
            </select>

            <input class="boxzilla-rule-value regular-text" name="boxzilla_box[rules][{{{data.key}}}][value]" type="text" value="" placeholder="<?php esc_attr_e('Leave empty for any or enter (comma-separated) names or ID\'s', 'boxzilla'); ?>" style="display: none;" />
        </td>
    </tr>
</script>
