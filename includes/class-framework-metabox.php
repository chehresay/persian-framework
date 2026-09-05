<?php
/**
 * Persian Framework - Metabox Class
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Metabox {

    private static $instance = null;
    private $metaboxes = array();
    private $sections = array();
    private $fields = array();

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('add_meta_boxes', array($this, 'register_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'), 10, 2);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    /**
     * Register a metabox
     */
    public function register_metabox($id, $title, $post_types = array('post'), $context = 'normal', $priority = 'default', $fields = array()) {
        $this->metaboxes[$id] = array(
            'id' => $id,
            'title' => $title,
            'post_types' => (array) $post_types,
            'context' => $context,
            'priority' => $priority,
            'fields' => $fields
        );
        return $this;
    }

    /**
     * Add a section to metabox (group fields)
     */
    public function add_section($metabox_id, $section_id, $title, $fields = array()) {
        if (isset($this->metaboxes[$metabox_id])) {
            $this->metaboxes[$metabox_id]['sections'][$section_id] = array(
                'id' => $section_id,
                'title' => $title,
                'fields' => $fields
            );
        }
        return $this;
    }

    /**
     * Add a field to metabox section
     */
    public function add_field($metabox_id, $section_id, $field) {
        if (isset($this->metaboxes[$metabox_id]['sections'][$section_id])) {
            $this->metaboxes[$metabox_id]['sections'][$section_id]['fields'][] = $field;
        } else {
            if (isset($this->metaboxes[$metabox_id])) {
                $this->metaboxes[$metabox_id]['fields'][] = $field;
            }
        }
        return $this;
    }

    /**
     * Register all metaboxes
     */
    public function register_meta_boxes() {
        foreach ($this->metaboxes as $id => $metabox) {
            add_meta_box(
                $id,
                $metabox['title'],
                array($this, 'render_metabox'),
                $metabox['post_types'],
                $metabox['context'],
                $metabox['priority'],
                array('metabox' => $metabox)
            );
        }
    }

    /**
     * Render a metabox
     */
    public function render_metabox($post, $callback_args) {
        $metabox = $callback_args['args']['metabox'];
        wp_nonce_field('pf_metabox_nonce_' . $metabox['id'], 'pf_metabox_nonce_' . $metabox['id']);

        echo '<div class="pf-metabox-container">';

        if (isset($metabox['sections']) && !empty($metabox['sections'])) {
            echo '<div class="pf-metabox-sections">';
            $first = true;
            foreach ($metabox['sections'] as $section) {
                echo '<div class="pf-metabox-section' . ($first ? ' active' : '') . '" id="pf-section-' . esc_attr($section['id']) . '">';
                echo '<h3 class="pf-metabox-section-title">' . esc_html($section['title']) . '</h3>';
                $this->render_fields($section['fields'], $post->ID, $metabox['id']);
                echo '</div>';
                $first = false;
            }
            echo '</div>';
        }

        if (isset($metabox['fields']) && !empty($metabox['fields'])) {
            $this->render_fields($metabox['fields'], $post->ID, $metabox['id']);
        }

        echo '</div>';
    }

    /**
     * Render fields
     */
    private function render_fields($fields, $post_id, $metabox_id) {
        echo '<div class="pf-metabox-fields">';
        foreach ($fields as $field) {
            $field_id = $metabox_id . '_' . $field['id'];
            $value = get_post_meta($post_id, $field_id, true);

            if (isset($field['default']) && empty($value)) {
                $value = $field['default'];
            }

            echo '<div class="pf-metabox-field pf-field-type-' . esc_attr($field['type']) . '">';

            if (isset($field['label'])) {
                echo '<label for="' . esc_attr($field_id) . '" class="pf-metabox-field-label">';
                echo esc_html($field['label']);
                if (isset($field['required']) && $field['required']) {
                    echo ' <span class="pf-required">*</span>';
                }
                echo '</label>';
            }

            $field['name'] = $field_id;
            $field['id'] = $field_id;

            if (class_exists('PersianFramework_Fields')) {
                echo PersianFramework_Fields::render_field($field, $value);
            }

            if (isset($field['description'])) {
                echo '<p class="pf-metabox-field-desc">' . esc_html($field['description']) . '</p>';
            }

            echo '</div>';
        }
        echo '</div>';
    }

    /**
     * Save metabox fields
     */
    public function save_meta_boxes($post_id, $post) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        foreach ($this->metaboxes as $metabox_id => $metabox) {
            $nonce_name = 'pf_metabox_nonce_' . $metabox_id;
            if (!isset($_POST[$nonce_name]) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[$nonce_name])), $nonce_name)) {
                continue;
            }

            if (!in_array($post->post_type, $metabox['post_types'])) {
                continue;
            }

            if (isset($metabox['fields'])) {
                $this->save_fields($metabox['fields'], $post_id, $metabox_id);
            }

            if (isset($metabox['sections'])) {
                foreach ($metabox['sections'] as $section) {
                    if (isset($section['fields'])) {
                        $this->save_fields($section['fields'], $post_id, $metabox_id);
                    }
                }
            }
        }
    }

    /**
     * Save individual fields
     */
    private function save_fields($fields, $post_id, $metabox_id) {
        foreach ($fields as $field) {
            $field_id = $metabox_id . '_' . $field['id'];

            if (isset($_POST[$field_id])) {
                $value = wp_unslash($_POST[$field_id]);
                $value = $this->sanitize_field_value($value, $field);
                update_post_meta($post_id, $field_id, $value);
            } else {
                if ($field['type'] === 'checkbox') {
                    delete_post_meta($post_id, $field_id);
                }
            }
        }
    }

    /**
     * Sanitize field value
     */
    private function sanitize_field_value($value, $field) {
        if (is_array($value)) {
            $sanitized = array();
            foreach ($value as $key => $val) {
                $sanitized[$key] = $this->sanitize_field_value($val, $field);
            }
            return $sanitized;
        }

        switch ($field['type']) {
            case 'textarea':
            case 'wp-editor':
                return wp_kses_post($value);
            case 'email':
                return sanitize_email($value);
            case 'url':
                return esc_url_raw($value);
            case 'number':
                return floatval($value);
            case 'checkbox':
                return (bool) $value;
            case 'color':
                return sanitize_hex_color($value);
            default:
                return sanitize_text_field($value);
        }
    }

    /**
     * Enqueue assets
     */
    public function enqueue_assets($hook) {
        if (!in_array($hook, array('post.php', 'post-new.php'))) {
            return;
        }

        wp_enqueue_style('pf-metabox', PERSIAN_FRAMEWORK_ASSETS . 'css/metabox.css', array(), PERSIAN_FRAMEWORK_VERSION);
        //wp_enqueue_script('pf-metabox', PERSIAN_FRAMEWORK_ASSETS . 'js/metabox.js', array('jquery'), PERSIAN_FRAMEWORK_VERSION, true);
    }

    /**
     * Get a field value
     */
    public function get_field($post_id, $metabox_id, $field_id, $default = null) {
        $value = get_post_meta($post_id, $metabox_id . '_' . $field_id, true);
        return !empty($value) ? $value : $default;
    }

    /**
     * Get all fields from a metabox
     */
    public function get_all_fields($post_id, $metabox_id) {
        $fields = array();
        if (isset($this->metaboxes[$metabox_id])) {
            $metabox = $this->metaboxes[$metabox_id];

            if (isset($metabox['fields'])) {
                foreach ($metabox['fields'] as $field) {
                    $fields[$field['id']] = get_post_meta($post_id, $metabox_id . '_' . $field['id'], true);
                }
            }

            if (isset($metabox['sections'])) {
                foreach ($metabox['sections'] as $section) {
                    if (isset($section['fields'])) {
                        foreach ($section['fields'] as $field) {
                            $fields[$field['id']] = get_post_meta($post_id, $metabox_id . '_' . $field['id'], true);
                        }
                    }
                }
            }
        }
        return $fields;
    }
}

/**
 * Helper functions
 */
function pf_metabox_get($post_id, $metabox_id, $field_id, $default = null) {
    return PersianFramework_Metabox::get_instance()->get_field($post_id, $metabox_id, $field_id, $default);
}

function pf_metabox_get_all($post_id, $metabox_id) {
    return PersianFramework_Metabox::get_instance()->get_all_fields($post_id, $metabox_id);
}