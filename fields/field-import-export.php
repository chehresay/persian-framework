<?php
/**
 * Persian Framework - Import Export Field
 * Import/Export JSON data
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_ImportExport {

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
        $rows = isset($this->field['rows']) ? $this->field['rows'] : 8;
        $current_value = is_scalar($value) ? $value : '';

        ?>
        <div class="pf-field-wrapper pf-field-import-export">
            <?php if (isset($this->field['title'])): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-import-export-field">
                <div class="pf-ie-actions">
                    <button type="button" class="pf-btn pf-btn-secondary pf-ie-export">
                        <span class="dashicons dashicons-download"></span>
                        <?php esc_html_e('Export', 'persian-framework'); ?>
                    </button>
                    <button type="button" class="pf-btn pf-btn-secondary pf-ie-import">
                        <span class="dashicons dashicons-upload"></span>
                        <?php esc_html_e('Import', 'persian-framework'); ?>
                    </button>
                    <button type="button" class="pf-btn pf-btn-secondary pf-ie-format">
                        <span class="dashicons dashicons-editor-code"></span>
                        <?php esc_html_e('Format JSON', 'persian-framework'); ?>
                    </button>
                    <button type="button" class="pf-btn pf-btn-danger pf-ie-clear">
                        <span class="dashicons dashicons-trash"></span>
                        <?php esc_html_e('Clear', 'persian-framework'); ?>
                    </button>
                </div>

                <textarea id="<?php echo esc_attr($id); ?>"
                          name="<?php echo esc_attr($name); ?>"
                          rows="<?php echo esc_attr($rows); ?>"
                          class="pf-field-input pf-ie-textarea"
                          spellcheck="false"
                          placeholder='<?php esc_attr_e('Paste JSON data here...', 'persian-framework'); ?>'><?php echo esc_textarea($current_value); ?></textarea>

                <div class="pf-ie-status">
                    <span class="pf-ie-status-text"><?php esc_html_e('Ready', 'persian-framework'); ?></span>
                    <span class="pf-ie-status-count">
                        <?php
                        printf( esc_html__( 'Characters: %d', 'persian-framework' ), esc_html( strlen( $current_value ) ) );
                        ?>
                    </span>
                </div>
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
                .pf-import-export-field {
                    margin-top: 8px;
                }

                .pf-ie-actions {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 8px;
                    margin-bottom: 10px;
                }

                .pf-ie-actions .pf-btn {
                    padding: 6px 14px;
                    font-size: 13px;
                }
                .pf-ie-actions .pf-btn .dashicons {
                    font-size: 16px;
                    width: 16px;
                    height: 16px;
                }

                .pf-ie-textarea {
                    min-height: 100px;
                    font-family: 'Courier New', Consolas, monospace;
                    font-size: 13px;
                    line-height: 1.6;
                    tab-size: 2;
                    -moz-tab-size: 2;
                    resize: vertical;
                }

                .pf-ie-status {
                    display: flex;
                    justify-content: space-between;
                    padding: 6px 12px;
                    background: #f8f9fa;
                    border: 2px solid #e8edf4;
                    border-top: none;
                    border-radius: 0 0 8px 8px;
                    font-size: 12px;
                    color: #6b7a8f;
                }
                body.dark-mode .pf-ie-status {
                    background: #0f172a;
                    border-color: #334155;
                    color: #94a3b8;
                }

                .pf-ie-status-text.success {
                    color: #10b981;
                }
                .pf-ie-status-text.error {
                    color: #ef4444;
                }

                .pf-ie-status-count {
                    font-weight: 600;
                }
            </style>

            <script>
                (function($) {
                    'use strict';

                    var $textarea = $('#<?php echo esc_js($this->field['id']); ?>');
                    var $status = $textarea.closest('.pf-import-export-field').find('.pf-ie-status-text');
                    var $count = $textarea.closest('.pf-import-export-field').find('.pf-ie-status-count');

                    // Update character count
                    function updateCount() {
                        var val = $textarea.val();
                        /* translators: %d: number of characters in the textarea */
                        $count.text('<?php esc_js( esc_html__( 'Characters: %d', 'persian-framework' ) ); ?>'.replace('%d', val.length));
                    }

                    $textarea.on('input', updateCount);

                    // Export
                    $(document).on('click', '.pf-ie-export', function() {
                        var val = $textarea.val();
                        if (!val) {
                            alert('<?php echo esc_js( esc_html__( 'Nothing to export. Please enter some JSON data.', 'persian-framework' ) ); ?>');
                            return;
                        }

                        try {
                            var data = JSON.parse(val);
                            var json = JSON.stringify(data, null, 2);
                            $textarea.val(json);
                            updateCount();
                            $status.text('<?php echo esc_js( esc_html__( 'Formatted', 'persian-framework' ) ); ?>').addClass('success');
                        } catch(e) {
                            $status.text('<?php echo esc_js( esc_html__( 'Error: Invalid JSON', 'persian-framework' ) ); ?>').addClass('error');
                            alert('<?php echo esc_js( esc_html__( 'Invalid JSON data. Please check your input.', 'persian-framework' ) ); ?>');
                        }
                    });

                    // Import (Upload file)
                    $(document).on('click', '.pf-ie-import', function() {
                        var $input = $('<input type="file" accept=".json">');
                        $input.on('change', function(e) {
                            var file = this.files[0];
                            if (!file) return;

                            var reader = new FileReader();
                            reader.onload = function(e) {
                                try {
                                    var data = JSON.parse(e.target.result);
                                    var json = JSON.stringify(data, null, 2);
                                    $textarea.val(json);
                                    updateCount();
                                    $status.text('<?php echo esc_js( esc_html__( 'Imported successfully', 'persian-framework' ) ); ?>').addClass('success');
                                } catch(err) {
                                    $status.text('<?php echo esc_js( esc_html__( 'Error: Invalid file', 'persian-framework' ) ); ?>').addClass('error');
                                    alert('<?php echo esc_js( esc_html__( 'Invalid JSON file.', 'persian-framework' ) ); ?>');
                                }
                            };
                            reader.readAsText(file);
                        });
                        $input.click();
                    });

                    // Format JSON
                    $(document).on('click', '.pf-ie-format', function() {
                        var val = $textarea.val();
                        if (!val) {
                            alert('<?php echo esc_js( esc_html__( 'Nothing to format.', 'persian-framework' ) ); ?>');
                            return;
                        }

                        try {
                            var data = JSON.parse(val);
                            var json = JSON.stringify(data, null, 2);
                            $textarea.val(json);
                            updateCount();
                            $status.text('<?php echo esc_js( esc_html__( 'Formatted', 'persian-framework' ) ); ?>').addClass('success');
                        } catch(e) {
                            $status.text('<?php echo esc_js( esc_html__( 'Error: Invalid JSON', 'persian-framework' ) ); ?>').addClass('error');
                            alert('<?php echo esc_js( esc_html__( 'Invalid JSON data. Please check your input.', 'persian-framework' ) ); ?>');
                        }
                    });

                    // Clear
                    $(document).on('click', '.pf-ie-clear', function() {
                        if (confirm('<?php echo esc_js( esc_html__( 'Clear the textarea?', 'persian-framework' ) ); ?>')) {
                            $textarea.val('');
                            updateCount();
                            $status.text('<?php echo esc_js( esc_html__( 'Cleared', 'persian-framework' ) ); ?>');
                        }
                    });

                    // Validate on change
                    $textarea.on('change blur', function() {
                        var val = $textarea.val();
                        if (!val) {
                            $status.text('<?php echo esc_js( esc_html__( 'Empty', 'persian-framework' ) ); ?>');
                            $status.removeClass('success error');
                            return;
                        }

                        try {
                            JSON.parse(val);
                            $status.text('<?php echo esc_js( esc_html__( 'Valid JSON', 'persian-framework' ) ); ?>').addClass('success').removeClass('error');
                        } catch(e) {
                            $status.text('<?php echo esc_js( esc_html__( 'Invalid JSON', 'persian-framework' ) ); ?>').addClass('error').removeClass('success');
                        }
                    });

                    // Initial validation
                    if ($textarea.val()) {
                        $textarea.trigger('blur');
                    }

                })(jQuery);
            </script>
            <?php
            $enqueued = true;
        }
    }
}