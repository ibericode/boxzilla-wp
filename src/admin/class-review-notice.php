<?php

namespace Boxzilla\Admin;

use WP_Screen;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class ReviewNotice
 *
 * @ignore
 */
class ReviewNotice
{
    /**
     * @var string
     */
    protected $meta_key_dismissed = '_boxzilla_review_notice_dismissed';

    /**
     * Add action & filter hooks.
     */
    public function init()
    {
        add_action('admin_init', [ $this, 'listen_for_dismiss' ]);
        add_action('admin_notices', [ $this, 'show' ]);
    }

    /**
     * Listen for notice dismissal request.
     */
    public function listen_for_dismiss()
    {
        if (empty($_POST['_boxzilla_review_notice_action'])) {
            return;
        }

        $action = sanitize_key(wp_unslash($_POST['_boxzilla_review_notice_action']));
        if ($action !== 'dismiss_review_notice') {
            return;
        }

        if (! current_user_can('edit_posts')) {
            return;
        }

        if (empty($_POST['_boxzilla_review_notice_nonce'])) {
            return;
        }

        $nonce = sanitize_text_field(wp_unslash($_POST['_boxzilla_review_notice_nonce']));
        if (! wp_verify_nonce($nonce, 'boxzilla_dismiss_review_notice')) {
            return;
        }

        $this->dismiss();
    }

    /**
     * Set flag in user meta so notice won't be shown.
     */
    public function dismiss()
    {
        $user = wp_get_current_user();
        update_user_meta($user->ID, $this->meta_key_dismissed, 1);
    }

    public function show()
    {
        $screen = get_current_screen();
        if (! $screen instanceof WP_Screen) {
            return;
        }

        // on some boxzilla screen?
        if ($screen->post_type !== 'boxzilla-box') {
            return;
        }

        // authorized?
        if (! current_user_can('edit_posts')) {
            return;
        }

        // only show if 2 weeks have passed since first use.
        $two_weeks_in_seconds = 60 * 60 * 24 * 14;
        if ($this->time_since_first_use() <= $two_weeks_in_seconds) {
            return;
        }

        // only show if user did not dismiss before
        $user = wp_get_current_user();
        if (get_user_meta($user->ID, $this->meta_key_dismissed, true)) {
            return;
        }

        echo '<div class="notice notice-info boxzilla-is-dismissible">';
        echo '<p>';
        echo '<strong>';
        echo esc_html__('Has Boxzilla been helpful on your site?', 'boxzilla'), ' <br />';
        echo '</strong>';
        echo esc_html__('Could you spare 60 seconds to leave a quick, honest review on WordPress.org?', 'boxzilla'), ' ';
        echo '<a href="https://wordpress.org/support/plugin/boxzilla/reviews/?rate=5#new-post">', esc_html__('Leave a quick review', 'boxzilla'), '</a><br />';
        echo esc_html__('Your review helps other site owners discover Boxzilla and helps us keep improving it.', 'boxzilla');
        echo '</p>';
        echo '<form method="post"><button type="submit" class="notice-dismiss"><span class="screen-reader-text">', esc_html__('Dismiss this notice.', 'boxzilla'), '</span></button><input type="hidden" name="_boxzilla_review_notice_action" value="dismiss_review_notice"/>';
        wp_nonce_field('boxzilla_dismiss_review_notice', '_boxzilla_review_notice_nonce', false);
        echo '</form>';
        echo '</div>';
    }

    /**
     * @return int
     */
    private function time_since_first_use()
    {
        $options = get_option('boxzilla_settings');

        // option was never added before, do it now.
        if (empty($options['first_activated_on'])) {
            $options['first_activated_on'] = time();
            update_option('boxzilla_settings', $options);
        }

        return time() - $options['first_activated_on'];
    }
}
