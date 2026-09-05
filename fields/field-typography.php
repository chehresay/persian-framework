<?php
/**
 * Persian Framework - Typography Field
 * Complete typography control with font family, size, weight, style, color, etc.
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Typography {

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
                'font-family' => '',
                'font-size' => '',
                'font-weight' => '400',
                'font-style' => 'normal',
                'line-height' => '',
                'letter-spacing' => '',
                'text-align' => '',
                'text-transform' => 'none',
                'color' => '#111827',
                'unit' => 'px'
        );

        $value = wp_parse_args(
                is_array($this->value) ? $this->value : array(),
                $defaults
        );

        $fonts = isset($this->field['fonts']) ? $this->field['fonts'] : array(
                'inherit' => esc_html__('Inherit', 'persian-framework'),
                'Arial, sans-serif' => 'Arial',
                'Helvetica, sans-serif' => 'Helvetica',
                'Tahoma, sans-serif' => 'Tahoma',
                'Verdana, sans-serif' => 'Verdana',
                'Georgia, serif' => 'Georgia',
                'Times New Roman, serif' => 'Times New Roman',
                'Courier New, monospace' => 'Courier New',
                'Vazirmatn, sans-serif' => 'Vazirmatn',
                'IRANSans, sans-serif' => 'IRANSans',
                'Yekan, sans-serif' => 'Yekan',
        );

        $weights = array(
                '100' => '100 ' . esc_html__('Thin', 'persian-framework'),
                '200' => '200 ' . esc_html__('Extra Light', 'persian-framework'),
                '300' => '300 ' . esc_html__('Light', 'persian-framework'),
                '400' => '400 ' . esc_html__('Normal', 'persian-framework'),
                '500' => '500 ' . esc_html__('Medium', 'persian-framework'),
                '600' => '600 ' . esc_html__('Semi Bold', 'persian-framework'),
                '700' => '700 ' . esc_html__('Bold', 'persian-framework'),
                '800' => '800 ' . esc_html__('Extra Bold', 'persian-framework'),
                '900' => '900 ' . esc_html__('Black', 'persian-framework'),
        );

        $styles = array(
                'normal' => esc_html__('Normal', 'persian-framework'),
                'italic' => esc_html__('Italic', 'persian-framework'),
                'oblique' => esc_html__('Oblique', 'persian-framework'),
        );

        $aligns = array(
                '' => esc_html__('Default', 'persian-framework'),
                'start' => esc_html__('Start', 'persian-framework'),
                'center' => esc_html__('Center', 'persian-framework'),
                'end' => esc_html__('End', 'persian-framework'),
                'justify' => esc_html__('Justify', 'persian-framework'),
        );

        $transforms = array(
                'none' => esc_html__('None', 'persian-framework'),
                'uppercase' => esc_html__('Uppercase', 'persian-framework'),
                'lowercase' => esc_html__('Lowercase', 'persian-framework'),
                'capitalize' => esc_html__('Capitalize', 'persian-framework'),
        );

        $units = array('px', 'em', 'rem', '%', 'pt', 'vw', 'vh');

        $preview_text = isset($this->field['preview']) && is_string($this->field['preview'])
                ? $this->field['preview']
                : esc_html__('Typography preview', 'persian-framework');

        ?>
        <div class="pf-field-wrapper pf-field-typography">
            <?php if (isset($this->field['title'])): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-typography-container">
                <!-- Font Family -->
                <div class="pf-typography-row pf-typography-font-family">
                    <label class="pf-typography-label">
                        <span class="pf-typography-label-text"><?php esc_html_e('Font Family', 'persian-framework'); ?></span>
                        <select name="<?php echo esc_attr($name); ?>[font-family]" class="pf-typography-select">
                            <?php foreach ($fonts as $key => $label): ?>
                                <option value="<?php echo esc_attr($key); ?>" <?php selected($value['font-family'], $key); ?>>
                                    <?php echo esc_html($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>

                <!-- Font Size & Line Height & Letter Spacing -->
                <div class="pf-typography-row pf-typography-size-row">
                    <label class="pf-typography-label">
                        <span class="pf-typography-label-text"><?php esc_html_e('Font Size', 'persian-framework'); ?></span>
                        <input type="number"
                               step="any"
                               min="0"
                               name="<?php echo esc_attr($name); ?>[font-size]"
                               value="<?php echo esc_attr($value['font-size']); ?>"
                               class="pf-typography-input" />
                    </label>

                    <label class="pf-typography-label">
                        <span class="pf-typography-label-text"><?php esc_html_e('Line Height', 'persian-framework'); ?></span>
                        <input type="number"
                               step="any"
                               min="0"
                               name="<?php echo esc_attr($name); ?>[line-height]"
                               value="<?php echo esc_attr($value['line-height']); ?>"
                               class="pf-typography-input" />
                    </label>

                    <label class="pf-typography-label">
                        <span class="pf-typography-label-text"><?php esc_html_e('Letter Spacing', 'persian-framework'); ?></span>
                        <input type="number"
                               step="any"
                               name="<?php echo esc_attr($name); ?>[letter-spacing]"
                               value="<?php echo esc_attr($value['letter-spacing']); ?>"
                               class="pf-typography-input" />
                    </label>
                </div>

                <!-- Weight & Style -->
                <div class="pf-typography-row pf-typography-style-row">
                    <label class="pf-typography-label">
                        <span class="pf-typography-label-text"><?php esc_html_e('Weight', 'persian-framework'); ?></span>
                        <select name="<?php echo esc_attr($name); ?>[font-weight]" class="pf-typography-select">
                            <?php foreach ($weights as $key => $label): ?>
                                <option value="<?php echo esc_attr($key); ?>" <?php selected($value['font-weight'], $key); ?>>
                                    <?php echo esc_html($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="pf-typography-label">
                        <span class="pf-typography-label-text"><?php esc_html_e('Style', 'persian-framework'); ?></span>
                        <select name="<?php echo esc_attr($name); ?>[font-style]" class="pf-typography-select">
                            <?php foreach ($styles as $key => $label): ?>
                                <option value="<?php echo esc_attr($key); ?>" <?php selected($value['font-style'], $key); ?>>
                                    <?php echo esc_html($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>

                <!-- Text Align & Transform -->
                <div class="pf-typography-row pf-typography-align-row">
                    <label class="pf-typography-label">
                        <span class="pf-typography-label-text"><?php esc_html_e('Text Align', 'persian-framework'); ?></span>
                        <select name="<?php echo esc_attr($name); ?>[text-align]" class="pf-typography-select">
                            <?php foreach ($aligns as $key => $label): ?>
                                <option value="<?php echo esc_attr($key); ?>" <?php selected($value['text-align'], $key); ?>>
                                    <?php echo esc_html($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="pf-typography-label">
                        <span class="pf-typography-label-text"><?php esc_html_e('Text Transform', 'persian-framework'); ?></span>
                        <select name="<?php echo esc_attr($name); ?>[text-transform]" class="pf-typography-select">
                            <?php foreach ($transforms as $key => $label): ?>
                                <option value="<?php echo esc_attr($key); ?>" <?php selected($value['text-transform'], $key); ?>>
                                    <?php echo esc_html($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>

                <!-- Color & Unit -->
                <div class="pf-typography-row pf-typography-color-row">
                    <label class="pf-typography-label pf-typography-color">
                        <span class="pf-typography-label-text"><?php esc_html_e('Color', 'persian-framework'); ?></span>
                        <input type="color"
                               name="<?php echo esc_attr($name); ?>[color]"
                               value="<?php echo esc_attr($value['color']); ?>"
                               class="pf-typography-color-picker" />
                        <input type="text"
                               class="pf-typography-color-hex"
                               value="<?php echo esc_attr($value['color']); ?>"
                               data-target="<?php echo esc_attr($name); ?>[color]" />
                    </label>

                    <label class="pf-typography-label">
                        <span class="pf-typography-label-text"><?php esc_html_e('Unit', 'persian-framework'); ?></span>
                        <select name="<?php echo esc_attr($name); ?>[unit]" class="pf-typography-select">
                            <?php foreach ($units as $unit): ?>
                                <option value="<?php echo esc_attr($unit); ?>" <?php selected($value['unit'], $unit); ?>>
                                    <?php echo esc_html($unit); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>

                <!-- Preview -->
                <?php if (!empty($this->field['preview'])): ?>
                    <div class="pf-typography-preview-wrapper">
                        <div class="pf-typography-preview"
                             contenteditable="true"
                             style="
                                     font-family: <?php echo esc_attr($value['font-family']); ?>;
                                     font-size: <?php echo esc_attr($value['font-size'] . $value['unit']); ?>;
                                     font-weight: <?php echo esc_attr($value['font-weight']); ?>;
                                     font-style: <?php echo esc_attr($value['font-style']); ?>;
                                     line-height: <?php echo esc_attr($value['line-height'] . $value['unit']); ?>;
                                     letter-spacing: <?php echo esc_attr($value['letter-spacing'] . $value['unit']); ?>;
                                     text-align: <?php echo esc_attr($value['text-align']); ?>;
                                     text-transform: <?php echo esc_attr($value['text-transform']); ?>;
                                     color: <?php echo esc_attr($value['color']); ?>;
                                     padding: 16px;
                                     border-radius: 8px;
                                     border: 2px dashed #e8edf4;
                                     background: white;
                                     ">
                            <?php echo esc_html($preview_text); ?>
                        </div>
                        <p class="pf-typography-preview-hint">
                            <span class="dashicons dashicons-edit"></span>
                            <?php esc_html_e('Click to edit preview text', 'persian-framework'); ?>
                        </p>
                    </div>
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
        static $pf_typography_enqueued = false;

        if (!$pf_typography_enqueued) {
            ?>
            <style>
                .pf-typography-container {
                    display: flex;
                    flex-direction: column;
                    gap: 12px;
                    margin-top: 8px;
                    padding: 16px;
                    background: #fafbfc;
                    border: 2px solid #e8edf4;
                    border-radius: 12px;
                }
                body.dark-mode .pf-typography-container {
                    background: #0f172a;
                    border-color: #334155;
                }

                .pf-typography-row {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 12px 16px;
                }

                .pf-typography-label {
                    display: flex;
                    flex-direction: column;
                    gap: 4px;
                    flex: 1;
                    min-width: 100px;
                }

                .pf-typography-label-text {
                    font-size: 12px;
                    font-weight: 600;
                    color: #6b7a8f;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                body.dark-mode .pf-typography-label-text {
                    color: #94a3b8;
                }

                .pf-typography-select,
                .pf-typography-input {
                    padding: 8px 12px;
                    border: 2px solid #e8edf4;
                    border-radius: 8px;
                    font-size: 14px;
                    font-family: inherit;
                    background: white;
                    transition: all 0.2s ease;
                    width: 100%;
                }
                body.dark-mode .pf-typography-select,
                body.dark-mode .pf-typography-input {
                    background: #1e293b;
                    border-color: #334155;
                    color: #e2e8f0;
                }

                .pf-typography-select:focus,
                .pf-typography-input:focus {
                    border-color: #6366f1;
                    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
                    outline: none;
                }

                .pf-typography-input {
                    width: 100%;
                    max-width: 120px;
                }

                .pf-typography-color {
                    flex-direction: row;
                    align-items: center;
                    gap: 8px;
                    flex: 2;
                }

                .pf-typography-color-picker {
                    width: 40px;
                    height: 40px;
                    padding: 2px;
                    border: 2px solid #e8edf4;
                    border-radius: 8px;
                    cursor: pointer;
                    background: none;
                    flex-shrink: 0;
                }
                body.dark-mode .pf-typography-color-picker {
                    border-color: #334155;
                }

                .pf-typography-color-hex {
                    flex: 1;
                    padding: 8px 12px;
                    border: 2px solid #e8edf4;
                    border-radius: 8px;
                    font-size: 14px;
                    font-family: monospace;
                    background: white;
                    min-width: 80px;
                }
                body.dark-mode .pf-typography-color-hex {
                    background: #1e293b;
                    border-color: #334155;
                    color: #e2e8f0;
                }

                .pf-typography-preview-wrapper {
                    margin-top: 8px;
                }

                .pf-typography-preview {
                    transition: all 0.3s ease;
                    background: white;
                    border: 2px dashed #e8edf4 !important;
                    min-height: 60px;
                    cursor: text;
                }
                body.dark-mode .pf-typography-preview {
                    background: #1e293b;
                    border-color: #334155 !important;
                    color: #e2e8f0;
                }

                .pf-typography-preview:focus {
                    outline: 2px solid #6366f1;
                    outline-offset: 2px;
                }

                .pf-typography-preview-hint {
                    margin: 8px 0 0 0;
                    font-size: 12px;
                    color: #94a3b8;
                    display: flex;
                    align-items: center;
                    gap: 6px;
                }
                .pf-typography-preview-hint .dashicons {
                    font-size: 14px;
                    width: 14px;
                    height: 14px;
                }

                @media (max-width: 768px) {
                    .pf-typography-row {
                        flex-direction: column;
                    }
                    .pf-typography-label {
                        min-width: auto;
                    }
                    .pf-typography-input {
                        max-width: 100%;
                    }
                    .pf-typography-color {
                        flex-wrap: wrap;
                    }
                    .pf-typography-color-hex {
                        min-width: 120px;
                    }
                }
            </style>

            <script>
                (function($) {
                    'use strict';

                    $(document).on('input', '.pf-typography-color-picker', function() {
                        var $wrapper = $(this).closest('.pf-typography-color');
                        var $hex = $wrapper.find('.pf-typography-color-hex');
                        $hex.val($(this).val());
                        updateTypographyPreview($(this));
                    });

                    $(document).on('input', '.pf-typography-color-hex', function() {
                        var $wrapper = $(this).closest('.pf-typography-color');
                        var $picker = $wrapper.find('.pf-typography-color-picker');
                        var val = $(this).val();
                        if (/^#[0-9a-f]{6}$/i.test(val) || /^#[0-9a-f]{3}$/i.test(val)) {
                            $picker.val(val);
                            updateTypographyPreview($(this));
                        }
                    });

                    $(document).on('change input', '.pf-typography-container select, .pf-typography-container input', function() {
                        updateTypographyPreview($(this));
                    });

                    function updateTypographyPreview($el) {
                        var $container = $el.closest('.pf-typography-container');
                        var $preview = $container.find('.pf-typography-preview');

                        if (!$preview.length) return;

                        var fontFamily = $container.find('[name$="[font-family]"]').val() || '';
                        var fontSize = $container.find('[name$="[font-size]"]').val() || '';
                        var fontWeight = $container.find('[name$="[font-weight]"]').val() || '400';
                        var fontStyle = $container.find('[name$="[font-style]"]').val() || 'normal';
                        var lineHeight = $container.find('[name$="[line-height]"]').val() || '';
                        var letterSpacing = $container.find('[name$="[letter-spacing]"]').val() || '';
                        var textAlign = $container.find('[name$="[text-align]"]').val() || '';
                        var textTransform = $container.find('[name$="[text-transform]"]').val() || 'none';
                        var color = $container.find('[name$="[color]"]').val() || '#111827';
                        var unit = $container.find('[name$="[unit]"]').val() || 'px';

                        $preview.css({
                            'font-family': fontFamily,
                            'font-size': fontSize ? fontSize + unit : '',
                            'font-weight': fontWeight,
                            'font-style': fontStyle,
                            'line-height': lineHeight ? lineHeight + unit : '',
                            'letter-spacing': letterSpacing ? letterSpacing + unit : '',
                            'text-align': textAlign,
                            'text-transform': textTransform,
                            'color': color
                        });
                    }

                    $(document).ready(function() {
                        $('.pf-typography-container').each(function() {
                            var $container = $(this);
                            var $firstInput = $container.find('input, select').first();
                            if ($firstInput.length) {
                                updateTypographyPreview($firstInput);
                            }
                        });
                    });

                })(jQuery);
            </script>
            <?php
            $pf_typography_enqueued = true;
        }
    }

    public function sanitize($value) {
        if (!is_array($value)) {
            return array();
        }

        $sanitized = array();

        if (isset($value['font-family'])) {
            $sanitized['font-family'] = sanitize_text_field($value['font-family']);
        }

        if (isset($value['font-size'])) {
            $sanitized['font-size'] = sanitize_text_field($value['font-size']);
        }

        if (isset($value['font-weight'])) {
            $allowed_weights = array('100', '200', '300', '400', '500', '600', '700', '800', '900');
            $sanitized['font-weight'] = in_array($value['font-weight'], $allowed_weights) ? $value['font-weight'] : '400';
        }

        if (isset($value['font-style'])) {
            $allowed_styles = array('normal', 'italic', 'oblique');
            $sanitized['font-style'] = in_array($value['font-style'], $allowed_styles) ? $value['font-style'] : 'normal';
        }

        if (isset($value['line-height'])) {
            $sanitized['line-height'] = sanitize_text_field($value['line-height']);
        }

        if (isset($value['letter-spacing'])) {
            $sanitized['letter-spacing'] = sanitize_text_field($value['letter-spacing']);
        }

        if (isset($value['text-align'])) {
            $allowed_aligns = array('', 'start', 'center', 'end', 'justify');
            $sanitized['text-align'] = in_array($value['text-align'], $allowed_aligns) ? $value['text-align'] : '';
        }

        if (isset($value['text-transform'])) {
            $allowed_transforms = array('none', 'uppercase', 'lowercase', 'capitalize');
            $sanitized['text-transform'] = in_array($value['text-transform'], $allowed_transforms) ? $value['text-transform'] : 'none';
        }

        if (isset($value['color'])) {
            $sanitized['color'] = sanitize_hex_color($value['color']);
            if (empty($sanitized['color'])) {
                $sanitized['color'] = '#111827';
            }
        }

        if (isset($value['unit'])) {
            $allowed_units = array('px', 'em', 'rem', '%', 'pt', 'vw', 'vh');
            $sanitized['unit'] = in_array($value['unit'], $allowed_units) ? $value['unit'] : 'px';
        }

        return $sanitized;
    }
}