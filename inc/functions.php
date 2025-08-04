<?php
function gsm_set_user_credits($user_id, $credits, $plan) {
    global $wpdb;
    $table = $wpdb->prefix . 'golf_session_credits';

    $wpdb->replace($table, [
        'user_id' => $user_id,
        'credit_balance' => $credits,
        'subscription_plan' => $plan,
        'cycle_start' => current_time('mysql'),
        'cycle_end' => date('Y-m-d H:i:s', strtotime('+1 month')),
    ]);
}

function gsm_get_user_credits($user_id) {
    global $wpdb;
    $table = $wpdb->prefix . 'golf_session_credits';

    return (int) $wpdb->get_var($wpdb->prepare("SELECT credit_balance FROM $table WHERE user_id = %d", $user_id));
}

function gsm_update_credits($user_id, $new_balance) {
    global $wpdb;
    $table = $wpdb->prefix . 'golf_session_credits';

    $wpdb->update($table, ['credit_balance' => $new_balance], ['user_id' => $user_id]);
}

function gsm_get_user_plan($user_id) {
    global $wpdb;
    $table = $wpdb->prefix . 'golf_session_credits';

    return $wpdb->get_var($wpdb->prepare("SELECT subscription_plan FROM $table WHERE user_id = %d", $user_id));
}