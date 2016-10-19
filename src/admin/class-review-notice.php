<?php

namespace Boxzilla\Admin;
use WP_Screen;

/**
 * Class ReviewNotice
 *
 * @ignore
 */
class ReviewNotice {
    
    /**
     * @var string
     */
    protected $meta_key_dismissed = '_boxzilla_review_notice_dismissed';

    /**
     * Constructor.
     */
    public function __construct() {
        
    }

    /**
     * Add action & filter hooks.
     */
    public function add_hooks() {
        add_action( 'admin_notices', array( $this, 'show' ) );
        add_action( 'boxzilla_admin_dismiss_review_notice', array( $this, 'dismiss' ) );
    }

    /**
     * Set flag in user meta so notice won't be shown.
     */
    public function dismiss() {
        $user = wp_get_current_user();
        update_user_meta( $user->ID, $this->meta_key_dismissed, 1 );
    }

    /**
     * @return bool
     */
    public function show() {
        $screen = get_current_screen();
        if( ! $screen instanceof WP_Screen ) {
            return false;
        }

        // on some boxzilla screen?
        if( $screen->post_type !== 'boxzilla-box' ) {
            return false;
        }

        // authorized?
        if( ! current_user_can( 'edit_posts' ) ) {
            return false;
        }

        // only show if 2 weeks have passed since first use.
        $two_weeks_in_seconds = ( 60 * 60 * 24 * 14 );
        if( $this->time_since_first_use() <= $two_weeks_in_seconds ) {
            return false;
        }

        // only show if user did not dismiss before
        $user = wp_get_current_user();
        if( get_user_meta( $user->ID, $this->meta_key_dismissed, true ) ) {
            return false;
        }

        echo '<div class="notice notice-info boxzilla-is-dismissible">';
        echo '<p>';
        echo __( 'You\'ve been using Boxzilla for some time now; we hope you love it!', 'boxzilla' ) . ' <br />';
        echo sprintf( __( 'If you do, please <a href="%s">leave us a 5★ rating on WordPress.org</a>. It would be of great help to us.', 'boxzilla' ), 'https://wordpress.org/support/view/plugin-reviews/boxzilla?rate=5#new-post' );
        echo '</p>';
        echo '<form method="POST"><button type="submit" class="notice-dismiss"><span class="screen-reader-text">'. __( 'Dismiss this notice.', 'boxzilla' ) .'</span></button><input type="hidden" name="_boxzilla_admin_action" value="dismiss_review_notice"/></form>';
        echo '</div>';
        return true;
    }

    /**
     * @return int
     */
    private function time_since_first_use() {
        $options = get_option( 'boxzilla_settings' );

        // option was never added before, do it now.
        if( empty( $options['first_activated_on'] ) ) {
            $options['first_activated_on'] = time();
            update_option( 'boxzilla_settings', $options );
        }

        return time() - $options['first_activated_on'];
    }
}