<?php 
if( ! defined( 'STB::VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
} ?>
<p><?php _e( 'If you like this plugin, consider supporting it by donating a token of your appreciation.', 'scroll-triggered-boxes' ); ?></p>

<p style="text-align: center">
	<a href="https://dannyvankooten.com/donate/" class="button-primary"><?php _e( 'Donate with PayPal', 'scroll-triggered-boxes' ); ?></a>
</p>

<p><?php _e( 'Some other ways to support this plugin', 'scroll-triggered-boxes' ); ?></p>
<ul class="ul-square">
	<li><a href="https://wordpress.org/support/view/plugin-reviews/scroll-triggered-boxes?rate=5#postform" target="_blank"><?php printf( __( 'Leave a %s review on WordPress.org', 'scroll-triggered-boxes' ), '&#9733;&#9733;&#9733;&#9733;&#9733;' ); ?></a></li>
	<li><a href="https://twitter.com/intent/tweet/?text=<?php echo urlencode('I am using Scroll Triggered Boxes on my WordPress site. It\'s great!'); ?>&via=DannyvanKooten&url=<?php echo urlencode('https://wordpress.org/plugins/scroll-triggered-boxes/'); ?>" target="_blank"><?php _e ('Tweet about Scroll Triggered Boxes', 'scroll-triggered-boxes' ); ?></a></li>
	<li><a href="https://wordpress.org/plugins/scroll-triggered-boxes/#compatibility"><?php printf( __( 'Vote %s on the WordPress.org plugin page', 'scroll-triggered-boxes' ), '"works"' ); ?></a></li>
</ul>