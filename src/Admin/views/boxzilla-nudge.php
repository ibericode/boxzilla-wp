<?php defined( 'ABSPATH' ) or exit;

add_thickbox();
$url = network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=boxzilla&TB_iframe=true&width=600&height=550' );
?>

<div class="notice notice-warning">
    <p style="font-weight: bold;"><a href="https://boxzillaplugin.com/blog/2016/hello-boxzilla/">Scroll Triggered Boxes is now Boxzilla</a>!</p>
    <p>Because of the new name accompanied with some breaking changes, Boxzilla is released as <a class="thickbox" href="<?php echo esc_url( $url ); ?>">a new plugin on the WordPress.org plugin repository</a>.</p>
    <p>While it is highly recommend you start using Boxzilla, you can do so at your own pace. Whenever you're ready to make the switch, please <a href="https://kb.boxzillaplugin.com/updating-from-scroll-triggered-boxes/">read through the upgrade guide</a> for a full list of changes.</p>
</div>