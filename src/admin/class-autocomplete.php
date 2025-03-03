<?php

namespace Boxzilla\Filter;

class Autocomplete
{
    public function init()
    {
        add_action('wp_ajax_boxzilla_autocomplete', [ $this, 'ajax' ]);
    }

    /**
     * AJAX listener for autocomplete
     */
    public function ajax()
    {
        $q    = ( isset($_GET['q']) ) ? sanitize_text_field($_GET['q']) : '';
        $type = ( isset($_GET['type']) && in_array($_GET['type'], [ 'page', 'post', 'category', 'post_type', 'post_tag' ], true) ) ? $_GET['type'] : 'post';

        // do nothing if supplied 'q' parameter is omitted or empty
        // or less than 2 characters long
        if (empty($q) || strlen($q) < 2) {
            die();
        }

        switch ($type) {
            default:
            case 'post':
            case 'page':
                echo $this->list_posts($q, $type);
                break;

            case 'category':
                echo $this->list_categories($q);
                break;

            case 'post_type':
                echo $this->list_post_types($q);
                break;

            case 'post_tag':
                echo $this->list_tags($q);
                break;
        }

        die();
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
        $sql        = $wpdb->prepare("SELECT p.post_name FROM $wpdb->posts p WHERE p.post_type = %s AND p.post_status = 'publish' AND ( p.post_title LIKE %s OR p.post_name LIKE %s ) GROUP BY p.post_name", $post_type, $query . '%%', $query . '%%');
        $post_slugs = $wpdb->get_col($sql);
        return join(PHP_EOL, $post_slugs);
    }

    /**
     * @param string $query
     *
     * @return string
     */
    protected function list_categories($query)
    {
        $terms = get_terms(
            'category',
            [
                'name__like' => $query,
                'fields'     => 'names',
                'hide_empty' => false,
            ]
        );
        return join(PHP_EOL, $terms);
    }

    /**
     * @param string $query
     *
     * @return string
     */
    protected function list_tags($query)
    {
        $terms = get_terms(
            'post_tag',
            [
                'name__like' => $query,
                'fields'     => 'names',
                'hide_empty' => false,
            ]
        );
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

        return join(PHP_EOL, $matched_post_types);
    }
}
