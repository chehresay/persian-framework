<?php
/**
 * Persian Framework - Section Field
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('PersianFramework_Field_Section')):

    class PersianFramework_Field_Section {

        private $field;
        private $value;

        public function __construct($field, $value = null) {
            $this->field = $field;
            $this->value = $value;
        }

        public function render() {
            $field = $this->field;

            // Default values
            $title = isset($field['title']) ? $field['title'] : '';
            $subtitle = isset($field['subtitle']) ? $field['subtitle'] : '';
            $desc = isset($field['desc']) ? $field['desc'] : '';
            $indent = isset($field['indent']) ? $field['indent'] : false;
            $id = isset($field['id']) ? $field['id'] : '';
            $class = isset($field['class']) ? $field['class'] : '';
            $style = isset($field['style']) ? $field['style'] : '';

            // Build classes
            $classes = array('pf-section-wrapper');
            if (!empty($class)) {
                $classes[] = $class;
            }
            if ($indent) {
                $classes[] = 'pf-section-indent';
            }

            // Build styles
            $styles = array();
            if (!empty($style)) {
                $styles[] = $style;
            }

            $output = '';

            // Start section
            $output .= '<div class="' . esc_attr(implode(' ', $classes)) . '"';

            if (!empty($id)) {
                $output .= ' id="pf-section-' . esc_attr($id) . '"';
            }
            if (!empty($styles)) {
                $output .= ' style="' . esc_attr(implode('; ', $styles)) . '"';
            }
            $output .= '>';

            // Section header
            if (!empty($title)) {
                $output .= '<div class="pf-section-header-wrap">';

                $output .= '<svg style="width: 20px; height: 20px; float: inline-start;margin-left: 10px;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M17 10H19C21 10 22 9 22 7V5C22 3 21 2 19 2H17C15 2 14 3 14 5V7C14 9 15 10 17 10Z" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M5 22H7C9 22 10 21 10 19V17C10 15 9 14 7 14H5C3 14 2 15 2 17V19C2 21 3 22 5 22Z" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path> <path opacity="0.34" d="M6 10C8.20914 10 10 8.20914 10 6C10 3.79086 8.20914 2 6 2C3.79086 2 2 3.79086 2 6C2 8.20914 3.79086 10 6 10Z" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path> <path opacity="0.34" d="M18 22C20.2091 22 22 20.2091 22 18C22 15.7909 20.2091 14 18 14C15.7909 14 14 15.7909 14 18C14 20.2091 15.7909 22 18 22Z" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>';

                // Title
                $output .= '<h3 class="pf-section-title">' . esc_html($title) . '</h3>';

                // Subtitle
                if (!empty($subtitle)) {
                    $output .= '<p class="pf-section-subtitle">' . esc_html($subtitle) . '</p>';
                }

                // Description
                if (!empty($desc)) {
                    $output .= '<p class="pf-section-desc">' . wp_kses_post($desc) . '</p>';
                }

                $output .= '</div>';
            }

            // End section
            $output .= '</div>';

            return $output;
        }

        public function enqueue() {
            // No scripts needed for section field
        }
    }

endif;