<?php
/**
 * Persian Framework - Fields Class
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Fields {

    private static $instance = null;
    private $field_types = array();

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->register_field_types();
        add_action('wp_ajax_pf_render_field', array($this, 'ajax_render_field'));
    }

    private function register_field_types() {
        $this->field_types = array(
            'section' => 'PersianFramework_Field_Section',
            'accordion' => 'PersianFramework_Field_Accordion',
            'text' => 'PersianFramework_Field_Text',
            'textarea' => 'PersianFramework_Field_Textarea',
            'number' => 'PersianFramework_Field_Number',
            'email' => 'PersianFramework_Field_Email',
            'url' => 'PersianFramework_Field_Url',
            'password' => 'PersianFramework_Field_Password',
            'hidden' => 'PersianFramework_Field_Hidden',
            'multi-text' => 'PersianFramework_Field_MultiText',
            'switch' => 'PersianFramework_Field_Switch',
            'checkbox' => 'PersianFramework_Field_Checkbox',
            'radio' => 'PersianFramework_Field_Radio',
            'select' => 'PersianFramework_Field_Select',
            'multi-select' => 'PersianFramework_Field_MultiSelect',
            'select-image' => 'PersianFramework_Field_SelectImage',
            'image' => 'PersianFramework_Field_Image',
            'gallery' => 'PersianFramework_Field_Gallery',
            'media' => 'PersianFramework_Field_Media',
            'slides' => 'PersianFramework_Field_Slides',
            'color' => 'PersianFramework_Field_Color',
            'gradient' => 'PersianFramework_Field_Gradient',
            'palette-color' => 'PersianFramework_Field_PaletteColor',
            'typography' => 'PersianFramework_Field_Typography',
            'border' => 'PersianFramework_Field_Border',
            'spacing' => 'PersianFramework_Field_Spacing',
            'dimensions' => 'PersianFramework_Field_Dimensions',
            'date' => 'PersianFramework_Field_Date',
            'time' => 'PersianFramework_Field_Time',
            'datetime' => 'PersianFramework_Field_Datetime',
            'slider' => 'PersianFramework_Field_Slider',
            'spinner' => 'PersianFramework_Field_Spinner',
            'code-editor' => 'PersianFramework_Field_CodeEditor',
            'ace-editor' => 'PersianFramework_Field_AceEditor',
            'repeater' => 'PersianFramework_Field_Repeater',
            'sortable' => 'PersianFramework_Field_Sortable',
            'sorter' => 'PersianFramework_Field_Sorter',
            'button-set' => 'PersianFramework_Field_ButtonSet',
            'icon-picker' => 'PersianFramework_Field_IconPicker',
            'font-awesome-picker' => 'PersianFramework_Field_FontAwesomePicker',
            'wp-editor' => 'PersianFramework_Field_WPEditor',
            'map' => 'PersianFramework_Field_Map',
            'backup' => 'PersianFramework_Field_Backup',
            'import-export' => 'PersianFramework_Field_ImportExport',
            'info' => 'PersianFramework_Field_Info',
        );
    }

    public static function render_field($field, $value = null) {
        $type = isset($field['type']) ? $field['type'] : 'text';
        $class_name = self::get_instance()->get_field_class($type);

        if ($class_name && class_exists($class_name)) {
            $field_obj = new $class_name($field, $value);
            $output = $field_obj->render();

            if (isset($field['required']) && is_array($field['required']) && class_exists('PersianFramework_Required')) {
                PersianFramework_Required::enqueue_scripts();
            }

            return $output;
        }

        return '<div class="pf-field-error">' . sprintf(
            /* translators: %s: Field type name */
                esc_html__('Field type "%s" not found', 'persian-framework'),
                esc_html($type)
            ) . '</div>';
    }

    public function get_field_class($type) {
        return isset($this->field_types[$type]) ? $this->field_types[$type] : false;
    }

    public function ajax_render_field() {
        check_ajax_referer('pf_ajax_nonce', 'nonce');

        $field = isset($_POST['field']) ? map_deep(wp_unslash($_POST['field']), 'sanitize_text_field') : array();
        $value = isset($_POST['value']) ? wp_unslash($_POST['value']) : null;

        $html = self::render_field($field, $value);
        wp_send_json_success(array('html' => $html));
    }
}