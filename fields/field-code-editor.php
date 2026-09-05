<?php
/**
 * Persian Framework - Code Editor Field
 * Simple textarea for code with monospace font
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_CodeEditor {

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
        $rows = isset($this->field['rows']) ? $this->field['rows'] : 12;
        $language = isset($this->field['language']) ? $this->field['language'] : 'plaintext';
        $readonly = isset($this->field['readonly']) && $this->field['readonly'];

        ?>
        <div class="pf-field-wrapper pf-field-code-editor">
            <?php if (isset($this->field['title'])): ?>
                <label for="<?php echo esc_attr($id); ?>" class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-code-editor-container">
                <div class="pf-code-editor-toolbar">
                    <span class="pf-code-editor-language"><?php echo esc_html(strtoupper($language)); ?></span>
                    <span class="pf-code-editor-line-count"><?php echo esc_html(substr_count($value, "\n") + 1); ?> <?php esc_html_e('lines', 'persian-framework'); ?></span>
                </div>
                <textarea id="<?php echo esc_attr($id); ?>"
                          name="<?php echo esc_attr($name); ?>"
                          rows="<?php echo esc_attr($rows); ?>"
                          class="pf-code-editor-textarea"
                          spellcheck="false"
                          data-language="<?php echo esc_attr($language); ?>"
                          <?php echo $readonly ? 'readonly' : ''; ?>><?php echo esc_textarea($value); ?></textarea>
            </div>

            <?php if (isset($this->field['desc'])): ?>
                <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php
        $this->enqueue_scripts();
    }

    private function enqueue_scripts() {
        static $pf_codeeditor_enqueued = false;

        if (!$pf_codeeditor_enqueued) {
            ?>
            <style>
                .pf-code-editor-container {
                    margin-top: 8px;
                    overflow: hidden;
                    border: 1px solid #e8edf4;
                }
                body.dark-mode .pf-code-editor-container {
                    border-color: #334155;
                }

                .pf-code-editor-container:focus-within {
                    border-color: #6366f1;
                    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
                }

                .pf-code-editor-toolbar {
                    display: flex;
                    justify-content: space-between;
                    padding: 6px 14px;
                    background: #f8f9fa;
                    border-bottom: 1px solid #e8edf4;
                    font-size: 12px;
                    font-weight: 600;
                    color: #6b7a8f;
                }
                body.dark-mode .pf-code-editor-toolbar {
                    background: #0f172a;
                    border-color: #334155;
                    color: #94a3b8;
                }

                .pf-code-editor-language {
                    color: #6366f1;
                    text-transform: uppercase;
                }

                .pf-code-editor-textarea {
                    width: 100%;
                    padding: 16px;
                    border: none;
                    font-family: 'Courier New', Consolas, Monaco, 'Andale Mono', monospace;
                    font-size: 14px;
                    line-height: 1.6;
                    background: #fafbfc;
                    color: #1a2332;
                    resize: vertical;
                    tab-size: 4;
                    -moz-tab-size: 4;
                    outline: none;
                    min-height: 150px;
                    direction: ltr;
                }
                body.dark-mode .pf-code-editor-textarea {
                    background: #0f172a;
                    color: #e2e8f0;
                }

                .pf-code-editor-textarea:focus {
                    background: white;
                }
                body.dark-mode .pf-code-editor-textarea:focus {
                    background: #1e293b;
                }

                .pf-code-editor-textarea[readonly] {
                    opacity: 0.7;
                    cursor: not-allowed;
                }
            </style>

            <script>
                (function($) {
                    'use strict';

                    $(document).on('input', '.pf-code-editor-textarea', function() {
                        var $toolbar = $(this).closest('.pf-code-editor-container').find('.pf-code-editor-line-count');
                        var lines = $(this).val().split('\n').length;
                        $toolbar.text(lines + ' <?php esc_html_e('lines', 'persian-framework'); ?>');
                    });

                    $(document).on('keydown', '.pf-code-editor-textarea', function(e) {
                        if (e.key === 'Tab') {
                            e.preventDefault();
                            var start = this.selectionStart;
                            var end = this.selectionEnd;

                            this.value = this.value.substring(0, start) + '    ' + this.value.substring(end);
                            this.selectionStart = this.selectionEnd = start + 4;

                            $(this).trigger('input');
                        }
                    });

                })(jQuery);
            </script>
            <?php
            $pf_codeeditor_enqueued = true;
        }
    }

    public function sanitize($value) {
        $value = wp_unslash($value);
        return sanitize_textarea_field($value);
    }
}