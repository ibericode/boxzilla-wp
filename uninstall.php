<?php

/**
 * Boxzilla Uninstall
 *
 * Fired when the plugin is deleted (not just deactivated).
 * Cleans up all data stored by the plugin.
 */

// Exit if not called from WordPress uninstall process
defined('WP_UNINSTALL_PLUGIN') or exit;

// 1. Delete all boxzilla-box posts (including revisions and post meta)
$post_ids = get_posts([
    'post_type'      => 'boxzilla-box',
    'post_status'    => 'any',
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'no_found_rows'  => true,
]);

foreach ($post_ids as $post_id) {
    wp_delete_post($post_id, true);
}

// 2. Delete any orphaned post meta (in case wp_delete_post missed some)
global $wpdb;
$wpdb->delete($wpdb->postmeta, ['meta_key' => 'boxzilla_options']);

// 3. Delete options
delete_option('boxzilla_settings');
delete_option('boxzilla_rules');
delete_option('boxzilla_version');
delete_option('boxzilla_license');

// 4. Delete transients
delete_transient('boxzilla_remote_extensions');
delete_transient('boxzilla_request_failed');

// 5. Delete user meta for review notice dismissal
$wpdb->delete($wpdb->usermeta, ['meta_key' => '_boxzilla_review_notice_dismissed']);

// 6. Remove custom capabilities from administrator role
$admins = get_role('administrator');
if ($admins instanceof WP_Role) {
    $admins->remove_cap('edit_box');
    $admins->remove_cap('edit_boxes');
    $admins->remove_cap('edit_other_boxes');
    $admins->remove_cap('publish_boxes');
    $admins->remove_cap('read_box');
    $admins->remove_cap('read_private_box');
    $admins->remove_cap('delete_box');
}

// 7. Unschedule cron event
wp_clear_scheduled_hook('boxzilla_check_license_status');
