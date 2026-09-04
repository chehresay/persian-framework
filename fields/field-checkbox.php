<?php
/**
 * Persian Framework - Checkbox Field
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Checkbox {

    private $field;
    private $value;

    public function __construct($field, $value = null) {
        $this->field = $field;
        $this->value = $value;
    }

    public function render() {
        $id = isset($this->field['id']) ? $this->field['id'] : '';
        $name = isset($this->field['name']) ? $this->field['name'] : $id;
        $value = $this->value !== null ? (array) $this->value : (isset($this->field['default']) ? (array) $this->field['default'] : array());
        $options = isset($this->field['options']) ? $this->field['options'] : array();
        $inline = isset($this->field['inline']) && $this->field['inline'] ? 'pf-inline' : '';
        $single = isset($this->field['single']) && $this->field['single'];

        // For single checkbox (boolean)
        if ($single) {
            $value = (bool) $this->value;
            ?>
            <div class="pf-field-wrapper pf-field-checkbox pf-field-checkbox-single">
                <?php if (isset($this->field['title'])): ?>
                    <label class="pf-field-label">
                        <?php echo esc_html($this->field['title']); ?>
                        <?php if (isset($this->field['subtitle'])): ?>
                            <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                        <?php endif; ?>
                    </label>
                <?php endif; ?>

                <div class="pf-checkbox-single-wrapper">
                    <!-- Hidden field for "off" state - sends 0 when unchecked -->
                    <input type="hidden"
                           name="<?php echo esc_attr($name); ?>"
                           value="0" />

                    <label class="pf-checkbox-item">
                        <input type="checkbox"
                               name="<?php echo esc_attr($name); ?>"
                               value="1"
                                <?php checked($value, true); ?> />
                        <span class="pf-checkbox-label">
                            <?php echo isset($this->field['label']) ? esc_html($this->field['label']) : __('Enable', 'persian-framework'); ?>
                        </span>
                    </label>
                </div>

                <?php if (isset($this->field['desc'])): ?>
                    <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
                <?php endif; ?>
            </div>
            <?php
            return;
        }

        // Multiple checkboxes
        ?>
        <div class="pf-field-wrapper pf-field-checkbox">
            <?php if (isset($this->field['title'])): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-checkbox-group <?php echo esc_attr($inline); ?>">
                <?php foreach ($options as $key => $label): ?>
                    <label class="pf-checkbox-item">
                        <input type="checkbox"
                               name="<?php echo esc_attr($name); ?>[]"
                               value="<?php echo esc_attr($key); ?>"
                                <?php checked(in_array($key, $value)); ?> />
                        <span class="pf-checkbox-label"><?php echo esc_html($label); ?></span>
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

    private function enqueue_scripts() {
        static $enqueued = false;

        if (!$enqueued) {
            ?>
            <style>
                .pf-checkbox-group {
                    display: flex;
                    flex-direction: column;
                    gap: 6px;
                    padding: 4px 0;
                }
                .pf-checkbox-group.pf-inline {
                    flex-direction: row;
                    flex-wrap: wrap;
                    gap: 12px 20px;
                }
                .pf-checkbox-item {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    cursor: pointer;
                    font-size: 14px;
                    color: #1a2332;
                }
                .pf-checkbox-item input[type="checkbox"] {
                    width: 18px;
                    height: 18px;
                    accent-color: #6366f1;
                    cursor: pointer;
                    flex-shrink: 0;
                }
                .pf-checkbox-single-wrapper {
                    display: flex;
                    align-items: center;
                    padding: 4px 0;
                }
                body.dark-mode .pf-checkbox-item {
                    color: #e2e8f0;
                }
            </style>
            <?php
            $enqueued = true;
        }
    }
}