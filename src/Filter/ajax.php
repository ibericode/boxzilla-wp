<?php

/**
 * Returns a newline-separated list of posts or pages with titles starting with the supplied 'q' parameter.
 *
 * todo: open up for all post types
 */
function stb_autocomplete() {
	global $wpdb;

	$q = ( isset( $_GET['q'] ) ) ? sanitize_text_field( $_GET['q'] ) : '';
	$post_type = ( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], array( 'page', 'post' ) ) ) ? $_GET['post_type'] : 'post';

	// do nothing if supplied 'q' parameter is omitted or empty
	if( empty( $q ) ) {
		die();
	}

	// query database for posts or pages
	$sql = $wpdb->prepare( "SELECT p.post_name FROM $wpdb->posts p WHERE p.post_type = '%s' AND p.post_status = 'publish' AND ( p.post_title LIKE '%s' OR p.post_name LIKE '%s' ) GROUP BY p.post_name", $post_type, $q . '%%', $q . '%%' );
	$post_slugs = $wpdb->get_col( $sql );
	echo join( $post_slugs, PHP_EOL );
	die();
}

add_action( 'wp_ajax_stb_autocomplete', 'stb_autocomplete' );

// todo: add category autocomplete