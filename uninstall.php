<?php
if (!defined('ABSPATH')) exit;

function gsm_uninstall() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'golf_session_credits';

    // Drop the custom table
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

register_uninstall_hook(__FILE__, 'gsm_uninstall');
