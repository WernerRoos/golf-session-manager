<?php
/*
Plugin Name: Golf Session Manager
Description: Adds session credit logic, subscription-based booking filtering, and check-in for Amelia Bookings.
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'inc/hooks.php';
require_once plugin_dir_path(__FILE__) . 'inc/functions.php';
require_once plugin_dir_path(__FILE__) . 'inc/admin-panel.php';
require_once plugin_dir_path(__FILE__) . 'inc/shortcode-dashboard.php';

// Activation: Create tables
register_activation_hook(__FILE__, 'gsm_create_tables');