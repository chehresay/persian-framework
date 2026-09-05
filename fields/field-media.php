<?php
/**
 * Persian Framework - Media Field
 * Single media file selection with WordPress Media Library
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Media {

    private $field;
    private $value;
    private static $frame_counter = 0;

    public function __construct($field, $value = null) {
        $this->field = $field;
        $this->value = $value;
        self::$frame_counter++;
    }

    public function render() {
        $id = isset($this->field['id']) ? $this->field['id'] : '';
        $name = isset($this->field['name']) ? $this->field['name'] : $id;

        $value = $this->value;

        if (is_string($value) && !empty($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded) && isset($decoded['id'])) {
                $value = $decoded;
            }
        }

        if (!is_array($value)) {
            $value = array(
                    'id' => 0,
                    'url' => (string) $value
            );
        }

        if (!isset($value['id'])) {
            $value['id'] = 0;
        }
        if (!isset($value['url'])) {
            $value['url'] = '';
        }

        $media_id = absint($value['id']);
        $media_url = esc_url($value['url']);

        if ($media_id > 0 && empty($media_url)) {
            $attachment_url = wp_get_attachment_url($media_id);
            if ($attachment_url) {
                $media_url = $attachment_url;
                $value['url'] = $media_url;
            }
        }

        if (empty($media_id) && !empty($media_url)) {
            $attachment_id = attachment_url_to_postid($media_url);
            if ($attachment_id) {
                $media_id = $attachment_id;
                $value['id'] = $media_id;
            }
        }

        $title = $media_id ? get_the_title($media_id) : '';
        $preview_size = isset($this->field['preview_size']) ? $this->field['preview_size'] : 'thumbnail';
        $image_only = isset($this->field['image_only']) && $this->field['image_only'];

        $preview_url = $media_url;
        if ($media_id) {
            $image = wp_get_attachment_image_src($media_id, $preview_size);
            if ($image) {
                $preview_url = $image[0];
            }
        }

        $required = isset($this->field['required']) ? $this->field['required'] : false;
        $required_attributes = '';
        if (is_array($required) && class_exists('PersianFramework_Required')) {
            $required_attributes = PersianFramework_Required::get_attributes($required);
        }

        $unique_id = 'pf-media-' . $id . '-' . self::$frame_counter;

        ?>
        <div class="pf-field-wrapper pf-field-media <?php echo $image_only ? 'pf-field-image' : ''; ?>" <?php echo wp_kses_data($required_attributes); ?>>
            <?php if (isset($this->field['title'])): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-media-container" data-unique-id="<?php echo esc_attr($unique_id); ?>">
                <div class="pf-media-preview">
                    <?php if ($preview_url): ?>
                        <?php if ($image_only || wp_attachment_is_image($media_id)): ?>
                            <img src="<?php echo esc_url($preview_url); ?>" alt="<?php echo esc_attr($title); ?>" />
                        <?php else: ?>
                            <span class="dashicons dashicons-media-default"></span>
                            <span class="pf-media-filename"><?php echo esc_html(basename($media_url)); ?></span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="dashicons dashicons-format-image"></span>
                        <span class="pf-media-placeholder"><?php esc_html_e('No media selected', 'persian-framework'); ?></span>
                    <?php endif; ?>
                </div>

                <div class="pf-media-fields-wrapper" style="width: 100%; display: inline-block;">
                    <div class="pf-media-fields">
                        <input type="hidden"
                               class="pf-media-id"
                               name="<?php echo esc_attr($name); ?>[id]"
                               value="<?php echo esc_attr($media_id); ?>" />

                        <input type="text"
                               class="pf-field-input pf-media-url"
                               name="<?php echo esc_attr($name); ?>[url]"
                               value="<?php echo esc_attr($media_url); ?>"
                               placeholder="<?php esc_attr_e('Media URL', 'persian-framework'); ?>" />

                        <?php if ($media_id && $title): ?>
                            <p class="pf-media-title"><?php echo esc_html($title); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="pf-media-actions">
                        <button type="button" class="pf-btn pf-btn-secondary pf-media-choose" data-unique-id="<?php echo esc_attr($unique_id); ?>">
                            <span class="dashicons dashicons-edit"></span>
                            <?php esc_html_e('Choose', 'persian-framework'); ?>
                        </button>
                        <button type="button" class="pf-btn pf-btn-danger pf-media-remove" data-unique-id="<?php echo esc_attr($unique_id); ?>" <?php echo !$media_id ? 'style="display:none;"' : ''; ?>>
                            <span class="dashicons dashicons-no-alt"></span>
                            <?php esc_html_e('Remove', 'persian-framework'); ?>
                        </button>
                    </div>
                </div>

            </div>

            <?php if (isset($this->field['desc'])): ?>
                <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php
        $this->enqueue_scripts($image_only, $unique_id);
    }

    private function enqueue_scripts($image_only, $unique_id) {
        static $pf_media_enqueued = false;

        if (!$pf_media_enqueued) {
            wp_enqueue_media();

            ?>
            <style>
                .pf-media-container {
                    margin-top: 8px;
                    padding: 16px;
                    background: #fff;
                    border: 1px solid #e8edf4;
                    border-radius: 24px;
                    display: flex;
                    flex-wrap: wrap;
                    gap: 16px;
                    align-items: flex-start;
                }
                body.dark-mode .pf-media-container {
                    background: #0f172a;
                    border-color: #334155;
                }

                .pf-media-preview {
                    width: 100%;
                    height: 160px;
                    flex-shrink: 0;
                    border-radius: 18px;
                    overflow: hidden;
                    background: white;
                    border: 2px dashed #e8edf4;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                }
                body.dark-mode .pf-media-preview {
                    background: #1e293b;
                    border-color: #334155;
                }

                .pf-media-preview img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    display: block;
                }

                .pf-media-preview .dashicons {
                    font-size: 48px;
                    width: 48px;
                    height: 48px;
                    color: #cbd5e1;
                }
                body.dark-mode .pf-media-preview .dashicons {
                    color: #334155;
                }

                .pf-media-placeholder {
                    font-size: 12px;
                    color: #94a3b8;
                    text-align: center;
                    width: 100%;
                    display: inline-block;
                }

                .pf-media-filename {
                    font-size: 12px;
                    color: #1a2332;
                    max-width: 90px;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                }
                body.dark-mode .pf-media-filename {
                    color: #e2e8f0;
                }

                .pf-media-fields {
                    flex: 1;
                    min-width: 150px;
                    display: flex;
                    flex-direction: column;
                    gap: 6px;
                }

                .pf-media-url {
                    width: 100%;
                    font-size: 13px;
                    background: white;
                    cursor: default;
                }
                body.dark-mode .pf-media-url {
                    background: #1e293b;
                }

                .pf-media-title {
                    margin: 0;
                    font-size: 13px;
                    color: #6b7a8f;
                }
                body.dark-mode .pf-media-title {
                    color: #94a3b8;
                }

                .pf-media-actions {
                    display: flex;
                    gap: 6px;
                    flex-wrap: wrap;
                    margin: 20px 0 0;
                }

                .pf-media-actions .pf-btn {
                    padding: 6px 14px;
                    font-size: 13px;
                }
                .pf-media-actions .pf-btn .dashicons {
                    font-size: 14px;
                    width: 14px;
                    height: 14px;
                }

                @media (max-width: 768px) {
                    .pf-media-container {
                        flex-direction: column;
                        align-items: stretch;
                    }
                    .pf-media-preview {
                        width: 100%;
                        height: 200px;
                        max-width: 300px;
                        margin: 0 auto;
                    }
                }
            </style>

            <script>
                (function($) {
                    'use strict';

                    var pfMediaFrames = {};

                    function pfOpenMediaFrame(uniqueId) {
                        var $container = $('.pf-media-container[data-unique-id="' + uniqueId + '"]');
                        if (!$container.length) {
                            console.warn('Media container not found for unique ID:', uniqueId);
                            return;
                        }

                        var $idInput = $container.find('.pf-media-id');
                        var $urlInput = $container.find('.pf-media-url');
                        var $preview = $container.find('.pf-media-preview');
                        var $removeBtn = $container.find('.pf-media-remove');
                        var isImage = $container.closest('.pf-field-image').length > 0;

                        if (pfMediaFrames[uniqueId]) {
                            pfMediaFrames[uniqueId].open();
                            return;
                        }

                        var frame = wp.media({
                            multiple: false,
                            library: {
                                type: isImage ? 'image' : null
                            },
                        });

                        pfMediaFrames[uniqueId] = frame;

                        frame.on('select', function() {
                            var attachment = frame.state().get('selection').first().toJSON();
                            var id = attachment.id;
                            var url = attachment.url;

                            $idInput.val(id);
                            $urlInput.val(url);

                            if (isImage || attachment.type === 'image') {
                                var previewUrl = attachment.sizes && attachment.sizes.thumbnail ?
                                    attachment.sizes.thumbnail.url :
                                    url;
                                $preview.html('<img src="' + previewUrl + '" alt="' + (attachment.title || '') + '" />');
                            } else {
                                $preview.html(
                                    '<span class="dashicons dashicons-media-default"></span>' +
                                    '<span class="pf-media-filename">' + (attachment.filename || '') + '</span>'
                                );
                            }

                            $removeBtn.show();

                            $idInput.trigger('change');
                            $urlInput.trigger('change');

                            $container.trigger('pf-media-selected', [attachment]);
                        });

                        frame.open();
                    }

                    $(document).on('click', '.pf-media-choose', function(e) {
                        e.preventDefault();
                        var uniqueId = $(this).data('unique-id');
                        if (uniqueId) {
                            pfOpenMediaFrame(uniqueId);
                        }
                    });

                    $(document).on('click', '.pf-media-remove', function(e) {
                        e.preventDefault();
                        var uniqueId = $(this).data('unique-id');
                        if (!uniqueId) return;

                        var $container = $('.pf-media-container[data-unique-id="' + uniqueId + '"]');
                        if (!$container.length) return;

                        var $idInput = $container.find('.pf-media-id');
                        var $urlInput = $container.find('.pf-media-url');
                        var $preview = $container.find('.pf-media-preview');

                        $idInput.val('0');
                        $urlInput.val('');
                        $preview.html(
                            '<span class="dashicons dashicons-format-image"></span>' +
                            '<span class="pf-media-placeholder"><?php esc_html_e('No media selected', 'persian-framework'); ?></span>'
                        );
                        $(this).hide();

                        $idInput.trigger('change');
                        $urlInput.trigger('change');

                        $container.trigger('pf-media-removed');

                        if (pfMediaFrames[uniqueId]) {
                            delete pfMediaFrames[uniqueId];
                        }
                    });

                    $(document).on('change', '.pf-media-id', function() {
                        var $container = $(this).closest('.pf-media-container');
                        var $urlInput = $container.find('.pf-media-url');
                        var id = $(this).val();

                        if (id && id > 0) {
                            if (!$urlInput.val()) {
                                $.ajax({
                                    url: ajaxurl,
                                    type: 'POST',
                                    data: {
                                        action: 'pf_get_attachment_url',
                                        attachment_id: id,
                                        nonce: '<?php echo esc_js(wp_create_nonce('pf_ajax_nonce')); ?>'
                                    },
                                    success: function(response) {
                                        if (response.success && response.data.url) {
                                            $urlInput.val(response.data.url);
                                            var $preview = $container.find('.pf-media-preview');
                                            if ($preview.find('img').length === 0 && response.data.url) {
                                                $preview.html('<img src="' + response.data.url + '" alt="">');
                                            }
                                            $container.find('.pf-media-remove').show();
                                        }
                                    }
                                });
                            }
                        }
                    });

                    $(document).on('change', '.pf-media-url', function() {
                        var $container = $(this).closest('.pf-media-container');
                        var $idInput = $container.find('.pf-media-id');
                        var url = $(this).val();

                        if (url && !$idInput.val()) {
                            $.ajax({
                                url: ajaxurl,
                                type: 'POST',
                                data: {
                                    action: 'pf_get_attachment_id',
                                    attachment_url: url,
                                    nonce: '<?php echo esc_js(wp_create_nonce('pf_ajax_nonce')); ?>'
                                },
                                success: function(response) {
                                    if (response.success && response.data.id) {
                                        $idInput.val(response.data.id);
                                        $container.find('.pf-media-remove').show();
                                    }
                                }
                            });
                        }
                    });

                    $(window).on('beforeunload', function() {
                        for (var key in pfMediaFrames) {
                            if (pfMediaFrames[key] && typeof pfMediaFrames[key].close === 'function') {
                                pfMediaFrames[key].close();
                            }
                        }
                        pfMediaFrames = {};
                    });

                })(jQuery);
            </script>
            <?php
            $pf_media_enqueued = true;
        }
    }

    public function sanitize($value) {
        if (!is_array($value)) {
            return array(
                    'id' => 0,
                    'url' => ''
            );
        }

        $sanitized = array();

        if (isset($value['id'])) {
            $sanitized['id'] = absint($value['id']);
        } else {
            $sanitized['id'] = 0;
        }

        if (isset($value['url'])) {
            $sanitized['url'] = esc_url_raw($value['url']);
        } else {
            $sanitized['url'] = '';
        }

        if ($sanitized['id'] > 0 && empty($sanitized['url'])) {
            $attachment_url = wp_get_attachment_url($sanitized['id']);
            if ($attachment_url) {
                $sanitized['url'] = $attachment_url;
            }
        }

        return $sanitized;
    }
}