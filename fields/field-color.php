<?php
/**
 * Persian Framework - Color Field
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Color {

    private $field;
    private $value;

    public function __construct($field, $value = null) {
        $this->field = $field;
        $this->value = $value;
    }

    public function render() {
        $id = isset($this->field['id']) ? $this->field['id'] : '';
        $name = isset($this->field['name']) ? $this->field['name'] : $id;
        $value = $this->value !== null ? $this->value : (isset($this->field['default']) ? $this->field['default'] : '#6366f1');
        $alpha = isset($this->field['alpha']) && $this->field['alpha'];

        $required = isset($this->field['required']) ? $this->field['required'] : false;
        $required_attributes = '';
        if (is_array($required) && class_exists('PersianFramework_Required')) {
            $required_attributes = PersianFramework_Required::get_attributes($required);
        }
        ?>

        <div class="pf-field-wrapper pf-field-color" <?php echo wp_kses_data($required_attributes); ?>>
            <?php if (isset($this->field['title'])): ?>
                <label for="<?php echo esc_attr($id); ?>" class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-color-wrapper">
                <input type="color"
                       id="<?php echo esc_attr($id); ?>"
                       name="<?php echo esc_attr($name); ?>"
                       value="<?php echo esc_attr($value); ?>"
                       class="pf-color-picker" />
                <input type="text"
                       class="pf-color-hex"
                       value="<?php echo esc_attr($value); ?>"
                       data-target="<?php echo esc_attr($id); ?>" />
            </div>

            <?php if (isset($this->field['desc'])): ?>
                <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php
        $this->enqueue_scripts();
    }

    private function enqueue_scripts() {
        static $pf_color_enqueued = false;
        if (!$pf_color_enqueued) {
            ?>
            <style>
                .pf-color-wrapper {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    margin-top: 8px;
                }

                .pf-color-picker {
                    width: 50px;
                    height: 50px;
                    padding: 4px;
                    border: 2px solid #e8edf4;
                    border-radius: 10px;
                    cursor: pointer;
                    background: none;
                    flex-shrink: 0;
                    transition: border-color 0.2s ease;
                }
                .pf-color-picker:hover {
                    border-color: #6366f1;
                }
                .pf-color-picker::-webkit-color-swatch-wrapper {
                    padding: 2px;
                }
                .pf-color-picker::-webkit-color-swatch {
                    border: none;
                    border-radius: 6px;
                }
                .pf-color-picker::-moz-color-swatch {
                    border: none;
                    border-radius: 6px;
                }

                body.dark-mode .pf-color-picker {
                    border-color: #334155;
                }

                .pf-color-hex {
                    padding: 10px 14px;
                    border: 2px solid #e8edf4;
                    border-radius: 8px;
                    font-size: 14px;
                    font-family: 'Courier New', monospace;
                    background: #fafbfc;
                    width: 120px;
                    transition: all 0.2s ease;
                }
                .pf-color-hex:focus {
                    border-color: #6366f1;
                    outline: none;
                    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
                }

                body.dark-mode .pf-color-hex {
                    background: #0f172a;
                    border-color: #334155;
                    color: #e2e8f0;
                }
            </style>

            <script>
                (function($) {
                    'use strict';

                    $(document).on('input', '.pf-color-picker', function() {
                        var $wrapper = $(this).closest('.pf-color-wrapper');
                        var $hex = $wrapper.find('.pf-color-hex');
                        var val = $(this).val();
                        $hex.val(val);
                    });

                    $(document).on('input', '.pf-color-hex', function() {
                        var $wrapper = $(this).closest('.pf-color-wrapper');
                        var $picker = $wrapper.find('.pf-color-picker');
                        var val = $(this).val();
                        if (/^#[0-9a-f]{6}$/i.test(val) || /^#[0-9a-f]{3}$/i.test(val)) {
                            $picker.val(val);
                        }
                    });

                })(jQuery);
            </script>
            <?php
            $pf_color_enqueued = true;
        }
    }

    public function sanitize($value) {
        $value = wp_unslash($value);
        $value = sanitize_hex_color($value);
        if (empty($value)) {
            return isset($this->field['default']) ? $this->field['default'] : '#6366f1';
        }
        return $value;
    }
}