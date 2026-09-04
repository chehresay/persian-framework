<?php
/**
 * Persian Framework - Switch Field
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Switch {

    private $field;
    private $value;

    public function __construct($field, $value = null) {
        $this->field = $field;
        $this->value = $value;
    }

    public function render() {
        $id = isset($this->field['id']) ? $this->field['id'] : '';
        $name = isset($this->field['name']) ? $this->field['name'] : $id;
        $value = $this->value !== null ? (bool) $this->value : (isset($this->field['default']) ? (bool) $this->field['default'] : false);
        $disabled = isset($this->field['disabled']) && $this->field['disabled'] ? 'disabled' : '';
        $on_text = isset($this->field['on']) ? $this->field['on'] : __('ON', 'persian-framework');
        $off_text = isset($this->field['off']) ? $this->field['off'] : __('OFF', 'persian-framework');

        // Switch itself can also have a conditional required rule.
        $required_attributes = '';
        if (isset($this->field['required']) && is_array($this->field['required']) && class_exists('PersianFramework_Required')) {
            $required_attributes = PersianFramework_Required::get_attributes($this->field['required']);
        }
        ?>

        <div class="pf-field-wrapper pf-field-switch"<?php echo $required_attributes; ?>>
            <?php if (isset($this->field['title'])): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-switch-wrapper">
                <!-- Hidden field for "off" state - sends 0 when unchecked -->
                <input type="hidden"
                       name="<?php echo esc_attr($name); ?>"
                       value="0" />

                <input type="checkbox"
                       id="<?php echo esc_attr($id); ?>"
                       name="<?php echo esc_attr($name); ?>"
                       value="1"
                        <?php checked($value, true); ?>
                        <?php echo $disabled; ?> />

                <label for="<?php echo esc_attr($id); ?>" class="pf-switch-label">
                    <span class="pf-switch-slider"></span>
                </label>

                <span class="pf-switch-status"><?php echo $value ? $on_text : $off_text; ?></span>
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
                .pf-switch-wrapper {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    padding: 4px 0;
                }

                .pf-switch-wrapper input[type="checkbox"] {
                    display: none;
                }

                .pf-switch-label {
                    position: relative;
                    width: 50px;
                    height: 28px;
                    cursor: pointer;
                    flex-shrink: 0;
                }

                .pf-switch-slider {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: #cbd5e1;
                    border-radius: 34px;
                    transition: 0.3s ease;
                }

                .pf-switch-slider::before {
                    content: '';
                    position: absolute;
                    height: 22px;
                    width: 22px;
                    left: 3px;
                    bottom: 3px;
                    background: white;
                    border-radius: 50%;
                    transition: 0.3s ease;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
                }

                .pf-switch-wrapper input:checked + .pf-switch-label .pf-switch-slider {
                    background: #6366f1;
                }

                .pf-switch-wrapper input:checked + .pf-switch-label .pf-switch-slider::before {
                    transform: translateX(22px);
                }

                .pf-switch-status {
                    font-size: 13px;
                    font-weight: 500;
                    color: #a7a9ad;
                    min-width: 30px;
                }

                body.dark-mode .pf-switch-status {
                    color: #e2e8f0;
                }
            </style>

            <script>
                (function($) {
                    'use strict';

                    $(document).on('change', '.pf-field-switch input[type="checkbox"]', function() {
                        var $wrapper = $(this).closest('.pf-switch-wrapper');
                        var $status = $wrapper.find('.pf-switch-status');
                        var isChecked = $(this).is(':checked');

                        var onText = $(this).data('on-text') || '<?php _e('ON', 'persian-framework');?>';
                        var offText = $(this).data('off-text') || '<?php _e('OFF', 'persian-framework');?>';
                        $status.text(isChecked ? onText : offText);

                        var fieldId = $(this).attr('id');
                        $(document).trigger('pf-switch-toggle', [fieldId, isChecked]);
                    });

                })(jQuery);
            </script>
            <?php
            $enqueued = true;
        }
    }
}
