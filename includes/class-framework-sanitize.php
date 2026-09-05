<?php
/**
 * Persian Framework - Sanitize Class
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Sanitize {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter('persian_framework_sanitize_value', array($this, 'sanitize'), 10, 3);
        add_filter('persian_framework_validate_value', array($this, 'validate'), 10, 3);
    }

    public function sanitize($value, $field, $type) {
        switch ($type) {
            case 'text':
            case 'textarea':
            case 'select':
            case 'radio':
                return sanitize_text_field($value);

            case 'email':
                return sanitize_email($value);

            case 'url':
                return esc_url_raw($value);

            case 'number':
                return floatval($value);

            case 'switch':
            case 'checkbox':
                return (bool) $value;

            case 'wp-editor':
                return wp_kses_post($value);

            case 'code-editor':
            case 'ace-editor':
                return wp_kses_post($value);

            case 'color':
                return sanitize_hex_color($value);

            case 'date':
                return sanitize_text_field($value);

            case 'image':
            case 'media':
                if (is_array($value)) {
                    return array(
                        'id' => isset($value['id']) ? intval($value['id']) : 0,
                        'url' => isset($value['url']) ? esc_url_raw($value['url']) : '',
                    );
                }
                return $value;

            case 'repeater':
            case 'sorter':
            case 'sortable':
                if (is_array($value)) {
                    return $this->sanitize_array($value);
                }
                return array();

            default:
                return sanitize_text_field($value);
        }
    }

    public function validate($value, $field, $type) {
        $errors = array();

        if (isset($field['required']) && $field['required']) {
            if (empty($value) || (is_array($value) && empty(array_filter($value)))) {
                $errors[] = sprintf(
                /* translators: %s: Field title or ID */
                    esc_html__('Field "%s" is required.', 'persian-framework'),
                    $field['title'] ?? $field['id']
                );
            }
        }

        if ($type === 'email' && !empty($value) && !is_email($value)) {
            $errors[] = esc_html__('Invalid email address.', 'persian-framework');
        }

        if ($type === 'url' && !empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
            $errors[] = esc_html__('Invalid URL.', 'persian-framework');
        }

        if ($type === 'number' && !empty($value) && !is_numeric($value)) {
            $errors[] = esc_html__('Value must be a number.', 'persian-framework');
        }

        if ($type === 'number') {
            if (isset($field['min']) && $value < $field['min']) {
                $errors[] = sprintf(
                /* translators: %d: Minimum value */
                    esc_html__('Value must be at least %d.', 'persian-framework'),
                    $field['min']
                );
            }
            if (isset($field['max']) && $value > $field['max']) {
                $errors[] = sprintf(
                /* translators: %d: Maximum value */
                    esc_html__('Value cannot exceed %d.', 'persian-framework'),
                    $field['max']
                );
            }
        }

        if (isset($field['validate_callback']) && is_callable($field['validate_callback'])) {
            $custom_error = call_user_func($field['validate_callback'], $value, $field);
            if (is_wp_error($custom_error)) {
                $errors[] = $custom_error->get_error_message();
            }
        }

        if (!empty($errors)) {
            return new WP_Error('validation_error', implode(' ', $errors));
        }

        return true;
    }

    private function sanitize_array($array) {
        if (!is_array($array)) {
            return array();
        }

        $sanitized = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitize_array($value);
            } else {
                $sanitized[$key] = sanitize_text_field($value);
            }
        }
        return $sanitized;
    }
}