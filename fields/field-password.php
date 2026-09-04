<?php
/**
 * Persian Framework - Password Field
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Password {

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
        $placeholder = isset($this->field['placeholder']) ? $this->field['placeholder'] : '';
        $required = isset($this->field['required']) && $this->field['required'] ? 'required' : '';
        ?>

        <div class="pf-field-wrapper pf-field-password">
            <?php if (isset($this->field['title'])): ?>
                <label for="<?php echo esc_attr($id); ?>" class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if ($required): ?>
                        <span class="pf-required">*</span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-password-wrapper">
                <input type="password"
                       id="<?php echo esc_attr($id); ?>"
                       name="<?php echo esc_attr($name); ?>"
                       value="<?php echo esc_attr($value); ?>"
                       placeholder="<?php echo esc_attr($placeholder); ?>"
                       class="pf-field-input pf-password-input"
                    <?php echo $required; ?> />
                <button type="button" class="pf-password-toggle">
                    <span class="dashicons dashicons-visibility"></span>
                </button>
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
                .pf-password-wrapper {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    position: relative;
                }
                .pf-password-input {
                    flex: 1;
                    padding-right: 50px;
                }
                .pf-password-toggle {
                    position: absolute;
                    right: 10px;
                    background: none;
                    border: none;
                    cursor: pointer;
                    color: #94a3b8;
                    font-size: 18px;
                    padding: 4px;
                }
                .pf-password-toggle:hover {
                    color: #6366f1;
                }
                .pf-password-toggle .dashicons {
                    font-size: 20px;
                    width: 20px;
                    height: 20px;
                }
            </style>
            <script>
                (function($) {
                    'use strict';
                    $(document).on('click', '.pf-password-toggle', function() {
                        var $input = $(this).closest('.pf-password-wrapper').find('.pf-password-input');
                        var $icon = $(this).find('.dashicons');
                        if ($input.attr('type') === 'password') {
                            $input.attr('type', 'text');
                            $icon.removeClass('dashicons-visibility').addClass('dashicons-hidden');
                        } else {
                            $input.attr('type', 'password');
                            $icon.removeClass('dashicons-hidden').addClass('dashicons-visibility');
                        }
                    });
                })(jQuery);
            </script>
            <?php
            $enqueued = true;
        }
    }
}