<?php
/**
 * Persian Framework - URL Field
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Url {

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
        $placeholder = isset($this->field['placeholder']) ? $this->field['placeholder'] : 'https://example.com';
        $required = isset($this->field['required']) && $this->field['required'] ? 'required' : '';
        ?>

        <div class="pf-field-wrapper pf-field-url">
            <?php if (isset($this->field['title'])): ?>
                <label for="<?php echo esc_attr($id); ?>" class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if ($required): ?>
                        <span class="pf-required">*</span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <input type="url"
                   id="<?php echo esc_attr($id); ?>"
                   name="<?php echo esc_attr($name); ?>"
                   value="<?php echo esc_attr($value); ?>"
                   placeholder="<?php echo esc_attr($placeholder); ?>"
                   class="pf-field-input"
                    <?php echo wp_kses_data($required); ?> />

            <?php if (isset($this->field['desc'])): ?>
                <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php
    }

    public function sanitize($value) {
        $value = wp_unslash($value);
        return esc_url_raw($value);
    }
}