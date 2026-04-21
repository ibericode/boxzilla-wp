<?php

use Boxzilla\Boxzilla;

/**
 * @return Boxzilla
 */
function boxzilla()
{
    static $instance;

    if (is_null($instance)) {
        $instance = new Boxzilla();
    }

    return $instance;
}

/**
 * Normalize a relative URL for Boxzilla rule storage and matching.
 *
 * @param string $url_string
 *
 * @return string
 */
function boxzilla_normalize_relative_url($url_string)
{
    $url_string = trim((string) $url_string);
    if ($url_string === '') {
        return '/';
    }

    if (strpos($url_string, '//') === 0) {
        $url_string = 'https:' . $url_string;
    } elseif (preg_match('/^[\w.-]+\.[a-z]{2,}(?:[\/?#]|$)/i', $url_string)) {
        $url_string = 'https://' . $url_string;
    }

    $parts = wp_parse_url($url_string);
    if (! is_array($parts)) {
        return '/';
    }

    $path = isset($parts['path']) ? $parts['path'] : '/';
    $path = '/' . ltrim((string) $path, '/');
    $path = untrailingslashit($path);
    if ($path === '') {
        $path = '/';
    }

    if (empty($parts['query'])) {
        return $path;
    }

    parse_str($parts['query'], $query_args);
    if (empty($query_args)) {
        return $path;
    }

    $tracking_keys = [
        '_ga',
        '_gl',
        'dclid',
        'fbclid',
        'gclid',
        'mc_cid',
        'mc_eid',
        'msclkid',
    ];

    foreach (array_keys($query_args) as $key) {
        if (strpos($key, 'utm_') === 0 || in_array($key, $tracking_keys, true)) {
            unset($query_args[ $key ]);
        }
    }

    if (empty($query_args)) {
        return $path;
    }

    return $path . '?' . build_query($query_args);
}
