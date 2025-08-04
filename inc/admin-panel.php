<?php
/*
Admin Panel for Golf Session Manager
This file contains the functionality for the admin panel to manage and view user credit usage.
*/

if (!defined('ABSPATH')) exit;

function gsm_admin_menu() {
    add_menu_page(
        'Golf Session Manager',
        'Golf Session Manager',
        'manage_options',
        'golf-session-manager',
        'gsm_admin_page',
        'dashicons-admin-generic',
        6
    );
}

add_action('admin_menu', 'gsm_admin_menu');

function gsm_admin_page() {
    global $wpdb;

    // Handle credit update
    if (isset($_POST['edit_credits']) && current_user_can('manage_options')) {
        $edit_user_id = intval($_POST['edit_user_id']);
        $new_credits = intval($_POST['new_credits']);
        $table = $wpdb->prefix . 'golf_session_credits';
        $wpdb->update(
            $table,
            ['credit_balance' => $new_credits],
            ['user_id' => $edit_user_id]
        );
        echo "<div class='updated'><p>Credits updated for user ID {$edit_user_id}.</p></div>";
    }
    ?>
    <div class="wrap">
        <h1>Golf Session Manager</h1>
        <h2>User Credits</h2>
        <table class="widefat">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Credit Balance</th>
                    <th>Subscription Plan</th>
                    <th>Cycle Start</th>
                    <th>Cycle End</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $table = $wpdb->prefix . 'golf_session_credits';
                $results = $wpdb->get_results("SELECT * FROM $table");

                foreach ($results as $row) {
                    $user_info = get_userdata($row->user_id);
                    $display_name = $user_info ? $user_info->display_name : 'Unknown';
                    echo "<tr>
                        <td>{$display_name} (ID: {$row->user_id})</td>
                        <td>{$row->credit_balance}</td>
                        <td>{$row->subscription_plan}</td>
                        <td>{$row->cycle_start}</td>
                        <td>{$row->cycle_end}</td>
                        <td>
                            <form method='post' style='display:inline;'>
                                <input type='hidden' name='edit_user_id' value='{$row->user_id}' />
                                <input type='number' name='new_credits' min='0' value='{$row->credit_balance}' />
                                <input type='submit' name='edit_credits' value='Update' class='button' />
                            </form>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Add a submenu for plan-service association
add_action('admin_menu', function() {
    add_submenu_page(
        'golf-session-manager',
        'Plan-Service Association',
        'Plan-Service Association',
        'manage_options',
        'golf-session-manager-plan-service',
        'gsm_plan_service_admin_page'
    );
});

function gsm_plan_service_admin_page() {
    // Fetch existing associations
    $associations = get_option('gsm_plan_service_assoc', []);

    // Handle form submission
    if (isset($_POST['save_assoc']) && current_user_can('manage_options')) {
        $new_assoc = [];
        foreach ($_POST['product_id'] as $i => $product_id) {
            $new_assoc[$product_id] = [
                'credits' => intval($_POST['credits'][$i]),
                'time' => intval($_POST['time'][$i]),
                'service_id' => intval($_POST['service_id'][$i])
            ];
        }
        update_option('gsm_plan_service_assoc', $new_assoc);
        echo '<div class="updated"><p>Associations saved!</p></div>';
        $associations = $new_assoc;
    }

    // Fetch Amelia services from DB
    global $wpdb;
    $services = [];
    $service_rows = $wpdb->get_results("SELECT id, name FROM {$wpdb->prefix}amelia_services");
    foreach ($service_rows as $service) {
        $services[$service->id] = $service->name;
    }

    // Fetch WooCommerce subscription products
    $products = [];
    $args = [
        'post_type' => 'product',
        'posts_per_page' => -1,
        'meta_query' => [
            [
                'key' => '_subscription_period',
                'compare' => 'EXISTS'
            ]
        ]
    ];
    $wc_products = get_posts($args);
    foreach ($wc_products as $product) {
        $products[$product->ID] = $product->post_title;
    }

    ?>
    <div class="wrap">
        <h1>Plan-Service Association</h1>
        <form method="post">
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Subscription Product</th>
                        <th>Credits per Cycle</th>
                        <th>Time per Session (minutes)</th>
                        <th>Amelia Service</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($products as $product_id => $product_name):
                        $assoc = isset($associations[$product_id]) ? $associations[$product_id] : ['credits'=>0,'time'=>0,'service_id'=>0];
                    ?>
                    <tr>
                        <td>
                            <input type="hidden" name="product_id[]" value="<?php echo esc_attr($product_id); ?>">
                            <?php echo esc_html($product_name); ?>
                        </td>
                        <td><input type="number" name="credits[]" value="<?php echo esc_attr($assoc['credits']); ?>" min="0"></td>
                        <td><input type="number" name="time[]" value="<?php echo esc_attr($assoc['time']); ?>" min="0"></td>
                        <td>
                            <select name="service_id[]">
                                <?php foreach ($services as $id => $name): ?>
                                    <option value="<?php echo esc_attr($id); ?>" <?php selected($assoc['service_id'], $id); ?>><?php echo esc_html($name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><input type="submit" name="save_assoc" class="button-primary" value="Save Associations"></p>
        </form>
    </div>
    <?php
}