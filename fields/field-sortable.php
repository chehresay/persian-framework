<?php
/**
 * Persian Framework - Sortable Field
 * Drag and drop sorting with SortableJS
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Sortable {

    private $field;
    private $value;

    public function __construct($field, $value = null) {
        $this->field = $field;
        $this->value = $value;
    }

    public function render() {
        $id = isset($this->field['id']) ? $this->field['id'] : '';
        $name = isset($this->field['name']) ? $this->field['name'] : $id;
        $options = isset($this->field['options']) ? $this->field['options'] : array();
        $value = $this->value !== null ? (array) $this->value : (isset($this->field['default']) ? (array) $this->field['default'] : array());

        if (empty($value)) {
            $value = $options;
        }

        $allow_new = isset($this->field['allow_new']) && $this->field['allow_new'];
        $max_items = isset($this->field['max']) ? intval($this->field['max']) : 0;
        $min_items = isset($this->field['min']) ? intval($this->field['min']) : 0;

        if ($allow_new) {
            $merged = $value;
            foreach ($options as $key => $label) {
                if (!isset($merged[$key])) {
                    // Keep only saved values
                }
            }
            $value = $merged;
        } else {
            $ordered = array();
            foreach ($options as $key => $label) {
                if (isset($value[$key])) {
                    $ordered[$key] = $value[$key];
                }
            }
            $value = $ordered;
        }
        ?>
        <div class="pf-field-wrapper pf-field-sortable">
            <?php if (isset($this->field['title'])): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                    <?php if ($max_items > 0): ?>
                        <span class="pf-sortable-max-label">
                            <?php
                            printf(
                            /* translators: %d: Maximum number of items */
                                    esc_html__('(Max: %d)', 'persian-framework'),
                                    $max_items
                            );
                            ?>
                        </span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-sortable-container"
                 data-field="<?php echo esc_attr($id); ?>"
                 data-name="<?php echo esc_attr($name); ?>"
                 data-max="<?php echo esc_attr($max_items); ?>"
                 data-min="<?php echo esc_attr($min_items); ?>">

                <div class="pf-sortable-list" id="sortable-<?php echo esc_attr($id); ?>">
                    <?php if (empty($value)): ?>
                        <div class="pf-sortable-empty"><?php esc_html_e('No items. Add new items below.', 'persian-framework'); ?></div>
                    <?php else: ?>
                        <?php foreach ($value as $key => $label): ?>
                            <div class="pf-sortable-item" data-key="<?php echo esc_attr($key); ?>">
                                <span class="pf-sortable-handle dashicons dashicons-move"></span>
                                <span class="pf-sortable-label"><?php echo esc_html($label); ?></span>
                                <input type="hidden"
                                       name="<?php echo esc_attr($name); ?>[<?php echo esc_attr($key); ?>]"
                                       value="<?php echo esc_attr($label); ?>" />
                                <button type="button" class="pf-sortable-remove dashicons dashicons-no-alt" title="<?php esc_attr_e('Remove', 'persian-framework'); ?>"></button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?php if ($allow_new): ?>
                    <div class="pf-sortable-add-new">
                        <div class="pf-sortable-add-input">
                            <input type="text" class="pf-sortable-new-key" placeholder="<?php esc_attr_e('Key', 'persian-framework'); ?>" />
                            <input type="text" class="pf-sortable-new-label" placeholder="<?php esc_attr_e('Label', 'persian-framework'); ?>" />
                            <button type="button" class="pf-btn pf-btn-secondary pf-sortable-add-btn">
                                <span class="dashicons dashicons-plus-alt"></span> <?php esc_html_e('Add Item', 'persian-framework'); ?>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (isset($this->field['desc'])): ?>
                <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php
        $this->enqueue_scripts($id);
    }

    private function enqueue_scripts($id) {
        static $pf_sortable_enqueued = false;
        $field_id = $id;

        if (!$pf_sortable_enqueued) {
            ?>
            <style>
                .pf-sortable-container {
                    margin-top: 8px;
                }

                .pf-sortable-list {
                    min-height: 50px;
                    border: 2px dashed #e8edf4;
                    border-radius: 12px;
                    padding: 8px;
                    background: #fafbfc;
                    transition: all 0.2s ease;
                }
                body.dark-mode .pf-sortable-list {
                    border-color: #334155;
                    background: #0f172a;
                }
                .pf-sortable-list.sortable-drag-over {
                    border-color: #6366f1;
                    background: rgba(99, 102, 241, 0.05);
                }

                .pf-sortable-item {
                    background: white;
                    border: 1px solid #e8edf4;
                    border-radius: 8px;
                    padding: 12px 16px;
                    margin-bottom: 6px;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    cursor: grab;
                    transition: all 0.2s ease;
                }
                body.dark-mode .pf-sortable-item {
                    background: #1e293b;
                    border-color: #334155;
                    color: #e2e8f0;
                }

                .pf-sortable-item:hover {
                    border-color: #6366f1;
                    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.1);
                }

                .pf-sortable-item:last-child {
                    margin-bottom: 0;
                }

                .pf-sortable-item.sortable-ghost {
                    background: rgba(99, 102, 241, 0.08) !important;
                    border: 2px dashed #6366f1 !important;
                    opacity: 0.6;
                }

                .pf-sortable-item.sortable-chosen {
                    background: #f0f0ff !important;
                    border: 2px solid #6366f1 !important;
                    box-shadow: 0 8px 30px rgba(99, 102, 241, 0.3) !important;
                    transform: scale(1.02);
                }

                .pf-sortable-item.sortable-drag {
                    cursor: grabbing !important;
                    opacity: 0.9;
                    z-index: 99999;
                }

                .pf-sortable-handle {
                    color: #94a3b8;
                    cursor: grab;
                    font-size: 18px;
                    flex-shrink: 0;
                    transition: color 0.2s ease;
                }

                .pf-sortable-handle:hover {
                    color: #6366f1;
                }

                .pf-sortable-label {
                    flex: 1;
                    font-size: 14px;
                    color: #1a2332;
                }
                body.dark-mode .pf-sortable-label {
                    color: #e2e8f0;
                }

                .pf-sortable-remove {
                    background: none;
                    border: none;
                    color: #94a3b8;
                    cursor: pointer;
                    font-size: 16px;
                    padding: 4px;
                    border-radius: 4px;
                    transition: all 0.2s ease;
                    flex-shrink: 0;
                }

                .pf-sortable-remove:hover {
                    color: #ef4444;
                    background: rgba(239, 68, 68, 0.1);
                }

                .pf-sortable-add-new {
                    margin-top: 12px;
                }

                .pf-sortable-add-input {
                    display: flex;
                    gap: 8px;
                    flex-wrap: wrap;
                }

                .pf-sortable-add-input input {
                    padding: 10px 14px;
                    border: 2px solid #e8edf4;
                    border-radius: 8px;
                    font-size: 14px;
                    flex: 1;
                    min-width: 100px;
                    background: #fafbfc;
                    transition: all 0.2s ease;
                }

                .pf-sortable-add-input input:focus {
                    border-color: #6366f1;
                    outline: none;
                    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
                }

                body.dark-mode .pf-sortable-add-input input {
                    background: #0f172a;
                    border-color: #334155;
                    color: #e2e8f0;
                }

                .pf-sortable-add-btn .dashicons {
                    font-size: 16px;
                    width: 16px;
                    height: 16px;
                }

                .pf-sortable-empty {
                    text-align: center;
                    color: #94a3b8;
                    padding: 20px 0;
                    font-size: 14px;
                }

                .pf-sortable-max-label {
                    font-size: 12px;
                    font-weight: 400;
                    color: #94a3b8;
                    margin-left: 6px;
                }

                @media (max-width: 768px) {
                    .pf-sortable-item {
                        flex-wrap: wrap;
                    }
                    .pf-sortable-add-input {
                        flex-direction: column;
                    }
                    .pf-sortable-add-input input {
                        min-width: auto;
                    }
                }
            </style>
            <?php
            $pf_sortable_enqueued = true;
        }

        ?>
        <script>
            (function($) {
                'use strict';

                var fieldId = '<?php echo esc_js($field_id); ?>';
                var containerSelector = '#sortable-' + fieldId;
                var container = document.getElementById('sortable-' + fieldId);
                var sortableInstance = null;
                var isInitializing = false;

                function ensureDataKeys(container) {
                    var items = container.querySelectorAll('.pf-sortable-item');
                    items.forEach(function(item) {
                        if (!item.dataset.key || item.dataset.key === '') {
                            var input = item.querySelector('input[type="hidden"]');
                            if (input) {
                                var name = input.getAttribute('name');
                                var match = name.match(/\[([^\]]+)\]$/);
                                if (match) {
                                    var key = match[1];
                                    item.dataset.key = key;
                                }
                            }
                        }
                    });
                }

                function initSortable() {
                    if (isInitializing) return;

                    var container = document.getElementById('sortable-' + fieldId);
                    if (!container) {
                        console.warn('Sortable container not found:', containerSelector);
                        return;
                    }

                    if (typeof Sortable === 'undefined') {
                        setTimeout(initSortable, 500);
                        return;
                    }

                    isInitializing = true;

                    if (sortableInstance) {
                        try {
                            sortableInstance.destroy();
                        } catch(e) {
                            console.warn('Error destroying sortable:', e);
                        }
                        sortableInstance = null;
                    }

                    var items = container.querySelectorAll('.pf-sortable-item');
                    var hasItems = items.length > 0;

                    if (!hasItems) {
                        var emptyEl = container.querySelector('.pf-sortable-empty');
                        if (!emptyEl) {
                            container.innerHTML = '<div class="pf-sortable-empty"><?php esc_html_e('No items. Add new items below.', 'persian-framework'); ?></div>';
                        }
                        isInitializing = false;
                        return;
                    }

                    var emptyEl = container.querySelector('.pf-sortable-empty');
                    if (emptyEl) {
                        emptyEl.remove();
                    }

                    ensureDataKeys(container);

                    try {
                        sortableInstance = new Sortable(container, {
                            animation: 150,
                            handle: '.pf-sortable-handle',
                            ghostClass: 'sortable-ghost',
                            chosenClass: 'sortable-chosen',
                            dragClass: 'sortable-drag',
                            forceFallback: true,
                            fallbackClass: 'sortable-ghost',
                            fallbackOnBody: true,
                            onEnd: function(evt) {
                                updateSortableData(container);
                            }
                        });

                        container._sortableInstance = sortableInstance;
                    } catch(e) {
                        console.error('Error creating sortable:', e);
                    }

                    isInitializing = false;
                }

                function updateSortableData(container) {
                    if (!container) return;

                    pfChanges();

                    var items = container.querySelectorAll('.pf-sortable-item');
                    var $container = $(container).closest('.pf-sortable-container');
                    var name = $container.data('name');

                    items.forEach(function(item, index) {
                        var key = item.dataset.key;
                        var labelEl = item.querySelector('.pf-sortable-label');
                        var input = item.querySelector('input[type="hidden"]');

                        if (!key || !labelEl) return;

                        if (!input) {
                            input = document.createElement('input');
                            input.type = 'hidden';
                            item.appendChild(input);
                        }

                        input.name = name + '[' + key + ']';
                        input.value = labelEl.textContent.trim();
                    });

                    $(container).trigger('change');
                    $(document).trigger('pf-sortable-updated', [fieldId]);
                }

                function addSortableItem(key, label) {
                    var container = document.getElementById('sortable-' + fieldId);
                    if (!container) return;

                    var $container = $(container).closest('.pf-sortable-container');
                    var name = $container.data('name');
                    var max = parseInt($container.data('max')) || 0;

                    var currentItems = container.querySelectorAll('.pf-sortable-item').length;
                    if (max > 0 && currentItems >= max) {
                        alert('<?php esc_html_e('Maximum number of items reached.', 'persian-framework'); ?>');
                        return false;
                    }

                    var emptyEl = container.querySelector('.pf-sortable-empty');
                    if (emptyEl) {
                        emptyEl.remove();
                    }

                    var existing = container.querySelector('.pf-sortable-item[data-key="' + key + '"]');
                    if (existing) {
                        alert('<?php esc_html_e('Item with this key already exists.', 'persian-framework'); ?>');
                        return false;
                    }

                    var html = '<div class="pf-sortable-item" data-key="' + key + '">';
                    html += '<span class="pf-sortable-handle dashicons dashicons-move"></span>';
                    html += '<span class="pf-sortable-label">' + label + '</span>';
                    html += '<input type="hidden" name="' + name + '[' + key + ']" value="' + label + '">';
                    html += '<button type="button" class="pf-sortable-remove dashicons dashicons-no-alt" title="<?php esc_attr_e('Remove', 'persian-framework'); ?>"></button>';
                    html += '</div>';

                    var tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    var newItem = tempDiv.firstElementChild;
                    container.appendChild(newItem);

                    setTimeout(function() {
                        initSortable();
                    }, 50);

                    $(container).trigger('change');
                    $(document).trigger('pf-sortable-add', [fieldId, key, label]);

                    return true;
                }

                function removeSortableItem($item) {
                    var container = $item.closest('.pf-sortable-list')[0];
                    var $container = $item.closest('.pf-sortable-container');
                    var min = parseInt($container.data('min')) || 0;
                    var currentCount = container.querySelectorAll('.pf-sortable-item').length;

                    if (currentCount <= min) {
                        alert('<?php esc_html_e('Minimum number of items required.', 'persian-framework'); ?>');
                        return;
                    }

                    if (!confirm('<?php esc_html_e('Remove this item?', 'persian-framework'); ?>')) {
                        return;
                    }

                    var key = $item.data('key');
                    $item.fadeOut(300, function() {
                        $item.remove();

                        if (container.querySelectorAll('.pf-sortable-item').length === 0) {
                            container.innerHTML = '<div class="pf-sortable-empty"><?php esc_html_e('No items. Add new items below.', 'persian-framework'); ?></div>';
                        }

                        setTimeout(function() {
                            initSortable();
                        }, 50);

                        $(container).trigger('change');
                        $(document).trigger('pf-sortable-remove', [fieldId, key]);
                    });
                }

                $(document).on('click', '.pf-sortable-add-btn', function(e) {
                    e.preventDefault();

                    var $container = $(this).closest('.pf-sortable-container');
                    var $keyInput = $container.find('.pf-sortable-new-key');
                    var $labelInput = $container.find('.pf-sortable-new-label');
                    var key = $keyInput.val().trim();
                    var label = $labelInput.val().trim();

                    if (!key || !label) {
                        alert('<?php esc_html_e('Please enter both key and label.', 'persian-framework'); ?>');
                        return;
                    }

                    if (addSortableItem(key, label)) {
                        $keyInput.val('');
                        $labelInput.val('');
                        $keyInput.focus();
                    }
                });

                $(document).on('click', '.pf-sortable-remove', function(e) {
                    e.stopPropagation();
                    var $item = $(this).closest('.pf-sortable-item');
                    removeSortableItem($item);
                });

                $(document).on('keydown', '.pf-sortable-new-key, .pf-sortable-new-label', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        $(this).closest('.pf-sortable-container').find('.pf-sortable-add-btn').click();
                    }
                });

                $(document).ready(function() {
                    var container = document.getElementById('sortable-' + fieldId);
                    if (container) {
                        ensureDataKeys(container);
                    }

                    setTimeout(initSortable, 300);
                });

                $(document).on('pf-tab-switch', function() {
                    setTimeout(initSortable, 400);
                });

                $(document).on('pf-repeater-add', function() {
                    setTimeout(initSortable, 500);
                });

                $(document).on('pf-sortable-updated', function() {
                    if (!isInitializing) {
                        setTimeout(initSortable, 100);
                    }
                });

                window.pfSortable = {
                    init: initSortable,
                    add: addSortableItem,
                    remove: removeSortableItem,
                    update: updateSortableData,
                    ensureDataKeys: ensureDataKeys
                };

            })(jQuery);
        </script>
        <?php
    }

    public function sanitize($value) {
        if (!is_array($value)) {
            return array();
        }

        $sanitized = array();
        foreach ($value as $key => $label) {
            $sanitized[sanitize_text_field($key)] = sanitize_text_field($label);
        }

        return $sanitized;
    }
}