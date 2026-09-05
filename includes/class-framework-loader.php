<?php
/**
 * Persian Framework - Loader Class
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Loader {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', array($this, 'load_fields'));
    }

    public function load_fields() {
        $field_types = array(
            'accordion', 'section',
            'text', 'textarea', 'number', 'email', 'url', 'password', 'hidden', 'multi-text',
            'switch', 'checkbox', 'radio', 'select', 'multi-select', 'select-image',
            'gallery', 'media', 'image', 'slides',
            'color', 'gradient', 'palette-color',
            'typography', 'border', 'spacing', 'dimensions',
            'date', 'time', 'datetime',
            'slider', 'spinner',
            'code-editor', 'ace-editor',
            'repeater', 'sortable', 'sorter',
            'button-set',
            'icon-picker', 'font-awesome-picker',
            'wp-editor',
            'map',
            'backup', 'import-export',
            'info'
        );

        foreach ($field_types as $type) {
            $file = PERSIAN_FRAMEWORK_FIELDS . 'field-' . $type . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }
}