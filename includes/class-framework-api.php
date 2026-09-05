<?php
/**
 * Persian Framework - Public API
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('pf_get')) {
    /**
     * Get a single option value
     *
     * @param string $key     Option key
     * @param mixed  $default Default value if option not found
     * @return mixed
     */
    function pf_get($key, $default = null) {
        return PersianFramework::get_option($key, $default);
    }
}

if (!function_exists('pf_set')) {
    /**
     * Set a single option value
     *
     * @param string $key   Option key
     * @param mixed  $value Value to set
     * @return bool
     */
    function pf_set($key, $value) {
        return PersianFramework::set_option($key, $value);
    }
}

if (!function_exists('pf_get_all')) {
    /**
     * Get all options
     *
     * @return array
     */
    function pf_get_all() {
        return PersianFramework::get_all_options();
    }
}

if (!function_exists('pf_render')) {
    /**
     * Render a field
     *
     * @param array $field Field configuration
     * @param mixed $value Field value
     * @return string
     */
    function pf_render($field, $value = null) {
        if (class_exists('PersianFramework_Fields')) {
            return PersianFramework_Fields::render_field($field, $value);
        }
        return '';
    }
}

if (!function_exists('pf_register_field')) {
    /**
     * Register a field
     *
     * @param array $field Field configuration
     */
    function pf_register_field($field) {
        do_action('persian_framework_register_field', $field);
    }
}

if (!function_exists('pf_register_section')) {
    /**
     * Register a section
     *
     * @param array $section Section configuration
     */
    function pf_register_section($section) {
        do_action('persian_framework_register_section', $section);
    }
}

if (!function_exists('pf_translate')) {
    /**
     * Translate a string
     *
     * @param string $text Text to translate
     * @return string
     */
    function pf_translate($text) {
        $translations = PersianFramework_Translations::get_instance();
        return $translations->translate_text($text, $text, 'persian-framework');
    }
}

if (!function_exists('pf_translate_e')) {
    /**
     * Echo translated string
     *
     * @param string $text Text to translate and echo
     */
    function pf_translate_e($text) {
        echo esc_html(pf_translate($text));
    }
}

if (!function_exists('pf_get_theme')) {
    /**
     * Get current theme name
     *
     * @return string
     */
    function pf_get_theme() {
        if (class_exists('PersianFramework_Theme')) {
            return PersianFramework_Theme::get_instance()->get_current_theme();
        }
        return 'default';
    }
}

if (!function_exists('pf_get_themes')) {
    /**
     * Get all available themes
     *
     * @return array
     */
    function pf_get_themes() {
        if (class_exists('PersianFramework_Theme')) {
            return PersianFramework_Theme::get_instance()->get_themes();
        }
        return array();
    }
}

if (!function_exists('pf_theme_colors')) {
    /**
     * Get current theme colors
     *
     * @return array
     */
    function pf_theme_colors() {
        if (class_exists('PersianFramework_Theme')) {
            return PersianFramework_Theme::get_instance()->get_theme_colors();
        }
        return array();
    }
}