<?php
/**
 * Persian Framework - Multi Select Field
 * Select multiple options with search and tagging support
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_MultiSelect {

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
        $placeholder = isset($this->field['placeholder']) ? $this->field['placeholder'] : esc_html__('Select options', 'persian-framework');
        $searchable = isset($this->field['searchable']) ? $this->field['searchable'] : true;
        $max = isset($this->field['max']) ? $this->field['max'] : 0;
        $required = isset($this->field['required']) && $this->field['required'] ? 'required' : '';

        ?>
        <div class="pf-field-wrapper pf-field-multi-select">
            <?php if (isset($this->field['title'])): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                    <?php if ($required): ?>
                        <span class="pf-required">*</span>
                    <?php endif; ?>
                    <?php if ($max > 0): ?>
                        <span class="pf-select-max">
                            <?php
                            /* translators: %d: Maximum number of items allowed */
                            printf( esc_html__( '(Max: %d)', 'persian-framework' ), esc_html( $max ) );
                            ?>
                        </span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-multi-select-container" data-max="<?php echo esc_attr($max); ?>">
                <?php if ($searchable): ?>
                    <div class="pf-multi-select-search">
                        <input type="text"
                               class="pf-multi-select-search-input"
                               placeholder="<?php esc_attr_e('Search...', 'persian-framework'); ?>"
                               autocomplete="off" />
                        <span class="dashicons dashicons-search"></span>
                    </div>
                <?php endif; ?>

                <select id="<?php echo esc_attr($id); ?>"
                        name="<?php echo esc_attr($name); ?>[]"
                        class="pf-field-input pf-multi-select"
                        multiple
                        <?php echo wp_kses_data($required); ?>
                        size="<?php echo esc_attr(min(count($options) + 1, 10)); ?>">
                    <?php foreach ($options as $key => $label): ?>
                        <option value="<?php echo esc_attr($key); ?>"
                                <?php echo in_array((string) $key, array_map('strval', $value), true) ? 'selected' : ''; ?>>
                            <?php echo esc_html($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="pf-multi-select-tags">
                    <?php foreach ($value as $selected): ?>
                        <?php if (isset($options[$selected])): ?>
                            <span class="pf-multi-select-tag" data-value="<?php echo esc_attr($selected); ?>">
                                <?php echo esc_html($options[$selected]); ?>
                                <button type="button" class="pf-multi-select-tag-remove" aria-label="<?php esc_attr_e('Remove', 'persian-framework'); ?>">×</button>
                            </span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <?php if (isset($this->field['desc'])): ?>
                    <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php
        $this->enqueue_scripts();
    }

    private function enqueue_scripts() {
        static $pf_multiselect_enqueued = false;

        if (!$pf_multiselect_enqueued) {
            ?>
            <style>
                .pf-multi-select-container {
                    margin-top: 8px;
                }

                .pf-multi-select {
                    min-height: 100px;
                    padding: 8px;
                    border: 2px solid #e8edf4;
                    border-radius: 12px;
                    font-size: 14px;
                    font-family: inherit;
                    background: #fafbfc;
                    color: #1a2332;
                    width: 100%;
                }
                body.dark-mode .pf-multi-select {
                    background: #0f172a;
                    border-color: #334155;
                    color: #e2e8f0;
                }

                .pf-multi-select:focus {
                    border-color: #6366f1;
                    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
                    outline: none;
                }

                .pf-multi-select option {
                    padding: 6px 10px;
                    border-radius: 4px;
                }
                .pf-multi-select option:checked {
                    background: #6366f1;
                    color: white;
                }
                body.dark-mode .pf-multi-select option:checked {
                    background: #818cf8;
                }

                .pf-multi-select-search {
                    position: relative;
                    margin-bottom: 8px;
                }

                .pf-multi-select-search-input {
                    width: 100%;
                    padding: 10px 14px 10px 40px;
                    border: 2px solid #e8edf4;
                    border-radius: 8px;
                    font-size: 14px;
                    background: white;
                    transition: all 0.2s ease;
                }
                body.dark-mode .pf-multi-select-search-input {
                    background: #1e293b;
                    border-color: #334155;
                    color: #e2e8f0;
                }

                .pf-multi-select-search-input:focus {
                    border-color: #6366f1;
                    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
                    outline: none;
                }

                .pf-multi-select-search .dashicons {
                    position: absolute;
                    left: 12px;
                    top: 50%;
                    transform: translateY(-50%);
                    color: #94a3b8;
                    font-size: 18px;
                }

                .pf-multi-select-tags {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 6px;
                    margin-top: 8px;
                    padding: 8px;
                    min-height: 40px;
                    border: 2px dashed #e8edf4;
                    border-radius: 8px;
                    background: #fafbfc;
                }
                body.dark-mode .pf-multi-select-tags {
                    background: #0f172a;
                    border-color: #334155;
                }

                .pf-multi-select-tag {
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    padding: 4px 10px 4px 14px;
                    background: #6366f1;
                    color: white;
                    border-radius: 20px;
                    font-size: 13px;
                    font-weight: 500;
                    animation: pfTagFadeIn 0.3s ease;
                }

                @keyframes pfTagFadeIn {
                    from { transform: scale(0.8); opacity: 0; }
                    to { transform: scale(1); opacity: 1; }
                }

                .pf-multi-select-tag-remove {
                    background: none;
                    border: none;
                    color: rgba(255, 255, 255, 0.7);
                    cursor: pointer;
                    font-size: 16px;
                    padding: 0 2px;
                    line-height: 1;
                    transition: color 0.2s ease;
                }
                .pf-multi-select-tag-remove:hover {
                    color: white;
                }

                .pf-select-max {
                    font-size: 12px;
                    font-weight: 400;
                    color: #94a3b8;
                    margin-left: 6px;
                }
            </style>

            <script>
                (function($) {
                    'use strict';

                    $(document).on('input', '.pf-multi-select-search-input', function() {
                        var $container = $(this).closest('.pf-multi-select-container');
                        var $select = $container.find('.pf-multi-select');
                        var search = $(this).val().toLowerCase();

                        $select.find('option').each(function() {
                            var text = $(this).text().toLowerCase();
                            $(this).toggle(text.indexOf(search) > -1);
                        });
                    });

                    $(document).on('change', '.pf-multi-select', function() {
                        var $container = $(this).closest('.pf-multi-select-container');
                        var $tags = $container.find('.pf-multi-select-tags');
                        var max = parseInt($container.data('max')) || 0;
                        var selected = $(this).val() || [];

                        if (max > 0 && selected.length > max) {
                            alert('<?php esc_html_e('Maximum selection limit reached.', 'persian-framework'); ?>');
                            $(this).find('option:selected:last').prop('selected', false);
                            return;
                        }

                        $tags.empty();
                        var $select = $(this);
                        $select.find('option:selected').each(function() {
                            var value = $(this).val();
                            var label = $(this).text();
                            var $tag = $('<span class="pf-multi-select-tag" data-value="' + value + '">' +
                                label +
                                '<button type="button" class="pf-multi-select-tag-remove" aria-label="<?php esc_attr_e('Remove', 'persian-framework'); ?>">×</button>' +
                                '</span>');
                            $tags.append($tag);
                        });

                        $(this).trigger('pf-multi-select-change', [selected]);
                    });

                    $(document).on('click', '.pf-multi-select-tag-remove', function(e) {
                        e.stopPropagation();
                        var $tag = $(this).closest('.pf-multi-select-tag');
                        var value = $tag.data('value');
                        var $container = $tag.closest('.pf-multi-select-container');
                        var $select = $container.find('.pf-multi-select');

                        $select.find('option[value="' + value + '"]').prop('selected', false);
                        $select.trigger('change');
                    });

                    $(document).ready(function() {
                        $('.pf-multi-select').each(function() {
                            $(this).trigger('change');
                        });
                    });

                })(jQuery);
            </script>
            <?php
            $pf_multiselect_enqueued = true;
        }
    }

    public function sanitize($value) {
        $options = isset($this->field['options']) ? array_keys($this->field['options']) : array();
        $value = (array) $value;

        $sanitized = array();
        foreach ($value as $item) {
            $item = sanitize_text_field($item);
            if (in_array($item, $options, true)) {
                $sanitized[] = $item;
            }
        }

        $max = isset($this->field['max']) ? intval($this->field['max']) : 0;
        if ($max > 0 && count($sanitized) > $max) {
            $sanitized = array_slice($sanitized, 0, $max);
        }

        return $sanitized;
    }
}