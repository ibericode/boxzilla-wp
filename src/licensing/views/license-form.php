<?php
// prevent direct file access
defined('ABSPATH') or exit;

/** @var Boxzilla\Licensing\License $license */
?>

<h2><?php esc_html_e('License & Plugin Updates', 'boxzilla'); ?></h2>

<?php
if (! $license->activated) {
    ?>
    <div class="error inline">
        <p>
            <strong><?php esc_html_e('Warning! You are not receiving plugin updates for the following plugin(s):', 'boxzilla'); ?></strong>
        </p>
        <ul class="ul-square">
            <?php
            foreach ($this->extensions as $p) {
                echo '<li>', esc_html($p->name()), '</li>';
            }
            ?>
        </ul>
        <p>
            <?php esc_html_e('To fix this, please activate your license using the form below.', 'boxzilla'); ?>
        </p>
    </div>
    <?php
}
?>

<?php
foreach ($this->notices as $notice) {
    ?>
    <div class="notice notice-<?php echo $notice['type']; ?> inline">
        <p><?php echo $notice['message']; ?></p>
    </div>
    <?php
}
?>

<form method="post">
    <table class="form-table">
        <tr valign="top">
            <th><?php esc_html_e('License Key', 'boxzilla'); ?></th>
            <td>
                <input
                    size="40"
                    name="boxzilla_license_key"
                    placeholder="<?php esc_attr_e('Enter your license key..', 'boxzilla'); ?>"
                    value="<?php echo esc_attr($this->license->key); ?>"
                    <?php if ($this->license->activated) {
                        echo 'readonly';
                    } ?>
                />
                <input class="button" type="submit" name="action" value="<?php echo ( $this->license->activated ) ? 'deactivate' : 'activate'; ?>" />
                <p class="help">
                    <?php
                    esc_html_e('The license key received when purchasing your premium Boxzilla plan.', 'bozilla');
                    echo '<a href="https://my.boxzillaplugin.com/">', esc_html__('You can find it here.', 'boxzilla'), '</a>';
                    ?>
                </p>
            </td>
        </tr>
        <tr valign="top">
            <th><?php esc_html_e('License Status', 'boxzilla'); ?></th>
            <td>
                <?php
                if ($license->activated) {
                    ?>
                    <p><span class="status positive"><?php esc_html_e('ACTIVE', 'boxzilla'); ?></span> - <?php esc_html_e('you are receiving plugin updates', 'boxzilla'); ?></p>
                    <?php
                } else {
                    ?>
                    <p><span class="status negative"><?php esc_html_e('INACTIVE', 'boxzilla'); ?></span> - <?php echo wp_kses(__('you are <strong>not</strong> receiving plugin updates', 'boxzilla'), [ 'strong' => [] ]); ?></p>
                    <?php
                }
                ?>
            </td>
        </tr>
    </table>




    <p>
        <input type="submit" class="button button-primary" name="action" value="<?php esc_attr_e('Save Changes', 'boxzilla'); ?>" />
    </p>

    <input type="hidden" name="boxzilla_license_form" value="1" />
</form>
