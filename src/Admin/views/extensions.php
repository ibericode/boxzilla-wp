<?php defined( 'ABSPATH' ) or exit; ?>
<div class="wrap" id="stb-admin" class="stb-extensions">

	<h2><?php _e( 'Available Add-On Plugins', 'scroll-triggered-boxes' ); ?></h2>
	<p>
		<?php _e( "There are various add-ons available for Scroll Triggered Boxes which further enhance the functionality of the core plugin.", 'scroll-triggered-boxes' ); ?>
	</p>
	<p>
		<?php printf( __( 'To gain instant access the premium add-on plugins listed here, <a href="%s">have a look at the available premium plans</a>.', 'scroll-triggered-boxes' ), 'https://scrolltriggeredboxes.com/pricing#utm_source=wp-plugin&utm_medium=scroll-triggered-boxes&utm_campaign=extensions-page' ); ?>
	</p>

	<?php if( empty( $extensions ) ) : ?>
		<script>
			window.setTimeout( function() {
				window.location.href = 'https://scrolltriggeredboxes.com/plugins#utm_source=wp-plugin&utm_medium=scroll-triggered-boxes&utm_campaign=extensions-page';
			}, 2000 );
		</script>
		<p><?php _e( 'You will be redirected to the Scroll Triggered Boxes site in a few seconds..', 'scroll-triggered-boxes' ); ?></p>
		<p><?php printf( __( 'If not, please click here: %s.', 'scroll-triggered-boxes' ), '<a href="https://scrolltriggeredboxes.com/plugins#utm_source=wp-plugin&utm_medium=scroll-triggered-boxes&utm_campaign=extensions-page" target="_blank">View add-on plugins</a>' ); ?></p>
	<?php else : ?>

		<?php foreach( $extensions as $plugin ) : ?>

		<div class="plugin">
			<a href="<?php echo esc_url( $plugin->page_url ); ?>" class="unstyled"><img src="<?php echo esc_url( $plugin->image_url ); ?>" alt="<?php echo $plugin->name; ?>" width="280" height="220"></a>
			<div class="caption">
				<h3><a href="<?php echo esc_url( $plugin->page_url ); ?>" class="unstyled"><?php echo $plugin->name; ?></a></h3>
				<p><?php echo esc_html( $plugin->short_description ); ?></p>
				<p>
					<a class="button" href="<?php echo esc_url( $plugin->page_url ); ?>" title="More about <?php echo esc_attr( $plugin->name ); ?>">Read More</a>
					<span class="type"><?php echo esc_html( $plugin->type ); ?></span>
				</p>
			</div>
		</div>

		<?php endforeach; ?>

		<br style="clear: both;" />

	<?php endif; ?>
</div>

<style type="text/css">
	.plugin {
		width: 280px;
		border: 1px solid #ccc;
		margin: 0 20px 20px 0;
		float: left;
	}

	.plugin .caption {
		padding: 0 20px;
	}

	.plugin .type {
		float: right;
		text-transform: uppercase;
		font-weight: bold;
	}
</style>