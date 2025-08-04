# Golf Session Manager

## Description
The Golf Session Manager is a WordPress plugin designed to enhance the Amelia booking system by adding a credit management system. This plugin allows users to earn credits based on their subscription plans, which can be used for booking sessions. It also includes a custom check-in feature and an admin panel for managing credit usage.

## Features
- **Credit System**: Automatically adds credits to a user's account upon subscription activation.
- **Booking Management**: Deducts credits when a booking is made and restores credits upon cancellation.
- **Service Filtering**: Displays only the services available to the user based on their subscription plan.
- **Check-In Feature**: Allows users to check in for their sessions, enhancing the booking experience.
- **Admin Panel**: Provides an interface for administrators to manage and view credit usage.

## Installation
1. Upload the `golf-session-manager` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Upon activation, the necessary database tables will be created automatically.

## Usage
- Users will receive credits based on their subscription plans.
- Credits will be deducted automatically when making a booking through the Amelia system.
- Users can check in for their sessions using the provided check-in button on the frontend dashboard.

## Development
This plugin is built using PHP and integrates with WordPress and WooCommerce. The following files are included in the project:

- **golf-session-manager.php**: Main plugin file with header information and hooks.
- **uninstall.php**: Handles cleanup on plugin uninstallation.
- **inc/hooks.php**: Contains action hooks for credit management.
- **inc/functions.php**: Utility functions for managing credits and plans.
- **inc/admin-panel.php**: Admin panel functionality.
- **inc/shortcode-dashboard.php**: Shortcodes for frontend display.
- **assets/css**: Directory for CSS files.
- **assets/js**: Directory for JavaScript files.
- **sql/install.sql**: SQL schema for database tables.

## Contributing
Contributions are welcome! Please submit a pull request or open an issue for any enhancements or bug fixes.

## License
This project is licensed under the MIT License. See the LICENSE file for details.