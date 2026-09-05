<?php
/**
 * Persian Framework - Sorter Field
 * Drag and drop sorting between enabled/disabled columns
 * Using SortableJS library
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Sorter {

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
        $default = isset($this->field['default']) ? $this->field['default'] : array();
        $value = $this->value !== null ? $this->value : $default;

        if (!isset($value['enabled']) || !is_array($value['enabled'])) {
            $value['enabled'] = array();
        }
        if (!isset($value['disabled']) || !is_array($value['disabled'])) {
            $value['disabled'] = array();
        }

        $all_items = $options;

        if (isset($options['enabled']) && is_array($options['enabled'])) {
            $all_items = array();
            if (isset($options['enabled']) && is_array($options['enabled'])) {
                $all_items = array_merge($all_items, $options['enabled']);
            }
            if (isset($options['disabled']) && is_array($options['disabled'])) {
                $all_items = array_merge($all_items, $options['disabled']);
            }
        }

        foreach ($all_items as $key => $label) {
            $in_enabled = isset($value['enabled'][$key]);
            $in_disabled = isset($value['disabled'][$key]);

            if (!$in_enabled && !$in_disabled) {
                $value['enabled'][$key] = $label;
            }
        }

        foreach ($value['enabled'] as $key => $label) {
            if (!isset($all_items[$key])) {
                unset($value['enabled'][$key]);
            }
        }
        foreach ($value['disabled'] as $key => $label) {
            if (!isset($all_items[$key])) {
                unset($value['disabled'][$key]);
            }
        }

        $enabled_items = $value['enabled'];
        $disabled_items = $value['disabled'];
        ?>

        <div class="pf-field-wrapper pf-field-sorter">
            <?php if (isset($this->field['title'])): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-sorter-container" data-field="<?php echo esc_attr($id); ?>">

                <div class="pf-sorter-column pf-sorter-enabled">
                    <div class="pf-sorter-header">
                        <span class="pf-sorter-title"><?php esc_html_e('Enabled', 'persian-framework'); ?></span>
                        <span class="pf-sorter-count" id="count-enabled-<?php echo esc_attr($id); ?>">
                            <?php echo count($enabled_items); ?>
                        </span>
                    </div>
                    <div class="pf-sorter-list" data-column="enabled" id="sorter-enabled-<?php echo esc_attr($id); ?>">
                        <?php foreach ($enabled_items as $key => $label): ?>
                            <div class="pf-sorter-item" data-key="<?php echo esc_attr($key); ?>">
                                <span class="pf-sorter-handle dashicons dashicons-move"></span>
                                <span class="pf-sorter-label"><?php echo esc_html($label); ?></span>
                                <input type="hidden"
                                       name="<?php echo esc_attr($name); ?>[enabled][<?php echo esc_attr($key); ?>]"
                                       value="<?php echo esc_attr($label); ?>" />
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="pf-sorter-column pf-sorter-disabled">
                    <div class="pf-sorter-header">
                        <span class="pf-sorter-title"><?php esc_html_e('Disabled', 'persian-framework'); ?></span>
                        <span class="pf-sorter-count" id="count-disabled-<?php echo esc_attr($id); ?>">
                            <?php echo count($disabled_items); ?>
                        </span>
                    </div>
                    <div class="pf-sorter-list" data-column="disabled" id="sorter-disabled-<?php echo esc_attr($id); ?>">
                        <?php foreach ($disabled_items as $key => $label): ?>
                            <div class="pf-sorter-item" data-key="<?php echo esc_attr($key); ?>">
                                <span class="pf-sorter-handle dashicons dashicons-move"></span>
                                <span class="pf-sorter-label"><?php echo esc_html($label); ?></span>
                                <input type="hidden"
                                       name="<?php echo esc_attr($name); ?>[disabled][<?php echo esc_attr($key); ?>]"
                                       value="<?php echo esc_attr($label); ?>" />
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

            <?php if (isset($this->field['desc'])): ?>
                <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php
        $this->enqueue_scripts($id);
    }

    private function enqueue_scripts($id = '') {
        static $pf_sorter_enqueued = false;
        static $field_initialized = array();

        if (in_array($id, $field_initialized)) {
            return;
        }
        $field_initialized[] = $id;

        if (!$pf_sorter_enqueued) {
            ?>
            <style>
                .pf-sorter-container {
                    display: flex;
                    gap: 20px;
                    margin-top: 8px;
                    border: 2px solid #e8edf4;
                    border-radius: 12px;
                    padding: 16px;
                    background: #fafbfc;
                    min-height: 200px;
                }
                body.dark-mode .pf-sorter-container {
                    border-color: #334155;
                    background: #0f172a;
                }

                .pf-sorter-column {
                    flex: 1;
                    background: white;
                    border-radius: 8px;
                    border: 1px solid #e8edf4;
                    overflow: hidden;
                    min-height: 150px;
                }
                body.dark-mode .pf-sorter-column {
                    background: #1e293b;
                    border-color: #334155;
                }

                .pf-sorter-header {
                    background: #f8f9fa;
                    padding: 10px 14px;
                    border-bottom: 1px solid #e8edf4;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    font-weight: 600;
                    font-size: 13px;
                    color: #1a2332;
                }
                body.dark-mode .pf-sorter-header {
                    background: #0f172a;
                    border-color: #334155;
                    color: #e2e8f0;
                }

                .pf-sorter-count {
                    background: #6366f1;
                    color: white;
                    border-radius: 50%;
                    width: 24px;
                    height: 24px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 12px;
                    font-weight: 700;
                }

                .pf-sorter-disabled .pf-sorter-count {
                    background: #ef4444;
                }

                .pf-sorter-list {
                    padding: 6px;
                    min-height: 100px;
                }

                .pf-sorter-item {
                    background: white;
                    border: 1px solid #e8edf4;
                    border-radius: 6px;
                    padding: 10px 12px;
                    margin-bottom: 6px;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    cursor: grab;
                    transition: all 0.2s ease;
                }
                body.dark-mode .pf-sorter-item {
                    background: #1e293b;
                    border-color: #334155;
                    color: #e2e8f0;
                }

                .pf-sorter-item:hover {
                    border-color: #6366f1;
                    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.1);
                }

                .pf-sorter-item:last-child {
                    margin-bottom: 0;
                }

                .pf-sorter-handle {
                    color: #94a3b8;
                    cursor: grab;
                    font-size: 16px;
                    flex-shrink: 0;
                }

                .pf-sorter-handle:hover {
                    color: #6366f1;
                }

                .pf-sorter-label {
                    flex: 1;
                    font-size: 14px;
                    color: #1a2332;
                }
                body.dark-mode .pf-sorter-label {
                    color: #e2e8f0;
                }

                .pf-sorter-item.sortable-ghost {
                    background: rgba(99, 102, 241, 0.08) !important;
                    border: 2px dashed #6366f1 !important;
                    opacity: 0.6;
                }

                .pf-sorter-item.sortable-chosen {
                    background: #f0f0ff !important;
                    border: 2px solid #6366f1 !important;
                    box-shadow: 0 8px 30px rgba(99, 102, 241, 0.3) !important;
                    transform: rotate(2deg) scale(1.02);
                    cursor: grabbing !important;
                }

                .pf-sorter-item.sortable-drag {
                    cursor: grabbing !important;
                    opacity: 0.9;
                    z-index: 99999;
                }

                @media (max-width: 768px) {
                    .pf-sorter-container {
                        flex-direction: column;
                    }
                }
            </style>

            <script>
                (function($) {
                    'use strict';

                    function initSorters() {
                        if (typeof Sortable === 'undefined') {
                            console.warn('SortableJS not loaded');
                            return;
                        }

                        $('.pf-sorter-container').each(function() {
                            var $container = $(this);
                            var fieldId = $container.data('field');
                            var $enabledList = $container.find('.pf-sorter-list[data-column="enabled"]');
                            var $disabledList = $container.find('.pf-sorter-list[data-column="disabled"]');

                            if ($enabledList.length === 0 || $disabledList.length === 0) {
                                return;
                            }

                            if ($enabledList.data('sortable')) {
                                $enabledList.data('sortable').destroy();
                            }
                            if ($disabledList.data('sortable')) {
                                $disabledList.data('sortable').destroy();
                            }

                            var enabledSortable = new Sortable($enabledList[0], {
                                group: 'sorter-' + fieldId,
                                animation: 150,
                                handle: '.pf-sorter-handle',
                                ghostClass: 'sortable-ghost',
                                chosenClass: 'sortable-chosen',
                                dragClass: 'sortable-drag',
                                onEnd: function(evt) {
                                    updateSorterData($container, fieldId);
                                }
                            });

                            var disabledSortable = new Sortable($disabledList[0], {
                                group: 'sorter-' + fieldId,
                                animation: 150,
                                handle: '.pf-sorter-handle',
                                ghostClass: 'sortable-ghost',
                                chosenClass: 'sortable-chosen',
                                dragClass: 'sortable-drag',
                                onEnd: function(evt) {
                                    updateSorterData($container, fieldId);
                                }
                            });

                            $enabledList.data('sortable', enabledSortable);
                            $disabledList.data('sortable', disabledSortable);
                            $container.data('sortable-initialized', true);
                        });
                    }

                    function updateSorterData($container, fieldId) {
                        $container.find('input[type="hidden"]').remove();

                        var optName = $('#pfSettingsForm').find('input[name="opt_name"]').val();

                        if (!optName) {
                            var firstInput = $container.find('input[type="hidden"]').first();
                            if (firstInput.length) {
                                var nameAttr = firstInput.attr('name');
                                if (nameAttr) {
                                    var match = nameAttr.match(/^([^\[]+)\[/);
                                    if (match) {
                                        optName = match[1];
                                    }
                                }
                            }
                        }

                        if (!optName) {
                            optName = 'persian_framework_options';
                        }

                        $container.find('.pf-sorter-list').each(function() {
                            var column = $(this).data('column');
                            var columnName = optName + '[' + fieldId + '][' + column + ']';

                            $(this).find('.pf-sorter-item').each(function() {
                                var key = $(this).data('key');
                                var label = $(this).find('.pf-sorter-label').text().trim();

                                var $input = $('<input>')
                                    .attr('type', 'hidden')
                                    .attr('name', columnName + '[' + key + ']')
                                    .val(label);

                                $(this).append($input);
                            });
                        });

                        $container.find('.pf-sorter-column').each(function() {
                            var count = $(this).find('.pf-sorter-item').length;
                            var $count = $(this).find('.pf-sorter-count');
                            $count.text(count);
                        });

                        $container.trigger('change');
                        $(document).trigger('pf-sorter-updated', [fieldId]);
                    }

                    $(document).ready(function() {
                        setTimeout(initSorters, 300);
                    });

                    $(document).on('pf-repeater-add', function() {
                        setTimeout(initSorters, 300);
                    });

                })(jQuery);
            </script>
            <?php

            if (!isset($GLOBALS['pf_sorter_initialized'])) {
                $GLOBALS['pf_sorter_initialized'] = array();
            }
            if (!in_array($id, $GLOBALS['pf_sorter_initialized'])) {
                $GLOBALS['pf_sorter_initialized'][] = $id;
            }

            $pf_sorter_enqueued = true;
        }
    }

    public function sanitize($value) {
        if (!is_array($value)) {
            return array(
                    'enabled' => array(),
                    'disabled' => array()
            );
        }

        $sanitized = array(
                'enabled' => array(),
                'disabled' => array()
        );

        $columns = array('enabled', 'disabled');

        foreach ($columns as $column) {
            if (isset($value[$column]) && is_array($value[$column])) {
                foreach ($value[$column] as $key => $label) {
                    $sanitized[$column][sanitize_text_field($key)] = sanitize_text_field($label);
                }
            }
        }

        return $sanitized;
    }
}