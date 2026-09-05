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
        add_action('admin_post_pf_export', array($this, 'export'));
        add_action('admin_post_pf_import', array($this, 'import'));

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
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Permission denied', 'persian-framework'));
        }

        $this->opt_name = isset($_GET['opt_name']) ? sanitize_text_field(wp_unslash($_GET['opt_name'])) : 'persian_framework_options';

        $options = get_option($this->opt_name, array());

        $data = array(
            'version' => PERSIAN_FRAMEWORK_VERSION,
            'export_date' => current_time('mysql'),
            'site_url' => get_site_url(),
            'opt_name' => $this->opt_name,
            'options' => $options,
        );

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . esc_attr($this->opt_name) . '-export-' . gmdate('Y-m-d') . '.json"');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: 0');
        header('Pragma: no-cache');

        echo wp_json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Import settings from JSON file (via POST)
     */
    public function import() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Permission denied', 'persian-framework'));
        }

        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
            wp_safe_redirect(add_query_arg('import_error', '1', wp_get_referer()));
            exit;
        }

        $file = $_FILES['import_file'];
        $json = file_get_contents($file['tmp_name']);
        $data = json_decode($json, true);

        if (!isset($data['options']) || !is_array($data['options'])) {
            wp_safe_redirect(add_query_arg('import_error', '2', wp_get_referer()));
            exit;
        }

        $this->opt_name = isset($_POST['opt_name']) ? sanitize_text_field(wp_unslash($_POST['opt_name'])) : 'persian_framework_options';

        update_option($this->opt_name, $data['options']);

        wp_safe_redirect(add_query_arg('import_success', '1', wp_get_referer()));
        exit;
    }

    /**
     * AJAX export
     */
    public function ajax_export() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'pf_ajax_nonce')) {
            wp_send_json_error(array('message' => esc_html__('Invalid nonce!', 'persian-framework')));
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => esc_html__('Permission denied', 'persian-framework')));
        }

        $this->opt_name = isset($_POST['opt_name']) ? sanitize_text_field(wp_unslash($_POST['opt_name'])) : 'persian_framework_options';

        $options = get_option($this->opt_name, array());

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
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'pf_ajax_nonce')) {
            wp_send_json_error(array('message' => esc_html__('Invalid nonce!', 'persian-framework')));
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => esc_html__('Permission denied', 'persian-framework')));
        }

        $data = isset($_POST['data']) ? map_deep(wp_unslash($_POST['data']), 'sanitize_text_field') : array();
        $this->opt_name = isset($_POST['opt_name']) ? sanitize_text_field(wp_unslash($_POST['opt_name'])) : 'persian_framework_options';

        if (!isset($data['options']) || !is_array($data['options'])) {
            wp_send_json_error(array('message' => esc_html__('Invalid data format', 'persian-framework')));
        }

        update_option($this->opt_name, $data['options']);

        wp_send_json_success(array(
            'message' => esc_html__('Import successful!', 'persian-framework')
        ));
    }

    /**
     * AJAX create backup
     */
    public function ajax_create_backup() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'pf_ajax_nonce')) {
            wp_send_json_error(array('message' => esc_html__('Invalid nonce!', 'persian-framework')));
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => esc_html__('Permission denied', 'persian-framework')));
        }

        $this->opt_name = isset($_POST['opt_name']) ? sanitize_text_field(wp_unslash($_POST['opt_name'])) : 'persian_framework_options';

        $options = get_option($this->opt_name, array());

        $backup_key = $this->opt_name . '_backups';
        $backups = get_option($backup_key, array());

        $timestamp = current_time('timestamp');
        $backups[$timestamp] = array(
            'time' => $timestamp,
            'options' => $options,
            'date' => date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $timestamp),
        );

        if (count($backups) > 10) {
            $backups = array_slice($backups, -10, 10, true);
        }

        update_option($backup_key, $backups);

        wp_send_json_success(array(
            'message' => esc_html__('Backup created successfully!', 'persian-framework')
        ));
    }

    /**
     * AJAX restore backup
     */
    public function ajax_restore_backup() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'pf_ajax_nonce')) {
            wp_send_json_error(array('message' => esc_html__('Invalid nonce!', 'persian-framework')));
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => esc_html__('Permission denied', 'persian-framework')));
        }

        $this->opt_name = isset($_POST['opt_name']) ? sanitize_text_field(wp_unslash($_POST['opt_name'])) : 'persian_framework_options';
        $timestamp = isset($_POST['timestamp']) ? intval(wp_unslash($_POST['timestamp'])) : 0;

        $backup_key = $this->opt_name . '_backups';
        $backups = get_option($backup_key, array());

        if (!isset($backups[$timestamp])) {
            wp_send_json_error(array('message' => esc_html__('Backup not found!', 'persian-framework')));
        }

        update_option($this->opt_name, $backups[$timestamp]['options']);

        wp_send_json_success(array(
            'message' => esc_html__('Backup restored successfully!', 'persian-framework')
        ));
    }

    /**
     * AJAX delete backup
     */
    public function ajax_delete_backup() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'pf_ajax_nonce')) {
            wp_send_json_error(array('message' => esc_html__('Invalid nonce!', 'persian-framework')));
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => esc_html__('Permission denied', 'persian-framework')));
        }

        $this->opt_name = isset($_POST['opt_name']) ? sanitize_text_field(wp_unslash($_POST['opt_name'])) : 'persian_framework_options';
        $timestamp = isset($_POST['timestamp']) ? intval(wp_unslash($_POST['timestamp'])) : 0;

        $backup_key = $this->opt_name . '_backups';
        $backups = get_option($backup_key, array());

        if (!isset($backups[$timestamp])) {
            wp_send_json_error(array('message' => esc_html__('Backup not found!', 'persian-framework')));
        }

        unset($backups[$timestamp]);

        update_option($backup_key, $backups);

        wp_send_json_success(array(
            'message' => esc_html__('Backup deleted successfully!', 'persian-framework')
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