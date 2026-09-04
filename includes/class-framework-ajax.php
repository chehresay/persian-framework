<?php
/**
 * Persian Framework - Ajax Class
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Ajax {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_ajax_pf_save_options', array($this, 'save_options'));
        add_action('wp_ajax_pf_reset_options', array($this, 'reset_options'));
        add_action('wp_ajax_pf_get_field', array($this, 'get_field'));
        add_action('wp_ajax_pf_upload_file', array($this, 'upload_file'));
    }

    public function save_options() {
        check_ajax_referer('pf_ajax_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'persian-framework')));
        }

        $options = isset($_POST['options']) ? $_POST['options'] : array();
        $opt_name = isset($_POST['opt_name']) ? sanitize_text_field($_POST['opt_name']) : PERSIAN_FRAMEWORK_OPTION;

        // Sanitize options
        foreach ($options as $key => $value) {
            $options[$key] = sanitize_text_field($value);
        }

        update_option($opt_name, $options);

        wp_send_json_success(array(
            'message' => __('Settings saved successfully!', 'persian-framework')
        ));
    }

    public function reset_options() {
        check_ajax_referer('pf_ajax_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'persian-framework')));
        }

        $opt_name = isset($_POST['opt_name']) ? sanitize_text_field($_POST['opt_name']) : PERSIAN_FRAMEWORK_OPTION;
        $defaults = apply_filters('persian_framework_default_options', array());

        update_option($opt_name, $defaults);

        wp_send_json_success(array(
            'message' => __('Settings reset to default!', 'persian-framework')
        ));
    }

    public function get_field() {
        check_ajax_referer('pf_ajax_nonce', 'nonce');

        $field = isset($_POST['field']) ? $_POST['field'] : array();
        $value = isset($_POST['value']) ? $_POST['value'] : null;

        if (class_exists('PersianFramework_Fields')) {
            $html = PersianFramework_Fields::render_field($field, $value);
            wp_send_json_success(array('html' => $html));
        }

        wp_send_json_error(array('message' => __('Field not found', 'persian-framework')));
    }

    public function upload_file() {
        check_ajax_referer('pf_ajax_nonce', 'nonce');

        if (!current_user_can('upload_files')) {
            wp_send_json_error(array('message' => __('Permission denied', 'persian-framework')));
        }

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error(array('message' => __('Upload failed', 'persian-framework')));
        }

        $file = $_FILES['file'];
        $allowed_types = apply_filters('persian_framework_allowed_file_types', array(
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf', 'application/zip'
        ));

        if (!in_array($file['type'], $allowed_types)) {
            wp_send_json_error(array('message' => __('File type not allowed', 'persian-framework')));
        }

        $upload = wp_handle_upload($file, array('test_form' => false));

        if (isset($upload['error'])) {
            wp_send_json_error(array('message' => $upload['error']));
        }

        wp_send_json_success(array(
            'url' => $upload['url'],
            'file' => $upload['file'],
            'type' => $upload['type']
        ));
    }
}