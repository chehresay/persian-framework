<?php
/**
 * Persian Framework - WP Editor Field
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_WPEditor {

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

        // Editor settings
        $settings = array(
                'textarea_name' => $name,
                'textarea_rows' => isset($this->field['rows']) ? $this->field['rows'] : 12,
                'media_buttons' => isset($this->field['media_buttons']) ? $this->field['media_buttons'] : true,
                'quicktags' => isset($this->field['quicktags']) ? $this->field['quicktags'] : true,
                'tinymce' => isset($this->field['tinymce']) ? $this->field['tinymce'] : true,
                'teeny' => isset($this->field['teeny']) ? $this->field['teeny'] : false,
                'editor_class' => isset($this->field['class']) ? $this->field['class'] : 'pf-editor',
                'editor_height' => isset($this->field['height']) ? $this->field['height'] : 250,
                'dfw' => isset($this->field['dfw']) ? $this->field['dfw'] : false,
                'drag_drop_upload' => isset($this->field['drag_drop_upload']) ? $this->field['drag_drop_upload'] : false,
        );

        // Merge with custom settings
        if (isset($this->field['settings']) && is_array($this->field['settings'])) {
            $settings = array_merge($settings, $this->field['settings']);
        }

        // Filter settings
        $settings = apply_filters('pf_wp_editor_settings', $settings, $this->field);

        ?>
        <div class="pf-field-wrapper pf-field-wp-editor <?php echo isset($this->field['class']) ? esc_attr($this->field['class']) : ''; ?>">
            <?php if (isset($this->field['title'])): ?>
                <label for="<?php echo esc_attr($id); ?>" class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                    <?php if (isset($this->field['required']) && $this->field['required']): ?>
                        <span class="pf-required">*</span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-editor-wrapper">
                <?php
                wp_editor(
                        $value,
                        sanitize_key($id),
                        $settings
                );
                ?>
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
                .pf-editor-wrapper {
                    margin: 8px 0;
                }
                .pf-editor-wrapper .wp-editor-container {
                    border-radius: 12px;
                    overflow: hidden;
                    border: 2px solid #e8edf4;
                }
                .pf-editor-wrapper .wp-editor-container .mce-tinymce {
                    border: none !important;
                }
                .pf-editor-wrapper .wp-editor-container .wp-editor-area {
                    padding: 12px;
                    font-family: inherit;
                    border: none !important;
                }
                .pf-editor-wrapper .wp-editor-container:focus-within {
                    border-color: #6366f1;
                    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
                }
                body.dark-mode .pf-editor-wrapper .wp-editor-container {
                    border-color: #334155;
                    background: #1e293b;
                }
                body.dark-mode .pf-editor-wrapper .wp-editor-container .mce-tinymce {
                    background: #1e293b;
                }
                body.dark-mode .pf-editor-wrapper .wp-editor-container .mce-toolbar .mce-btn button {
                    color: #e2e8f0;
                }
                body.dark-mode .pf-editor-wrapper .wp-editor-container .mce-toolbar .mce-btn:hover {
                    background: #334155;
                }
                body.dark-mode .pf-editor-wrapper .wp-editor-container .wp-editor-area {
                    background: #1e293b;
                    color: #e2e8f0;
                }
                @media (max-width: 768px) {
                    .pf-editor-wrapper .wp-editor-container {
                        border-radius: 8px;
                    }
                }
            </style>
            <?php
            $enqueued = true;
        }
    }
}