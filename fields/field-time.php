<?php
/**
 * Persian Framework - Time Field
 * HTML5 time input
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Time {

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
        $placeholder = isset($this->field['placeholder']) ? $this->field['placeholder'] : 'HH:MM';
        $required = isset($this->field['required']) && $this->field['required'] ? 'required' : '';
        $step = isset($this->field['step']) ? $this->field['step'] : 60; // seconds
        $min = isset($this->field['min']) ? $this->field['min'] : '';
        $max = isset($this->field['max']) ? $this->field['max'] : '';

        // Format value for time input (HH:MM)
        if (!empty($value) && strpos($value, ':') === false) {
            // Convert timestamp to time
            $value = gmdate('H:i', intval($value));
        }

        ?>
        <div class="pf-field-wrapper pf-field-time">
            <?php if (isset($this->field['title'])): ?>
                <label for="<?php echo esc_attr($id); ?>" class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                    <?php if ($required): ?>
                        <span class="pf-required">*</span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <input type="time"
                   id="<?php echo esc_attr($id); ?>"
                   name="<?php echo esc_attr($name); ?>"
                   value="<?php echo esc_attr($value); ?>"
                   placeholder="<?php echo esc_attr($placeholder); ?>"
                   class="pf-field-input pf-time-input"
                   step="<?php echo esc_attr($step); ?>"
                <?php echo $min ? 'min="' . esc_attr($min) . '"' : ''; ?>
                <?php echo $max ? 'max="' . esc_attr($max) . '"' : ''; ?>
                <?php echo wp_kses_data($required); ?> />

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
                .pf-time-input {
                    padding: 14px 18px;
                    border: 2px solid #e8edf4;
                    border-radius: 12px;
                    font-size: 14px;
                    font-family: inherit;
                    transition: all 0.2s ease;
                    background: #fafbfc;
                    color: #1a2332;
                    width: 100%;
                    max-width: 200px;
                }
                .pf-time-input:focus {
                    border-color: #6366f1;
                    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
                    outline: none;
                    background: white;
                }
                body.dark-mode .pf-time-input {
                    background: #0f172a;
                    border-color: #334155;
                    color: #e2e8f0;
                }
                body.dark-mode .pf-time-input:focus {
                    background: #1e293b;
                    border-color: #818cf8;
                }
                @media (max-width: 768px) {
                    .pf-time-input {
                        max-width: 100%;
                    }
                }
            </style>
            <?php
            $enqueued = true;
        }
    }
}