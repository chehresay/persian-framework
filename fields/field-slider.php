<?php
/**
 * Persian Framework - Slider Field
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Slider {

    private $field;
    private $value;

    public function __construct($field, $value = null) {
        $this->field = $field;
        $this->value = $value;
    }

    public function render() {
        $id = isset($this->field['id']) ? $this->field['id'] : '';
        $name = isset($this->field['name']) ? $this->field['name'] : $id;
        $value = $this->value !== null ? $this->value : (isset($this->field['default']) ? $this->field['default'] : 0);
        $min = isset($this->field['min']) ? $this->field['min'] : 0;
        $max = isset($this->field['max']) ? $this->field['max'] : 100;
        $step = isset($this->field['step']) ? $this->field['step'] : 1;
        $unit = isset($this->field['unit']) ? $this->field['unit'] : '';

        $required = isset($this->field['required']) ? $this->field['required'] : false;
        $required_attributes = '';
        if (is_array($required) && class_exists('PersianFramework_Required')) {
            $required_attributes = PersianFramework_Required::get_attributes($required);
        }
        ?>

        <div class="pf-field-wrapper pf-field-slider" <?php echo wp_kses_data($required_attributes); ?>>
            <?php if (isset($this->field['title'])): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-slider-wrapper">
                <input type="range"
                       id="<?php echo esc_attr($id); ?>"
                       name="<?php echo esc_attr($name); ?>"
                       value="<?php echo esc_attr($value); ?>"
                       min="<?php echo esc_attr($min); ?>"
                       max="<?php echo esc_attr($max); ?>"
                       step="<?php echo esc_attr($step); ?>"
                       class="pf-slider-input" />

                <span class="pf-slider-value">
                    <?php echo esc_html($value); ?>
                    <?php if ($unit): ?>
                        <span class="pf-slider-unit"><?php echo esc_html($unit); ?></span>
                    <?php endif; ?>
                </span>
            </div>

            <?php if (isset($this->field['desc'])): ?>
                <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php
        $this->enqueue_scripts();
    }

    private function enqueue_scripts() {
        static $pf_slider_enqueued = false;

        if (!$pf_slider_enqueued) {
            ?>
            <style>
                .pf-slider-wrapper {
                    display: flex;
                    align-items: center;
                    gap: 16px;
                    padding: 4px 0;
                }
                .pf-slider-input {
                    flex: 1;
                    height: 6px;
                    -webkit-appearance: none;
                    background: #e8edf4;
                    border-radius: 3px;
                    outline: none;
                }
                .pf-slider-input::-webkit-slider-thumb {
                    -webkit-appearance: none;
                    width: 18px;
                    height: 18px;
                    border-radius: 50%;
                    background: #6366f1;
                    cursor: pointer;
                }
                .pf-slider-input::-moz-range-thumb {
                    width: 18px;
                    height: 18px;
                    border-radius: 50%;
                    background: #6366f1;
                    cursor: pointer;
                    border: none;
                }
                .pf-slider-value {
                    font-size: 16px;
                    font-weight: 700;
                    color: #1a2332;
                    min-width: 50px;
                    text-align: center;
                }
                .pf-slider-unit {
                    font-size: 12px;
                    font-weight: 400;
                    color: #94a3b8;
                }
                body.dark-mode .pf-slider-input {
                    background: #334155;
                }
                body.dark-mode .pf-slider-value {
                    color: #e2e8f0;
                }
            </style>

            <script>
                (function($) {
                    'use strict';

                    $(document).on('input', '.pf-slider-input', function() {
                        var $wrapper = $(this).closest('.pf-slider-wrapper');
                        var $value = $wrapper.find('.pf-slider-value');
                        var val = $(this).val();

                        $value.html(val + ' <span class="pf-slider-unit">' + ($value.find('.pf-slider-unit').text() || '') + '</span>');

                        var fieldId = $(this).attr('id');
                        $(document).trigger('pf-slider-change', [fieldId, val]);
                    });

                })(jQuery);
            </script>
            <?php
            $pf_slider_enqueued = true;
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