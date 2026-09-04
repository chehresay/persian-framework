<?php
/**
 * Persian Framework - Palette Color Field
 * Select a color palette from predefined color sets
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_PaletteColor {

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
        $palettes = isset($this->field['palettes']) ? $this->field['palettes'] : array();
        $cols = isset($this->field['cols']) ? intval($this->field['cols']) : 3;
        $required = isset($this->field['required']) && $this->field['required'] ? 'required' : '';

        // Default palettes if none provided
        if (empty($palettes)) {
            $palettes = array(
                'default' => array(
                    '#6366f1',
                    '#8b5cf6',
                    '#a78bfa',
                    '#c4b5fd',
                ),
                'green' => array(
                    '#059669',
                    '#10b981',
                    '#34d399',
                    '#6ee7b7',
                ),
                'red' => array(
                    '#dc2626',
                    '#ef4444',
                    '#f87171',
                    '#fca5a5',
                ),
                'orange' => array(
                    '#d97706',
                    '#f59e0b',
                    '#fbbf24',
                    '#fcd34d',
                ),
                'blue' => array(
                    '#2563eb',
                    '#3b82f6',
                    '#60a5fa',
                    '#93c5fd',
                ),
                'pink' => array(
                    '#db2777',
                    '#ec4899',
                    '#f472b6',
                    '#f9a8d4',
                ),
                'gray' => array(
                    '#4b5563',
                    '#6b7280',
                    '#9ca3af',
                    '#d1d5db',
                ),
                'dark' => array(
                    '#1e293b',
                    '#334155',
                    '#475569',
                    '#64748b',
                ),
            );
        }

        // Get palette colors for preview
        $selected_colors = isset($palettes[$value]) ? $palettes[$value] : array();

        ?>
        <div class="pf-field-wrapper pf-field-palette-color">
            <?php if (isset($this->field['title'])): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                    <?php if ($required): ?>
                        <span class="pf-required">*</span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-palette-container" data-cols="<?php echo esc_attr($cols); ?>">
                <div class="pf-palette-grid">
                    <?php foreach ($palettes as $key => $colors): ?>
                        <?php
                        $selected = ($value == $key) ? 'selected' : '';
                        $first_color = isset($colors[0]) ? $colors[0] : '#6366f1';
                        ?>
                        <label class="pf-palette-item <?php echo esc_attr($selected); ?>" data-palette="<?php echo esc_attr($key); ?>">
                            <input type="radio"
                                   name="<?php echo esc_attr($name); ?>"
                                   value="<?php echo esc_attr($key); ?>"
                                <?php checked($value, $key); ?>
                                <?php echo $required; ?> />

                            <div class="pf-palette-preview">
                                <?php foreach ($colors as $index => $color): ?>
                                    <span class="pf-palette-swatch" style="background-color: <?php echo esc_attr($color); ?>;
                                    <?php if ($index === 0): ?>border-radius: 8px 0 0 8px;<?php endif; ?>
                                    <?php if ($index === count($colors) - 1): ?>border-radius: 0 8px 8px 0;<?php endif; ?>">
                                    </span>
                                <?php endforeach; ?>
                                <span class="pf-palette-check">
                                    <span class="dashicons dashicons-yes-alt"></span>
                                </span>
                            </div>

                            <span class="pf-palette-label"><?php echo esc_html(ucfirst(str_replace('_', ' ', $key))); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($selected_colors)): ?>
                    <div class="pf-palette-selected-preview">
                        <strong><?php _e('Selected Palette:', 'persian-framework'); ?></strong>
                        <span class="pf-palette-selected-name"><?php echo esc_html(ucfirst(str_replace('_', ' ', $value))); ?></span>
                        <div class="pf-palette-selected-colors">
                            <?php foreach ($selected_colors as $color): ?>
                                <span class="pf-palette-swatch" style="background-color: <?php echo esc_attr($color); ?>;"></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
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
                /* ============================================================
                   Palette Color Field
                   ============================================================ */
                .pf-palette-container {
                    margin-top: 8px;
                }

                .pf-palette-grid {
                    display: grid;
                    grid-template-columns: repeat(var(--pf-palette-cols, 4), 1fr);
                    gap: 12px;
                    margin-bottom: 16px;
                }

                .pf-palette-item {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 8px;
                    cursor: pointer;
                    padding: 8px;
                    border: 3px solid transparent;
                    border-radius: 12px;
                    background: #fafbfc;
                    transition: all 0.25s ease;
                    position: relative;
                    user-select: none;
                }
                body.dark-mode .pf-palette-item {
                    background: #0f172a;
                }

                .pf-palette-item:hover {
                    border-color: #cbd5e1;
                    background: #f1f5f9;
                }
                body.dark-mode .pf-palette-item:hover {
                    border-color: #334155;
                    background: #1e293b;
                }

                .pf-palette-item.selected {
                    border-color: #6366f1;
                    background: #eef2ff;
                    box-shadow: 0 4px 16px rgba(99, 102, 241, 0.25);
                }
                body.dark-mode .pf-palette-item.selected {
                    background: #1e1b4b;
                    border-color: #818cf8;
                }

                .pf-palette-item input[type="radio"] {
                    position: absolute;
                    opacity: 0;
                    width: 0;
                    height: 0;
                }

                .pf-palette-preview {
                    position: relative;
                    display: flex;
                    width: 100%;
                    height: 40px;
                    border-radius: 8px;
                    overflow: hidden;
                    border: 2px solid #e8edf4;
                    transition: all 0.25s ease;
                }
                body.dark-mode .pf-palette-preview {
                    border-color: #334155;
                }

                .pf-palette-item.selected .pf-palette-preview {
                    border-color: #6366f1;
                }

                .pf-palette-swatch {
                    flex: 1;
                    height: 100%;
                    min-width: 8px;
                    transition: all 0.3s ease;
                }

                .pf-palette-swatch:first-child {
                    border-radius: 6px 0 0 6px;
                }

                .pf-palette-swatch:last-child {
                    border-radius: 0 6px 6px 0;
                }

                .pf-palette-check {
                    position: absolute;
                    top: -6px;
                    right: -6px;
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

                .pf-palette-item.selected .pf-palette-check {
                    opacity: 1;
                    transform: scale(1);
                }

                .pf-palette-check .dashicons {
                    font-size: 14px;
                    width: 14px;
                    height: 14px;
                    color: white;
                }

                .pf-palette-label {
                    font-size: 13px;
                    font-weight: 500;
                    color: #1a2332;
                    text-align: center;
                    transition: color 0.25s ease;
                }
                body.dark-mode .pf-palette-label {
                    color: #e2e8f0;
                }

                .pf-palette-item.selected .pf-palette-label {
                    color: #6366f1;
                }
                body.dark-mode .pf-palette-item.selected .pf-palette-label {
                    color: #818cf8;
                }

                /* Selected palette preview */
                .pf-palette-selected-preview {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    padding: 12px 16px;
                    background: #f8f9fa;
                    border-radius: 8px;
                    border: 1px solid #e8edf4;
                    flex-wrap: wrap;
                }
                body.dark-mode .pf-palette-selected-preview {
                    background: #0f172a;
                    border-color: #334155;
                }

                .pf-palette-selected-name {
                    font-weight: 600;
                    color: #6366f1;
                }
                body.dark-mode .pf-palette-selected-name {
                    color: #818cf8;
                }

                .pf-palette-selected-colors {
                    display: flex;
                    gap: 4px;
                    flex: 1;
                }

                .pf-palette-selected-colors .pf-palette-swatch {
                    width: 30px;
                    height: 30px;
                    border-radius: 4px;
                    border: 1px solid #e8edf4;
                }
                body.dark-mode .pf-palette-selected-colors .pf-palette-swatch {
                    border-color: #334155;
                }

                /* Responsive */
                .pf-palette-grid {
                    --pf-palette-cols: <?php echo intval($this->field['cols'] ?? 4); ?>;
                }

                @media (max-width: 1024px) {
                    .pf-palette-grid {
                        --pf-palette-cols: 3;
                    }
                }

                @media (max-width: 768px) {
                    .pf-palette-grid {
                        --pf-palette-cols: 2;
                    }
                    .pf-palette-preview {
                        height: 50px;
                    }
                }

                @media (max-width: 480px) {
                    .pf-palette-grid {
                        --pf-palette-cols: 2;
                        gap: 8px;
                    }
                    .pf-palette-item {
                        padding: 4px;
                    }
                    .pf-palette-label {
                        font-size: 11px;
                    }
                    .pf-palette-selected-preview {
                        flex-direction: column;
                        align-items: stretch;
                        text-align: center;
                    }
                }
            </style>

            <script>
                (function($) {
                    'use strict';

                    // Handle palette selection
                    $(document).on('change', '.pf-palette-item input[type="radio"]', function() {
                        var $item = $(this).closest('.pf-palette-item');
                        var $container = $item.closest('.pf-palette-grid');

                        // Remove selected class from all items
                        $container.find('.pf-palette-item').removeClass('selected');

                        // Add selected class to the clicked item
                        $item.addClass('selected');

                        // Update selected preview
                        var $parentContainer = $container.closest('.pf-palette-container');
                        var paletteKey = $item.data('palette');
                        var paletteLabel = $item.find('.pf-palette-label').text();
                        var paletteColors = [];

                        $item.find('.pf-palette-swatch').each(function() {
                            paletteColors.push($(this).css('background-color'));
                        });

                        // Update preview
                        var $preview = $parentContainer.find('.pf-palette-selected-preview');
                        if ($preview.length) {
                            $preview.find('.pf-palette-selected-name').text(paletteLabel);
                            var $colorsContainer = $preview.find('.pf-palette-selected-colors');
                            $colorsContainer.empty();
                            paletteColors.forEach(function(color) {
                                $colorsContainer.append('<span class="pf-palette-swatch" style="background-color: ' + color + ';"></span>');
                            });
                        } else {
                            // Create preview if not exists
                            var previewHtml = '<div class="pf-palette-selected-preview">' +
                                '<strong><?php _e('Selected Palette:', 'persian-framework'); ?></strong>' +
                                '<span class="pf-palette-selected-name">' + paletteLabel + '</span>' +
                                '<div class="pf-palette-selected-colors">';
                            paletteColors.forEach(function(color) {
                                previewHtml += '<span class="pf-palette-swatch" style="background-color: ' + color + ';"></span>';
                            });
                            previewHtml += '</div></div>';
                            $parentContainer.append(previewHtml);
                        }

                        // Trigger event
                        $container.trigger('pf-palette-select', [paletteKey]);
                        $(document).trigger('pf-palette-updated', [paletteKey]);
                    });

                    // Initialize selected state
                    $(document).ready(function() {
                        $('.pf-palette-grid').each(function() {
                            var $container = $(this);
                            var $checked = $container.find('input[type="radio"]:checked');
                            if ($checked.length) {
                                $checked.closest('.pf-palette-item').addClass('selected');
                            } else {
                                // If no checked item, select the first one if default is set
                                var $first = $container.find('.pf-palette-item').first();
                                if ($first.length) {
                                    // Don't auto-select, let the default value handle it
                                }
                            }
                        });
                    });

                })(jQuery);
            </script>
            <?php
            $enqueued = true;
        }
    }
}