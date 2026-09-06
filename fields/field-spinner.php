<?php
/**
 * Persian Framework - Spinner Field
 * Number input with increment/decrement buttons
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Spinner {

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
        $placeholder = isset($this->field['placeholder']) ? $this->field['placeholder'] : '';
        $required = isset($this->field['required']) && $this->field['required'] ? 'required' : '';
        $prefix = isset($this->field['prefix']) ? $this->field['prefix'] : '';
        $suffix = isset($this->field['suffix']) ? $this->field['suffix'] : '';

        ?>
        <div class="pf-field-wrapper pf-field-spinner">
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

            <div class="pf-spinner-wrapper">
                <?php if ($prefix): ?>
                    <span class="pf-spinner-prefix"><?php echo esc_html($prefix); ?></span>
                <?php endif; ?>

                <div class="pf-spinner-control">
                    <button type="button" class="pf-spinner-btn pf-spinner-minus" data-step="<?php echo esc_attr($step); ?>">
                        <span class="dashicons dashicons-minus"></span>
                    </button>
                    <input type="number"
                           id="<?php echo esc_attr($id); ?>"
                           name="<?php echo esc_attr($name); ?>"
                           value="<?php echo esc_attr($value); ?>"
                           min="<?php echo esc_attr($min); ?>"
                           max="<?php echo esc_attr($max); ?>"
                           step="<?php echo esc_attr($step); ?>"
                           placeholder="<?php echo esc_attr($placeholder); ?>"
                           class="pf-spinner-input"
                            <?php echo wp_kses_data($required); ?> />
                    <button type="button" class="pf-spinner-btn pf-spinner-plus" data-step="<?php echo esc_attr($step); ?>">
                        <span class="dashicons dashicons-plus-alt2"></span>
                    </button>
                </div>

                <?php if ($suffix): ?>
                    <span class="pf-spinner-suffix"><?php echo esc_html($suffix); ?></span>
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
        static $enqueued = false;

        if (!$enqueued) {
            ?>
            <style>
                .pf-spinner-wrapper {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    margin-top: 4px;
                }
                .pf-spinner-prefix,
                .pf-spinner-suffix {
                    font-size: 14px;
                    font-weight: 600;
                    color: #1a2332;
                }
                body.dark-mode .pf-spinner-prefix,
                body.dark-mode .pf-spinner-suffix {
                    color: #e2e8f0;
                }
                .pf-spinner-control {
                    display: flex;
                    align-items: center;
                    border: 2px solid #e8edf4;
                    border-radius: 12px;
                    overflow: hidden;
                    background: white;
                }
                body.dark-mode .pf-spinner-control {
                    border-color: #334155;
                    background: #1e293b;
                }
                .pf-spinner-control:focus-within {
                    border-color: #6366f1;
                    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
                }
                .pf-spinner-btn {
                    background: transparent;
                    border: none;
                    padding: 8px 12px;
                    cursor: pointer;
                    color: #6b7a8f;
                    transition: all 0.2s ease;
                    font-size: 16px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .pf-spinner-btn:hover {
                    background: rgba(99, 102, 241, 0.08);
                    color: #6366f1;
                }
                .pf-spinner-btn:active {
                    transform: scale(0.95);
                }
                body.dark-mode .pf-spinner-btn {
                    color: #94a3b8;
                }
                body.dark-mode .pf-spinner-btn:hover {
                    background: rgba(99, 102, 241, 0.15);
                    color: #818cf8;
                }
                .pf-spinner-btn .dashicons {
                    font-size: 16px;
                    width: 16px;
                    height: 16px;
                }
                .pf-spinner-input {
                    width: 80px;
                    padding: 8px 4px;
                    border: none;
                    border-left: 1px solid #e8edf4;
                    border-right: 1px solid #e8edf4;
                    font-size: 14px;
                    text-align: center;
                    background: transparent;
                    color: #1a2332;
                    -moz-appearance: textfield;
                }
                .pf-spinner-input::-webkit-outer-spin-button,
                .pf-spinner-input::-webkit-inner-spin-button {
                    -webkit-appearance: none;
                    margin: 0;
                }
                .pf-spinner-input:focus {
                    outline: none;
                }
                body.dark-mode .pf-spinner-input {
                    border-color: #334155;
                    color: #e2e8f0;
                }
                .pf-spinner-input:disabled {
                    opacity: 0.5;
                    cursor: not-allowed;
                }

                @media (max-width: 768px) {
                    .pf-spinner-wrapper {
                        flex-wrap: wrap;
                    }
                    .pf-spinner-control {
                        flex: 1;
                    }
                    .pf-spinner-input {
                        flex: 1;
                        width: auto;
                    }
                }
            </style>

            <script>
                (function($) {
                    'use strict';

                    $(document).on('click', '.pf-spinner-btn', function() {
                        var $btn = $(this);
                        var $control = $btn.closest('.pf-spinner-control');
                        var $input = $control.find('.pf-spinner-input');
                        var step = parseFloat($btn.data('step')) || 1;
                        var min = parseFloat($input.attr('min')) || 0;
                        var max = parseFloat($input.attr('max')) || 100;
                        var current = parseFloat($input.val()) || 0;

                        if ($btn.hasClass('pf-spinner-plus')) {
                            var newVal = Math.min(current + step, max);
                        } else {
                            var newVal = Math.max(current - step, min);
                        }

                        $input.val(newVal).trigger('change');
                        $input.trigger('input');
                    });

                    // Validate on input change
                    $(document).on('change', '.pf-spinner-input', function() {
                        var $input = $(this);
                        var min = parseFloat($input.attr('min')) || 0;
                        var max = parseFloat($input.attr('max')) || 100;
                        var val = parseFloat($input.val());

                        if (isNaN(val)) {
                            $input.val(min);
                            return;
                        }

                        if (val < min) {
                            $input.val(min);
                        } else if (val > max) {
                            $input.val(max);
                        }
                    });

                })(jQuery);
            </script>
            <?php
            $enqueued = true;
        }
    }
}