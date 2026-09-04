<?php
/**
 * Persian Framework - Gallery Field
 * Manage multiple images with WordPress Media Library
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Gallery {

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
        $max_items = isset($this->field['max']) ? intval($this->field['max']) : 0;

        ?>
        <div class="pf-field-wrapper pf-field-gallery">
            <?php if (isset($this->field['title'])): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                    <?php if ($max_items > 0): ?>
                        <span class="pf-gallery-max"><?php printf(__('(Max: %d)', 'persian-framework'), $max_items); ?></span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-gallery-container" data-max="<?php echo esc_attr($max_items); ?>">
                <div class="pf-gallery-list">
                    <?php foreach ($value as $item): ?>
                        <?php
                        $media_id = is_array($item) ? absint($item['id'] ?? 0) : absint($item);
                        if ($media_id):
                            $url = wp_get_attachment_image_url($media_id, 'thumbnail');
                            $title = get_the_title($media_id);
                            $full_url = wp_get_attachment_image_url($media_id, 'full');
                            ?>
                            <div class="pf-gallery-item" data-id="<?php echo esc_attr($media_id); ?>">
                                <img src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr($title); ?>" />
                                <div class="pf-gallery-item-overlay">
                                    <button type="button" class="pf-gallery-remove" aria-label="<?php esc_attr_e('Remove', 'persian-framework'); ?>">
                                        <span class="dashicons dashicons-no-alt"></span>
                                    </button>
                                </div>
                                <input type="hidden"
                                       name="<?php echo esc_attr($name); ?>[]"
                                       value="<?php echo esc_attr($media_id); ?>" />
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <div class="pf-gallery-actions">
                    <button type="button" class="pf-btn pf-btn-secondary pf-gallery-choose">
                        <span class="dashicons dashicons-format-gallery"></span>
                        <?php _e('Add Images', 'persian-framework'); ?>
                    </button>
                    <button type="button" class="pf-btn pf-btn-danger pf-gallery-clear" style="<?php echo empty($value) ? 'display:none;' : ''; ?>">
                        <span class="dashicons dashicons-trash"></span>
                        <?php _e('Clear All', 'persian-framework'); ?>
                    </button>
                </div>

                <p class="pf-gallery-empty" style="<?php echo !empty($value) ? 'display:none;' : ''; ?>">
                    <?php _e('No images selected. Click "Add Images" to select.', 'persian-framework'); ?>
                </p>

                <?php if (isset($this->field['desc'])): ?>
                    <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php
        $this->enqueue_scripts();
    }

    private function enqueue_scripts() {
        static $enqueued = false;

        if (!$enqueued) {
            wp_enqueue_media();

            ?>
            <style>
                .pf-gallery-container {
                    margin-top: 8px;
                }

                .pf-gallery-list {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
                    gap: 10px;
                    margin-bottom: 12px;
                    min-height: 50px;
                }

                .pf-gallery-item {
                    position: relative;
                    aspect-ratio: 1;
                    border-radius: 8px;
                    overflow: hidden;
                    border: 2px solid #e8edf4;
                    background: #fafbfc;
                }
                body.dark-mode .pf-gallery-item {
                    border-color: #334155;
                    background: #0f172a;
                }

                .pf-gallery-item img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    display: block;
                }

                .pf-gallery-item-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.4);
                    opacity: 0;
                    transition: opacity 0.3s ease;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .pf-gallery-item:hover .pf-gallery-item-overlay {
                    opacity: 1;
                }

                .pf-gallery-remove {
                    background: rgba(239, 68, 68, 0.9);
                    border: none;
                    color: white;
                    width: 36px;
                    height: 36px;
                    border-radius: 50%;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: all 0.2s ease;
                }

                .pf-gallery-remove:hover {
                    background: #ef4444;
                    transform: scale(1.1);
                }

                .pf-gallery-remove .dashicons {
                    font-size: 20px;
                    width: 20px;
                    height: 20px;
                    color: white;
                }

                .pf-gallery-actions {
                    display: flex;
                    gap: 10px;
                    flex-wrap: wrap;
                }

                .pf-gallery-actions .pf-btn .dashicons {
                    font-size: 16px;
                    width: 16px;
                    height: 16px;
                }

                .pf-gallery-empty {
                    color: #94a3b8;
                    text-align: center;
                    padding: 20px;
                    border: 2px dashed #e8edf4;
                    border-radius: 8px;
                }
                body.dark-mode .pf-gallery-empty {
                    border-color: #334155;
                }

                .pf-gallery-max {
                    font-size: 12px;
                    font-weight: 400;
                    color: #94a3b8;
                    margin-left: 6px;
                }
            </style>

            <script>
                (function($) {
                    'use strict';

                    var frame;

                    // Open media library
                    $(document).on('click', '.pf-gallery-choose', function() {
                        var $container = $(this).closest('.pf-gallery-container');
                        var $list = $container.find('.pf-gallery-list');
                        var maxItems = parseInt($container.data('max')) || 0;
                        var existingIds = [];

                        $list.find('.pf-gallery-item').each(function() {
                            var id = $(this).data('id');
                            if (id) {
                                existingIds.push(parseInt(id));
                            }
                        });

                        if (frame) {
                            frame.open();
                            return;
                        }

                        frame = wp.media({
                            multiple: true,
                            library: {
                                type: 'image'
                            }
                        });

                        frame.on('select', function() {
                            var selection = frame.state().get('selection');

                            // ✅ Get the field name safely
                            var firstInput = $list.find('input[type="hidden"]').first();
                            var nameAttr = firstInput.attr('name');
                            var fieldName = nameAttr ? nameAttr.replace('[]', '') : '<?php echo esc_js($this->field['name']); ?>';

                            selection.each(function(attachment) {
                                var id = attachment.id;
                                var url = attachment.attributes.url;

                                // Check max limit
                                if (maxItems > 0 && $list.find('.pf-gallery-item').length >= maxItems) {
                                    alert('<?php esc_js(__('Maximum gallery items reached.', 'persian-framework')); ?>');
                                    return false;
                                }

                                // Check duplicate
                                if (existingIds.indexOf(id) !== -1) {
                                    return;
                                }

                                var $item = $('<div class="pf-gallery-item" data-id="' + id + '">' +
                                    '<img src="' + url + '" alt="">' +
                                    '<div class="pf-gallery-item-overlay">' +
                                    '<button type="button" class="pf-gallery-remove" aria-label="<?php esc_js(__('Remove', 'persian-framework')); ?>">' +
                                    '<span class="dashicons dashicons-no-alt"></span>' +
                                    '</button>' +
                                    '</div>' +
                                    '<input type="hidden" name="' + fieldName + '[]" value="' + id + '">' +
                                    '</div>');

                                $list.append($item);
                                existingIds.push(id);
                            });

                            // Show clear button
                            if ($list.find('.pf-gallery-item').length > 0) {
                                $container.find('.pf-gallery-clear').show();
                                $container.find('.pf-gallery-empty').hide();
                            }

                            $container.trigger('pf-gallery-updated');
                        });

                        frame.open();
                    });

                    // Remove individual item
                    $(document).on('click', '.pf-gallery-remove', function(e) {
                        e.stopPropagation();
                        var $item = $(this).closest('.pf-gallery-item');
                        var $container = $item.closest('.pf-gallery-container');

                        $item.fadeOut(300, function() {
                            $item.remove();
                            if ($container.find('.pf-gallery-item').length === 0) {
                                $container.find('.pf-gallery-clear').hide();
                                $container.find('.pf-gallery-empty').show();
                            }
                            $container.trigger('pf-gallery-updated');
                        });
                    });

                    // Clear all
                    $(document).on('click', '.pf-gallery-clear', function() {
                        var $container = $(this).closest('.pf-gallery-container');
                        var $list = $container.find('.pf-gallery-list');

                        if (confirm('<?php esc_js(__('Remove all images?', 'persian-framework')); ?>')) {
                            $list.empty();
                            $(this).hide();
                            $container.find('.pf-gallery-empty').show();
                            $container.trigger('pf-gallery-updated');
                        }
                    });

                    // Show clear button if items exist
                    $(document).ready(function() {
                        $('.pf-gallery-container').each(function() {
                            var $container = $(this);
                            if ($container.find('.pf-gallery-item').length > 0) {
                                $container.find('.pf-gallery-clear').show();
                                $container.find('.pf-gallery-empty').hide();
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