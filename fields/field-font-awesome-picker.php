<?php
/**
 * Persian Framework - Font Awesome Picker
 * Select Font Awesome icons with search
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_FontAwesomePicker {

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
        $version = isset($this->field['version']) ? $this->field['version'] : '6'; // 5 or 6
        $placeholder = isset($this->field['placeholder']) ? $this->field['placeholder'] : 'fa-solid fa-star';

        // Font Awesome icons list (version 6)
        $icons = $this->get_font_awesome_icons($version);

        ?>
        <div class="pf-field-wrapper pf-field-font-awesome-picker">
            <?php if (isset($this->field['title'])): ?>
                <label for="<?php echo esc_attr($id); ?>" class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-fa-picker-container">
                <div class="pf-fa-picker-input">
                    <input type="text"
                           id="<?php echo esc_attr($id); ?>"
                           name="<?php echo esc_attr($name); ?>"
                           value="<?php echo esc_attr($value); ?>"
                           placeholder="<?php echo esc_attr($placeholder); ?>"
                           class="pf-field-input pf-fa-picker-search"
                           autocomplete="off" />
                    <span class="pf-fa-picker-preview <?php echo esc_attr($value); ?>"></span>
                </div>

                <div class="pf-fa-picker-dropdown" style="display:none;">
                    <div class="pf-fa-picker-search-wrap">
                        <input type="text"
                               class="pf-fa-picker-filter"
                               placeholder="<?php esc_attr_e('Filter icons...', 'persian-framework'); ?>" />
                    </div>
                    <div class="pf-fa-picker-grid">
                        <?php foreach ($icons as $icon): ?>
                            <button type="button"
                                    class="pf-fa-picker-icon <?php echo esc_attr($icon); ?>"
                                    data-icon="<?php echo esc_attr($icon); ?>"
                                    title="<?php echo esc_attr($icon); ?>">
                                <i class="<?php echo esc_attr($icon); ?>"></i>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <?php if (isset($this->field['desc'])): ?>
                <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php
        $this->enqueue_scripts($version);
    }

    private function get_font_awesome_icons($version) {
        // Common icons for both versions
        $common_icons = array(
            'fa-solid fa-star',
            'fa-solid fa-heart',
            'fa-solid fa-home',
            'fa-solid fa-user',
            'fa-solid fa-cog',
            'fa-solid fa-search',
            'fa-solid fa-envelope',
            'fa-solid fa-phone',
            'fa-solid fa-map-marker-alt',
            'fa-solid fa-calendar',
            'fa-solid fa-clock',
            'fa-solid fa-arrow-right',
            'fa-solid fa-arrow-left',
            'fa-solid fa-arrow-up',
            'fa-solid fa-arrow-down',
            'fa-solid fa-check',
            'fa-solid fa-times',
            'fa-solid fa-plus',
            'fa-solid fa-minus',
            'fa-solid fa-edit',
            'fa-solid fa-trash',
            'fa-solid fa-save',
            'fa-solid fa-print',
            'fa-solid fa-download',
            'fa-solid fa-upload',
            'fa-solid fa-camera',
            'fa-solid fa-image',
            'fa-solid fa-video',
            'fa-solid fa-music',
            'fa-solid fa-headphones',
            'fa-solid fa-globe',
            'fa-solid fa-lock',
            'fa-solid fa-unlock',
            'fa-solid fa-eye',
            'fa-solid fa-eye-slash',
            'fa-solid fa-warning',
            'fa-solid fa-info-circle',
            'fa-solid fa-question-circle',
            'fa-solid fa-exclamation-circle',
            'fa-solid fa-check-circle',
            'fa-solid fa-times-circle',
            'fa-solid fa-bell',
            'fa-solid fa-tag',
            'fa-solid fa-tags',
            'fa-solid fa-book',
            'fa-solid fa-graduation-cap',
            'fa-solid fa-briefcase',
            'fa-solid fa-building',
            'fa-solid fa-hospital',
            'fa-solid fa-truck',
            'fa-solid fa-shipping-fast',
            'fa-solid fa-cart-plus',
            'fa-solid fa-shopping-cart',
            'fa-solid fa-credit-card',
            'fa-solid fa-wallet',
            'fa-solid fa-gift',
            'fa-solid fa-gem',
            'fa-solid fa-crown',
            'fa-solid fa-users',
            'fa-solid fa-user-plus',
            'fa-solid fa-user-minus',
            'fa-solid fa-user-check',
            'fa-solid fa-user-cog',
            'fa-solid fa-robot',
            'fa-solid fa-rocket',
            'fa-solid fa-plane',
            'fa-solid fa-ship',
            'fa-solid fa-train',
            'fa-solid fa-bus',
            'fa-solid fa-car',
            'fa-solid fa-bicycle',
            'fa-solid fa-tree',
            'fa-solid fa-leaf',
            'fa-solid fa-sun',
            'fa-solid fa-moon',
            'fa-solid fa-cloud',
            'fa-solid fa-rain',
            'fa-solid fa-snowflake',
            'fa-solid fa-fire',
            'fa-solid fa-bolt',
            'fa-solid fa-paw',
            'fa-solid fa-dove'
        );

        // Version 6 specific icons
        if ($version === '6') {
            $v6_icons = array(
                'fa-solid fa-earth-americas',
                'fa-solid fa-square-check',
                'fa-solid fa-square-plus',
                'fa-solid fa-square-minus',
                'fa-solid fa-folder',
                'fa-solid fa-folder-open',
                'fa-solid fa-file',
                'fa-solid fa-file-pen',
                'fa-solid fa-file-export',
                'fa-solid fa-file-import',
                'fa-solid fa-arrows-rotate',
                'fa-solid fa-repeat'
            );
            return array_merge($common_icons, $v6_icons);
        }

        // Version 5 specific icons
        $v5_icons = array(
            'fa-solid fa-address-book',
            'fa-solid fa-address-card',
            'fa-solid fa-anchor',
            'fa-solid fa-archway',
            'fa-solid fa-at'
        );
        return array_merge($common_icons, $v5_icons);
    }

    private function enqueue_scripts($version) {
        static $enqueued = false;

        if (!$enqueued) {
            // Enqueue Font Awesome
            wp_enqueue_style(
                'font-awesome-6',
                PERSIAN_FRAMEWORK_URL . 'vendor/font-awesome/css/all.min.css',
                array(),
                '6.4.2'
            );

            $enqueued = true;
        }

        ?>
        <style>
            .pf-fa-picker-container {
                position: relative;
                margin-top: 8px;
            }

            .pf-fa-picker-input {
                position: relative;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .pf-fa-picker-search {
                flex: 1;
                padding-right: 50px !important;
            }

            .pf-fa-picker-preview {
                position: absolute;
                right: 14px;
                top: 50%;
                transform: translateY(-50%);
                font-size: 20px;
                width: 30px;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #6366f1;
            }

            .pf-fa-picker-dropdown {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                z-index: 9999;
                background: white;
                border: 2px solid #e8edf4;
                border-radius: 12px;
                margin-top: 4px;
                padding: 12px;
                max-height: 300px;
                overflow: hidden;
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            }
            body.dark-mode .pf-fa-picker-dropdown {
                background: #1e293b;
                border-color: #334155;
            }

            .pf-fa-picker-search-wrap {
                margin-bottom: 10px;
            }

            .pf-fa-picker-filter {
                width: 100%;
                padding: 8px 12px;
                border: 2px solid #e8edf4;
                border-radius: 8px;
                font-size: 14px;
            }
            body.dark-mode .pf-fa-picker-filter {
                background: #0f172a;
                border-color: #334155;
                color: #e2e8f0;
            }

            .pf-fa-picker-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(40px, 1fr));
                gap: 4px;
                max-height: 220px;
                overflow-y: auto;
                padding: 4px;
            }

            .pf-fa-picker-grid::-webkit-scrollbar {
                width: 6px;
            }
            .pf-fa-picker-grid::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 10px;
            }

            .pf-fa-picker-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                border: 2px solid transparent;
                border-radius: 8px;
                cursor: pointer;
                background: transparent;
                color: #1a2332;
                font-size: 18px;
                transition: all 0.2s ease;
            }
            body.dark-mode .pf-fa-picker-icon {
                color: #e2e8f0;
            }

            .pf-fa-picker-icon:hover {
                background: #f0f0ff;
                border-color: #6366f1;
                transform: scale(1.1);
            }
            body.dark-mode .pf-fa-picker-icon:hover {
                background: #334155;
            }

            .pf-fa-picker-icon.selected {
                background: #6366f1;
                border-color: #6366f1;
                color: white;
            }
            .pf-fa-picker-icon.selected i {
                color: white;
            }

            .pf-fa-picker-icon.hidden {
                display: none;
            }

            .pf-fa-picker-icon i {
                font-size: 0px;
            }
        </style>

        <script>
            (function($) {
                'use strict';

                var $input = $('#<?php echo esc_js($this->field['id']); ?>');
                var $container = $input.closest('.pf-fa-picker-container');
                var $dropdown = $container.find('.pf-fa-picker-dropdown');
                var $filter = $container.find('.pf-fa-picker-filter');

                // Toggle dropdown
                $input.on('click focus', function() {
                    $dropdown.slideDown(200);
                    populateIcons();
                });

                // Close dropdown on outside click
                $(document).on('click', function(e) {
                    if (!$container.is(e.target) && $container.has(e.target).length === 0) {
                        $dropdown.slideUp(200);
                    }
                });

                // Filter icons
                $filter.on('input', function() {
                    var search = $(this).val().toLowerCase();
                    $container.find('.pf-fa-picker-icon').each(function() {
                        var icon = $(this).data('icon').toLowerCase();
                        $(this).toggleClass('hidden', icon.indexOf(search) === -1);
                    });
                });

                // Select icon
                $(document).on('click', '.pf-fa-picker-icon', function() {
                    var icon = $(this).data('icon');
                    $input.val(icon).trigger('change');
                    $container.find('.pf-fa-picker-preview').removeClass().addClass('pf-fa-picker-preview ' + icon);
                    $container.find('.pf-fa-picker-icon').removeClass('selected');
                    $(this).addClass('selected');
                    $dropdown.slideUp(200);
                });

                function populateIcons() {
                    // Icons are already in DOM
                }

                // Update preview on input change
                $input.on('change input', function() {
                    var val = $(this).val();
                    $container.find('.pf-fa-picker-preview').removeClass().addClass('pf-fa-picker-preview ' + val);
                });

            })(jQuery);
        </script>
        <?php
    }
}