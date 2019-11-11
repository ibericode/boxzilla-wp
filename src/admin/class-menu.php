<?php

namespace Boxzilla\Admin;

use Boxzilla\Plugin;
use Boxzilla\Box;
use Boxzilla\Boxzilla;

class Menu {

	public function init() {
		add_action( 'admin_head-nav-menus.php', array( $this, 'add_nav_menu_meta_boxes' ) );

		// Include custom items to customizer nav menu settings.
		add_filter( 'customize_nav_menu_available_item_types', array( $this, 'register_customize_nav_menu_item_types' ) );
		add_filter( 'customize_nav_menu_available_items', array( $this, 'register_customize_nav_menu_items' ), 10, 4 );
	}

	/**
	 * Add custom nav meta box.
	 *
	 * Adapted from http://www.johnmorrisonline.com/how-to-add-a-fully-functional-custom-meta-box-to-wordpress-navigation-menus/.
	 */
	public function add_nav_menu_meta_boxes() {
		add_meta_box( 'boxzilla_nav_link', __( 'Boxzilla Pop-ups', 'boxzilla' ), array( $this, 'nav_menu_links' ), 'nav-menus', 'side', 'low' );
	}

	private function get_boxes() {
		$q     = new \WP_Query;
		$posts = $q->query(
			array(
				'post_type'           => 'boxzilla-box',
				'post_status'         => 'publish',
				'posts_per_page'      => -1,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
		return $posts;
	}

	/**
	 * Output menu links.
	 */
	public function nav_menu_links() {
		$posts = $this->get_boxes();

		?>
		<div id="posttype-boxzilla-boxes" class="posttypediv">
			<div id="tabs-panel-boxzilla-boxes" class="tabs-panel tabs-panel-active">
				<ul id="boxzilla-boxes-checklist" class="categorychecklist form-no-clear">
					<?php
					$i = -1;
					foreach ( $posts as $key => $post ) :
						?>
						<li>
							<label class="menu-item-title">
								<input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-object-id]" value="<?php echo esc_attr( $i ); ?>" /> <?php echo esc_html( $post->post_title ); ?>
							</label>
							<input type="hidden" class="menu-item-type" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-type]" value="custom" />
							<input type="hidden" class="menu-item-title" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-title]" value="<?php echo esc_html( $post->post_title ); ?>" />
							<input type="hidden" class="menu-item-url" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-url]" value="<?php echo sprintf( '#boxzilla-%d', $post->ID ); ?>" />
							<input type="hidden" class="menu-item-classes" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-classes]" />
						</li>
						<?php
						$i--;
					endforeach;
					?>
				</ul>
			</div>
			<p class="button-controls">
				<span class="list-controls">
				</span>
				<span class="add-to-menu">
					<button type="submit" class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to menu', 'boxzilla' ); ?>" name="add-post-type-menu-item" id="submit-posttype-boxzilla-boxes"><?php esc_html_e( 'Add to menu', 'boxzilla' ); ?></button>
					<span class="spinner"></span>
				</span>
			</p>
		</div>
		<?php
	}

	/**
	 * Register customize new nav menu item types.
	 *
	 * @since  3.1.0
	 * @param  array $item_types Menu item types.
	 * @return array
	 */
	public function register_customize_nav_menu_item_types( $item_types ) {
		$item_types[] = array(
			'title'      => __( 'Boxzilla Pop-ups', 'boxzilla' ),
			'type_label' => __( 'Boxzilla Pop-ups', 'boxzilla' ),
			'type'       => 'boxzilla_nav',
			'object'     => 'boxzilla_box',
		);

		return $item_types;
	}

	/**
	 * Register account endpoints to customize nav menu items.
	 *
	 * @since  3.1.0
	 * @param  array   $items  List of nav menu items.
	 * @param  string  $type   Nav menu type.
	 * @param  string  $object Nav menu object.
	 * @param  integer $page   Page number.
	 * @return array
	 */
	public function register_customize_nav_menu_items( $items = array(), $type = '', $object = '', $page = 0 ) {
		if ( 'boxzilla_box' !== $object ) {
			return $items;
		}

		// Don't allow pagination since all items are loaded at once.
		if ( 0 < $page ) {
			return $items;
		}

		$boxes = $this->get_boxes();
		foreach ( $boxes as $i => $post ) {
			$items[] = array(
				'id'         => $i,
				'title'      => $post->post_title,
				'type_label' => __( 'Custom Link', 'boxzilla' ),
				'url'        => sprintf( '#boxzilla-%d', $post->ID ),
			);
		}

		return $items;
	}
}
