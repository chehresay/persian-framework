<?php
/**
 * Persian Framework - Dimensions Field
 * Control for width, height, min/max dimensions
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Dimensions {

    private $field;
    private $value;

    public function __construct($field, $value = null) {
        $this->field = $field;
        $this->value = $value;
    }

    public function render() {
        $id = isset($this->field['id']) ? $this->field['id'] : '';
        $name = isset($this->field['name']) ? $this->field['name'] : $id;

        $defaults = array(
                'width' => '',
                'height' => '',
                'min-width' => '',
                'max-width' => '',
                'min-height' => '',
                'max-height' => '',
                'unit' => 'px'
        );

        $value = wp_parse_args(
                is_array($this->value) ? $this->value : array(),
                $defaults
        );

        $basic_keys = array(
                'width' => esc_html__('Width', 'persian-framework'),
                'height' => esc_html__('Height', 'persian-framework')
        );

        $advanced_keys = array(
                'min-width' => esc_html__('Min Width', 'persian-framework'),
                'max-width' => esc_html__('Max Width', 'persian-framework'),
                'min-height' => esc_html__('Min Height', 'persian-framework'),
                'max-height' => esc_html__('Max Height', 'persian-framework')
        );

        $show_advanced = isset($this->field['advanced']) && $this->field['advanced'];

        ?>
        <div class="pf-field-wrapper pf-field-dimensions">
            <?php if (isset($this->field['title'])): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-dimensions-container">
                <div class="pf-dimensions-basic">
                    <?php foreach ($basic_keys as $key => $label): ?>
                        <label class="pf-dimensions-label">
                            <span class="pf-dimensions-label-text"><?php echo esc_html($label); ?></span>
                            <input type="number"
                                   step="any"
                                   min="0"
                                   name="<?php echo esc_attr($name); ?>[<?php echo esc_attr($key); ?>]"
                                   value="<?php echo esc_attr($value[$key]); ?>"
                                   class="pf-dimensions-input" />
                        </label>
                    <?php endforeach; ?>
                </div>

                <?php if ($show_advanced): ?>
                    <div class="pf-dimensions-advanced">
                        <div class="pf-dimensions-advanced-toggle">
                            <button type="button" class="pf-dimensions-toggle-btn">
                                <span class="dashicons dashicons-arrow-down-alt2"></span>
                                <?php esc_html_e('Advanced Dimensions', 'persian-framework'); ?>
                            </button>
                        </div>
                        <div class="pf-dimensions-advanced-fields" style="display:none;">
                            <?php foreach ($advanced_keys as $key => $label): ?>
                                <label class="pf-dimensions-label">
                                    <span class="pf-dimensions-label-text"><?php echo esc_html($label); ?></span>
                                    <input type="number"
                                           step="any"
                                           min="0"
                                           name="<?php echo esc_attr($name); ?>[<?php echo esc_attr($key); ?>]"
                                           value="<?php echo esc_attr($value[$key]); ?>"
                                           class="pf-dimensions-input" />
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="pf-dimensions-unit">
                    <label class="pf-dimensions-label">
                        <span class="pf-dimensions-label-text"><?php esc_html_e('Unit', 'persian-framework'); ?></span>
                        <select name="<?php echo esc_attr($name); ?>[unit]" class="pf-dimensions-unit-select">
                            <?php
                            $units = array('px', '%', 'rem', 'em', 'vw', 'vh', 'auto');
                            foreach ($units as $unit):
                                ?>
                                <option value="<?php echo esc_attr($unit); ?>" <?php selected($value['unit'], $unit); ?>>
                                    <?php echo esc_html($unit); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>
            </div>

            <?php if (isset($this->field['desc'])): ?>
                <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php
        $this->enqueue_scripts();
    }

    private function enqueue_scripts() {
        static $pf_dimensions_enqueued = false;

        if (!$pf_dimensions_enqueued) {
            ?>
            <style>
                .pf-dimensions-container {
                    display: flex;
                    flex-direction: column;
                    gap: 12px;
                    margin-top: 8px;
                    padding: 16px;
                    background: #fafbfc;
                    border: 2px solid #e8edf4;
                    border-radius: 12px;
                }
                body.dark-mode .pf-dimensions-container {
                    background: #0f172a;
                    border-color: #334155;
                }

                .pf-dimensions-basic {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 12px 16px;
                }

                .pf-dimensions-advanced-fields {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 12px 16px;
                    padding-top: 8px;
                    border-top: 1px solid #e8edf4;
                }
                body.dark-mode .pf-dimensions-advanced-fields {
                    border-color: #334155;
                }

                .pf-dimensions-label {
                    display: flex;
                    flex-direction: column;
                    gap: 4px;
                    flex: 1;
                    min-width: 80px;
                }

                .pf-dimensions-label-text {
                    font-size: 12px;
                    font-weight: 600;
                    color: #6b7a8f;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                body.dark-mode .pf-dimensions-label-text {
                    color: #94a3b8;
                }

                .pf-dimensions-input {
                    padding: 8px 12px;
                    border: 2px solid #e8edf4;
                    border-radius: 8px;
                    font-size: 14px;
                    font-family: inherit;
                    background: white;
                    transition: all 0.2s ease;
                    width: 100%;
                }
                body.dark-mode .pf-dimensions-input {
                    background: #1e293b;
                    border-color: #334155;
                    color: #e2e8f0;
                }

                .pf-dimensions-input:focus {
                    border-color: #6366f1;
                    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
                    outline: none;
                }

                .pf-dimensions-unit-select {
                    padding: 8px 12px;
                    border: 2px solid #e8edf4;
                    border-radius: 8px;
                    font-size: 14px;
                    background: white;
                    width: 100%;
                }
                body.dark-mode .pf-dimensions-unit-select {
                    background: #1e293b;
                    border-color: #334155;
                    color: #e2e8f0;
                }

                .pf-dimensions-unit {
                    min-width: 100px;
                    max-width: 150px;
                }

                .pf-dimensions-advanced-toggle {
                    margin: 4px 0;
                }

                .pf-dimensions-toggle-btn {
                    background: none;
                    border: none;
                    color: #6366f1;
                    cursor: pointer;
                    font-size: 13px;
                    font-weight: 600;
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    padding: 4px 0;
                }
                .pf-dimensions-toggle-btn:hover {
                    color: #4f46e5;
                }
                .pf-dimensions-toggle-btn .dashicons {
                    font-size: 16px;
                    width: 16px;
                    height: 16px;
                    transition: transform 0.3s ease;
                }
                .pf-dimensions-toggle-btn.active .dashicons {
                    transform: rotate(180deg);
                }

                @media (max-width: 768px) {
                    .pf-dimensions-basic,
                    .pf-dimensions-advanced-fields {
                        flex-direction: column;
                    }
                    .pf-dimensions-label {
                        min-width: auto;
                    }
                    .pf-dimensions-unit {
                        max-width: 100%;
                    }
                }
            </style>

            <script>
                (function($) {
                    'use strict';

                    $(document).on('click', '.pf-dimensions-toggle-btn', function() {
                        var $btn = $(this);
                        var $fields = $btn.closest('.pf-dimensions-advanced').find('.pf-dimensions-advanced-fields');
                        $fields.slideToggle(300);
                        $btn.toggleClass('active');
                    });

                })(jQuery);
            </script>
            <?php
            $pf_dimensions_enqueued = true;
        }
    }

    public function sanitize($value) {
        if (!is_array($value)) {
            return array(
                    'width' => '',
                    'height' => '',
                    'min-width' => '',
                    'max-width' => '',
                    'min-height' => '',
                    'max-height' => '',
                    'unit' => 'px'
            );
        }

        $sanitized = array();

        $keys = array('width', 'height', 'min-width', 'max-width', 'min-height', 'max-height');
        foreach ($keys as $key) {
            if (isset($value[$key])) {
                $sanitized[$key] = sanitize_text_field($value[$key]);
            }
        }

        if (isset($value['unit'])) {
            $allowed_units = array('px', '%', 'rem', 'em', 'vw', 'vh', 'auto');
            $sanitized['unit'] = in_array($value['unit'], $allowed_units) ? $value['unit'] : 'px';
        }

        return $sanitized;
    }
}