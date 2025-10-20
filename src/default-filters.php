<?php

use Automattic\Jetpack\Image_CDN\Image_CDN;

defined('ABSPATH') or exit;

add_filter('boxzilla_box_content', 'wptexturize');
add_filter('boxzilla_box_content', 'convert_smilies');
add_filter('boxzilla_box_content', 'convert_chars');
add_filter('boxzilla_box_content', 'wpautop');
add_filter('boxzilla_box_content', 'shortcode_unautop');
add_filter('boxzilla_box_content', 'do_shortcode', 11);

/**
* Allow Jetpack to filter images inside Boxzilla box content.
*/
if (class_exists(Image_CDN::class) && method_exists(Image_CDN::class, 'filter_the_content')) {
    add_filter('boxzilla_box_content', [ Image_CDN::class, 'filter_the_content' ], 999999);
}


/**
 * Filter nav menu items to use an onclick event instead of a href attribute.
 */
add_filter(
    'nav_menu_link_attributes',
    function ($atts) {
        if (isset($atts['href']) && strpos($atts['href'], '#boxzilla-') !== 0) {
            return $atts;
        }

        $id              = substr($atts['href'], strlen('#boxzilla-'));
        $atts['onclick'] = "Boxzilla.show({$id}); return false;";
        $atts['href']    = '';
        return $atts;
    },
    10,
    1
);
