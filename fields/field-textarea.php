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

        <div class="pf-field-wrapper pf-field-textarea" <?php echo $required_attributes; ?>>
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
                      <?php echo $required; ?>><?php echo esc_textarea($value); ?></textarea>

            <?php if (isset($this->field['desc'])): ?>
                <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php
        $this->enqueue_scripts();
    }

    private function enqueue_scripts() {
        static $enqueued = false;
        if (!$enqueued) {
            ?>
            <style>
                .pf-textarea-input {
                    width: 100%;
                    min-height: 100px;
                    resize: vertical;
                    font-family: inherit;
                }
            </style>
            <?php
            $enqueued = true;
        }
    }
}