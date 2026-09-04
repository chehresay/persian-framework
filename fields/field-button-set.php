<?php
/**
 * Persian Framework - Button Set Field
 * Radio buttons styled as toggle buttons
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_ButtonSet {

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
        $options = isset($this->field['options']) ? $this->field['options'] : array();

        // Multi-select support
        $multi = isset($this->field['multi']) && $this->field['multi'];
        $input_type = $multi ? 'checkbox' : 'radio';
        $name_suffix = $multi ? '[]' : '';

        ?>
        <div class="pf-field-wrapper pf-field-button-set <?php echo $multi ? 'pf-multi' : ''; ?>">
            <?php if (isset($this->field['title'])): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-button-set-container">
                <?php foreach ($options as $key => $label): ?>
                    <label class="pf-button-set-item <?php echo $this->is_selected($value, $key, $multi) ? 'selected' : ''; ?>">
                        <input type="<?php echo esc_attr($input_type); ?>"
                               name="<?php echo esc_attr($name . $name_suffix); ?>"
                               value="<?php echo esc_attr($key); ?>"
                            <?php echo $this->is_selected($value, $key, $multi) ? 'checked' : ''; ?> />
                        <span class="pf-button-set-label">
                            <?php if (isset($this->field['icons']) && isset($this->field['icons'][$key])): ?>
                                <span class="dashicons <?php echo esc_attr($this->field['icons'][$key]); ?>"></span>
                            <?php endif; ?>
                            <?php echo esc_html($label); ?>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>

            <?php if (isset($this->field['desc'])): ?>
                <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php
        $this->enqueue_scripts();
    }

    private function is_selected($value, $key, $multi) {
        if ($multi) {
            $values = (array) $value;
            return in_array($key, $values);
        }
        return (string) $value === (string) $key;
    }

    private function enqueue_scripts() {
        static $enqueued = false;

        if (!$enqueued) {
            ?>
            <style>
                .pf-button-set-container {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 6px;
                    margin-top: 4px;
                }

                .pf-button-set-item {
                    position: relative;
                    cursor: pointer;
                    flex: 0 0 auto;
                }

                .pf-button-set-item input[type="radio"],
                .pf-button-set-item input[type="checkbox"] {
                    position: absolute;
                    opacity: 0;
                    width: 0;
                    height: 0;
                }

                .pf-button-set-label {
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    padding: 8px 18px;
                    border: 2px solid #e8edf4;
                    border-radius: 8px;
                    font-size: 14px;
                    font-weight: 500;
                    color: #4a5568;
                    background: white;
                    transition: all 0.25s ease;
                    user-select: none;
                }
                body.dark-mode .pf-button-set-label {
                    background: #1e293b;
                    border-color: #334155;
                    color: #94a3b8;
                }

                .pf-button-set-item:hover .pf-button-set-label {
                    border-color: #94a3b8;
                }

                .pf-button-set-item.selected .pf-button-set-label {
                    background: #6366f1;
                    border-color: #6366f1;
                    color: white;
                    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.3);
                }

                .pf-button-set-item.selected .pf-button-set-label .dashicons {
                    color: white;
                }

                .pf-button-set-item .pf-button-set-label .dashicons {
                    font-size: 16px;
                    width: 16px;
                    height: 16px;
                    color: #94a3b8;
                }

                .pf-button-set-item.selected .pf-button-set-label .dashicons {
                    color: white;
                }

                /* Size variants */
                .pf-button-set-item.small .pf-button-set-label {
                    padding: 4px 12px;
                    font-size: 12px;
                }

                .pf-button-set-item.large .pf-button-set-label {
                    padding: 12px 24px;
                    font-size: 16px;
                }

                @media (max-width: 768px) {
                    .pf-button-set-container {
                        flex-wrap: wrap;
                    }
                    .pf-button-set-item {
                        flex: 1;
                        min-width: 60px;
                    }
                    .pf-button-set-label {
                        justify-content: center;
                    }
                }
            </style>

            <script>
                (function($) {
                    'use strict';

                    // Handle button set selection
                    $(document).on('change', '.pf-button-set-item input', function() {
                        var $item = $(this).closest('.pf-button-set-item');
                        var $container = $item.closest('.pf-button-set-container');
                        var isMulti = $item.closest('.pf-field-button-set').hasClass('pf-multi');

                        if (!isMulti) {
                            // Radio mode - deselect others
                            $container.find('.pf-button-set-item').removeClass('selected');
                            $item.addClass('selected');
                        } else {
                            // Checkbox mode - toggle class
                            $item.toggleClass('selected');
                        }

                        $(document).trigger('pf-button-set-change', [
                            $item.find('input').val(),
                            $item.hasClass('selected'),
                            $container.closest('.pf-field-button-set').attr('id')
                        ]);
                    });

                    // Initialize selected state
                    $(document).ready(function() {
                        $('.pf-button-set-item input:checked').each(function() {
                            $(this).closest('.pf-button-set-item').addClass('selected');
                        });
                    });

                })(jQuery);
            </script>
            <?php
            $enqueued = true;
        }
    }
}