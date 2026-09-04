<?php
/**
 * Persian Framework - Gradient Field
 * Create linear/radial gradients with color picker
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Gradient {

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
            'type' => 'linear',
            'from' => '#6366f1',
            'to' => '#8b5cf6',
            'angle' => 135,
            'position' => 'center-center'
        );

        $value = wp_parse_args(
            is_array($this->value) ? $this->value : array(),
            $defaults
        );

        $positions = array(
            'top-left' => __('Top Left', 'persian-framework'),
            'top-center' => __('Top Center', 'persian-framework'),
            'top-right' => __('Top Right', 'persian-framework'),
            'center-left' => __('Center Left', 'persian-framework'),
            'center-center' => __('Center Center', 'persian-framework'),
            'center-right' => __('Center Right', 'persian-framework'),
            'bottom-left' => __('Bottom Left', 'persian-framework'),
            'bottom-center' => __('Bottom Center', 'persian-framework'),
            'bottom-right' => __('Bottom Right', 'persian-framework'),
        );

        ?>
        <div class="pf-field-wrapper pf-field-gradient">
            <?php if (isset($this->field['title'])): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-gradient-container">
                <div class="pf-gradient-row">
                    <label class="pf-gradient-label">
                        <span class="pf-gradient-label-text"><?php _e('Type', 'persian-framework'); ?></span>
                        <select name="<?php echo esc_attr($name); ?>[type]" class="pf-gradient-select">
                            <option value="linear" <?php selected($value['type'], 'linear'); ?>>
                                <?php _e('Linear', 'persian-framework'); ?>
                            </option>
                            <option value="radial" <?php selected($value['type'], 'radial'); ?>>
                                <?php _e('Radial', 'persian-framework'); ?>
                            </option>
                        </select>
                    </label>

                    <?php if ($value['type'] === 'radial'): ?>
                        <label class="pf-gradient-label">
                            <span class="pf-gradient-label-text"><?php _e('Position', 'persian-framework'); ?></span>
                            <select name="<?php echo esc_attr($name); ?>[position]" class="pf-gradient-select">
                                <?php foreach ($positions as $key => $label): ?>
                                    <option value="<?php echo esc_attr($key); ?>" <?php selected($value['position'], $key); ?>>
                                        <?php echo esc_html($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    <?php else: ?>
                        <label class="pf-gradient-label">
                            <span class="pf-gradient-label-text"><?php _e('Angle', 'persian-framework'); ?></span>
                            <div class="pf-gradient-angle">
                                <input type="range"
                                       name="<?php echo esc_attr($name); ?>[angle]"
                                       value="<?php echo esc_attr($value['angle']); ?>"
                                       min="0"
                                       max="360"
                                       class="pf-gradient-range" />
                                <output class="pf-gradient-angle-output"><?php echo esc_html($value['angle']); ?>°</output>
                            </div>
                        </label>
                    <?php endif; ?>
                </div>

                <div class="pf-gradient-row pf-gradient-colors">
                    <label class="pf-gradient-label">
                        <span class="pf-gradient-label-text"><?php _e('Start Color', 'persian-framework'); ?></span>
                        <div class="pf-gradient-color-wrap">
                            <input type="color"
                                   name="<?php echo esc_attr($name); ?>[from]"
                                   value="<?php echo esc_attr($value['from']); ?>"
                                   class="pf-gradient-color-picker" />
                            <input type="text"
                                   class="pf-gradient-color-hex"
                                   value="<?php echo esc_attr($value['from']); ?>"
                                   data-target="<?php echo esc_attr($name); ?>[from]" />
                        </div>
                    </label>

                    <label class="pf-gradient-label">
                        <span class="pf-gradient-label-text"><?php _e('End Color', 'persian-framework'); ?></span>
                        <div class="pf-gradient-color-wrap">
                            <input type="color"
                                   name="<?php echo esc_attr($name); ?>[to]"
                                   value="<?php echo esc_attr($value['to']); ?>"
                                   class="pf-gradient-color-picker" />
                            <input type="text"
                                   class="pf-gradient-color-hex"
                                   value="<?php echo esc_attr($value['to']); ?>"
                                   data-target="<?php echo esc_attr($name); ?>[to]" />
                        </div>
                    </label>
                </div>

                <div class="pf-gradient-preview" style="
                    background: <?php echo $this->build_gradient_css($value); ?>;
                    ">
                    <span><?php _e('Preview', 'persian-framework'); ?></span>
                </div>
            </div>

            <?php if (isset($this->field['desc'])): ?>
                <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php
        $this->enqueue_scripts();
    }

    private function build_gradient_css($value) {
        if ($value['type'] === 'linear') {
            return 'linear-gradient(' . $value['angle'] . 'deg, ' . $value['from'] . ', ' . $value['to'] . ')';
        } else {
            $pos = str_replace('-', ' ', $value['position']);
            return 'radial-gradient(circle at ' . $pos . ', ' . $value['from'] . ', ' . $value['to'] . ')';
        }
    }

    private function enqueue_scripts() {
        static $enqueued = false;

        if (!$enqueued) {
            ?>
            <style>
                .pf-gradient-container {
                    display: flex;
                    flex-direction: column;
                    gap: 12px;
                    margin-top: 8px;
                    padding: 16px;
                    background: #fafbfc;
                    border: 2px solid #e8edf4;
                    border-radius: 12px;
                }
                body.dark-mode .pf-gradient-container {
                    background: #0f172a;
                    border-color: #334155;
                }

                .pf-gradient-row {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 12px 16px;
                }

                .pf-gradient-label {
                    display: flex;
                    flex-direction: column;
                    gap: 4px;
                    flex: 1;
                    min-width: 120px;
                }

                .pf-gradient-label-text {
                    font-size: 12px;
                    font-weight: 600;
                    color: #6b7a8f;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                body.dark-mode .pf-gradient-label-text {
                    color: #94a3b8;
                }

                .pf-gradient-select {
                    padding: 8px 12px;
                    border: 2px solid #e8edf4;
                    border-radius: 8px;
                    font-size: 14px;
                    background: white;
                    width: 100%;
                }
                body.dark-mode .pf-gradient-select {
                    background: #1e293b;
                    border-color: #334155;
                    color: #e2e8f0;
                }

                .pf-gradient-angle {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                }

                .pf-gradient-range {
                    flex: 1;
                    accent-color: #6366f1;
                    height: 6px;
                    border-radius: 3px;
                    cursor: pointer;
                }

                .pf-gradient-angle-output {
                    min-width: 45px;
                    font-weight: 600;
                    color: #6366f1;
                }

                .pf-gradient-color-wrap {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }

                .pf-gradient-color-picker {
                    width: 40px;
                    height: 40px;
                    padding: 2px;
                    border: 2px solid #e8edf4;
                    border-radius: 8px;
                    cursor: pointer;
                    background: none;
                }
                body.dark-mode .pf-gradient-color-picker {
                    border-color: #334155;
                }

                .pf-gradient-color-hex {
                    flex: 1;
                    padding: 8px 12px;
                    border: 2px solid #e8edf4;
                    border-radius: 8px;
                    font-size: 14px;
                    font-family: monospace;
                    background: white;
                }
                body.dark-mode .pf-gradient-color-hex {
                    background: #1e293b;
                    border-color: #334155;
                    color: #e2e8f0;
                }

                .pf-gradient-preview {
                    padding: 30px;
                    text-align: center;
                    border-radius: 8px;
                    min-height: 80px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: background 0.3s ease;
                }

                .pf-gradient-preview span {
                    font-size: 14px;
                    font-weight: 600;
                    color: white;
                    text-shadow: 0 1px 4px rgba(0,0,0,0.3);
                }

                @media (max-width: 768px) {
                    .pf-gradient-row {
                        flex-direction: column;
                    }
                    .pf-gradient-label {
                        min-width: auto;
                    }
                }
            </style>

            <script>
                (function($) {
                    'use strict';

                    // Sync color pickers with hex inputs
                    $(document).on('input', '.pf-gradient-color-picker', function() {
                        var $wrap = $(this).closest('.pf-gradient-color-wrap');
                        var $hex = $wrap.find('.pf-gradient-color-hex');
                        $hex.val($(this).val());
                        updateGradientPreview($(this));
                    });

                    $(document).on('input', '.pf-gradient-color-hex', function() {
                        var $wrap = $(this).closest('.pf-gradient-color-wrap');
                        var $picker = $wrap.find('.pf-gradient-color-picker');
                        var val = $(this).val();
                        if (/^#[0-9a-f]{6}$/i.test(val) || /^#[0-9a-f]{3}$/i.test(val)) {
                            $picker.val(val);
                            updateGradientPreview($(this));
                        }
                    });

                    // Update preview on any change
                    $(document).on('change input', '.pf-gradient-container select, .pf-gradient-container input', function() {
                        updateGradientPreview($(this));
                    });

                    // Angle display
                    $(document).on('input', '.pf-gradient-range', function() {
                        var $output = $(this).closest('.pf-gradient-angle').find('.pf-gradient-angle-output');
                        $output.text($(this).val() + '°');
                    });

                    function updateGradientPreview($el) {
                        var $container = $el.closest('.pf-gradient-container');
                        var $preview = $container.find('.pf-gradient-preview');

                        if (!$preview.length) return;

                        var type = $container.find('[name$="[type]"]').val() || 'linear';
                        var from = $container.find('[name$="[from]"]').val() || '#6366f1';
                        var to = $container.find('[name$="[to]"]').val() || '#8b5cf6';

                        var gradient = '';

                        if (type === 'linear') {
                            var angle = $container.find('[name$="[angle]"]').val() || 135;
                            gradient = 'linear-gradient(' + angle + 'deg, ' + from + ', ' + to + ')';
                        } else {
                            var position = $container.find('[name$="[position]"]').val() || 'center-center';
                            var pos = position.replace('-', ' ');
                            gradient = 'radial-gradient(circle at ' + pos + ', ' + from + ', ' + to + ')';
                        }

                        $preview.css('background', gradient);
                    }

                })(jQuery);
            </script>
            <?php
            $enqueued = true;
        }
    }
}