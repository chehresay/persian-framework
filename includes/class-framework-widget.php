<?php
/**
 * Persian Framework - Widget Class
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Widget extends WP_Widget {

    private static $instance = null;
    private $registered_widgets = array();

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('widgets_init', array($this, 'register_widgets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    /**
     * Register a custom widget
     */
    public function register_widget($id, $name, $description, $fields = array(), $defaults = array()) {
        $this->registered_widgets[$id] = array(
                'id' => $id,
                'name' => $name,
                'description' => $description,
                'fields' => $fields,
                'defaults' => $defaults
        );
        return $this;
    }

    /**
     * Register all widgets with WordPress
     */
    public function register_widgets() {
        foreach ($this->registered_widgets as $widget) {
            register_widget('PersianFramework_Widget_Instance');
        }
    }

    /**
     * Get a registered widget by ID
     */
    public function get_widget($id) {
        return isset($this->registered_widgets[$id]) ? $this->registered_widgets[$id] : null;
    }

    /**
     * Get all registered widgets
     */
    public function get_all_widgets() {
        return $this->registered_widgets;
    }

    /**
     * Enqueue assets for widget admin
     */
    public function enqueue_assets($hook) {
        if ($hook !== 'widgets.php') {
            return;
        }

        wp_enqueue_style('pf-widget', PERSIAN_FRAMEWORK_ASSETS . 'css/widget.css', array(), PERSIAN_FRAMEWORK_VERSION);
        wp_enqueue_script('pf-widget', PERSIAN_FRAMEWORK_ASSETS . 'js/widget.js', array('jquery'), PERSIAN_FRAMEWORK_VERSION, true);
    }

    /**
     * Helper: Get widget field value
     */
    public static function get_field($instance, $field_id, $default = '') {
        return isset($instance[$field_id]) ? $instance[$field_id] : $default;
    }

    /**
     * Helper: Render widget fields
     */
    public static function render_fields($fields, $instance) {
        foreach ($fields as $field) {
            $value = isset($instance[$field['id']]) ? $instance[$field['id']] : (isset($field['default']) ? $field['default'] : '');

            echo '<div class="pf-widget-field pf-widget-field-' . esc_attr($field['type']) . '">';

            if (isset($field['label'])) {
                echo '<label for="' . esc_attr($field['id']) . '" class="pf-widget-field-label">';
                echo esc_html($field['label']);
                if (isset($field['required']) && $field['required']) {
                    echo ' <span class="pf-required">*</span>';
                }
                echo '</label>';
            }

            $field['name'] = $field['id'];
            $field['id'] = $field['id'];

            if (class_exists('PersianFramework_Fields')) {
                $field_html = PersianFramework_Fields::render_field($field, $value);
                echo wp_kses_post($field_html);
            }

            if (isset($field['description'])) {
                echo '<p class="pf-widget-field-desc">' . esc_html($field['description']) . '</p>';
            }

            echo '</div>';
        }
    }
}

/**
 * Base Widget Instance Class
 */
class PersianFramework_Widget_Instance extends WP_Widget {

    private $widget_config = array();

    public function __construct() {
        $this->widget_config = $this->get_widget_config();

        parent::__construct(
                $this->widget_config['id'] ?? 'pf_widget',
                $this->widget_config['name'] ?? esc_html__('Persian Framework Widget', 'persian-framework'),
                array(
                        'description' => $this->widget_config['description'] ?? esc_html__('A Persian Framework widget', 'persian-framework'),
                        'classname' => 'pf-widget-' . ($this->widget_config['id'] ?? 'default')
                )
        );
    }

    /**
     * Get widget configuration from registry
     */
    private function get_widget_config() {
        $widget_id = $this->id_base ?? 'pf_widget';
        $framework = PersianFramework_Widget::get_instance();
        $config = $framework->get_widget($widget_id);
        return $config ? $config : array(
                'id' => 'pf_widget',
                'name' => esc_html__('Persian Framework Widget', 'persian-framework'),
                'description' => esc_html__('Default Persian Framework widget', 'persian-framework'),
                'fields' => array(),
                'defaults' => array()
        );
    }

    /**
     * Widget frontend display
     */
    public function widget($args, $instance) {
        echo wp_kses_post($args['before_widget']);

        if (!empty($instance['title'])) {
            $title = apply_filters('widget_title', $instance['title']);
            echo wp_kses_post($args['before_title']) . esc_html($title) . wp_kses_post($args['after_title']);
        }

        $fields = $this->widget_config['fields'] ?? array();
        $option_key = isset($instance['option_key']) ? $instance['option_key'] : '';

        if (!empty($option_key)) {
            $value = pf_get($option_key);
            if (!empty($value)) {
                if (is_array($value)) {
                    echo '<ul class="pf-widget-list">';
                    foreach ($value as $item) {
                        echo '<li>' . esc_html($item) . '</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<div class="pf-widget-content">' . esc_html($value) . '</div>';
                }
            }
        } else {
            $this->render_widget_content($instance);
        }

        echo wp_kses_post($args['after_widget']);
    }

    /**
     * Render widget content based on fields
     */
    private function render_widget_content($instance) {
        $fields = $this->widget_config['fields'] ?? array();

        foreach ($fields as $field) {
            if (!isset($field['display']) || $field['display'] === false) {
                continue;
            }

            $value = isset($instance[$field['id']]) ? $instance[$field['id']] : '';
            if (empty($value)) {
                continue;
            }

            echo '<div class="pf-widget-item pf-widget-item-' . esc_attr($field['id']) . '">';

            if (isset($field['display_label']) && $field['display_label']) {
                echo '<strong>' . esc_html($field['label'] ?? $field['id']) . ':</strong> ';
            }

            $this->render_field_value($field, $value);

            echo '</div>';
        }
    }

    /**
     * Render individual field value
     */
    private function render_field_value($field, $value) {
        $type = $field['type'] ?? 'text';

        switch ($type) {
            case 'image':
                if (is_array($value) && isset($value['url'])) {
                    echo '<img src="' . esc_url($value['url']) . '" alt="" style="max-width:100%;height:auto;border-radius:8px;">';
                } elseif (is_numeric($value)) {
                    $img = wp_get_attachment_image($value, 'medium');
                    if ($img) {
                        echo wp_kses_post($img);
                    }
                }
                break;

            case 'gallery':
                if (is_array($value)) {
                    echo '<div class="pf-widget-gallery" style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">';
                    foreach (array_slice($value, 0, 6) as $item) {
                        if (isset($item['url'])) {
                            echo '<img src="' . esc_url($item['url']) . '" alt="" style="width:100%;height:80px;object-fit:cover;border-radius:4px;">';
                        }
                    }
                    echo '</div>';
                }
                break;

            case 'color':
                echo '<span style="display:inline-block;width:20px;height:20px;background:' . esc_attr($value) . ';border-radius:4px;vertical-align:middle;"></span>';
                echo ' <span style="vertical-align:middle;">' . esc_html($value) . '</span>';
                break;

            case 'wp-editor':
            case 'textarea':
                echo wp_kses_post($value);
                break;

            case 'url':
                echo '<a href="' . esc_url($value) . '" target="_blank">' . esc_html($value) . '</a>';
                break;

            case 'email':
                echo '<a href="mailto:' . esc_attr($value) . '">' . esc_html($value) . '</a>';
                break;

            default:
                echo esc_html($value);
                break;
        }
    }

    /**
     * Widget admin form
     */
    public function form($instance) {
        $fields = $this->widget_config['fields'] ?? array();
        $defaults = $this->widget_config['defaults'] ?? array();

        $instance = wp_parse_args($instance, $defaults);

        $title = !empty($instance['title']) ? $instance['title'] : '';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_html_e('Title:', 'persian-framework'); ?>
            </label>
            <input class="widefat"
                   id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>"
                   type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>

        <?php
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('option_key')); ?>">
                <?php esc_html_e('Framework Option Key (optional):', 'persian-framework'); ?>
            </label>
            <input class="widefat"
                   id="<?php echo esc_attr($this->get_field_id('option_key')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('option_key')); ?>"
                   type="text"
                   value="<?php echo esc_attr($instance['option_key'] ?? ''); ?>"
                   placeholder="<?php esc_attr_e('e.g., header_text, footer_logo, etc.', 'persian-framework'); ?>">
            <small style="color:#94a3b8;display:block;margin-top:4px;">
                <?php esc_html_e('If set, the widget will display the value from framework options.', 'persian-framework'); ?>
            </small>
        </p>

        <hr style="margin:16px 0;border-color:#e8edf4;">

        <?php
        if (!empty($fields)) {
            echo '<div class="pf-widget-fields">';
            PersianFramework_Widget::render_fields($fields, $instance);
            echo '</div>';
        }
    }

    /**
     * Save widget settings
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $fields = $this->widget_config['fields'] ?? array();

        $instance['title'] = isset($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        $instance['option_key'] = isset($new_instance['option_key']) ? sanitize_text_field($new_instance['option_key']) : '';

        foreach ($fields as $field) {
            $field_id = $field['id'];
            if (isset($new_instance[$field_id])) {
                $instance[$field_id] = $this->sanitize_field_value($new_instance[$field_id], $field);
            }
        }

        return $instance;
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
            case 'wp-editor':
            case 'textarea':
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
}

/**
 * Helper function to register a widget easily
 */
function pf_register_widget($id, $name, $description, $fields = array(), $defaults = array()) {
    return PersianFramework_Widget::get_instance()->register_widget($id, $name, $description, $fields, $defaults);
}