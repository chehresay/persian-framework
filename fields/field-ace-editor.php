<?php
/**
 * Persian Framework - Ace Editor Field
 * Advanced code editor with Ace.js
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_AceEditor {

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

        // Ace Editor settings
        $mode = isset($this->field['mode']) ? $this->field['mode'] : 'html';
        $theme = isset($this->field['theme']) ? $this->field['theme'] : 'monokai';
        $height = isset($this->field['height']) ? $this->field['height'] : '300px';
        $font_size = isset($this->field['font_size']) ? $this->field['font_size'] : 14;
        $show_gutter = isset($this->field['gutter']) ? $this->field['gutter'] : true;
        $readonly = isset($this->field['readonly']) && $this->field['readonly'];
        $wrap = isset($this->field['wrap']) && $this->field['wrap'];

        ?>
        <div class="pf-field-wrapper pf-field-ace-editor">
            <?php if (isset($this->field['title'])): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-ace-container">
                <div class="pf-ace-editor-wrapper" style="height: <?php echo esc_attr($height); ?>;">
                    <div id="ace-editor-<?php echo esc_attr($id); ?>" class="pf-ace-editor"></div>
                </div>

                <textarea id="<?php echo esc_attr($id); ?>"
                          name="<?php echo esc_attr($name); ?>"
                          class="pf-ace-textarea"
                          style="display:none;"><?php echo esc_textarea($value); ?></textarea>

                <?php if (isset($this->field['status']) && $this->field['status']): ?>
                    <div class="pf-ace-status">
                        <span class="pf-ace-status-mode"><?php echo esc_html($mode); ?></span>
                        <span class="pf-ace-status-cursor">Ln: 1 | Col: 1</span>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (isset($this->field['desc'])): ?>
                <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php
        $this->enqueue_scripts($id, $mode, $theme, $font_size, $show_gutter, $readonly, $wrap);
    }

    private function enqueue_scripts($id, $mode, $theme, $font_size, $show_gutter, $readonly, $wrap) {
        static $pf_ace_enqueued = false;

        if (!$pf_ace_enqueued) {
            // ✅ Enqueue Ace Editor from LOCAL
            wp_enqueue_script(
                    'pf-ace-editor',
                    PERSIAN_FRAMEWORK_URL . 'vendor/ace-editor/ace.min.js',
                    array(),
                    PERSIAN_FRAMEWORK_VERSION,
                    true
            );

            wp_enqueue_script(
                    'pf-ace-ext-language_tools',
                    PERSIAN_FRAMEWORK_URL . 'vendor/ace-editor/ext-language_tools.min.js',
                    array('pf-ace-editor'),
                    PERSIAN_FRAMEWORK_VERSION,
                    true
            );

            $pf_ace_enqueued = true;
        }

        ?>
        <style>
            .pf-ace-container {
                margin-top: 8px;
                border-radius: 12px;
                overflow: hidden;
                border: 2px solid #e8edf4;
            }
            body.dark-mode .pf-ace-container {
                border-color: #334155;
            }

            .pf-ace-editor-wrapper {
                width: 100%;
                position: relative;
            }

            .pf-ace-editor {
                width: 100%;
                height: 100%;
                min-height: 200px;
            }

            .pf-ace-editor-wrapper:focus-within {
                border-color: #6366f1;
                box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            }

            .pf-ace-status {
                display: flex;
                justify-content: space-between;
                padding: 6px 12px;
                background: #1e1e1e;
                color: #94a3b8;
                font-size: 12px;
                font-family: monospace;
                border-top: 1px solid #333;
            }
            body.dark-mode .pf-ace-status {
                background: #0f172a;
                border-color: #334155;
            }

            .pf-ace-status-mode {
                text-transform: uppercase;
                color: #6366f1;
            }

            .pf-ace-status-cursor {
                color: #94a3b8;
            }
        </style>

        <script>
            (function($) {
                'use strict';

                function initAceEditor() {
                    var editorId = 'ace-editor-<?php echo esc_attr($id); ?>';
                    var textareaId = '<?php echo esc_attr($id); ?>';
                    var $textarea = $('#' + textareaId);

                    if (typeof ace === 'undefined') {
                        console.warn('Ace Editor not loaded');
                        return;
                    }

                    if (document.getElementById(editorId)) {
                        var editor = ace.edit(editorId);

                        editor.setTheme('ace/theme/<?php echo esc_attr($theme); ?>');
                        editor.session.setMode('ace/mode/<?php echo esc_attr($mode); ?>');
                        editor.setFontSize(<?php echo intval($font_size); ?>);
                        editor.setReadOnly(<?php echo $readonly ? 'true' : 'false'; ?>);
                        editor.setShowPrintMargin(false);
                        editor.setDisplayIndentGuides(true);

                        <?php if (!$show_gutter): ?>
                        editor.renderer.setShowGutter(false);
                        <?php endif; ?>

                        <?php if ($wrap): ?>
                        editor.session.setUseWrapMode(true);
                        <?php endif; ?>

                        editor.setOptions({
                            enableBasicAutocompletion: true,
                            enableLiveAutocompletion: true,
                            enableSnippets: true
                        });

                        var initialValue = $textarea.val();
                        editor.setValue(initialValue, -1);

                        editor.session.on('change', function() {
                            var value = editor.getValue();
                            $textarea.val(value).trigger('change');
                        });

                        editor.on('changeSelection', function() {
                            var pos = editor.getCursorPosition();
                            var row = pos.row + 1;
                            var col = pos.column + 1;
                            $('.pf-ace-status-cursor').text('Ln: ' + row + ' | Col: ' + col);
                        });

                        $(window).on('resize', function() {
                            editor.resize();
                        });

                        $(document).data('pf-ace-editor-' + textareaId, editor);
                    }
                }

                $(document).ready(function() {
                    if (typeof ace !== 'undefined') {
                        initAceEditor();
                    } else {
                        setTimeout(initAceEditor, 500);
                    }
                });

                $(document).on('pf-repeater-add', function() {
                    setTimeout(initAceEditor, 500);
                });

            })(jQuery);
        </script>
        <?php
    }
}