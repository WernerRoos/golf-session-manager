<?php
// Shortcode for displaying the user's upcoming appointments and check-in functionality

add_shortcode('golf_checkin_list', 'gsm_checkin_list_shortcode');

function gsm_checkin_list_shortcode() {
    $user_id = get_current_user_id();
    $appointments = gsm_get_today_appointments($user_id);

    ob_start();
    foreach ($appointments as $app) {
        echo "<p>Session at {$app['time']} - Booth {$app['booth']} 
              <button data-id='{$app['id']}' class='checkin-btn'>Check In</button></p>";
    }
    return ob_get_clean();
}

// Shortcode for displaying user's credits and subscription info inside Amelia customer panel
add_shortcode('golf_credits_panel', 'golf_credits_panel_shortcode');

function golf_credits_panel_shortcode() {
    if (!is_user_logged_in()) return '';
    $user_id = get_current_user_id();
    $credits = gsm_get_user_credits($user_id); // Now only in functions.php
    $plan = gsm_get_user_plan($user_id);       // Now only in functions.php

    ob_start();
    ?>
    <div id="golf-credits-panel">
        <h3>Your Subscription</h3>
        <p>Plan: <strong><?php echo esc_html($plan); ?></strong></p>
        <p>Remaining Credits: <strong><?php echo esc_html($credits); ?></strong></p>
    </div>
    <?php
    return ob_get_clean();
}
