<?php
/**
 * Persian Framework - Radio Field
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Radio {

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
        $inline = isset($this->field['inline']) && $this->field['inline'] ? 'pf-inline' : '';
        ?>

        <div class="pf-field-wrapper pf-field-radio">
            <?php if (isset($this->field['title'])): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-radio-group <?php echo esc_attr($inline); ?>">
                <?php foreach ($options as $key => $label): ?>
                    <label class="pf-radio-item">
                        <input type="radio"
                               name="<?php echo esc_attr($name); ?>"
                               value="<?php echo esc_attr($key); ?>"
                            <?php checked($value, $key); ?> />
                        <span class="pf-radio-label"><?php echo esc_html($label); ?></span>
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
                .pf-radio-group {
                    display: flex;
                    flex-direction: column;
                    gap: 6px;
                    padding: 4px 0;
                }
                .pf-radio-group.pf-inline {
                    flex-direction: row;
                    flex-wrap: wrap;
                    gap: 12px 20px;
                }
                .pf-radio-item {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    cursor: pointer;
                    font-size: 14px;
                    color: #1a2332;
                }
                .pf-radio-item input[type="radio"] {
                    width: 18px;
                    height: 18px;
                    accent-color: #6366f1;
                    cursor: pointer;
                    flex-shrink: 0;
                }
                body.dark-mode .pf-radio-item {
                    color: #e2e8f0;
                }
            </style>
            <?php
            $enqueued = true;
        }
    }
}