<?php
/**
 * Plugin Name: Persian Framework
 * Plugin URI: https://parswp.ir/persian-framework
 * Description: Advanced WordPress Options Framework with 40+ professional fields
 * Version: 1.0.0
 * Author: Parswp.ir
 * Author URI: https://parswp.ir
 * License: GPL v2 or later
 * Text Domain: persian-framework
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires WP: 5.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// ========================================
// Constants
// ========================================
define('PERSIAN_FRAMEWORK_VERSION', '1.0.0');
define('PERSIAN_FRAMEWORK_FILE', __FILE__);
define('PERSIAN_FRAMEWORK_PATH', plugin_dir_path(__FILE__));
define('PERSIAN_FRAMEWORK_URL', plugin_dir_url(__FILE__));
define('PERSIAN_FRAMEWORK_OPTION', 'persian_framework_options');
define('PERSIAN_FRAMEWORK_THEME', 'persian_framework_theme');

// ========================================
// Load Main Class
// ========================================
require_once PERSIAN_FRAMEWORK_PATH . 'includes/class-persian-framework.php';

// ========================================
// Helper Function
// ========================================
function persian_framework() {
    return PersianFramework::get_instance();
}

// ========================================
// Run Plugin
// ========================================
persian_framework();