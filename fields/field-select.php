<?php
/**
 * Persian Framework - Select Field
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Select {

    private $field;
    private $value;

    public function __construct($field, $value = null) {
        $this->field = $field;
        $this->value = $value;
    }

    public function render() {
        $id = isset($this->field['id']) ? $this->field['id'] : '';
        $name = isset($this->field['name']) ? $this->field['name'] : $id;
        $value = $this->value !== null ? $this->value : (isset($this->field['default']) ? $this->field['default'] : '');
        $options = isset($this->field['options']) ? $this->field['options'] : array();
        $placeholder = isset($this->field['placeholder']) ? $this->field['placeholder'] : esc_html__('Select an option', 'persian-framework');
        $multiple = isset($this->field['multiple']) && $this->field['multiple'] ? 'multiple' : '';

        $data_type = isset($this->field['data']) ? $this->field['data'] : '';
        $args = isset($this->field['args']) ? $this->field['args'] : array();

        $is_repeater = isset($this->field['is_repeater']) && $this->field['is_repeater'] === true;

        if (!empty($data_type) && empty($options) && !$is_repeater) {
            $options = $this->get_dynamic_options($data_type, $args);
        }

        if (empty($options) && isset($this->field['options_callback']) && is_callable($this->field['options_callback'])) {
            $options = call_user_func($this->field['options_callback'], $this->field, $value);
        }

        if (!is_array($options)) {
            $options = array();
        }

        // Handle required attribute properly
        $required = isset($this->field['required']) ? $this->field['required'] : false;
        $required_attributes = '';
        $required_html = '';

        if (is_array($required) && class_exists('PersianFramework_Required')) {
            $required_attributes = PersianFramework_Required::get_attributes($required);
            if (is_array($required_attributes)) {
                $required_html = implode(' ', array_map(function($key, $value) {
                    return $key . '="' . esc_attr($value) . '"';
                }, array_keys($required_attributes), $required_attributes));
            } else {
                $required_html = $required_attributes;
            }
        } elseif (is_string($required) && !empty($required)) {
            $required_html = $required;
        } elseif ($required === true) {
            $required_html = 'required';
        }

        // Ensure value is properly handled for multiple select
        if ($multiple) {
            if (!is_array($value)) {
                $value = !empty($value) ? array($value) : array();
            }
        } else {
            if (is_array($value)) {
                $value = !empty($value) ? reset($value) : '';
            }
        }
        ?>

        <div class="pf-field-wrapper pf-field-select" <?php echo wp_kses_data($required_attributes); ?>>
            <?php if (isset($this->field['title'])): ?>
                <label for="<?php echo esc_attr($id); ?>" class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if ($required === true || (is_array($required) && !empty($required))): ?>
                        <span class="pf-required">*</span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <select id="<?php echo esc_attr($id); ?>"
                    name="<?php echo esc_attr($name); ?><?php echo $multiple ? '[]' : ''; ?>"
                    class="pf-field-input"
                    <?php echo wp_kses_data($required_html); ?>
                    <?php echo wp_kses_data($multiple); ?>>

                <?php if (!$multiple): ?>
                    <option value=""><?php echo esc_html($placeholder); ?></option>
                <?php endif; ?>

                <?php foreach ($options as $key => $label): ?>
                    <?php
                    $is_selected = false;
                    if ($multiple) {
                        $is_selected = in_array((string) $key, $value, true);
                    } else {
                        $is_selected = ((string) $value === (string) $key);
                    }
                    ?>
                    <option value="<?php echo esc_attr($key); ?>" <?php echo $is_selected ? 'selected="selected"' : ''; ?>>
                        <?php echo esc_html($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if (isset($this->field['desc'])): ?>
                <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php
    }

    /**
     * Get dynamic options based on data type
     */
    private function get_dynamic_options($data_type, $args) {
        $options = array();

        switch ($data_type) {
            case 'terms':
            case 'categories':
                $taxonomy = isset($args['taxonomy']) ? $args['taxonomy'] : 'category';
                $hide_empty = isset($args['hide_empty']) ? $args['hide_empty'] : false;
                $parent = isset($args['parent']) ? $args['parent'] : 0;

                $terms = get_terms(array(
                        'taxonomy' => $taxonomy,
                        'hide_empty' => $hide_empty,
                        'parent' => $parent,
                ));

                if (!is_wp_error($terms) && !empty($terms)) {
                    foreach ($terms as $term) {
                        $options[$term->term_id] = $term->name;
                    }
                }
                break;

            case 'posts':
            case 'pages':
                $post_type = isset($args['post_type']) ? $args['post_type'] : 'post';
                $posts_per_page = isset($args['posts_per_page']) ? $args['posts_per_page'] : -1;

                $posts = get_posts(array(
                        'post_type' => $post_type,
                        'posts_per_page' => $posts_per_page,
                        'post_status' => 'publish',
                ));

                if (!empty($posts)) {
                    foreach ($posts as $post) {
                        $options[$post->ID] = $post->post_title;
                    }
                }
                break;

            case 'users':
                $role = isset($args['role']) ? $args['role'] : '';

                $users = get_users(array(
                        'role' => $role,
                ));

                if (!empty($users)) {
                    foreach ($users as $user) {
                        $options[$user->ID] = $user->display_name . ' (' . $user->user_email . ')';
                    }
                }
                break;

            case 'menus':
                $menus = wp_get_nav_menus();
                if (!empty($menus)) {
                    foreach ($menus as $menu) {
                        $options[$menu->term_id] = $menu->name;
                    }
                }
                break;

            case 'sidebar':
                global $wp_registered_sidebars;
                if (!empty($wp_registered_sidebars)) {
                    foreach ($wp_registered_sidebars as $sidebar) {
                        $options[$sidebar['id']] = $sidebar['name'];
                    }
                }
                break;

            case 'callback':
                if (isset($args['callback']) && is_callable($args['callback'])) {
                    $options = call_user_func($args['callback']);
                }
                break;

            default:
                break;
        }

        return $options;
    }

    public function sanitize($value) {
        $options = isset($this->field['options']) ? array_keys($this->field['options']) : array();
        $multiple = isset($this->field['multiple']) && $this->field['multiple'];

        if ($multiple) {
            $value = (array) $value;
            $sanitized = array();
            foreach ($value as $item) {
                $item = sanitize_text_field($item);
                if (in_array($item, $options, true)) {
                    $sanitized[] = $item;
                }
            }
            return $sanitized;
        }

        $value = sanitize_text_field($value);
        return in_array($value, $options, true) ? $value : '';
    }
}