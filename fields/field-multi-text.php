<?php
/**
 * Persian Framework - Multi Text Field
 * Multiple text inputs with add/remove functionality
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_MultiText {

    private $field;
    private $value;

    public function __construct($field, $value = null) {
        $this->field = $field;
        $this->value = $value;
    }

    /**
     * Normalize value to always be an array
     */
    private function normalize_value($value) {
        if (is_null($value)) {
            return array();
        }

        if (is_array($value)) {
            // ✅ Ensure values are strings
            $normalized = array();
            foreach ($value as $item) {
                if (is_string($item) || is_numeric($item)) {
                    $normalized[] = (string) $item;
                }
            }
            return $normalized;
        }

        // If it's a string, try to decode JSON
        if (is_string($value) && !empty($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded;
            }
            // If it's a comma-separated string
            if (strpos($value, ',') !== false) {
                return array_map('trim', explode(',', $value));
            }
            // Single value
            return array((string) $value);
        }

        // If it's a string like "0" or "1", treat as empty
        if (is_string($value) && (empty($value) || $value === '0' || $value === '1')) {
            return array();
        }

        // Default fallback
        return array();
    }

    public function render() {
        $id = isset($this->field['id']) ? $this->field['id'] : '';
        $name = isset($this->field['name']) ? $this->field['name'] : $id;
        $value = $this->normalize_value($this->value);
        $show_empty = isset($this->field['show_empty']) ? $this->field['show_empty'] : true;
        $add_text = isset($this->field['add_text']) ? $this->field['add_text'] : __('Add More', 'persian-framework');
        $placeholder = isset($this->field['placeholder']) ? $this->field['placeholder'] : __('Enter text...', 'persian-framework');
        $max_items = isset($this->field['max']) ? intval($this->field['max']) : 0;
        $min_items = isset($this->field['min']) ? intval($this->field['min']) : 0;
        $validate = isset($this->field['validate']) ? $this->field['validate'] : '';
        $required = isset($this->field['required']) && $this->field['required'] ? 'required' : '';

        // Ensure we have at least minimum items
        if (empty($value) && $min_items > 0) {
            $value = array_fill(0, $min_items, '');
        }

        ?>
        <div class="pf-field-wrapper pf-field-multi-text"
             data-max="<?php echo esc_attr($max_items); ?>"
             data-min="<?php echo esc_attr($min_items); ?>"
             data-validate="<?php echo esc_attr($validate); ?>">

            <?php if (isset($this->field['title'])): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                    <?php if ($max_items > 0): ?>
                        <span class="pf-multi-text-max-label">
                            <?php
                            /* translators: %d: maximum number of items allowed */
                            printf( esc_html__( '(Max: %d)', 'persian-framework' ), esc_html( $max_items ) );
                            ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($required): ?>
                        <span class="pf-required">*</span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-multi-text-container">
                <div class="pf-multi-text-items" id="multi-text-<?php echo esc_attr($id); ?>">
                    <?php
                    $index = 0;
                    if (empty($value) && $show_empty) {
                        $value = array('');
                    }
                    foreach ($value as $text):
                        if (!is_string($text)) {
                            $text = '';
                        }
                        ?>
                        <div class="pf-multi-text-item" data-index="<?php echo esc_attr($index); ?>">
                            <div class="pf-multi-text-input-wrap">
                                <input type="text"
                                       name="<?php echo esc_attr($name); ?>[<?php echo esc_attr($index); ?>]"
                                       value="<?php echo esc_attr($text); ?>"
                                       placeholder="<?php echo esc_attr($placeholder); ?>"
                                       class="pf-field-input pf-multi-text-input"
                                       data-validate="<?php echo esc_attr($validate); ?>"
                                        <?php echo esc_attr($required); ?> />
                                <button type="button" class="pf-multi-text-remove" aria-label="<?php esc_attr_e('Remove', 'persian-framework'); ?>">
                                    <span class="dashicons dashicons-no-alt"></span>
                                </button>
                            </div>
                            <?php if ($validate === 'color' && !empty($text)): ?>
                                <div class="pf-multi-text-color-preview" style="background:<?php echo esc_attr($text); ?>;"></div>
                            <?php endif; ?>
                            <div class="pf-multi-text-validation-error"></div>
                        </div>
                        <?php
                        $index++;
                    endforeach;
                    ?>
                </div>

                <div class="pf-multi-text-actions">
                    <button type="button" class="pf-btn pf-btn-secondary pf-multi-text-add">
                        <span class="dashicons dashicons-plus-alt"></span>
                        <?php echo esc_html($add_text); ?>
                    </button>
                    <?php if (count($value) > $min_items): ?>
                        <button type="button" class="pf-btn pf-btn-danger pf-multi-text-clear">
                            <span class="dashicons dashicons-trash"></span>
                            <?php esc_html_e('Clear All', 'persian-framework'); ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (isset($this->field['desc'])): ?>
                <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php
        $this->enqueue_scripts($id);
    }

    private function enqueue_scripts($id) {
        static $enqueued = false;

        if (!$enqueued) {
            ?>
            <style>
                .pf-multi-text-container {
                    margin-top: 8px;
                }

                .pf-multi-text-items {
                    display: flex;
                    flex-direction: column;
                    gap: 8px;
                    margin-bottom: 12px;
                    min-height: 30px;
                }

                .pf-multi-text-item {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    animation: pfMultiTextFadeIn 0.3s ease;
                }

                @keyframes pfMultiTextFadeIn {
                    from { opacity: 0; transform: translateY(-10px); }
                    to { opacity: 1; transform: translateY(0); }
                }

                .pf-multi-text-input-wrap {
                    display: flex;
                    flex: 1;
                    gap: 8px;
                    align-items: center;
                }

                .pf-multi-text-input {
                    flex: 1;
                    padding: 10px 14px;
                    border: 2px solid #e8edf4;
                    border-radius: 8px;
                    font-size: 14px;
                    font-family: inherit;
                    background: #fafbfc;
                    color: #1a2332;
                    transition: all 0.2s ease;
                    min-width: 0;
                }
                body.dark-mode .pf-multi-text-input {
                    background: #0f172a;
                    border-color: #334155;
                    color: #e2e8f0;
                }

                .pf-multi-text-input:focus {
                    border-color: #6366f1;
                    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
                    outline: none;
                }
                body.dark-mode .pf-multi-text-input:focus {
                    border-color: #818cf8;
                }

                .pf-multi-text-input.error {
                    border-color: #ef4444;
                    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
                }

                .pf-multi-text-input.valid {
                    border-color: #22c55e;
                }

                .pf-multi-text-remove {
                    background: none;
                    border: none;
                    cursor: pointer;
                    color: #94a3b8;
                    padding: 4px;
                    transition: all 0.2s ease;
                    flex-shrink: 0;
                }
                .pf-multi-text-remove:hover {
                    color: #ef4444;
                    transform: scale(1.2);
                }
                .pf-multi-text-remove .dashicons {
                    font-size: 18px;
                    width: 18px;
                    height: 18px;
                }

                .pf-multi-text-color-preview {
                    width: 30px;
                    height: 30px;
                    border-radius: 4px;
                    border: 2px solid #e8edf4;
                    flex-shrink: 0;
                }
                body.dark-mode .pf-multi-text-color-preview {
                    border-color: #334155;
                }

                .pf-multi-text-actions {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 10px;
                }

                .pf-multi-text-actions .pf-btn .dashicons {
                    font-size: 16px;
                    width: 16px;
                    height: 16px;
                }

                .pf-multi-text-max-label {
                    font-size: 12px;
                    font-weight: 400;
                    color: #94a3b8;
                    margin-left: 6px;
                }

                .pf-multi-text-validation-error {
                    font-size: 12px;
                    color: #ef4444;
                    margin-top: 2px;
                    display: none;
                }

                .pf-multi-text-validation-error.show {
                    display: block;
                }

                @media (max-width: 768px) {
                    .pf-multi-text-input-wrap {
                        flex-wrap: wrap;
                    }
                    .pf-multi-text-remove {
                        padding: 8px;
                    }
                    .pf-multi-text-actions {
                        flex-direction: column;
                    }
                    .pf-multi-text-actions .pf-btn {
                        justify-content: center;
                    }
                }
            </style>

            <script>
                (function($) {
                    'use strict';

                    var fieldId = '<?php echo esc_js($id); ?>';
                    var fieldName = '<?php echo esc_js($this->field['name']); ?>';
                    var maxItems = <?php echo intval($this->field['max'] ?? 0); ?>;
                    var minItems = <?php echo intval($this->field['min'] ?? 0); ?>;
                    var validate = '<?php echo esc_js($this->field['validate'] ?? ''); ?>';
                    var placeholder = '<?php echo esc_js($this->field['placeholder'] ?? __('Enter text...', 'persian-framework')); ?>';
                    var $container = $('#multi-text-<?php echo esc_js($id); ?>');

                    // Use the count from PHP
                    var itemCounter = <?php echo max(count($this->value), 0); ?>;

                    // Validation functions
                    var validators = {
                        'color': function(value) {
                            if (/^#[0-9a-f]{3}$/i.test(value)) return true;
                            if (/^#[0-9a-f]{6}$/i.test(value)) return true;
                            var colors = ['red', 'blue', 'green', 'yellow', 'orange', 'purple', 'pink', 'brown', 'black', 'white', 'gray', 'grey'];
                            return colors.indexOf(value.toLowerCase()) !== -1;
                        },
                        'email': function(value) {
                            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                        },
                        'url': function(value) {
                            try {
                                var url = new URL(value);
                                return url.protocol === 'http:' || url.protocol === 'https:';
                            } catch(e) {
                                return false;
                            }
                        },
                        'numeric': function(value) {
                            return /^[0-9]+$/.test(value);
                        },
                        'alpha': function(value) {
                            return /^[a-zA-Z]+$/.test(value);
                        },
                        'alphanumeric': function(value) {
                            return /^[a-zA-Z0-9]+$/.test(value);
                        }
                    };

                    // Validate a single input
                    function validateInput($input) {
                        var val = $input.val().trim();
                        var type = $input.data('validate') || validate;
                        var $item = $input.closest('.pf-multi-text-item');
                        var $error = $item.find('.pf-multi-text-validation-error');

                        // Clear previous state
                        $input.removeClass('error valid');
                        if ($error) $error.removeClass('show');

                        if (val === '') {
                            $input.addClass('error');
                            if ($error) $error.text('<?php echo esc_js( esc_html__( 'This field cannot be empty.', 'persian-framework' ) ); ?>').addClass('show');
                            return false;
                        }

                        if (type && validators[type]) {
                            if (!validators[type](val)) {
                                $input.addClass('error');
                                if ($error) {
                                    var msg = '<?php echo esc_js( esc_html__( 'Invalid value.', 'persian-framework' ) ); ?>';
                                    if (type === 'color') msg = '<?php echo esc_js( esc_html__( 'Invalid color value. Please enter a valid hex color or color name.', 'persian-framework' ) ); ?>';
                                    if (type === 'email') msg = '<?php echo esc_js( esc_html__( 'Invalid email address.', 'persian-framework' ) ); ?>';
                                    if (type === 'url') msg = '<?php echo esc_js( esc_html__( 'Invalid URL. Please enter a valid URL.', 'persian-framework' ) ); ?>';
                                    if (type === 'numeric') msg = '<?php echo esc_js( esc_html__( 'Please enter only numbers.', 'persian-framework' ) ); ?>';
                                    if (type === 'alpha') msg = '<?php echo esc_js( esc_html__( 'Please enter only letters.', 'persian-framework' ) ); ?>';
                                    if (type === 'alphanumeric') msg = '<?php echo esc_js( esc_html__( 'Please enter only letters and numbers.', 'persian-framework' ) ); ?>';
                                    $error.text(msg).addClass('show');
                                }
                                return false;
                            }
                        }

                        $input.addClass('valid');
                        return true;
                    }

                    // Update input names with proper indexing
                    function updateItemIndexes() {
                        $container.find('.pf-multi-text-item').each(function(index) {
                            var $item = $(this);
                            var $input = $item.find('.pf-multi-text-input');
                            var name = $input.attr('name');
                            if (name) {
                                // Replace the index in the name
                                var newName = name.replace(/\[\d*\]/, '[' + index + ']');
                                $input.attr('name', newName);
                            }
                        });
                    }

                    // Generate HTML for a single item
                    function generateItem(value) {
                        var html = '<div class="pf-multi-text-item" data-index="' + itemCounter + '">';
                        html += '<div class="pf-multi-text-input-wrap">';
                        html += '<input type="text"';
                        html += ' name="' + fieldName + '[' + itemCounter + ']"';
                        html += ' value="' + (value || '') + '"';
                        html += ' placeholder="' + placeholder + '"';
                        html += ' class="pf-field-input pf-multi-text-input"';
                        html += ' data-validate="' + validate + '"';
                        html += ' />';
                        html += '<button type="button" class="pf-multi-text-remove" aria-label="<?php echo esc_js( esc_html__( 'Remove', 'persian-framework' ) ); ?>">';
                        html += '<span class="dashicons dashicons-no-alt"></span>';
                        html += '</button>';
                        html += '</div>';
                        if (validate === 'color' && value) {
                            html += '<div class="pf-multi-text-color-preview" style="background:' + value + ';"></div>';
                        }
                        html += '<div class="pf-multi-text-validation-error"></div>';
                        html += '</div>';
                        return html;
                    }

                    // Add new item
                    $('.pf-multi-text-add').on('click', function() {
                        if (maxItems > 0 && $container.find('.pf-multi-text-item').length >= maxItems) {
                            alert('<?php echo esc_js( esc_html__( 'Maximum number of items reached.', 'persian-framework' ) ); ?>');
                            return;
                        }

                        var $item = $(generateItem(''));
                        $container.append($item);
                        itemCounter++;
                        updateItemIndexes();
                        updateRemoveButtons();

                        // Focus new input
                        $item.find('.pf-multi-text-input').focus();

                        // Trigger event
                        $(document).trigger('pf-multi-text-add', [fieldId]);

                        // Show clear button
                        if ($container.find('.pf-multi-text-item').length > minItems) {
                            $('.pf-multi-text-clear').show();
                        }
                    });

                    // Remove item
                    $(document).on('click', '.pf-multi-text-remove', function() {
                        var $item = $(this).closest('.pf-multi-text-item');
                        var count = $container.find('.pf-multi-text-item').length;

                        if (count <= minItems) {
                            alert('<?php echo esc_js( esc_html__( 'Minimum number of items required.', 'persian-framework' ) ); ?>');
                            return;
                        }

                        $item.fadeOut(300, function() {
                            $item.remove();
                            updateItemIndexes();
                            updateRemoveButtons();

                            if ($container.find('.pf-multi-text-item').length <= minItems) {
                                $('.pf-multi-text-clear').hide();
                            }

                            $(document).trigger('pf-multi-text-remove', [fieldId]);
                        });
                    });

                    // Clear all items
                    $(document).on('click', '.pf-multi-text-clear', function() {
                        var count = $container.find('.pf-multi-text-item').length;
                        if (count <= minItems) {
                            alert('<?php echo esc_js( esc_html__( 'Minimum number of items required.', 'persian-framework' ) ); ?>');
                            return;
                        }

                        if (confirm('<?php echo esc_js( esc_html__( 'Remove all items?', 'persian-framework' ) ); ?>')) {
                            $container.find('.pf-multi-text-item').fadeOut(300, function() {
                                $container.empty();
                                // Reset itemCounter to minItems
                                itemCounter = minItems;
                                for (var i = 0; i < minItems; i++) {
                                    var $item = $(generateItem(''));
                                    $container.append($item);
                                }
                                updateItemIndexes();
                                updateRemoveButtons();
                                $('.pf-multi-text-clear').hide();

                                $(document).trigger('pf-multi-text-clear', [fieldId]);
                            });
                        }
                    });

                    // Validate on input
                    $(document).on('input', '.pf-multi-text-input', function() {
                        validateInput($(this));
                    });

                    // Validate on blur
                    $(document).on('blur', '.pf-multi-text-input', function() {
                        validateInput($(this));
                    });

                    // Update remove buttons visibility
                    function updateRemoveButtons() {
                        var count = $container.find('.pf-multi-text-item').length;
                        $container.find('.pf-multi-text-remove').toggle(count > minItems);
                    }

                    // Initialize
                    $(document).ready(function() {
                        // Make sure itemCounter is correct
                        var currentCount = $container.find('.pf-multi-text-item').length;
                        if (currentCount > 0) {
                            var maxIndex = 0;
                            $container.find('.pf-multi-text-item').each(function() {
                                var index = parseInt($(this).data('index'));
                                if (index > maxIndex) maxIndex = index;
                            });
                            itemCounter = maxIndex + 1;
                        } else if (minItems > 0) {
                            itemCounter = minItems;
                        }

                        updateItemIndexes();
                        updateRemoveButtons();

                        // Validate all existing items
                        $container.find('.pf-multi-text-input').each(function() {
                            validateInput($(this));
                        });

                        if ($container.find('.pf-multi-text-item').length <= minItems) {
                            $('.pf-multi-text-clear').hide();
                        }
                    });

                    // Enter key to add new item
                    $(document).on('keydown', '.pf-multi-text-input', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            $('.pf-multi-text-add').click();
                        }
                    });

                })(jQuery);
            </script>
            <?php
            $enqueued = true;
        }
    }
}