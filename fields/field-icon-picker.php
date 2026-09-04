<?php
/**
 * Persian Framework - Icon Picker Field
 * Select WordPress Dashicons
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_IconPicker {

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
        $placeholder = isset($this->field['placeholder']) ? $this->field['placeholder'] : 'dashicons-admin-generic';

        // Get Dashicons list
        $icons = $this->get_dashicons_list();

        ?>
        <div class="pf-field-wrapper pf-field-icon-picker">
            <?php if (isset($this->field['title'])): ?>
                <label for="<?php echo esc_attr($id); ?>" class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-icon-picker-container">
                <div class="pf-icon-picker-input">
                    <input type="text"
                           id="<?php echo esc_attr($id); ?>"
                           name="<?php echo esc_attr($name); ?>"
                           value="<?php echo esc_attr($value); ?>"
                           placeholder="<?php echo esc_attr($placeholder); ?>"
                           class="pf-field-input pf-icon-picker-search"
                           autocomplete="off" />
                    <span class="pf-icon-picker-preview <?php echo esc_attr($value); ?>"></span>
                </div>

                <div class="pf-icon-picker-dropdown" style="display:none;">
                    <div class="pf-icon-picker-search-wrap">
                        <input type="text"
                               class="pf-icon-picker-filter"
                               placeholder="<?php esc_attr_e('Filter icons...', 'persian-framework'); ?>" />
                    </div>
                    <div class="pf-icon-picker-grid">
                        <?php foreach ($icons as $icon): ?>
                            <button type="button"
                                    class="pf-icon-picker-icon <?php echo esc_attr($icon); ?>"
                                    data-icon="<?php echo esc_attr($icon); ?>"
                                    title="<?php echo esc_attr($icon); ?>">
                                <span class="dashicons <?php echo esc_attr($icon); ?>"></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <?php if (isset($this->field['desc'])): ?>
                <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php
        $this->enqueue_scripts();
    }

    private function get_dashicons_list() {
        return array(
            'dashicons-admin-appearance',
            'dashicons-admin-collapse',
            'dashicons-admin-comments',
            'dashicons-admin-customizer',
            'dashicons-admin-generic',
            'dashicons-admin-home',
            'dashicons-admin-links',
            'dashicons-admin-media',
            'dashicons-admin-multisite',
            'dashicons-admin-network',
            'dashicons-admin-page',
            'dashicons-admin-plugins',
            'dashicons-admin-post',
            'dashicons-admin-settings',
            'dashicons-admin-site',
            'dashicons-admin-tools',
            'dashicons-admin-users',
            'dashicons-album',
            'dashicons-align-center',
            'dashicons-align-left',
            'dashicons-align-none',
            'dashicons-align-right',
            'dashicons-arrow-down',
            'dashicons-arrow-left',
            'dashicons-arrow-right',
            'dashicons-arrow-up',
            'dashicons-art',
            'dashicons-awards',
            'dashicons-backup',
            'dashicons-book',
            'dashicons-book-alt',
            'dashicons-buddicons-activity',
            'dashicons-buddicons-bbpress-logo',
            'dashicons-buddicons-buddypress-logo',
            'dashicons-buddicons-community',
            'dashicons-buddicons-forums',
            'dashicons-buddicons-friends',
            'dashicons-buddicons-groups',
            'dashicons-buddicons-pm',
            'dashicons-buddicons-replies',
            'dashicons-buddicons-topics',
            'dashicons-buddicons-tracking',
            'dashicons-building',
            'dashicons-businessman',
            'dashicons-calendar',
            'dashicons-calendar-alt',
            'dashicons-camera',
            'dashicons-car',
            'dashicons-cart',
            'dashicons-category',
            'dashicons-chart-area',
            'dashicons-chart-bar',
            'dashicons-chart-line',
            'dashicons-chart-pie',
            'dashicons-clipboard',
            'dashicons-clock',
            'dashicons-cloud',
            'dashicons-cloud-saved',
            'dashicons-cloud-upload',
            'dashicons-code-standards',
            'dashicons-controls-back',
            'dashicons-controls-forward',
            'dashicons-controls-pause',
            'dashicons-controls-play',
            'dashicons-controls-repeat',
            'dashicons-controls-skipback',
            'dashicons-controls-skipforward',
            'dashicons-controls-volumeoff',
            'dashicons-controls-volumeon',
            'dashicons-dashboard',
            'dashicons-database',
            'dashicons-database-add',
            'dashicons-database-export',
            'dashicons-database-import',
            'dashicons-database-remove',
            'dashicons-database-view',
            'dashicons-desktop',
            'dashicons-dismiss',
            'dashicons-download',
            'dashicons-duplicate',
            'dashicons-edit',
            'dashicons-edit-page',
            'dashicons-editor-aligncenter',
            'dashicons-editor-alignleft',
            'dashicons-editor-alignright',
            'dashicons-editor-bold',
            'dashicons-editor-break',
            'dashicons-editor-code',
            'dashicons-editor-contract',
            'dashicons-editor-customchar',
            'dashicons-editor-expand',
            'dashicons-editor-help',
            'dashicons-editor-indent',
            'dashicons-editor-insertmore',
            'dashicons-editor-italic',
            'dashicons-editor-justify',
            'dashicons-editor-kitchensink',
            'dashicons-editor-ltr',
            'dashicons-editor-ol',
            'dashicons-editor-outdent',
            'dashicons-editor-paragraph',
            'dashicons-editor-paste-text',
            'dashicons-editor-paste-word',
            'dashicons-editor-quote',
            'dashicons-editor-removeformatting',
            'dashicons-editor-rtl',
            'dashicons-editor-spellcheck',
            'dashicons-editor-strikethrough',
            'dashicons-editor-table',
            'dashicons-editor-textcolor',
            'dashicons-editor-ul',
            'dashicons-editor-underline',
            'dashicons-editor-unlink',
            'dashicons-editor-video',
            'dashicons-email',
            'dashicons-email-alt',
            'dashicons-email-alt2',
            'dashicons-embed-audio',
            'dashicons-embed-generic',
            'dashicons-embed-photo',
            'dashicons-embed-post',
            'dashicons-embed-video',
            'dashicons-excerpt-view',
            'dashicons-external',
            'dashicons-facebook',
            'dashicons-facebook-alt',
            'dashicons-feedback',
            'dashicons-filter',
            'dashicons-flag',
            'dashicons-food',
            'dashicons-format-aside',
            'dashicons-format-audio',
            'dashicons-format-chat',
            'dashicons-format-gallery',
            'dashicons-format-image',
            'dashicons-format-quote',
            'dashicons-format-status',
            'dashicons-format-video',
            'dashicons-forms',
            'dashicons-googleplus',
            'dashicons-groups',
            'dashicons-hammer',
            'dashicons-heading',
            'dashicons-heart',
            'dashicons-hidden',
            'dashicons-id',
            'dashicons-id-alt',
            'dashicons-image-crop',
            'dashicons-image-filter',
            'dashicons-image-flip-horizontal',
            'dashicons-image-flip-vertical',
            'dashicons-image-rotate',
            'dashicons-image-rotate-left',
            'dashicons-image-rotate-right',
            'dashicons-images-alt',
            'dashicons-images-alt2',
            'dashicons-index-card',
            'dashicons-info',
            'dashicons-instagram',
            'dashicons-laptop',
            'dashicons-layout',
            'dashicons-leftright',
            'dashicons-lightbulb',
            'dashicons-list-view',
            'dashicons-location',
            'dashicons-location-alt',
            'dashicons-lock',
            'dashicons-marker',
            'dashicons-media-archive',
            'dashicons-media-audio',
            'dashicons-media-code',
            'dashicons-media-default',
            'dashicons-media-document',
            'dashicons-media-interactive',
            'dashicons-media-spreadsheet',
            'dashicons-media-text',
            'dashicons-media-video',
            'dashicons-megaphone',
            'dashicons-menu',
            'dashicons-menu-alt',
            'dashicons-menu-alt2',
            'dashicons-menu-alt3',
            'dashicons-microphone',
            'dashicons-migrate',
            'dashicons-minus',
            'dashicons-money',
            'dashicons-money-alt',
            'dashicons-move',
            'dashicons-nametag',
            'dashicons-networking',
            'dashicons-no',
            'dashicons-no-alt',
            'dashicons-notification',
            'dashicons-palmtree',
            'dashicons-paperclip',
            'dashicons-performance',
            'dashicons-phone',
            'dashicons-playlist-audio',
            'dashicons-playlist-video',
            'dashicons-plus',
            'dashicons-plus-alt',
            'dashicons-plus-alt2',
            'dashicons-portfolio',
            'dashicons-post-status',
            'dashicons-pressthis',
            'dashicons-products',
            'dashicons-randomize',
            'dashicons-redo',
            'dashicons-redirect',
            'dashicons-rest-api',
            'dashicons-rss',
            'dashicons-schedule',
            'dashicons-screenoptions',
            'dashicons-search',
            'dashicons-share',
            'dashicons-share-alt',
            'dashicons-share-alt2',
            'dashicons-shield',
            'dashicons-shield-alt',
            'dashicons-slides',
            'dashicons-smartphone',
            'dashicons-smiley',
            'dashicons-sort',
            'dashicons-sos',
            'dashicons-star-empty',
            'dashicons-star-filled',
            'dashicons-star-half',
            'dashicons-sticky',
            'dashicons-store',
            'dashicons-table-col-after',
            'dashicons-table-col-before',
            'dashicons-table-col-delete',
            'dashicons-table-row-after',
            'dashicons-table-row-before',
            'dashicons-table-row-delete',
            'dashicons-tag',
            'dashicons-tagcloud',
            'dashicons-testimonial',
            'dashicons-text',
            'dashicons-text-page',
            'dashicons-thumbs-down',
            'dashicons-thumbs-up',
            'dashicons-tickets',
            'dashicons-tickets-alt',
            'dashicons-tide',
            'dashicons-translation',
            'dashicons-trash',
            'dashicons-twitter',
            'dashicons-twitter-alt',
            'dashicons-undo',
            'dashicons-universal-access',
            'dashicons-universal-access-alt',
            'dashicons-unlock',
            'dashicons-update',
            'dashicons-upload',
            'dashicons-warning',
            'dashicons-welcome-add-page',
            'dashicons-welcome-comments',
            'dashicons-welcome-learn-more',
            'dashicons-welcome-view-site',
            'dashicons-welcome-widgets-menus',
            'dashicons-welcome-write-blog',
            'dashicons-wordpress',
            'dashicons-wordpress-alt',
            'dashicons-yes',
            'dashicons-yes-alt',
            'dashicons-youtube'
        );
    }

    private function enqueue_scripts() {
        static $enqueued = false;

        if (!$enqueued) {
            wp_enqueue_style('dashicons');

            ?>
            <style>
                .pf-icon-picker-container {
                    position: relative;
                    margin-top: 8px;
                }

                .pf-icon-picker-input {
                    position: relative;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }

                .pf-icon-picker-search {
                    flex: 1;
                    padding-right: 50px !important;
                }

                .pf-icon-picker-preview {
                    position: absolute;
                    right: 14px;
                    top: 50%;
                    transform: translateY(-50%);
                    font-size: 20px;
                    width: 30px;
                    height: 30px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: #6366f1;
                }

                .pf-icon-picker-preview .dashicons {
                    font-size: 20px;
                    width: 20px;
                    height: 20px;
                }

                .pf-icon-picker-dropdown {
                    position: absolute;
                    top: 100%;
                    left: 0;
                    right: 0;
                    z-index: 9999;
                    background: white;
                    border: 2px solid #e8edf4;
                    border-radius: 12px;
                    margin-top: 4px;
                    padding: 12px;
                    max-height: 300px;
                    overflow: hidden;
                    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
                }
                body.dark-mode .pf-icon-picker-dropdown {
                    background: #1e293b;
                    border-color: #334155;
                }

                .pf-icon-picker-search-wrap {
                    margin-bottom: 10px;
                }

                .pf-icon-picker-filter {
                    width: 100%;
                    padding: 8px 12px;
                    border: 2px solid #e8edf4;
                    border-radius: 8px;
                    font-size: 14px;
                }
                body.dark-mode .pf-icon-picker-filter {
                    background: #0f172a;
                    border-color: #334155;
                    color: #e2e8f0;
                }

                .pf-icon-picker-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(40px, 1fr));
                    gap: 4px;
                    max-height: 220px;
                    overflow-y: auto;
                    padding: 4px;
                }

                .pf-icon-picker-grid::-webkit-scrollbar {
                    width: 6px;
                }
                .pf-icon-picker-grid::-webkit-scrollbar-thumb {
                    background: #cbd5e1;
                    border-radius: 10px;
                }

                .pf-icon-picker-icon {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 40px;
                    height: 40px;
                    border: 2px solid transparent;
                    border-radius: 8px;
                    cursor: pointer;
                    background: transparent;
                    color: #1a2332;
                    transition: all 0.2s ease;
                }
                body.dark-mode .pf-icon-picker-icon {
                    color: #e2e8f0;
                }

                .pf-icon-picker-icon:hover {
                    background: #f0f0ff;
                    border-color: #6366f1;
                    transform: scale(1.1);
                }
                body.dark-mode .pf-icon-picker-icon:hover {
                    background: #334155;
                }

                .pf-icon-picker-icon.selected {
                    background: #6366f1;
                    border-color: #6366f1;
                    color: white;
                }
                .pf-icon-picker-icon.selected .dashicons {
                    color: white;
                }

                .pf-icon-picker-icon.hidden {
                    display: none;
                }

                .pf-icon-picker-icon .dashicons {
                    font-size: 18px;
                    width: 18px;
                    height: 18px;
                }
            </style>

            <script>
                (function($) {
                    'use strict';

                    var $input = $('#<?php echo esc_js($this->field['id']); ?>');
                    var $container = $input.closest('.pf-icon-picker-container');
                    var $dropdown = $container.find('.pf-icon-picker-dropdown');
                    var $filter = $container.find('.pf-icon-picker-filter');

                    // Toggle dropdown
                    $input.on('click focus', function() {
                        $dropdown.slideDown(200);
                    });

                    // Close dropdown on outside click
                    $(document).on('click', function(e) {
                        if (!$container.is(e.target) && $container.has(e.target).length === 0) {
                            $dropdown.slideUp(200);
                        }
                    });

                    // Filter icons
                    $filter.on('input', function() {
                        var search = $(this).val().toLowerCase();
                        $container.find('.pf-icon-picker-icon').each(function() {
                            var icon = $(this).data('icon').toLowerCase();
                            $(this).toggleClass('hidden', icon.indexOf(search) === -1);
                        });
                    });

                    // Select icon
                    $(document).on('click', '.pf-icon-picker-icon', function() {
                        var icon = $(this).data('icon');
                        $input.val(icon).trigger('change');
                        $container.find('.pf-icon-picker-preview').removeClass().addClass('pf-icon-picker-preview ' + icon);
                        $container.find('.pf-icon-picker-icon').removeClass('selected');
                        $(this).addClass('selected');
                        $dropdown.slideUp(200);
                    });

                    // Update preview on input change
                    $input.on('change input', function() {
                        var val = $(this).val();
                        $container.find('.pf-icon-picker-preview').removeClass().addClass('pf-icon-picker-preview ' + val);
                    });

                    // Select initial icon
                    <?php if (!empty($this->value)): ?>
                    $input.trigger('change');
                    <?php endif; ?>

                })(jQuery);
            </script>
            <?php
            $enqueued = true;
        }
    }
}