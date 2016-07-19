<?php
defined( 'ABSPATH' ) or exit;
$user = wp_get_current_user(); ?>
<form action="//dannyvankooten.us1.list-manage.com/subscribe/post?u=a2d08947dcd3683512ce174c5&amp;id=e3e1e0f8d8" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
	<p>Get the most out of the Boxzilla plugin by subscribing to our newsletter filled with tips & tricks.</p>

	<p class="mc-field-group">
		<label for="mce-EMAIL" style="margin-bottom: 0;">Email Address &nbsp;<span style="color: red;">*</span></label>
		<input type="email" value="<?php echo esc_attr( $user->user_email ); ?>" name="EMAIL" class="widefat" id="mce-EMAIL">
	</p>
	<p class="mc-field-group">
		<label for="mce-FNAME" style="margin-bottom: 0;">First Name &nbsp;<small style="font-weight: normal;">(optional)</small></label>
		<input type="text" value="<?php echo esc_attr( $user->first_name ); ?>" name="FNAME" class="widefat" id="mce-FNAME">
	</p>
	<div id="mce-responses" class="clear">
		<div class="response" id="mce-error-response" style="display:none"></div>
		<div class="response" id="mce-success-response" style="display:none"></div>
	</div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
	<div style="position: absolute; left: -5000px;"><input type="text" name="b_a2d08947dcd3683512ce174c5_e3e1e0f8d8" tabindex="-1" value=""></div>
	<div class="clear"><input type="submit" value="Subscribe" name="subscribe" class="button button-primary"></div>

	<p class="help" style="margin-bottom: 0;"><small>No spam, unsubscribe at any time, max once a month.</small></p>
</form>