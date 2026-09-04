<?php
/**
 * Persian Framework - Public API
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

function pf_get($key, $default = null) {
    return PersianFramework::get_option($key, $default);
}

function pf_set($key, $value) {
    return PersianFramework::set_option($key, $value);
}

function pf_get_all() {
    return PersianFramework::get_all_options();
}

function pf_render($field, $value = null) {
    if (class_exists('PersianFramework_Fields')) {
        return PersianFramework_Fields::render_field($field, $value);
    }
    return '';
}

function pf_register_field($field) {
    do_action('persian_framework_register_field', $field);
}

function pf_register_section($section) {
    do_action('persian_framework_register_section', $section);
}

function pf_translate($text) {
    $translations = PersianFramework_Translations::get_instance();
    return $translations->translate_text($text, $text, 'persian-framework');
}

function pf_translate_e($text) {
    echo pf_translate($text);
}

function pf_get_theme() {
    if (class_exists('PersianFramework_Theme')) {
        return PersianFramework_Theme::get_instance()->get_current_theme();
    }
    return 'default';
}

function pf_get_themes() {
    if (class_exists('PersianFramework_Theme')) {
        return PersianFramework_Theme::get_instance()->get_themes();
    }
    return array();
}

function pf_theme_colors() {
    if (class_exists('PersianFramework_Theme')) {
        return PersianFramework_Theme::get_instance()->get_theme_colors();
    }
    return array();
}