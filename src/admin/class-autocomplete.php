<?php

namespace Boxzilla\Filter;

if (! defined('ABSPATH')) {
    exit;
}

class Autocomplete
{
    private const MAX_RESULTS = 20;

    public function init(): void
    {
        add_action('wp_ajax_boxzilla_autocomplete', [ $this, 'ajax' ], 10, 0);
    }

    /**
     * AJAX listener for autocomplete
     */
    public function ajax(): void
    {
        if (! current_user_can('edit_box')) {
            wp_die('', '', [ 'response' => 403 ]);
        }

        $q = isset($_GET['q']) ? sanitize_text_field(wp_unslash($_GET['q'])) : '';
        $allowed_types = [ 'page', 'post', 'category', 'post_type', 'post_tag' ];
        $type = isset($_GET['type']) ? sanitize_key(wp_unslash($_GET['type'])) : '';
        if (! in_array($type, $allowed_types, true)) {
            $type = 'post';
        }

        // do nothing if supplied 'q' parameter is omitted or empty
        // or less than 2 characters long
        if (empty($q) || strlen($q) < 2) {
            wp_die();
        }

        switch ($type) {
            default:
            case 'post':
            case 'page':
                echo esc_html($this->list_posts($q, $type));
                break;

            case 'category':
                echo esc_html($this->list_categories($q));
                break;

            case 'post_type':
                echo esc_html($this->list_post_types($q));
                break;

            case 'post_tag':
                echo esc_html($this->list_tags($q));
                break;
        }

        wp_die();
    }

    /**
     * @param string $query
     * @param string $post_type
     *
     * @return string
     */
    protected function list_posts($query, $post_type = 'post')
    {
        global $wpdb;
        $like = $wpdb->esc_like($query) . '%';
        $limit = self::MAX_RESULTS;

        $post_slugs = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT p.post_name FROM $wpdb->posts p WHERE p.post_type = %s AND p.post_status = 'publish' AND ( p.post_title LIKE %s OR p.post_name LIKE %s ) GROUP BY p.post_name ORDER BY p.post_name ASC LIMIT %d",
                $post_type,
                $like,
                $like,
                $limit
            )
        );
        return join(PHP_EOL, $post_slugs);
    }

    /**
     * @param string $query
     *
     * @return string
     */
    protected function list_categories($query)
    {
        $terms = get_terms([
            'taxonomy' => 'category',
            'name__like' => $query,
            'number'     => self::MAX_RESULTS,
            'fields'     => 'names',
            'hide_empty' => false,
        ]);

        if (is_wp_error($terms)) {
            return '';
        }

        return join(PHP_EOL, $terms);
    }

    /**
     * @param string $query
     *
     * @return string
     */
    protected function list_tags($query)
    {
        $terms = get_terms([
            'taxonomy' => 'post_tag',
            'name__like' => $query,
            'number'     => self::MAX_RESULTS,
            'fields'     => 'names',
            'hide_empty' => false,
        ]);

        if (is_wp_error($terms)) {
            return '';
        }

        return join(PHP_EOL, $terms);
    }


    /**
     * @param string $query
     *
     * @return string
     */
    protected function list_post_types($query)
    {
        $post_types         = get_post_types([ 'public' => true ], 'names');
        $matched_post_types = array_filter(
            $post_types,
            function ($name) use ($query) {
                return strpos($name, $query) === 0;
            }
        );

        $matched_post_types = array_slice($matched_post_types, 0, self::MAX_RESULTS);

        return join(PHP_EOL, $matched_post_types);
    }
}
