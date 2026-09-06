<?php
/**
 * Persian Framework - Number Field
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Number {

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
        $min = isset($this->field['min']) ? $this->field['min'] : '';
        $max = isset($this->field['max']) ? $this->field['max'] : '';
        $step = isset($this->field['step']) ? $this->field['step'] : 1;
        $placeholder = isset($this->field['placeholder']) ? $this->field['placeholder'] : '';
        $required = isset($this->field['required']) && $this->field['required'] ? 'required' : '';
        ?>

        <div class="pf-field-wrapper pf-field-number">
            <?php if (isset($this->field['title'])): ?>
                <label for="<?php echo esc_attr($id); ?>" class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if ($required): ?>
                        <span class="pf-required">*</span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-number-wrapper">
                <button type="button" class="pf-number-btn pf-number-minus">−</button>
                <input type="number"
                       id="<?php echo esc_attr($id); ?>"
                       name="<?php echo esc_attr($name); ?>"
                       value="<?php echo esc_attr($value); ?>"
                       min="<?php echo esc_attr($min); ?>"
                       max="<?php echo esc_attr($max); ?>"
                       step="<?php echo esc_attr($step); ?>"
                       placeholder="<?php echo esc_attr($placeholder); ?>"
                       class="pf-field-input pf-number-input"
                        <?php echo wp_kses_data($required); ?> />
                <button type="button" class="pf-number-btn pf-number-plus">+</button>
            </div>

            <?php if (isset($this->field['desc'])): ?>
                <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php
        $this->enqueue_scripts();
    }

    private function enqueue_scripts() {
        static $pf_number_enqueued = false;

        if (!$pf_number_enqueued) {
            ?>
            <style>
                .pf-number-wrapper {
                    display: flex;
                    align-items: center;
                    gap: 6px;
                }
                .pf-number-btn {
                    width: 38px;
                    height: 38px;
                    border: 2px solid #e8edf4;
                    border-radius: 8px;
                    background: #fafbfc;
                    cursor: pointer;
                    font-size: 18px;
                    font-weight: 700;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: #1a2332;
                    transition: 0.2s ease;
                }
                .pf-number-btn:hover {
                    border-color: #6366f1;
                    color: #6366f1;
                }
                .pf-number-input {
                    width: 120px;
                    text-align: center;
                }
                body.dark-mode .pf-number-btn {
                    background: #0f172a;
                    border-color: #334155;
                    color: #e2e8f0;
                }
            </style>

            <script>
                (function($) {
                    'use strict';

                    $(document).on('click', '.pf-number-btn', function() {
                        var $input = $(this).closest('.pf-number-wrapper').find('.pf-number-input');
                        var current = parseFloat($input.val()) || 0;
                        var step = parseFloat($input.attr('step')) || 1;
                        var min = parseFloat($input.attr('min')) || 0;
                        var max = parseFloat($input.attr('max')) || 999999;

                        if ($(this).hasClass('pf-number-plus')) {
                            current = Math.min(current + step, max);
                        } else {
                            current = Math.max(current - step, min);
                        }

                        $input.val(current).trigger('change');
                    });

                })(jQuery);
            </script>
            <?php
            $pf_number_enqueued = true;
        }
    }

    public function sanitize($value) {
        $value = wp_unslash($value);

        if (!is_numeric($value)) {
            return isset($this->field['default']) ? $this->field['default'] : 0;
        }

        $value = floatval($value);

        if (isset($this->field['min']) && $value < $this->field['min']) {
            $value = $this->field['min'];
        }

        if (isset($this->field['max']) && $value > $this->field['max']) {
            $value = $this->field['max'];
        }

        return $value;
    }
}