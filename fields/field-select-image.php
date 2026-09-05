<?php
/**
 * Persian Framework - Select Image Field
 * Select an image from a set of predefined images
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_SelectImage {

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
        $images_dir = isset($this->field['images_dir']) ? $this->field['images_dir'] : '';
        $image_width = isset($this->field['image_width']) ? $this->field['image_width'] : 80;
        $image_height = isset($this->field['image_height']) ? $this->field['image_height'] : 60;
        $cols = isset($this->field['cols']) ? $this->field['cols'] : 4;
        $title = isset($this->field['title']) ? $this->field['title'] : '';
        $subtitle = isset($this->field['subtitle']) ? $this->field['subtitle'] : '';
        $desc = isset($this->field['desc']) ? $this->field['desc'] : '';
        $required = isset($this->field['required']) && $this->field['required'] ? 'required' : '';

        $image_urls = array();
        foreach ($options as $key => $label) {
            if (!empty($images_dir)) {
                $image_url = trailingslashit($images_dir) . $key . '.png';
            } else {
                $image_url = PERSIAN_FRAMEWORK_ASSETS . 'images/' . $key . '.png';
            }
            $image_urls[$key] = $image_url;
        }

        ?>
        <div class="pf-field-wrapper pf-field-select-image">
            <?php if ($title): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($title); ?>
                    <?php if ($subtitle): ?>
                        <span class="pf-subtitle"><?php echo esc_html($subtitle); ?></span>
                    <?php endif; ?>
                    <?php if ($required): ?>
                        <span class="pf-required">*</span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-select-image-container" style="--pf-cols: <?php echo intval($cols); ?>;">
                <?php foreach ($options as $key => $label): ?>
                    <?php
                    $image_url = isset($image_urls[$key]) ? $image_urls[$key] : '';
                    $selected = ($value == $key) ? 'selected' : '';
                    ?>
                    <label class="pf-select-image-item <?php echo esc_attr($selected); ?>" data-value="<?php echo esc_attr($key); ?>">
                        <input type="radio"
                               name="<?php echo esc_attr($name); ?>"
                               value="<?php echo esc_attr($key); ?>"
                                <?php checked($value, $key); ?>
                                <?php echo wp_kses_data($required); ?> />

                        <div class="pf-select-image-preview" style="width:<?php echo intval($image_width); ?>px; height:<?php echo intval($image_height); ?>px;">
                            <?php if ($image_url): ?>
                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($label); ?>" loading="lazy" />
                            <?php else: ?>
                                <span class="dashicons dashicons-format-image"></span>
                            <?php endif; ?>
                            <span class="pf-select-image-check">
                                <span class="dashicons dashicons-yes-alt"></span>
                            </span>
                        </div>

                        <span class="pf-select-image-label"><?php echo esc_html($label); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>

            <?php if ($desc): ?>
                <p class="pf-field-desc"><?php echo esc_html($desc); ?></p>
            <?php endif; ?>
        </div>

        <?php
        $this->enqueue_scripts();
    }

    private function enqueue_scripts() {
        static $pf_selectimage_enqueued = false;

        if (!$pf_selectimage_enqueued) {
            ?>
            <style>
                .pf-select-image-container {
                    display: grid;
                    grid-template-columns: repeat(var(--pf-cols, 4), 1fr);
                    gap: 12px;
                    margin-top: 8px;
                }

                .pf-select-image-item {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 6px;
                    cursor: pointer;
                    padding: 8px;
                    border: 1px solid transparent;
                    border-radius: 12px;
                    background: #fafbfc;
                    transition: all 0.25s ease;
                    position: relative;
                    user-select: none;
                }
                body.dark-mode .pf-select-image-item {
                    background: #0f172a;
                }

                .pf-select-image-item:hover {
                    border-color: #cbd5e1;
                    background: #f1f5f9;
                }
                body.dark-mode .pf-select-image-item:hover {
                    border-color: #334155;
                    background: #1e293b;
                }

                .pf-select-image-item.selected {
                    border-color: #6366f1;
                    background: #eef2ff;
                    box-shadow: 0 4px 16px rgba(99, 102, 241, 0.25);
                }
                body.dark-mode .pf-select-image-item.selected {
                    background: #1e1b4b;
                    border-color: #818cf8;
                }

                .pf-select-image-item input[type="radio"] {
                    position: absolute;
                    opacity: 0;
                    width: 0;
                    height: 0;
                }

                .pf-select-image-preview {
                    position: relative;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 8px;
                    overflow: hidden;
                    background: white;
                    border: 1px solid #e8edf4;
                    transition: all 0.25s ease;
                    flex-shrink: 0;
                }
                body.dark-mode .pf-select-image-preview {
                    background: #1e293b;
                    border-color: #334155;
                }

                .pf-select-image-item.selected .pf-select-image-preview {
                    border-color: #6366f1;
                }

                .pf-select-image-preview img {
                    width: 100%;
                    height: 100%;
                    object-fit: contain;
                    display: block;
                }

                .pf-select-image-preview .dashicons {
                    font-size: 32px;
                    width: 32px;
                    height: 32px;
                    color: #cbd5e1;
                }
                body.dark-mode .pf-select-image-preview .dashicons {
                    color: #334155;
                }

                .pf-select-image-check {
                    position: absolute;
                    top: 4px;
                    right: 4px;
                    width: 22px;
                    height: 22px;
                    background: #6366f1;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    opacity: 0;
                    transform: scale(0.5);
                    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
                    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.4);
                }

                .pf-select-image-item.selected .pf-select-image-check {
                    opacity: 1;
                    transform: scale(1);
                }

                .pf-select-image-check .dashicons {
                    font-size: 14px;
                    width: 14px;
                    height: 14px;
                    color: white;
                }

                .pf-select-image-label {
                    font-size: 13px;
                    font-weight: 500;
                    color: #1a2332;
                    text-align: center;
                    transition: color 0.25s ease;
                }
                body.dark-mode .pf-select-image-label {
                    color: #e2e8f0;
                }

                .pf-select-image-item.selected .pf-select-image-label {
                    color: #6366f1;
                }
                body.dark-mode .pf-select-image-item.selected .pf-select-image-label {
                    color: #818cf8;
                }

                @media (max-width: 1024px) {
                    .pf-select-image-container {
                        --pf-cols: 3;
                    }
                }

                @media (max-width: 768px) {
                    .pf-select-image-container {
                        --pf-cols: 2;
                    }
                    .pf-select-image-preview {
                        width: 100% !important;
                        height: auto !important;
                        aspect-ratio: 4/3;
                    }
                }

                @media (max-width: 480px) {
                    .pf-select-image-container {
                        --pf-cols: 2;
                        gap: 8px;
                    }
                    .pf-select-image-item {
                        padding: 4px;
                    }
                    .pf-select-image-label {
                        font-size: 11px;
                    }
                }
            </style>

            <script>
                (function($) {
                    'use strict';

                    $(document).on('change', '.pf-select-image-item input[type="radio"]', function() {
                        var $item = $(this).closest('.pf-select-image-item');
                        var $container = $item.closest('.pf-select-image-container');

                        $container.find('.pf-select-image-item').removeClass('selected');
                        $item.addClass('selected');

                        $container.trigger('pf-select-image-change', [$item.data('value')]);
                        $(document).trigger('pf-select-image-updated', [$item.data('value')]);
                    });

                    $(document).ready(function() {
                        $('.pf-select-image-container').each(function() {
                            var $container = $(this);
                            var $checked = $container.find('input[type="radio"]:checked');
                            if ($checked.length) {
                                $checked.closest('.pf-select-image-item').addClass('selected');
                            }
                        });
                    });

                })(jQuery);
            </script>
            <?php
            $pf_selectimage_enqueued = true;
        }
    }

    public function sanitize($value) {
        $options = isset($this->field['options']) ? array_keys($this->field['options']) : array();
        $value = wp_unslash($value);

        if (in_array($value, $options)) {
            return sanitize_text_field($value);
        }

        return isset($this->field['default']) ? $this->field['default'] : '';
    }
}