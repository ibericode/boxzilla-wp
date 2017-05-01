<?php

defined( 'ABSPATH' ) or exit;

add_filter( 'boxzilla_box_content', 'wptexturize') ;
add_filter( 'boxzilla_box_content', 'convert_smilies' );
add_filter( 'boxzilla_box_content', 'convert_chars' );
add_filter( 'boxzilla_box_content', 'wpautop' );
add_filter( 'boxzilla_box_content', 'shortcode_unautop' );
add_filter( 'boxzilla_box_content', 'do_shortcode', 11 );