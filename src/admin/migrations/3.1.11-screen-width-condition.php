<?php

defined( 'ABSPATH' ) or exit;

$posts = get_posts( array( 'post_type' => 'boxzilla-box' ) );

if( ! empty( $posts ) ) {
    foreach( $posts as $post ) {
        $settings = get_post_meta( $post->ID, 'boxzilla_options', true );

        if( ! is_array( $settings ) ) {
            continue;
        }

        if( empty( $settings['hide_on_screen_size'] ) ) {
            continue;
        }

        // set updated option
        $settings['screen_size_condition'] = array(
            'condition' => 'larger',
            'value' => intval( $settings['hide_on_screen_size'] ),
        );

        unset( $settings['hide_on_screen_size'] );

        update_post_meta( $post->ID, 'boxzilla_options', $settings );
    }
}