<?php
/**
 * Persian Framework - Border Field
 * Complete border control with width, style, color, radius
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Border {

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
                'top' => 0,
                'right' => 0,
                'bottom' => 0,
                'left' => 0,
                'style' => 'solid',
                'color' => '#000000',
                'radius' => 0,
                'unit' => 'px'
        );

        $value = wp_parse_args(
                is_array($this->value) ? $this->value : array(),
                $defaults
        );

        $styles = array(
                'none' => esc_html__('None', 'persian-framework'),
                'solid' => esc_html__('Solid', 'persian-framework'),
                'dashed' => esc_html__('Dashed', 'persian-framework'),
                'dotted' => esc_html__('Dotted', 'persian-framework'),
                'double' => esc_html__('Double', 'persian-framework'),
                'groove' => esc_html__('Groove', 'persian-framework'),
                'ridge' => esc_html__('Ridge', 'persian-framework'),
                'inset' => esc_html__('Inset', 'persian-framework'),
                'outset' => esc_html__('Outset', 'persian-framework')
        );

        $units = array('px', 'em', 'rem', '%');

        ?>
        <div class="pf-field-wrapper pf-field-border">
            <?php if (isset($this->field['title'])): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-border-container">
                <!-- Width fields -->
                <div class="pf-border-row">
                    <span class="pf-border-row-label"><?php esc_html_e('Width', 'persian-framework'); ?></span>
                    <?php
                    $sides = array(
                            'top' => esc_html__('Top', 'persian-framework'),
                            'right' => esc_html__('Right', 'persian-framework'),
                            'bottom' => esc_html__('Bottom', 'persian-framework'),
                            'left' => esc_html__('Left', 'persian-framework')
                    );
                    foreach ($sides as $key => $label):
                        ?>
                        <label class="pf-border-label">
                            <span class="pf-border-label-text"><?php echo esc_html($label); ?></span>
                            <input type="number"
                                   min="0"
                                   step="any"
                                   name="<?php echo esc_attr($name); ?>[<?php echo esc_attr($key); ?>]"
                                   value="<?php echo esc_attr($value[$key]); ?>"
                                   class="pf-border-input" />
                        </label>
                    <?php endforeach; ?>
                </div>

                <!-- Style & Color -->
                <div class="pf-border-row pf-border-style-row">
                    <label class="pf-border-label">
                        <span class="pf-border-label-text"><?php esc_html_e('Style', 'persian-framework'); ?></span>
                        <select name="<?php echo esc_attr($name); ?>[style]" class="pf-border-select">
                            <?php foreach ($styles as $key => $label): ?>
                                <option value="<?php echo esc_attr($key); ?>" <?php selected($value['style'], $key); ?>>
                                    <?php echo esc_html($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="pf-border-label pf-border-color-label">
                        <span class="pf-border-label-text"><?php esc_html_e('Color', 'persian-framework'); ?></span>
                        <input type="color"
                               name="<?php echo esc_attr($name); ?>[color]"
                               value="<?php echo esc_attr($value['color']); ?>"
                               class="pf-border-color-picker" />
                        <input type="text"
                               class="pf-border-color-hex"
                               value="<?php echo esc_attr($value['color']); ?>"
                               data-target="<?php echo esc_attr($name); ?>[color]" />
                    </label>
                </div>

                <!-- Radius & Unit -->
                <div class="pf-border-row">
                    <label class="pf-border-label">
                        <span class="pf-border-label-text"><?php esc_html_e('Border Radius', 'persian-framework'); ?></span>
                        <input type="number"
                               min="0"
                               step="any"
                               name="<?php echo esc_attr($name); ?>[radius]"
                               value="<?php echo esc_attr($value['radius']); ?>"
                               class="pf-border-input" />
                    </label>

                    <label class="pf-border-label">
                        <span class="pf-border-label-text"><?php esc_html_e('Unit', 'persian-framework'); ?></span>
                        <select name="<?php echo esc_attr($name); ?>[unit]" class="pf-border-select">
                            <?php foreach ($units as $unit): ?>
                                <option value="<?php echo esc_attr($unit); ?>" <?php selected($value['unit'], $unit); ?>>
                                    <?php echo esc_html($unit); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>

                <!-- Preview -->
                <div class="pf-border-preview" style="
                        border-top: <?php echo esc_attr($value['top'] . $value['unit'] . ' ' . $value['style'] . ' ' . $value['color']); ?>;
                        border-right: <?php echo esc_attr($value['right'] . $value['unit'] . ' ' . $value['style'] . ' ' . $value['color']); ?>;
                        border-bottom: <?php echo esc_attr($value['bottom'] . $value['unit'] . ' ' . $value['style'] . ' ' . $value['color']); ?>;
                        border-left: <?php echo esc_attr($value['left'] . $value['unit'] . ' ' . $value['style'] . ' ' . $value['color']); ?>;
                        border-radius: <?php echo esc_attr($value['radius'] . $value['unit']); ?>;
                        ">
                    <span><?php esc_html_e('Preview', 'persian-framework'); ?></span>
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
        static $pf_border_enqueued = false;

        if (!$pf_border_enqueued) {
            ?>
            <style>
                .pf-border-container {
                    display: flex;
                    flex-direction: column;
                    gap: 12px;
                    margin-top: 8px;
                    padding: 16px;
                    background: #fafbfc;
                    border: 2px solid #e8edf4;
                    border-radius: 12px;
                }
                body.dark-mode .pf-border-container {
                    background: #0f172a;
                    border-color: #334155;
                }

                .pf-border-row {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 12px 16px;
                    align-items: flex-end;
                }

                .pf-border-row-label {
                    font-size: 12px;
                    font-weight: 600;
                    color: #6b7a8f;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    min-width: 60px;
                    padding-bottom: 4px;
                }
                body.dark-mode .pf-border-row-label {
                    color: #94a3b8;
                }

                .pf-border-label {
                    display: flex;
                    flex-direction: column;
                    gap: 4px;
                    flex: 1;
                    min-width: 70px;
                }

                .pf-border-label-text {
                    font-size: 12px;
                    font-weight: 600;
                    color: #6b7a8f;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                body.dark-mode .pf-border-label-text {
                    color: #94a3b8;
                }

                .pf-border-input,
                .pf-border-select {
                    padding: 8px 12px;
                    border: 2px solid #e8edf4;
                    border-radius: 8px;
                    font-size: 14px;
                    font-family: inherit;
                    background: white;
                    transition: all 0.2s ease;
                    width: 100%;
                }
                body.dark-mode .pf-border-input,
                body.dark-mode .pf-border-select {
                    background: #1e293b;
                    border-color: #334155;
                    color: #e2e8f0;
                }

                .pf-border-input:focus,
                .pf-border-select:focus {
                    border-color: #6366f1;
                    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
                    outline: none;
                }

                .pf-border-color-label {
                    flex-direction: row;
                    align-items: center;
                    gap: 8px;
                }

                .pf-border-color-picker {
                    width: 40px;
                    height: 40px;
                    padding: 2px;
                    border: 2px solid #e8edf4;
                    border-radius: 8px;
                    cursor: pointer;
                    background: none;
                }
                body.dark-mode .pf-border-color-picker {
                    border-color: #334155;
                }

                .pf-border-color-hex {
                    width: 100px;
                    padding: 8px 12px;
                    border: 2px solid #e8edf4;
                    border-radius: 8px;
                    font-size: 14px;
                    font-family: monospace;
                    background: white;
                }
                body.dark-mode .pf-border-color-hex {
                    background: #1e293b;
                    border-color: #334155;
                    color: #e2e8f0;
                }

                .pf-border-preview {
                    padding: 20px;
                    text-align: center;
                    background: white;
                    border-radius: 8px;
                    transition: all 0.3s ease;
                    min-height: 60px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                body.dark-mode .pf-border-preview {
                    background: #1e293b;
                }

                .pf-border-preview span {
                    font-size: 14px;
                    font-weight: 600;
                    color: #1a2332;
                }
                body.dark-mode .pf-border-preview span {
                    color: #e2e8f0;
                }

                @media (max-width: 768px) {
                    .pf-border-row {
                        flex-direction: column;
                    }
                    .pf-border-label {
                        min-width: auto;
                    }
                    .pf-border-color-label {
                        flex-wrap: wrap;
                    }
                }
            </style>

            <script>
                (function($) {
                    'use strict';

                    $(document).on('input', '.pf-border-color-picker', function() {
                        var $wrapper = $(this).closest('.pf-border-color-label');
                        var $hex = $wrapper.find('.pf-border-color-hex');
                        $hex.val($(this).val());
                        updateBorderPreview($(this));
                    });

                    $(document).on('input', '.pf-border-color-hex', function() {
                        var $wrapper = $(this).closest('.pf-border-color-label');
                        var $picker = $wrapper.find('.pf-border-color-picker');
                        var val = $(this).val();
                        if (/^#[0-9a-f]{6}$/i.test(val) || /^#[0-9a-f]{3}$/i.test(val)) {
                            $picker.val(val);
                            updateBorderPreview($(this));
                        }
                    });

                    $(document).on('change input', '.pf-border-container input, .pf-border-container select', function() {
                        updateBorderPreview($(this));
                    });

                    function updateBorderPreview($el) {
                        var $container = $el.closest('.pf-border-container');
                        var $preview = $container.find('.pf-border-preview');

                        if (!$preview.length) return;

                        var top = $container.find('[name$="[top]"]').val() || 0;
                        var right = $container.find('[name$="[right]"]').val() || 0;
                        var bottom = $container.find('[name$="[bottom]"]').val() || 0;
                        var left = $container.find('[name$="[left]"]').val() || 0;
                        var style = $container.find('[name$="[style]"]').val() || 'solid';
                        var color = $container.find('[name$="[color]"]').val() || '#000000';
                        var radius = $container.find('[name$="[radius]"]').val() || 0;
                        var unit = $container.find('[name$="[unit]"]').val() || 'px';

                        $preview.css({
                            'border-top': top + unit + ' ' + style + ' ' + color,
                            'border-right': right + unit + ' ' + style + ' ' + color,
                            'border-bottom': bottom + unit + ' ' + style + ' ' + color,
                            'border-left': left + unit + ' ' + style + ' ' + color,
                            'border-radius': radius + unit
                        });
                    }

                })(jQuery);
            </script>
            <?php
            $pf_border_enqueued = true;
        }
    }

    public function sanitize($value) {
        if (!is_array($value)) {
            return array();
        }

        $sanitized = array();

        $sides = array('top', 'right', 'bottom', 'left');
        foreach ($sides as $side) {
            if (isset($value[$side])) {
                $sanitized[$side] = floatval($value[$side]);
            }
        }

        if (isset($value['style'])) {
            $allowed_styles = array('none', 'solid', 'dashed', 'dotted', 'double', 'groove', 'ridge', 'inset', 'outset');
            $sanitized['style'] = in_array($value['style'], $allowed_styles) ? $value['style'] : 'solid';
        }

        if (isset($value['color'])) {
            $sanitized['color'] = sanitize_hex_color($value['color']);
        }

        if (isset($value['radius'])) {
            $sanitized['radius'] = floatval($value['radius']);
        }

        if (isset($value['unit'])) {
            $allowed_units = array('px', 'em', 'rem', '%');
            $sanitized['unit'] = in_array($value['unit'], $allowed_units) ? $value['unit'] : 'px';
        }

        return $sanitized;
    }
}