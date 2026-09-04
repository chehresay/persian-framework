<?php
/**
 * Persian Framework - Export/Import Class
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Export_Import {

    private static $instance = null;
    private $opt_name = '';

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Register admin post actions
        add_action('admin_post_pf_export', array($this, 'export'));
        add_action('admin_post_pf_import', array($this, 'import'));

        // Register AJAX actions
        add_action('wp_ajax_pf_export', array($this, 'ajax_export'));
        add_action('wp_ajax_pf_import', array($this, 'ajax_import'));
        add_action('wp_ajax_pf_create_backup', array($this, 'ajax_create_backup'));
        add_action('wp_ajax_pf_restore_backup', array($this, 'ajax_restore_backup'));
        add_action('wp_ajax_pf_delete_backup', array($this, 'ajax_delete_backup'));
    }

    /**
     * Export settings as JSON file
     */
    public function export() {
        // Check user permissions
        if (!current_user_can('manage_options')) {
            wp_die('Permission denied');
        }

        // Get option name from request
        $this->opt_name = isset($_GET['opt_name']) ? sanitize_text_field($_GET['opt_name']) : 'persian_framework_options';

        // Get options from database
        $options = get_option($this->opt_name, array());

        // Prepare export data
        $data = array(
            'version' => PERSIAN_FRAMEWORK_VERSION,
            'export_date' => current_time('mysql'),
            'site_url' => get_site_url(),
            'opt_name' => $this->opt_name,
            'options' => $options,
        );

        // Set headers for file download
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $this->opt_name . '-export-' . date('Y-m-d') . '.json"');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: 0');
        header('Pragma: no-cache');

        // Output JSON with pretty print
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Import settings from JSON file (via POST)
     */
    public function import() {
        // Check user permissions
        if (!current_user_can('manage_options')) {
            wp_die('Permission denied');
        }

        // Check if file was uploaded
        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
            wp_redirect(add_query_arg('import_error', '1', wp_get_referer()));
            exit;
        }

        // Read file content
        $file = $_FILES['import_file'];
        $json = file_get_contents($file['tmp_name']);
        $data = json_decode($json, true);

        // Validate data
        if (!isset($data['options']) || !is_array($data['options'])) {
            wp_redirect(add_query_arg('import_error', '2', wp_get_referer()));
            exit;
        }

        // Get option name
        $this->opt_name = isset($_POST['opt_name']) ? sanitize_text_field($_POST['opt_name']) : 'persian_framework_options';

        // Update options
        update_option($this->opt_name, $data['options']);

        wp_redirect(add_query_arg('import_success', '1', wp_get_referer()));
        exit;
    }

    /**
     * AJAX export
     */
    public function ajax_export() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'pf_ajax_nonce')) {
            wp_send_json_error(array('message' => __('Invalid nonce!', 'persian-framework')));
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'persian-framework')));
        }

        // Get option name
        $this->opt_name = isset($_POST['opt_name']) ? sanitize_text_field($_POST['opt_name']) : 'persian_framework_options';

        // Get options
        $options = get_option($this->opt_name, array());

        // Prepare data
        $data = array(
            'version' => PERSIAN_FRAMEWORK_VERSION,
            'export_date' => current_time('mysql'),
            'site_url' => get_site_url(),
            'opt_name' => $this->opt_name,
            'options' => $options,
        );

        wp_send_json_success(array('data' => $data));
    }

    /**
     * AJAX import
     */
    public function ajax_import() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'pf_ajax_nonce')) {
            wp_send_json_error(array('message' => __('Invalid nonce!', 'persian-framework')));
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'persian-framework')));
        }

        // Get data
        $data = isset($_POST['data']) ? $_POST['data'] : array();
        $this->opt_name = isset($_POST['opt_name']) ? sanitize_text_field($_POST['opt_name']) : 'persian_framework_options';

        // Validate data
        if (!isset($data['options']) || !is_array($data['options'])) {
            wp_send_json_error(array('message' => __('Invalid data format', 'persian-framework')));
        }

        // Update options
        update_option($this->opt_name, $data['options']);

        wp_send_json_success(array(
            'message' => __('Import successful!', 'persian-framework')
        ));
    }

    /**
     * AJAX create backup
     */
    public function ajax_create_backup() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'pf_ajax_nonce')) {
            wp_send_json_error(array('message' => __('Invalid nonce!', 'persian-framework')));
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'persian-framework')));
        }

        // Get option name
        $this->opt_name = isset($_POST['opt_name']) ? sanitize_text_field($_POST['opt_name']) : 'persian_framework_options';

        // Get current options
        $options = get_option($this->opt_name, array());

        // Get existing backups
        $backup_key = $this->opt_name . '_backups';
        $backups = get_option($backup_key, array());

        // Add new backup
        $timestamp = current_time('timestamp');
        $backups[$timestamp] = array(
            'time' => $timestamp,
            'options' => $options,
            'date' => date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $timestamp),
        );

        // Limit backups to 10
        if (count($backups) > 10) {
            $backups = array_slice($backups, -10, 10, true);
        }

        // Save backups
        update_option($backup_key, $backups);

        wp_send_json_success(array(
            'message' => __('Backup created successfully!', 'persian-framework')
        ));
    }

    /**
     * AJAX restore backup
     */
    public function ajax_restore_backup() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'pf_ajax_nonce')) {
            wp_send_json_error(array('message' => __('Invalid nonce!', 'persian-framework')));
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'persian-framework')));
        }

        // Get parameters
        $this->opt_name = isset($_POST['opt_name']) ? sanitize_text_field($_POST['opt_name']) : 'persian_framework_options';
        $timestamp = isset($_POST['timestamp']) ? intval($_POST['timestamp']) : 0;

        // Get backups
        $backup_key = $this->opt_name . '_backups';
        $backups = get_option($backup_key, array());

        // Check if backup exists
        if (!isset($backups[$timestamp])) {
            wp_send_json_error(array('message' => __('Backup not found!', 'persian-framework')));
        }

        // Restore options
        update_option($this->opt_name, $backups[$timestamp]['options']);

        wp_send_json_success(array(
            'message' => __('Backup restored successfully!', 'persian-framework')
        ));
    }

    /**
     * AJAX delete backup
     */
    public function ajax_delete_backup() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'pf_ajax_nonce')) {
            wp_send_json_error(array('message' => __('Invalid nonce!', 'persian-framework')));
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'persian-framework')));
        }

        // Get parameters
        $this->opt_name = isset($_POST['opt_name']) ? sanitize_text_field($_POST['opt_name']) : 'persian_framework_options';
        $timestamp = isset($_POST['timestamp']) ? intval($_POST['timestamp']) : 0;

        // Get backups
        $backup_key = $this->opt_name . '_backups';
        $backups = get_option($backup_key, array());

        // Check if backup exists
        if (!isset($backups[$timestamp])) {
            wp_send_json_error(array('message' => __('Backup not found!', 'persian-framework')));
        }

        // Remove backup
        unset($backups[$timestamp]);

        // Save backups
        update_option($backup_key, $backups);

        wp_send_json_success(array(
            'message' => __('Backup deleted successfully!', 'persian-framework')
        ));
    }

    /**
     * Get all backups for a specific option name
     */
    public function get_backups($opt_name = '') {
        $this->opt_name = !empty($opt_name) ? $opt_name : 'persian_framework_options';
        $backup_key = $this->opt_name . '_backups';
        return get_option($backup_key, array());
    }
}