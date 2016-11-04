<?php

defined( 'ABSPATH' ) or exit;

$posts = get_posts( array( 'post_type' => 'boxzilla-box' ) );

if( ! empty( $posts ) ) {
    foreach( $posts as $post ) {
        $settings = get_post_meta( $post->ID, 'boxzilla_options', true );

        if( ! is_array( $settings ) ) {
            continue;
        }

        // translate from days to hours
        $new_value = intval( $settings['cookie'] ) * 24;

        // store in new location
        $settings['cookie'] = array(
            'dismissed' => $new_value
        );
        update_post_meta( $post->ID, 'boxzilla_options', $settings );
    }
}