<?php
// This file contains functions that hook into WordPress and WooCommerce actions.

// Activation: Create tables
function gsm_create_tables() {
    global $wpdb;
    $table = $wpdb->prefix . 'golf_session_credits';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT NOT NULL,
        credit_balance INT DEFAULT 0,
        subscription_plan VARCHAR(50),
        cycle_start DATETIME,
        cycle_end DATETIME,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Hook: When a WooCommerce subscription becomes active, assign credits and plan
add_action('woocommerce_subscription_status_active', 'gsm_assign_credits_on_subscription', 10, 1);

function gsm_assign_credits_on_subscription($subscription) {
    $user_id = $subscription->get_user_id();
    $items = $subscription->get_items();
    $associations = get_option('gsm_plan_service_assoc', []);

    foreach ($items as $item) {
        $product_id = $item->get_product_id();
        if (isset($associations[$product_id])) {
            $assoc = $associations[$product_id];
            global $wpdb;
            $table = $wpdb->prefix . 'golf_session_credits';
            $wpdb->replace($table, [
                'user_id' => $user_id,
                'credit_balance' => $assoc['credits'],
                'subscription_plan' => $product_id,
                'cycle_start' => current_time('mysql'),
                'cycle_end' => date('Y-m-d H:i:s', strtotime('+1 month')),
                'booking_time' => $assoc['time']
            ]);
        }
    }
}

// Deduct Credit on Booking
add_action('amelia_booking_created', 'gsm_deduct_credit_on_booking');

function gsm_deduct_credit_on_booking($booking) {
    $user_id = get_current_user_id();
    $current_credits = gsm_get_user_credits($user_id);

    if ($current_credits <= 0) {
        wp_die("No credits left.");
    }

    gsm_update_credits($user_id, $current_credits - 1);
}

// Return Credit on Cancellation
add_action('amelia_cancel_appointment', 'gsm_return_credit_on_cancel');

function gsm_return_credit_on_cancel($booking_id) {
    $user_id = get_current_user_id();
    $current_credits = gsm_get_user_credits($user_id);
    gsm_update_credits($user_id, $current_credits + 1);
}

// Helper: Map WooCommerce product IDs to your internal plan names
function gsm_map_product_to_plan($items) {
    foreach ($items as $item) {
        $product_id = is_object($item) ? $item->get_product_id() : $item['product_id'];
        if ($product_id == 123) return 'Basic';
        if ($product_id == 456) return 'Premium';
    }
    return 'Unknown';
}

// Helper: Define credits per plan
function gsm_plan_to_credits($plan) {
    $plans = [
        'Basic' => 4,
        'Premium' => 10,
    ];
    return isset($plans[$plan]) ? $plans[$plan] : 0;
}

// Helper: Define allowed booking time per plan (in minutes)
function gsm_plan_to_booking_time($plan) {
    $plans = [
        'Basic' => 60,
        'Premium' => 120,
    ];
    return isset($plans[$plan]) ? $plans[$plan] : 0;
}
?>