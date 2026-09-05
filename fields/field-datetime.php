<?php
/**
 * Persian Framework - DateTime Field
 * HTML5 datetime-local input
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Datetime {

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
        $placeholder = isset($this->field['placeholder']) ? $this->field['placeholder'] : 'YYYY-MM-DDTHH:MM';
        $required = isset($this->field['required']) && $this->field['required'] ? 'required' : '';
        $step = isset($this->field['step']) ? $this->field['step'] : 60;
        $min = isset($this->field['min']) ? $this->field['min'] : '';
        $max = isset($this->field['max']) ? $this->field['max'] : '';

        if (!empty($value) && is_numeric($value)) {
            $value = gmdate('Y-m-d\TH:i', intval($value));
        }

        ?>
        <div class="pf-field-wrapper pf-field-datetime">
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

            <div class="pf-datetime-wrapper">
                <input type="datetime-local"
                       id="<?php echo esc_attr($id); ?>"
                       name="<?php echo esc_attr($name); ?>"
                       value="<?php echo esc_attr($value); ?>"
                       placeholder="<?php echo esc_attr($placeholder); ?>"
                       class="pf-field-input pf-datetime-input"
                       step="<?php echo esc_attr($step); ?>"
                        <?php echo $min ? 'min="' . esc_attr($min) . '"' : ''; ?>
                        <?php echo $max ? 'max="' . esc_attr($max) . '"' : ''; ?>
                        <?php echo wp_kses_data($required); ?> />

                <?php if (isset($this->field['date_format'])): ?>
                    <span class="pf-datetime-format"><?php echo esc_html($this->field['date_format']); ?></span>
                <?php endif; ?>
            </div>

            <?php if (isset($this->field['desc'])): ?>
                <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php
        $this->enqueue_scripts();
    }

    private function enqueue_scripts() {
        static $pf_datetime_enqueued = false;

        if (!$pf_datetime_enqueued) {
            ?>
            <style>
                .pf-datetime-wrapper {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    flex-wrap: wrap;
                }

                .pf-datetime-input {
                    padding: 14px 18px;
                    border: 2px solid #e8edf4;
                    border-radius: 12px;
                    font-size: 14px;
                    font-family: inherit;
                    transition: all 0.2s ease;
                    background: #fafbfc;
                    color: #1a2332;
                    width: 100%;
                    max-width: 300px;
                }
                body.dark-mode .pf-datetime-input {
                    background: #0f172a;
                    border-color: #334155;
                    color: #e2e8f0;
                }

                .pf-datetime-input:focus {
                    border-color: #6366f1;
                    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
                    outline: none;
                    background: white;
                }
                body.dark-mode .pf-datetime-input:focus {
                    background: #1e293b;
                    border-color: #818cf8;
                }

                .pf-datetime-format {
                    font-size: 12px;
                    color: #94a3b8;
                    font-family: monospace;
                    padding: 4px 8px;
                    background: #f8f9fa;
                    border-radius: 4px;
                }
                body.dark-mode .pf-datetime-format {
                    background: #0f172a;
                    color: #94a3b8;
                }

                @media (max-width: 768px) {
                    .pf-datetime-input {
                        max-width: 100%;
                    }
                }
            </style>
            <?php
            $pf_datetime_enqueued = true;
        }
    }

    public function sanitize($value) {
        $value = wp_unslash($value);
        return sanitize_text_field($value);
    }
}