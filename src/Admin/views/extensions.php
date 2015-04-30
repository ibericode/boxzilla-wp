<?php defined( 'ABSPATH' ) or exit; ?>
<div class="wrap" id="stb-admin" class="stb-extensions">

	<?php if( empty( $extensions ) ) : ?>
		<script>
			window.setTimeout( function() {
				//window.location.href = 'https://scrolltriggeredboxes.com/plugins';
			}, 2000 );
		</script>
		<p><?php _e( 'You will be redirected to the Scroll Triggered Boxes site in a few seconds..', 'scroll-triggered-boxes' ); ?></p>
		<p><?php printf( __( 'If not, please click here: %s.', 'scroll-triggered-boxes' ), '<a href="https://scrolltriggeredboxes.com/plugins" target="_blank">View add-on plugins</a>' ); ?></p>
	<?php else : ?>

		<h3><?php _e( 'Available add-on plugins', 'scroll-triggered-boxes' ); ?></h3>
		<?php foreach( $extensions as $plugin ) : ?>

		<div class="plugin">
			<img src="<?php echo esc_url( $plugin->image_url ); ?>" alt="<?php echo $plugin->name; ?>">
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