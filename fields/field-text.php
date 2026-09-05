<?php
/**
 * Persian Framework - Text Field
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Text {

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
        $placeholder = isset($this->field['placeholder']) ? $this->field['placeholder'] : '';
        $dir = isset($this->field['dir']) ? $this->field['dir'] : 'rtl';

        $required = isset($this->field['required']) ? $this->field['required'] : false;
        $is_html_required = ($required === true || $required === 1 || $required === '1') ? 'required' : '';

        $required_attributes = '';
        if (is_array($required) && class_exists('PersianFramework_Required')) {
            $required_attributes = PersianFramework_Required::get_attributes($required);
        }
        ?>

        <div class="pf-field-wrapper pf-field-text" <?php echo wp_kses_data($required_attributes); ?>>
            <?php if (isset($this->field['title'])): ?>
                <label for="<?php echo esc_attr($id); ?>" class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if ($is_html_required): ?>
                        <span class="pf-required">*</span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <input type="text"
                   id="<?php echo esc_attr($id); ?>"
                   name="<?php echo esc_attr($name); ?>"
                   value="<?php echo esc_attr($value); ?>"
                   dir="<?php echo esc_attr($dir); ?>"
                   placeholder="<?php echo esc_attr($placeholder); ?>"
                   class="pf-field-input"
                    <?php echo wp_kses_data($is_html_required); ?> />

            <?php if (isset($this->field['desc'])): ?>
                <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php
    }

    public function sanitize($value) {
        $value = wp_unslash($value);
        return sanitize_text_field($value);
    }
}