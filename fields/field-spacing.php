<?php
/**
 * Persian Framework - Spacing Field
 * Margin/Padding control with top, right, bottom, left
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Spacing {

    private $field;
    private $value;

    public function __construct($field, $value = null) {
        $this->field = $field;
        $this->value = $value;
    }

    public function render() {
        $id = isset($this->field['id']) ? $this->field['id'] : '';
        $name = isset($this->field['name']) ? $this->field['name'] : $id;
        $value = wp_parse_args(
            is_array($this->value) ? $this->value : array(),
            array(
                'top' => '',
                'right' => '',
                'bottom' => '',
                'left' => '',
                'unit' => isset($this->field['unit']) ? $this->field['unit'] : 'px',
                'linked' => isset($this->field['linked']) ? $this->field['linked'] : true
            )
        );

        $units = isset($this->field['units']) ? $this->field['units'] : array('px', 'em', 'rem', '%', 'vw', 'vh');

        ?>
        <div class="pf-field-wrapper pf-field-spacing">
            <?php if (isset($this->field['title'])): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-spacing-container">
                <div class="pf-spacing-controls">
                    <?php
                    $directions = array(
                        'top' => __('Top', 'persian-framework'),
                        'right' => __('Right', 'persian-framework'),
                        'bottom' => __('Bottom', 'persian-framework'),
                        'left' => __('Left', 'persian-framework')
                    );
                    foreach ($directions as $dir => $label):
                        ?>
                        <label class="pf-spacing-label">
                            <span class="pf-spacing-dir-label"><?php echo esc_html($label); ?></span>
                            <input type="number"
                                   step="any"
                                   name="<?php echo esc_attr($name); ?>[<?php echo esc_attr($dir); ?>]"
                                   value="<?php echo esc_attr($value[$dir]); ?>"
                                   class="pf-spacing-input pf-spacing-<?php echo esc_attr($dir); ?>" />
                        </label>
                    <?php endforeach; ?>

                    <label class="pf-spacing-label pf-spacing-unit">
                        <span class="pf-spacing-dir-label"><?php _e('Unit', 'persian-framework'); ?></span>
                        <select name="<?php echo esc_attr($name); ?>[unit]" class="pf-spacing-unit-select">
                            <?php foreach ($units as $unit): ?>
                                <option value="<?php echo esc_attr($unit); ?>" <?php selected($value['unit'], $unit); ?>>
                                    <?php echo esc_html($unit); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="pf-spacing-label pf-spacing-linked">
                        <input type="hidden" name="<?php echo esc_attr($name); ?>[linked]" value="0" />
                        <input type="checkbox"
                               name="<?php echo esc_attr($name); ?>[linked]"
                               value="1"
                            <?php checked(!empty($value['linked']), true); ?>
                               class="pf-spacing-linked-checkbox" />
                        <span class="pf-spacing-linked-icon dashicons dashicons-admin-links"></span>
                        <span class="pf-spacing-linked-label"><?php _e('Link values', 'persian-framework'); ?></span>
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
        static $enqueued = false;

        if (!$enqueued) {
            ?>
            <style>
                .pf-spacing-container {
                    margin-top: 8px;
                }
                .pf-spacing-controls {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 12px 16px;
                    padding: 16px;
                    background: #fafbfc;
                    border: 2px solid #e8edf4;
                    border-radius: 12px;
                }
                body.dark-mode .pf-spacing-controls {
                    background: #0f172a;
                    border-color: #334155;
                }
                .pf-spacing-label {
                    display: flex;
                    flex-direction: column;
                    gap: 4px;
                    flex: 0 0 auto;
                }
                .pf-spacing-dir-label {
                    font-size: 12px;
                    font-weight: 600;
                    color: #6b7a8f;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                body.dark-mode .pf-spacing-dir-label {
                    color: #94a3b8;
                }
                .pf-spacing-input {
                    width: 80px;
                    padding: 8px 10px;
                    border: 2px solid #e8edf4;
                    border-radius: 8px;
                    font-size: 14px;
                    text-align: center;
                    background: white;
                    transition: all 0.2s ease;
                }
                .pf-spacing-input:focus {
                    border-color: #6366f1;
                    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
                    outline: none;
                }
                body.dark-mode .pf-spacing-input {
                    background: #1e293b;
                    border-color: #334155;
                    color: #e2e8f0;
                }
                .pf-spacing-unit {
                    flex: 0 0 auto;
                }
                .pf-spacing-unit-select {
                    padding: 8px 12px;
                    border: 2px solid #e8edf4;
                    border-radius: 8px;
                    font-size: 14px;
                    background: white;
                    min-width: 70px;
                }
                body.dark-mode .pf-spacing-unit-select {
                    background: #1e293b;
                    border-color: #334155;
                    color: #e2e8f0;
                }
                .pf-spacing-linked {
                    flex-direction: row;
                    align-items: center;
                    gap: 8px;
                    cursor: pointer;
                }
                .pf-spacing-linked-checkbox {
                    width: 18px;
                    height: 18px;
                    accent-color: #6366f1;
                    cursor: pointer;
                }
                .pf-spacing-linked-icon {
                    font-size: 18px;
                    color: #94a3b8;
                }
                .pf-spacing-linked-checkbox:checked + .pf-spacing-linked-icon {
                    color: #6366f1;
                }
                .pf-spacing-linked-label {
                    font-size: 13px;
                    color: #6b7a8f;
                }
                body.dark-mode .pf-spacing-linked-label {
                    color: #94a3b8;
                }

                /* Linked values - sync inputs */
                .pf-spacing-linked-checkbox:checked ~ .pf-spacing-input {
                    /* Visual indication that values are linked */
                }

                @media (max-width: 768px) {
                    .pf-spacing-controls {
                        flex-direction: column;
                        align-items: stretch;
                    }
                    .pf-spacing-label {
                        flex: 1;
                    }
                    .pf-spacing-input {
                        width: 100%;
                    }
                }
            </style>

            <script>
                (function($) {
                    'use strict';

                    // Link/unlink spacing values
                    $(document).on('change', '.pf-spacing-linked-checkbox', function() {
                        var $container = $(this).closest('.pf-spacing-controls');
                        var $inputs = $container.find('.pf-spacing-input');
                        var isLinked = $(this).is(':checked');

                        if (isLinked) {
                            // Get first non-empty value
                            var firstValue = '';
                            $inputs.each(function() {
                                if ($(this).val() !== '') {
                                    firstValue = $(this).val();
                                    return false;
                                }
                            });
                            // Set all inputs to same value
                            $inputs.val(firstValue);
                        }
                    });

                    // Sync linked inputs
                    $(document).on('input', '.pf-spacing-input', function() {
                        var $container = $(this).closest('.pf-spacing-controls');
                        var $checkbox = $container.find('.pf-spacing-linked-checkbox');

                        if ($checkbox.is(':checked')) {
                            var value = $(this).val();
                            $container.find('.pf-spacing-input').val(value);
                        }
                    });

                })(jQuery);
            </script>
            <?php
            $enqueued = true;
        }
    }
}