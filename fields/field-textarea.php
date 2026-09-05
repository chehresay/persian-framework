<?php
/**
 * Persian Framework - Textarea Field
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Textarea {

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
        $rows = isset($this->field['rows']) ? $this->field['rows'] : 5;
        $cols = isset($this->field['cols']) ? $this->field['cols'] : 50;
        $required = isset($this->field['required']) && $this->field['required'] ? 'required' : '';

        $required_attributes = '';
        if (is_array($required) && class_exists('PersianFramework_Required')) {
            $required_attributes = PersianFramework_Required::get_attributes($required);
        }
        ?>

        <div class="pf-field-wrapper pf-field-textarea" <?php echo wp_kses_data($required_attributes); ?>>
            <?php if (isset($this->field['title'])): ?>
                <label for="<?php echo esc_attr($id); ?>" class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if ($required): ?>
                        <span class="pf-required">*</span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <textarea id="<?php echo esc_attr($id); ?>"
                      name="<?php echo esc_attr($name); ?>"
                      placeholder="<?php echo esc_attr($placeholder); ?>"
                      rows="<?php echo esc_attr($rows); ?>"
                      cols="<?php echo esc_attr($cols); ?>"
                      class="pf-field-input pf-textarea-input"
                      <?php echo wp_kses_data($required); ?>><?php echo esc_textarea($value); ?></textarea>

            <?php if (isset($this->field['desc'])): ?>
                <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php
        $this->enqueue_scripts();
    }

    private function enqueue_scripts() {
        static $pf_textarea_enqueued = false;
        if (!$pf_textarea_enqueued) {
            ?>
            <style>
                .pf-textarea-input {
                    width: 100%;
                    min-height: 100px;
                    resize: vertical;
                    font-family: inherit;
                    padding: 12px 16px;
                    border: 2px solid #e8edf4;
                    border-radius: 10px;
                    background: #fafbfc;
                    color: #1a2332;
                    transition: all 0.2s ease;
                    font-size: 14px;
                    line-height: 1.6;
                }
                body.dark-mode .pf-textarea-input {
                    background: #0f172a;
                    border-color: #334155;
                    color: #e2e8f0;
                }
                .pf-textarea-input:focus {
                    border-color: #6366f1;
                    outline: none;
                    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
                    background: white;
                }
                body.dark-mode .pf-textarea-input:focus {
                    background: #1e293b;
                }
            </style>
            <?php
            $pf_textarea_enqueued = true;
        }
    }

    public function sanitize($value) {
        $value = wp_unslash($value);
        return sanitize_textarea_field($value);
    }
}