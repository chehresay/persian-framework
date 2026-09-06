<?php
/**
 * Persian Framework - Slides Field
 * Unlimited slides with drag and drop sorting
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Slides {

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
            // Ensure all items are arrays
            $normalized = array();
            foreach ($value as $item) {
                if (is_array($item)) {
                    $normalized[] = $item;
                } else {
                    $normalized[] = array();
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

        // Normalize value to always be an array
        $value = $this->normalize_value($this->value);

        // Placeholders
        $placeholders = isset($this->field['placeholder']) ? $this->field['placeholder'] : array();
        $title_placeholder = isset($placeholders['title']) ? $placeholders['title'] : __('Slide Title', 'persian-framework');
        $desc_placeholder = isset($placeholders['description']) ? $placeholders['description'] : __('Slide Description', 'persian-framework');
        $url_placeholder = isset($placeholders['url']) ? $placeholders['url'] : __('https://example.com', 'persian-framework');
        $image_placeholder = isset($placeholders['image']) ? $placeholders['image'] : __('Select Image', 'persian-framework');

        // Button text
        $button_text = isset($this->field['button_text']) ? $this->field['button_text'] : __('Add Slide', 'persian-framework');
        $max_items = isset($this->field['max']) ? intval($this->field['max']) : 0;
        $min_items = isset($this->field['min']) ? intval($this->field['min']) : 0;
        $sortable = isset($this->field['sortable']) ? $this->field['sortable'] : true;
        $collapsible = isset($this->field['collapsible']) ? $this->field['collapsible'] : true;

        // Ensure we have at least minimum items
        if (empty($value) && $min_items > 0) {
            $value = array_fill(0, $min_items, array());
        }

        ?>
        <div class="pf-field-wrapper pf-field-slides"
             data-max="<?php echo esc_attr($max_items); ?>"
             data-min="<?php echo esc_attr($min_items); ?>"
             data-sortable="<?php echo $sortable ? 'true' : 'false'; ?>"
             data-collapsible="<?php echo $collapsible ? 'true' : 'false'; ?>">

            <?php if (isset($this->field['title'])): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                    <?php if ($max_items > 0): ?>
                        <span class="pf-slides-max-label">
                            <?php
                            /* translators: %d: maximum number of slides allowed */
                            printf( esc_html__( '(Max: %d)', 'persian-framework' ), esc_html( $max_items ) );
                            ?>
                        </span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-slides-container">
                <div class="pf-slides-items" id="slides-<?php echo esc_attr($id); ?>">
                    <?php
                    $index = 0;
                    foreach ($value as $slide):
                        if (!is_array($slide)) {
                            $slide = array();
                        }
                        $slide_title = isset($slide['title']) ? $slide['title'] : '';
                        $slide_desc = isset($slide['description']) ? $slide['description'] : '';
                        $slide_url = isset($slide['url']) ? $slide['url'] : '';
                        $slide_image = isset($slide['image']) ? $slide['image'] : '';
                        $slide_target = isset($slide['target']) ? $slide['target'] : '';
                        $slide_rel = isset($slide['rel']) ? $slide['rel'] : '';
                        ?>
                        <div class="pf-slides-item" data-index="<?php echo esc_attr($index); ?>">
                            <div class="pf-slides-item-header">
                                <?php if ($sortable): ?>
                                    <span class="pf-slides-handle dashicons dashicons-move"></span>
                                <?php endif; ?>
                                <span class="pf-slides-item-title">
                                    <?php
                                    if (!empty($slide_title)) {
                                        echo esc_html($slide_title);
                                    } else {
                                        /* translators: %d: slide number */
                                        printf( esc_html__( 'Slide %d', 'persian-framework' ), esc_html( $index + 1 ) );
                                    }
                                    ?>
                                </span>
                                <?php if ($collapsible): ?>
                                    <button type="button" class="pf-slides-toggle">
                                        <span class="dashicons dashicons-arrow-down-alt2"></span>
                                    </button>
                                <?php endif; ?>
                                <button type="button" class="pf-slides-remove" aria-label="<?php esc_attr_e('Remove', 'persian-framework'); ?>">
                                    <span class="dashicons dashicons-no-alt"></span>
                                </button>
                            </div>
                            <div class="pf-slides-item-body" <?php echo $collapsible ? 'style="display:block;"' : ''; ?>>
                                <!-- Title -->
                                <div class="pf-slides-field">
                                    <label class="pf-slides-field-label">
                                        <?php esc_html_e('Title', 'persian-framework'); ?>
                                    </label>
                                    <input type="text"
                                           name="<?php echo esc_attr($name); ?>[<?php echo esc_attr($index); ?>][title]"
                                           value="<?php echo esc_attr($slide_title); ?>"
                                           placeholder="<?php echo esc_attr($title_placeholder); ?>"
                                           class="pf-slides-title pf-field-input" />
                                </div>

                                <!-- Description -->
                                <div class="pf-slides-field">
                                    <label class="pf-slides-field-label">
                                        <?php esc_html_e('Description', 'persian-framework'); ?>
                                    </label>
                                    <textarea name="<?php echo esc_attr($name); ?>[<?php echo esc_attr($index); ?>][description]"
                                              placeholder="<?php echo esc_attr($desc_placeholder); ?>"
                                              class="pf-slides-desc pf-field-input pf-textarea-input"
                                              rows="3"><?php echo esc_textarea($slide_desc); ?></textarea>
                                </div>

                                <!-- URL -->
                                <div class="pf-slides-field">
                                    <label class="pf-slides-field-label">
                                        <?php esc_html_e('URL', 'persian-framework'); ?>
                                    </label>
                                    <input type="url"
                                           name="<?php echo esc_attr($name); ?>[<?php echo esc_attr($index); ?>][url]"
                                           value="<?php echo esc_attr($slide_url); ?>"
                                           placeholder="<?php echo esc_attr($url_placeholder); ?>"
                                           class="pf-slides-url pf-field-input" />
                                </div>

                                <!-- Image -->
                                <div class="pf-slides-field pf-slides-image-field">
                                    <label class="pf-slides-field-label">
                                        <?php esc_html_e('Image', 'persian-framework'); ?>
                                    </label>
                                    <div class="pf-slides-image-control">
                                        <input type="hidden"
                                               name="<?php echo esc_attr($name); ?>[<?php echo esc_attr($index); ?>][image]"
                                               value="<?php echo esc_attr($slide_image); ?>"
                                               class="pf-slides-image-id" />
                                        <div class="pf-slides-image-preview">
                                            <?php if (!empty($slide_image)): ?>
                                                <?php
                                                $image_url = wp_get_attachment_image_url($slide_image, 'thumbnail');
                                                if ($image_url):
                                                    ?>
                                                    <img src="<?php echo esc_url($image_url); ?>" alt="" />
                                                <?php else: ?>
                                                    <span class="dashicons dashicons-format-image"></span>
                                                    <span class="pf-slides-image-placeholder"><?php esc_html_e('No image', 'persian-framework'); ?></span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="dashicons dashicons-format-image"></span>
                                                <span class="pf-slides-image-placeholder"><?php echo esc_html($image_placeholder); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="pf-slides-image-actions">
                                            <button type="button" class="pf-btn pf-btn-secondary pf-slides-image-choose">
                                                <span class="dashicons dashicons-edit"></span>
                                                <?php esc_html_e('Choose', 'persian-framework'); ?>
                                            </button>
                                            <button type="button" class="pf-btn pf-btn-danger pf-slides-image-remove" <?php echo empty($slide_image) ? 'style="display:none;"' : ''; ?>>
                                                <span class="dashicons dashicons-no-alt"></span>
                                                <?php esc_html_e('Remove', 'persian-framework'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Target (Open in new window) -->
                                <div class="pf-slides-field pf-slides-checkbox-field">
                                    <label class="pf-slides-field-label pf-slides-checkbox-label">
                                        <input type="hidden"
                                               name="<?php echo esc_attr($name); ?>[<?php echo esc_attr($index); ?>][target]"
                                               value="0" />
                                        <input type="checkbox"
                                               name="<?php echo esc_attr($name); ?>[<?php echo esc_attr($index); ?>][target]"
                                               value="_blank"
                                                <?php checked($slide_target, '_blank'); ?> />
                                        <?php esc_html_e('Open in new window', 'persian-framework'); ?>
                                    </label>
                                </div>

                                <!-- Rel (nofollow) -->
                                <div class="pf-slides-field pf-slides-checkbox-field">
                                    <label class="pf-slides-field-label pf-slides-checkbox-label">
                                        <input type="hidden"
                                               name="<?php echo esc_attr($name); ?>[<?php echo esc_attr($index); ?>][rel]"
                                               value="0" />
                                        <input type="checkbox"
                                               name="<?php echo esc_attr($name); ?>[<?php echo esc_attr($index); ?>][rel]"
                                               value="nofollow"
                                                <?php checked($slide_rel, 'nofollow'); ?> />
                                        <?php esc_html_e('Add rel="nofollow"', 'persian-framework'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <?php
                        $index++;
                    endforeach;
                    ?>
                </div>

                <div class="pf-slides-actions">
                    <button type="button" class="pf-btn pf-btn-primary pf-slides-add">
                        <span class="dashicons dashicons-plus-alt"></span>
                        <?php echo esc_html($button_text); ?>
                    </button>
                    <?php if (!empty($value) && count($value) > $min_items): ?>
                        <button type="button" class="pf-btn pf-btn-danger pf-slides-clear">
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
        // Pass the count properly
        $item_count = is_array($value) ? count($value) : 0;
        $this->enqueue_scripts($id, $sortable, $collapsible, $item_count);
    }

    private function enqueue_scripts($id, $sortable, $collapsible, $item_count) {
        static $enqueued = false;

        // Enqueue Media Library
        if (!$enqueued) {
            wp_enqueue_media();
            $enqueued = true;
        }

        ?>
        <style>
            .pf-slides-container {
                margin-top: 8px;
            }

            .pf-slides-items {
                display: flex;
                flex-direction: column;
                gap: 12px;
                margin-bottom: 12px;
                min-height: 50px;
            }

            .pf-slides-item {
                border: 1px solid #e8edf4;
                border-radius: 12px;
                background: white;
                transition: all 0.2s ease;
                overflow: hidden;
            }
            body.dark-mode .pf-slides-item {
                background: #1e293b;
                border-color: #334155;
            }

            .pf-slides-item:hover {
                border-color: #94a3b8;
            }
            body.dark-mode .pf-slides-item:hover {
                border-color: #475569;
            }

            .pf-slides-item.sortable-ghost {
                opacity: 0.4;
                border-color: #6366f1;
            }

            .pf-slides-item.sortable-chosen {
                border-color: #6366f1;
                box-shadow: 0 8px 30px rgba(99, 102, 241, 0.3);
                transform: scale(1.02);
            }

            .pf-slides-item-header {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 12px 16px;
                background: #f8f9fa;
                border-bottom: 1px solid #e8edf4;
                cursor: default;
            }
            body.dark-mode .pf-slides-item-header {
                background: #0f172a;
                border-bottom-color: #334155;
            }

            .pf-slides-item-header .pf-slides-handle {
                color: #94a3b8;
                cursor: grab;
                font-size: 18px;
                flex-shrink: 0;
            }
            .pf-slides-item-header .pf-slides-handle:hover {
                color: #6366f1;
            }

            .pf-slides-item-title {
                flex: 1;
                font-weight: 600;
                font-size: 14px;
                color: #1a2332;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            body.dark-mode .pf-slides-item-title {
                color: #e2e8f0;
            }

            .pf-slides-toggle {
                background: none;
                border: none;
                cursor: pointer;
                color: #94a3b8;
                padding: 4px;
                transition: transform 0.3s ease;
            }
            .pf-slides-toggle:hover {
                color: #6366f1;
            }
            .pf-slides-toggle .dashicons {
                font-size: 18px;
                width: 18px;
                height: 18px;
            }
            .pf-slides-toggle.collapsed .dashicons {
                transform: rotate(-90deg);
            }

            .pf-slides-remove {
                background: none;
                border: none;
                cursor: pointer;
                color: #94a3b8;
                padding: 4px;
                transition: all 0.2s ease;
            }
            .pf-slides-remove:hover {
                color: #ef4444;
                transform: scale(1.2);
            }
            .pf-slides-remove .dashicons {
                font-size: 18px;
                width: 18px;
                height: 18px;
            }

            .pf-slides-item-body {
                padding: 16px;
                transition: all 0.3s ease;
            }

            .pf-slides-item-body.collapsed {
                display: none !important;
            }

            .pf-slides-field {
                margin-bottom: 12px;
            }
            .pf-slides-field:last-child {
                margin-bottom: 0;
            }

            .pf-slides-field-label {
                display: block;
                font-size: 13px;
                font-weight: 600;
                color: #1a2332;
                margin-bottom: 4px;
            }
            body.dark-mode .pf-slides-field-label {
                color: #e2e8f0;
            }

            .pf-slides-field .pf-field-input {
                width: 100%;
                padding: 10px 14px;
                border: 1px solid #e8edf4;
                border-radius: 8px;
                font-size: 14px;
                font-family: inherit;
                background: #fafbfc;
                color: #1a2332;
                transition: all 0.2s ease;
            }
            body.dark-mode .pf-slides-field .pf-field-input {
                background: #0f172a;
                border-color: #334155;
                color: #e2e8f0;
            }

            .pf-slides-field .pf-field-input:focus {
                border-color: #6366f1;
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
                outline: none;
            }

            .pf-slides-checkbox-field {
                display: flex;
                align-items: center;
            }

            .pf-slides-checkbox-label {
                display: flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
                font-weight: 400;
            }

            .pf-slides-checkbox-label input[type="checkbox"] {
                width: 18px;
                height: 18px;
                accent-color: #6366f1;
                cursor: pointer;
            }

            .pf-slides-image-control {
                display: flex;
                align-items: center;
                gap: 12px;
                flex-wrap: wrap;
            }

            .pf-slides-image-preview {
                width: 80px;
                height: 60px;
                border-radius: 8px;
                overflow: hidden;
                background: #f8f9fa;
                border: 1px solid #e8edf4;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            body.dark-mode .pf-slides-image-preview {
                background: #0f172a;
                border-color: #334155;
            }

            .pf-slides-image-preview img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            .pf-slides-image-preview .dashicons {
                font-size: 32px;
                width: 32px;
                height: 32px;
                color: #cbd5e1;
            }
            body.dark-mode .pf-slides-image-preview .dashicons {
                color: #334155;
            }

            .pf-slides-image-placeholder {
                font-size: 11px;
                color: #94a3b8;
                text-align: center;
            }

            .pf-slides-image-actions {
                display: flex;
                gap: 6px;
                flex-wrap: wrap;
            }

            .pf-slides-image-actions .pf-btn {
                padding: 6px 14px;
                font-size: 13px;
            }
            .pf-slides-image-actions .pf-btn .dashicons {
                font-size: 14px;
                width: 14px;
                height: 14px;
            }

            .pf-slides-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }

            .pf-slides-actions .pf-btn .dashicons {
                font-size: 16px;
                width: 16px;
                height: 16px;
            }

            .pf-slides-max-label {
                font-size: 12px;
                font-weight: 400;
                color: #94a3b8;
                margin-left: 6px;
            }

            @media (max-width: 768px) {
                .pf-slides-item-header {
                    flex-wrap: wrap;
                    gap: 6px;
                }
                .pf-slides-item-title {
                    order: 1;
                    width: 100%;
                }
                .pf-slides-toggle {
                    order: 2;
                }
                .pf-slides-remove {
                    order: 3;
                }
                .pf-slides-handle {
                    order: 0;
                }
                .pf-slides-item-body {
                    padding: 12px;
                }
                .pf-slides-actions {
                    flex-direction: column;
                }
                .pf-slides-actions .pf-btn {
                    justify-content: center;
                }
                .pf-slides-image-control {
                    flex-direction: column;
                    align-items: stretch;
                }
                .pf-slides-image-preview {
                    width: 100%;
                    height: 120px;
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
                var sortableEnabled = <?php echo $sortable ? 'true' : 'false'; ?>;
                var collapsibleEnabled = <?php echo $collapsible ? 'true' : 'false'; ?>;
                var $container = $('#slides-<?php echo esc_js($id); ?>');

                // Use the passed item count
                var itemCounter = <?php echo intval($item_count); ?>;

                // Generate HTML for a single slide item
                function generateSlideItem(index) {
                    var titlePlaceholder = '<?php echo esc_js($this->field['placeholder']['title'] ?? __('Slide Title', 'persian-framework')); ?>';
                    var descPlaceholder = '<?php echo esc_js($this->field['placeholder']['description'] ?? __('Slide Description', 'persian-framework')); ?>';
                    var urlPlaceholder = '<?php echo esc_js($this->field['placeholder']['url'] ?? __('https://example.com', 'persian-framework')); ?>';

                    var html = '<div class="pf-slides-item" data-index="' + index + '">';
                    html += '<div class="pf-slides-item-header">';
                    if (sortableEnabled) {
                        html += '<span class="pf-slides-handle dashicons dashicons-move"></span>';
                    }
                    html += '<span class="pf-slides-item-title"><?php echo esc_js( esc_html__( 'Slide', 'persian-framework' ) ); ?> ' + (index + 1) + '</span>';
                    if (collapsibleEnabled) {
                        html += '<button type="button" class="pf-slides-toggle"><span class="dashicons dashicons-arrow-down-alt2"></span></button>';
                    }
                    html += '<button type="button" class="pf-slides-remove" aria-label="<?php echo esc_js( esc_html__( 'Remove', 'persian-framework' ) ); ?>"><span class="dashicons dashicons-no-alt"></span></button>';
                    html += '</div>';
                    html += '<div class="pf-slides-item-body">';

                    // Title
                    html += '<div class="pf-slides-field">';
                    html += '<label class="pf-slides-field-label"><?php echo esc_js( esc_html__( 'Title', 'persian-framework' ) ); ?></label>';
                    html += '<input type="text" name="' + fieldName + '[' + index + '][title]" value="" placeholder="' + titlePlaceholder + '" class="pf-slides-title pf-field-input">';
                    html += '</div>';

                    // Description
                    html += '<div class="pf-slides-field">';
                    html += '<label class="pf-slides-field-label"><?php echo esc_js( esc_html__( 'Description', 'persian-framework' ) ); ?></label>';
                    html += '<textarea name="' + fieldName + '[' + index + '][description]" placeholder="' + descPlaceholder + '" class="pf-slides-desc pf-field-input pf-textarea-input" rows="3"></textarea>';
                    html += '</div>';

                    // URL
                    html += '<div class="pf-slides-field">';
                    html += '<label class="pf-slides-field-label"><?php echo esc_js( esc_html__( 'URL', 'persian-framework' ) ); ?></label>';
                    html += '<input type="url" name="' + fieldName + '[' + index + '][url]" value="" placeholder="' + urlPlaceholder + '" class="pf-slides-url pf-field-input">';
                    html += '</div>';

                    // Image
                    html += '<div class="pf-slides-field pf-slides-image-field">';
                    html += '<label class="pf-slides-field-label"><?php echo esc_js( esc_html__( 'Image', 'persian-framework' ) ); ?></label>';
                    html += '<div class="pf-slides-image-control">';
                    html += '<input type="hidden" name="' + fieldName + '[' + index + '][image]" value="" class="pf-slides-image-id">';
                    html += '<div class="pf-slides-image-preview">';
                    html += '<span class="dashicons dashicons-format-image"></span>';
                    html += '<span class="pf-slides-image-placeholder"><?php echo esc_js( esc_html__( 'Select Image', 'persian-framework' ) ); ?></span>';
                    html += '</div>';
                    html += '<div class="pf-slides-image-actions">';
                    html += '<button type="button" class="pf-btn pf-btn-secondary pf-slides-image-choose"><span class="dashicons dashicons-edit"></span> <?php echo esc_js( esc_html__( 'Choose', 'persian-framework' ) ); ?></button>';
                    html += '<button type="button" class="pf-btn pf-btn-danger pf-slides-image-remove" style="display:none;"><span class="dashicons dashicons-no-alt"></span> <?php echo esc_js( esc_html__( 'Remove', 'persian-framework' ) ); ?></button>';
                    html += '</div></div></div>';

                    // Target
                    html += '<div class="pf-slides-field pf-slides-checkbox-field">';
                    html += '<label class="pf-slides-field-label pf-slides-checkbox-label">';
                    html += '<input type="hidden" name="' + fieldName + '[' + index + '][target]" value="0">';
                    html += '<input type="checkbox" name="' + fieldName + '[' + index + '][target]" value="_blank">';
                    html += '<?php echo esc_js( esc_html__( 'Open in new window', 'persian-framework' ) ); ?>';
                    html += '</label></div>';

                    // Rel
                    html += '<div class="pf-slides-field pf-slides-checkbox-field">';
                    html += '<label class="pf-slides-field-label pf-slides-checkbox-label">';
                    html += '<input type="hidden" name="' + fieldName + '[' + index + '][rel]" value="0">';
                    html += '<input type="checkbox" name="' + fieldName + '[' + index + '][rel]" value="nofollow">';
                    html += '<?php echo esc_js( esc_html__( 'Add rel="nofollow"', 'persian-framework' ) ); ?>';
                    html += '</label></div>';

                    html += '</div></div>';
                    return html;
                }

                // Add new slide
                $('.pf-slides-add').on('click', function() {
                    if (maxItems > 0 && $container.find('.pf-slides-item').length >= maxItems) {
                        alert('<?php echo esc_js( esc_html__( 'Maximum number of slides reached.', 'persian-framework' ) ); ?>');
                        return;
                    }

                    var index = itemCounter++;
                    var $item = $(generateSlideItem(index));
                    $container.append($item);
                    updateItemIndexes();
                    updateRemoveButtons();
                    updateSlideTitles();

                    // Trigger event
                    $(document).trigger('pf-slides-add', [fieldId, $item]);

                    // Re-init sortable
                    if (sortableEnabled) {
                        initSortable();
                    }

                    // Show clear button
                    $('.pf-slides-clear').show();
                });

                // Remove slide
                $(document).on('click', '.pf-slides-remove', function() {
                    var $item = $(this).closest('.pf-slides-item');
                    var count = $container.find('.pf-slides-item').length;

                    if (count <= minItems) {
                        alert('<?php echo esc_js( esc_html__( 'Minimum number of slides required.', 'persian-framework' ) ); ?>');
                        return;
                    }

                    if (confirm('<?php echo esc_js( esc_html__( 'Remove this slide?', 'persian-framework' ) ); ?>')) {
                        $item.fadeOut(300, function() {
                            $item.remove();
                            updateItemIndexes();
                            updateRemoveButtons();
                            updateSlideTitles();

                            if ($container.find('.pf-slides-item').length === 0) {
                                $('.pf-slides-clear').hide();
                            }

                            $(document).trigger('pf-slides-remove', [fieldId]);
                        });
                    }
                });

                // Clear all slides
                $(document).on('click', '.pf-slides-clear', function() {
                    var count = $container.find('.pf-slides-item').length;
                    if (count <= minItems) {
                        alert('<?php echo esc_js( esc_html__( 'Minimum number of slides required.', 'persian-framework' ) ); ?>');
                        return;
                    }

                    if (confirm('<?php echo esc_js( esc_html__( 'Remove all slides?', 'persian-framework' ) ); ?>')) {
                        $container.find('.pf-slides-item').fadeOut(300, function() {
                            $container.empty();
                            for (var i = 0; i < minItems; i++) {
                                var index = itemCounter++;
                                var $item = $(generateSlideItem(index));
                                $container.append($item);
                            }
                            updateItemIndexes();
                            updateRemoveButtons();
                            updateSlideTitles();
                            $('.pf-slides-clear').hide();

                            $(document).trigger('pf-slides-clear', [fieldId]);

                            if (sortableEnabled) {
                                initSortable();
                            }
                        });
                    }
                });

                // Toggle slide body
                $(document).on('click', '.pf-slides-toggle', function() {
                    if (!collapsibleEnabled) return;

                    var $header = $(this).closest('.pf-slides-item-header');
                    var $body = $header.next('.pf-slides-item-body');
                    $body.slideToggle(300);
                    $(this).toggleClass('collapsed');
                });

                // Update item indexes
                function updateItemIndexes() {
                    $container.find('.pf-slides-item').each(function(index) {
                        var $item = $(this);
                        $item.data('index', index);
                        // Update all field names in this item
                        $item.find('input, textarea, select').each(function() {
                            var $field = $(this);
                            var name = $field.attr('name');
                            if (name) {
                                var newName = name.replace(/\[\d+\]/, '[' + index + ']');
                                $field.attr('name', newName);
                            }
                        });
                    });
                }

                // Update remove buttons visibility
                function updateRemoveButtons() {
                    var count = $container.find('.pf-slides-item').length;
                    $container.find('.pf-slides-remove').toggle(count > minItems);
                }

                // Update slide titles
                function updateSlideTitles() {
                    $container.find('.pf-slides-item').each(function(index) {
                        var $item = $(this);
                        var $title = $item.find('.pf-slides-item-title');
                        var $titleInput = $item.find('.pf-slides-title');
                        if ($titleInput.length) {
                            var value = $titleInput.val() || '<?php echo esc_js( esc_html__( 'Slide', 'persian-framework' ) ); ?> ' + (index + 1);
                            $title.text(value);
                        } else {
                            $title.text('<?php echo esc_js( esc_html__( 'Slide', 'persian-framework' ) ); ?> ' + (index + 1));
                        }
                    });
                }

                // Update title on input change
                $(document).on('input', '.pf-slides-title', function() {
                    var $item = $(this).closest('.pf-slides-item');
                    var $title = $item.find('.pf-slides-item-title');
                    var value = $(this).val() || '<?php echo esc_js( esc_html__( 'Slide', 'persian-framework' ) ); ?> ' + ($item.data('index') + 1);
                    $title.text(value);
                });

                // Image selection for slides
                var mediaFrame = null;

                $(document).on('click', '.pf-slides-image-choose', function() {
                    var $button = $(this);
                    var $container = $button.closest('.pf-slides-image-control');
                    var $idInput = $container.find('.pf-slides-image-id');
                    var $preview = $container.find('.pf-slides-image-preview');
                    var $removeBtn = $container.find('.pf-slides-image-remove');

                    if (mediaFrame) {
                        mediaFrame.open();
                        return;
                    }

                    mediaFrame = wp.media({
                        multiple: false,
                        library: {
                            type: 'image'
                        }
                    });

                    mediaFrame.on('select', function() {
                        var attachment = mediaFrame.state().get('selection').first().toJSON();
                        var id = attachment.id;
                        var url = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

                        $idInput.val(id);
                        $preview.html('<img src="' + url + '" alt="">');
                        $removeBtn.show();

                        $container.trigger('pf-slides-image-selected', [attachment]);
                    });

                    mediaFrame.open();
                });

                // Remove image
                $(document).on('click', '.pf-slides-image-remove', function() {
                    var $container = $(this).closest('.pf-slides-image-control');
                    var $idInput = $container.find('.pf-slides-image-id');
                    var $preview = $container.find('.pf-slides-image-preview');

                    $idInput.val('');
                    $preview.html(
                        '<span class="dashicons dashicons-format-image"></span>' +
                        '<span class="pf-slides-image-placeholder"><?php echo esc_js( esc_html__( 'Select Image', 'persian-framework' ) ); ?></span>'
                    );
                    $(this).hide();

                    $container.trigger('pf-slides-image-removed');
                });

                // Init SortableJS
                function initSortable() {
                    if (!sortableEnabled || typeof Sortable === 'undefined') return;

                    var container = document.getElementById('slides-<?php echo esc_js($id); ?>');
                    if (!container) return;

                    if (container.sortableInstance) {
                        container.sortableInstance.destroy();
                    }

                    var sortable = new Sortable(container, {
                        animation: 150,
                        handle: '.pf-slides-handle',
                        ghostClass: 'sortable-ghost',
                        chosenClass: 'sortable-chosen',
                        dragClass: 'sortable-drag',
                        onEnd: function() {
                            updateItemIndexes();
                            updateSlideTitles();
                            $(document).trigger('pf-slides-sort', [fieldId]);
                        }
                    });

                    container.sortableInstance = sortable;
                }



                // Initialize on document ready
                $(document).ready(function() {
                    var existingItems = $container.find('.pf-slides-item');
                    if (existingItems.length > 0) {
                        var maxIndex = -1;
                        existingItems.each(function() {
                            var index = parseInt($(this).data('index'));
                            if (!isNaN(index) && index > maxIndex) {
                                maxIndex = index;
                            }
                        });
                        if (maxIndex >= 0) {
                            itemCounter = maxIndex + 1;
                        } else {
                            itemCounter = existingItems.length;
                        }

                        // ✅ Collapse all items on page load
                        setTimeout(function() {
                            $container.find('.pf-slides-toggle').addClass('collapsed');
                            $container.find('.pf-slides-item-body').slideUp(300);
                        }, 100);
                    }


                    updateItemIndexes();
                    updateRemoveButtons();
                    updateSlideTitles();

                    if (sortableEnabled) {
                        initSortable();
                    }

                    if ($container.find('.pf-slides-item').length === 0) {
                        $('.pf-slides-clear').hide();
                    }
                });

            })(jQuery);
        </script>
        <?php
    }
}