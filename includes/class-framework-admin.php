<?php
/**
 * Persian Framework - Admin Class
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Admin {

    private static $instance = null;
    private $framework = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('admin_post_pf_save_options', array($this, 'save_options'));
        add_action('wp_ajax_pf_ajax_save', array($this, 'ajax_save_options'));
        add_action('wp_ajax_pf_import', array($this, 'ajax_import'));
        add_action('wp_ajax_pf_reset_options', array($this, 'ajax_reset_options'));
        add_action('wp_ajax_pf_reset_section', array($this, 'ajax_reset_section'));
        add_action('admin_notices', array($this, 'show_notices'));
    }

    public function render_page($framework) {
        $this->framework = $framework;
        $config = $framework->get_config();
        $sections = $framework->get_sections();
        $opt_name = $framework->get_opt_name();
        $options = get_option($opt_name, array());
        $instance_id = $framework->get_instance_id();
        ?>
        <div class="pf-framework-container">

            <!-- ============================================================ -->
            <!-- Unsaved Changes Notification Bar -->
            <!-- ============================================================ -->
            <div id="pf_notification_bar" style="display:none;">
                <div class="pf-save-warn notice-yellow">
                    <span class="dashicons dashicons-warning"></span>
                    <strong><?php _e('Settings have changed, you should save them!', 'persian-framework'); ?></strong>
                    <button type="button" class="pf-btn pf-btn-success pfAjaxSaveBtn pf-save-now-btn"
                            data-instance="<?php echo esc_attr($instance_id); ?>"
                            data-optname="<?php echo esc_attr($opt_name); ?>">
                        <i class="dashicons dashicons-yes"></i>
                        <span class="pfSaveText"><?php _e('Save Settings', 'persian-framework'); ?></span>
                    </button>
                    <button type="button" class="pf-btn pf-btn-secondary pf-dismiss-warn">
                        <span class="dashicons dashicons-no-alt"></span>
                    </button>
                </div>
                <div class="pf-field-errors notice-red" style="display:none;">
                    <strong><span class="dashicons dashicons-dismiss"></span> <?php _e('Error(s) found!', 'persian-framework'); ?></strong>
                </div>
                <div class="pf-field-warnings notice-yellow" style="display:none;">
                    <strong><span class="dashicons dashicons-warning"></span> <?php _e('Warning(s) found!', 'persian-framework'); ?></strong>
                </div>
            </div>

            <header class="pf-header">
                <div class="pf-header-brand">
                    <div class="pf-brand-icon">
                        <img src="<?php echo PERSIAN_FRAMEWORK_URL;?>/assets/images/logo.svg" width="50px">
                    </div>
                    <div>
                        <div class="pf-brand-title"><?php echo esc_html($config['display_name'] ?? _e('Persian Framework', 'persian-framework')); ?></div>
                        <div class="pf-brand-subtitle">v<?php echo esc_html($config['display_version'] ?? PERSIAN_FRAMEWORK_VERSION); ?> | <?php _e('Advanced Options Framework', 'persian-framework'); ?></div>
                    </div>
                </div>
                <div class="pf-header-actions">
                    <div class="pf-search">
                        <span class="dashicons dashicons-search"></span>
                        <input type="search" class="pf-settings-search" placeholder="<?php echo esc_attr__('Search settings…', 'persian-framework'); ?>">
                    </div>

                    <button type="button" class="pf-btn pf-btn-secondary" id="pfThemeToggle">
                        <i class="dashicons dashicons-admin-appearance"></i> <?php _e('Dark Mode', 'persian-framework'); ?>
                    </button>

                    <!-- Reset All Button -->
                    <button type="button" class="pf-btn pf-btn-danger pfResetBtn"
                            data-optname="<?php echo esc_attr($opt_name); ?>"
                            data-instance="<?php echo esc_attr($instance_id); ?>">
                        <i class="dashicons dashicons-image-rotate"></i> <?php _e('Reset All', 'persian-framework'); ?>
                    </button>

                    <button type="button" class="pf-btn pf-btn-success pfAjaxSaveBtn"
                            data-instance="<?php echo esc_attr($instance_id); ?>"
                            data-optname="<?php echo esc_attr($opt_name); ?>">
                        <i class="dashicons dashicons-yes"></i>
                        <span class="pfSaveText"><?php _e('Save Settings', 'persian-framework'); ?></span>
                        <span class="pf-spinner pfSaveSpinner" style="display:none;">
                            <span class="dashicons dashicons-update spin"></span>
                        </span>
                    </button>
                </div>
            </header>

            <div class="pf-layout">
                <nav class="pf-sidebar" id="pfSidebar">
                    <?php foreach ($sections as $index => $section): ?>
                        <button class="pf-sidebar-tab <?php echo $index === 0 ? 'active' : ''; ?>" data-tab="<?php echo esc_attr($section['id']); ?>">
                            <?php if (!empty($section['icon'])): ?>
                                <i class="dashicons <?php echo esc_attr($section['icon']); ?>"></i>
                            <?php endif; ?>
                            <span><?php echo esc_html($section['title']); ?></span>
                            <?php if (!empty($section['fields'])): ?>
                                <span class="pf-tab-badge"><?php echo count($section['fields']); ?></span>
                            <?php endif; ?>
                        </button>
                    <?php endforeach; ?>

                    <div class="pf-sidebar-divider"></div>

                    <button class="pf-sidebar-tab" data-tab="import-export">
                        <i class="dashicons dashicons-upload"></i>
                        <span><?php _e('Import / Export', 'persian-framework'); ?></span>
                    </button>

                    <button class="pf-sidebar-tab" data-tab="backup">
                        <i class="dashicons dashicons-backup"></i>
                        <span><?php _e('Backup', 'persian-framework'); ?></span>
                    </button>
                </nav>

                <div class="pf-content-panel">
                    <form id="pfSettingsForm" method="post" data-instance="<?php echo esc_attr($instance_id); ?>">
                        <?php wp_nonce_field('pf_ajax_nonce', 'pf_ajax_nonce'); ?>
                        <input type="hidden" name="opt_name" value="<?php echo esc_attr($opt_name); ?>">
                        <input type="hidden" name="instance_id" value="<?php echo esc_attr($instance_id); ?>">
                        <input type="hidden" name="tab" id="pfActiveTab" value="">

                        <?php foreach ($sections as $index => $section): ?>
                            <div class="pf-tab-content <?php echo $index === 0 ? 'active' : ''; ?>" id="pfTab-<?php echo esc_attr($section['id']); ?>">

                                <!-- ============================================================ -->
                                <!-- Section Header with Reset Section Button -->
                                <!-- ============================================================ -->
                                <div class="pf-section-header">
                                    <div>
                                        <h2><?php echo esc_html($section['title']); ?></h2>
                                        <?php if (isset($section['subtitle'])): ?>
                                            <p class="pf-tab-description"><?php echo esc_html($section['subtitle']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="pf-section-actions">
                                        <button type="button" class="pf-btn pf-btn-secondary pfResetSectionBtn"
                                                data-section="<?php echo esc_attr($section['id']); ?>"
                                                data-optname="<?php echo esc_attr($opt_name); ?>"
                                                data-instance="<?php echo esc_attr($instance_id); ?>"
                                                title="<?php esc_attr_e('Reset this section to default values', 'persian-framework'); ?>">
                                            <i class="dashicons dashicons-image-rotate"></i>
                                            <?php _e('Reset Section', 'persian-framework'); ?>
                                        </button>
                                    </div>
                                </div>

                                <div class="pf-fields-grid">
                                    <?php if (!empty($section['fields'])): ?>
                                        <?php foreach ($section['fields'] as $field): ?>
                                            <?php
                                            $field['name'] = $opt_name . '[' . $field['id'] . ']';
                                            $value = isset($options[$field['id']]) ? $options[$field['id']] : (isset($field['default']) ? $field['default'] : '');

                                            if (class_exists('PersianFramework_Fields')) {
                                                echo PersianFramework_Fields::render_field($field, $value);
                                            }
                                            ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="pf-tab-content" id="pfTab-import-export">
                            <h2><?php _e('Import / Export', 'persian-framework'); ?></h2>
                            <p class="pf-tab-description"><?php _e('Import or export your settings as JSON file.', 'persian-framework'); ?></p>
                            <div class="pf-import-export-grid">
                                <div class="pf-ie-box">
                                    <h3><?php _e('Export Settings', 'persian-framework'); ?></h3>
                                    <p><?php _e('Download your current settings as a JSON file.', 'persian-framework'); ?></p>
                                    <button type="button" class="pf-btn pf-btn-secondary pfExportBtn" data-optname="<?php echo esc_attr($opt_name); ?>">
                                        <i class="dashicons dashicons-download"></i> <?php _e('Export JSON', 'persian-framework'); ?>
                                    </button>
                                </div>
                                <div class="pf-ie-box">
                                    <h3><?php _e('Import Settings', 'persian-framework'); ?></h3>
                                    <p><?php _e('Upload a JSON file to import settings.', 'persian-framework'); ?></p>
                                    <input type="file" class="pfImportFile" accept=".json" style="display:block;margin-bottom:10px;">
                                    <button type="button" class="pf-btn pf-btn-primary pfImportBtn" data-optname="<?php echo esc_attr($opt_name); ?>" data-instance="<?php echo esc_attr($instance_id); ?>">
                                        <i class="dashicons dashicons-upload"></i> <?php _e('Import', 'persian-framework'); ?>
                                    </button>
                                </div>
                                <div class="pf-ie-box pf-ie-danger">
                                    <h3><?php _e('Reset All Settings', 'persian-framework'); ?></h3>
                                    <p><?php _e('⚠️ This will delete ALL current settings.', 'persian-framework'); ?></p>
                                    <button type="button" class="pf-btn pf-btn-danger pfResetBtn" data-optname="<?php echo esc_attr($opt_name); ?>" data-instance="<?php echo esc_attr($instance_id); ?>">
                                        <i class="dashicons dashicons-trash"></i> <?php _e('Reset All', 'persian-framework'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="pf-tab-content" id="pfTab-backup">
                            <h2><?php _e('Backup', 'persian-framework'); ?></h2>
                            <p class="pf-tab-description"><?php _e('Create and manage backups of your settings.', 'persian-framework'); ?></p>
                            <div class="pf-backup-actions">
                                <button type="button" class="pf-btn pf-btn-primary pfCreateBackupBtn" data-optname="<?php echo esc_attr($opt_name); ?>" data-instance="<?php echo esc_attr($instance_id); ?>">
                                    <i class="dashicons dashicons-backup"></i> <?php _e('Create Backup', 'persian-framework'); ?>
                                </button>
                            </div>
                            <div id="pfBackupList">
                                <p style="color:#94a3b8;text-align:center;padding:40px 0;">
                                    <?php _e('No backups found.', 'persian-framework'); ?>
                                </p>
                            </div>
                        </div>

                        <div class="pf-actions">
                            <button type="button" class="pf-btn pf-btn-success pfAjaxSaveBtn"
                                    data-optname="<?php echo esc_attr($opt_name); ?>"
                                    data-instance="<?php echo esc_attr($instance_id); ?>">
                                <i class="dashicons dashicons-yes"></i>
                                <span class="pfSaveText"><?php _e('Save Settings', 'persian-framework'); ?></span>
                                <span class="pf-spinner pfSaveSpinner" style="display:none;">
                                    <span class="dashicons dashicons-update spin"></span>
                                </span>
                            </button>
                        </div>

                    </form>

                    <div id="pfNotification" class="pf-notification" style="display:none;">
                        <span class="pf-notification-icon dashicons dashicons-yes-alt"></span>
                        <span class="pf-notification-text"></span>
                    </div>
                </div>
            </div>
        </div>

        <?php $this->enqueue_styles_and_scripts(); ?>
        <?php
    }

    /**
     * AJAX handler for resetting a single section
     * FIXED: Properly gets framework instance via get_instance() method
     */
    public function ajax_reset_section() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'pf_ajax_nonce')) {
            wp_send_json_error(array('message' => __('Invalid nonce!', 'persian-framework')));
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied!', 'persian-framework')));
        }

        // Get parameters
        $opt_name = isset($_POST['opt_name']) ? sanitize_text_field($_POST['opt_name']) : 'persian_framework_options';
        $section_id = isset($_POST['section_id']) ? sanitize_text_field($_POST['section_id']) : '';
        $instance_id = isset($_POST['instance_id']) ? sanitize_text_field($_POST['instance_id']) : 'default';

        if (empty($section_id)) {
            wp_send_json_error(array('message' => __('Section ID is required!', 'persian-framework')));
        }

        // ✅ CRITICAL FIX: Get the framework instance using the proper method
        $framework = null;

        // Method 1: Try to get instance from PersianFramework class
        if (class_exists('PersianFramework')) {
            try {
                $framework = PersianFramework::get_instance($instance_id);

                // If config is empty, load it
                if (empty($framework->get_config())) {
                    $framework->load_config();
                }
            } catch (Exception $e) {
                // Fallback to method 2
            }
        }

        // Method 2: Fallback to global variable
        if ($framework === null) {
            global $persian_framework;
            if (isset($persian_framework)) {
                $framework = $persian_framework;
            }
        }

        // Method 3: If we have the framework from render_page, use it
        if ($framework === null && $this->framework !== null) {
            $framework = $this->framework;
        }

        $defaults = array();

        if ($framework !== null) {
            $sections = $framework->get_sections();

            // Find the target section
            foreach ($sections as $section) {
                if ($section['id'] === $section_id && isset($section['fields'])) {
                    foreach ($section['fields'] as $field) {
                        if (isset($field['id'])) {
                            $field_id = $field['id'];

                            // Check if this is a repeater field
                            if (isset($field['type']) && $field['type'] === 'repeater') {
                                // For repeater fields, get default from 'default' key
                                if (isset($field['default']) && is_array($field['default'])) {
                                    $defaults[$field_id] = $field['default'];
                                } else {
                                    $defaults[$field_id] = array();
                                }
                            } else {
                                // For regular fields
                                if (isset($field['default'])) {
                                    $defaults[$field_id] = $field['default'];
                                } else {
                                    // If no default, remove the field from options
                                    $defaults[$field_id] = null;
                                }
                            }
                        }
                    }
                    break;
                }
            }
        }

        // If still no defaults found, try defaults file
        if (empty($defaults)) {
            $defaults_file = PERSIAN_FRAMEWORK_CONFIG . 'defaults.php';
            if (file_exists($defaults_file)) {
                $all_defaults = include $defaults_file;
                if (isset($all_defaults[$section_id])) {
                    $defaults = $all_defaults[$section_id];
                }
            }
        }

        // Get current options
        $current_options = get_option($opt_name, array());

        // Apply defaults to current options
        foreach ($defaults as $field_id => $default_value) {
            if ($default_value === null) {
                // Remove the field if no default
                if (isset($current_options[$field_id])) {
                    unset($current_options[$field_id]);
                }
            } else {
                $current_options[$field_id] = $default_value;
            }
        }

        update_option($opt_name, $current_options);

        wp_send_json_success(array(
                'message' => sprintf(__('Section "%s" has been reset to defaults!', 'persian-framework'), $section_id),
                'section_id' => $section_id
        ));
    }

    private function enqueue_styles_and_scripts() {
        ?>
        <style>
            /* ============================================================
               Section Header with Reset Button
               ============================================================ */
            .pf-section-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 24px;
                padding-bottom: 16px;
                border-bottom: 2px dashed #e8edf4;
                flex-wrap: wrap;
                gap: 12px;
            }

            body.dark-mode .pf-section-header {
                border-bottom-color: #334155;
            }

            .pf-section-header h2 {
                margin: 0;
                font-size: 20px;
                font-weight: 700;
                color: #1a2332;
            }

            body.dark-mode .pf-section-header h2 {
                color: #e2e8f0;
            }

            .pf-section-header .pf-tab-description {
                margin: 4px 0 0 0;
                color: #64748b;
                font-size: 14px;
            }

            body.dark-mode .pf-section-header .pf-tab-description {
                color: #94a3b8;
            }

            .pf-section-actions {
                display: flex;
                gap: 8px;
                flex-shrink: 0;
                align-items: center;
            }

            .pf-section-actions .pf-btn {
                font-size: 13px;
                padding: 6px 14px;
            }

            .pf-section-actions .pf-btn .dashicons {
                font-size: 16px;
                width: 16px;
                height: 16px;
                margin-right: 4px;
            }

            body.dark-mode .pf-section-actions .pf-btn-secondary {
                background: #1e293b;
                border-color: #475569;
                color: #e2e8f0;
            }

            body.dark-mode .pf-section-actions .pf-btn-secondary:hover {
                background: #334155;
                border-color: #6366f1;
            }

            /* Spinner animation */
            @keyframes pfSpin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }

            .pf-section-actions .dashicons.spin,
            .pf-btn .dashicons.spin {
                animation: pfSpin 1s linear infinite;
            }

            /* Unsaved Changes Notification Bar */
            #pf_notification_bar {
                margin-bottom: 20px;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
                animation: pfSlideDown 0.4s ease forwards;
            }

            @keyframes pfSlideDown {
                from { opacity: 0; transform: translateY(-20px); }
                to { opacity: 1; transform: translateY(0); }
            }

            #pf_notification_bar .notice-yellow {
                background: #fffbeb;
                border-left: 4px solid #f59e0b;
                padding: 14px 20px;
                display: flex;
                align-items: center;
                gap: 14px;
                flex-wrap: wrap;
                font-size: 14px;
                color: #92400e;
            }

            #pf_notification_bar .notice-red {
                background: #fef2f2;
                border-left: 4px solid #ef4444;
                padding: 14px 20px;
                display: flex;
                align-items: center;
                gap: 14px;
                flex-wrap: wrap;
                font-size: 14px;
                color: #991b1b;
            }

            #pf_notification_bar .notice-yellow .dashicons {
                color: #f59e0b;
                font-size: 20px;
                width: 20px;
                height: 20px;
            }

            #pf_notification_bar .notice-red .dashicons {
                color: #ef4444;
                font-size: 20px;
                width: 20px;
                height: 20px;
            }

            #pf_notification_bar .pf-save-now-btn {
                padding: 4px 16px;
                font-size: 13px;
                border-radius: 8px;
                margin-left: auto;
            }

            #pf_notification_bar .pf-save-now-btn .dashicons {
                font-size: 14px;
                width: 14px;
                height: 14px;
            }

            #pf_notification_bar .pf-dismiss-warn {
                background: none;
                border: none;
                cursor: pointer;
                padding: 4px;
                color: #94a3b8;
                transition: color 0.2s ease;
            }

            #pf_notification_bar .pf-dismiss-warn:hover {
                color: #1a2332;
            }

            #pf_notification_bar .pf-dismiss-warn .dashicons {
                font-size: 18px;
                width: 18px;
                height: 18px;
            }

            body.dark-mode #pf_notification_bar .notice-yellow {
                background: #1e1a0a;
                border-color: #f59e0b;
                color: #fde68a;
            }

            body.dark-mode #pf_notification_bar .notice-red {
                background: #1a0a0a;
                border-color: #ef4444;
                color: #fca5a5;
            }

            body.dark-mode #pf_notification_bar .pf-dismiss-warn:hover {
                color: #e2e8f0;
            }

            .pf-search {
                position: relative;
                display: inline-flex;
                align-items: center;
                margin-right: 12px;
                background: rgba(0,0,0,0.04);
                border-radius: 30px;
                padding: 0 12px;
                transition: background .25s;
                border: 1px solid transparent;
            }
            .pf-search:focus-within {
                background: #fff;
                border-color: #6366f1;
                box-shadow: 0 0 0 3px rgba(99,102,241,.15);
            }
            body.dark-mode .pf-search {
                background: rgba(255,255,255,0.06);
            }
            body.dark-mode .pf-search:focus-within {
                background: #1e293b;
                border-color: #818cf8;
            }
            .pf-search .dashicons {
                color: #94a3b8;
                font-size: 18px;
                width: 18px;
                height: 18px;
            }
            .pf-settings-search {
                border: none;
                background: transparent;
                padding: 8px 6px;
                font-size: 14px;
                color: #1a2332;
                width: 180px;
                outline: none;
                font-family: inherit;
            }
            body.dark-mode .pf-settings-search {
                color: #e2e8f0;
            }
            .pf-settings-search::placeholder {
                color: #94a3b8;
            }

            @media (max-width: 768px) {
                #pf_notification_bar .notice-yellow,
                #pf_notification_bar .notice-red {
                    flex-direction: column;
                    align-items: stretch;
                    text-align: center;
                    padding: 12px 16px;
                }
                #pf_notification_bar .pf-save-now-btn {
                    margin-left: 0;
                }
                .pf-section-header {
                    flex-direction: column;
                    align-items: stretch;
                }
                .pf-section-actions {
                    justify-content: flex-start;
                }
            }

            .pf-section-wrapper {
                margin: 20px 0 10px;
                padding: 0;
                border-bottom: 1px dashed #e8edf4;
                position: relative;
            }

            .pf-section-wrapper.pf-section-indent {
                padding-left: 20px;
                border-left: 3px solid #6366f1;
            }

            .pf-section-header-wrap {
                margin-bottom: 15px;
            }

            .pf-section-title {
                font-size: 18px;
                font-weight: 700;
                color: #1a2332;
                margin: 0 0 5px 0;
                padding: 0;
                line-height: 1.4;
            }

            .pf-section-subtitle {
                font-size: 14px;
                color: #64748b;
                margin: 0 0 8px 0;
                font-weight: 400;
            }

            .pf-section-desc {
                font-size: 13px;
                color: #94a3b8;
                margin: 0;
                font-style: italic;
            }

            /* Dark Mode */
            body.dark-mode .pf-section-wrapper {
                border-bottom-color: #334155;
            }

            body.dark-mode .pf-section-wrapper.pf-section-indent {
                border-left-color: #818cf8;
            }

            body.dark-mode .pf-section-title {
                color: #e2e8f0;
            }

            body.dark-mode .pf-section-subtitle {
                color: #94a3b8;
            }

            body.dark-mode .pf-section-desc {
                color: #64748b;
            }

        </style>

        <script>

            (function($) {
                'use strict';

                // ============================================================
                // 1. Track unsaved changes
                // ============================================================
                var pf_has_changes = false;
                var pf_initial_state = {};

                function pfCaptureInitialState() {
                    $('#pfSettingsForm').find('input, select, textarea').each(function() {
                        var $el = $(this);
                        var name = $el.attr('name');
                        if (name && name.indexOf('pf_ajax_nonce') === -1 && name.indexOf('opt_name') === -1 && name.indexOf('instance_id') === -1 && name.indexOf('tab') === -1) {
                            if ($el.is(':checkbox') || $el.is(':radio')) {
                                pf_initial_state[name] = $el.is(':checked') ? $el.val() : '';
                            } else {
                                pf_initial_state[name] = $el.val();
                            }
                        }
                    });
                }

                function pfCheckChanges() {
                    var hasChanged = false;

                    $('#pfSettingsForm').find('input, select, textarea').each(function() {
                        var $el = $(this);
                        var name = $el.attr('name');
                        if (!name || name.indexOf('pf_ajax_nonce') !== -1 || name.indexOf('opt_name') !== -1 || name.indexOf('instance_id') !== -1 || name.indexOf('tab') !== -1) {
                            return;
                        }

                        var currentValue;
                        if ($el.is(':checkbox') || $el.is(':radio')) {
                            currentValue = $el.is(':checked') ? $el.val() : '';
                        } else {
                            currentValue = $el.val();
                        }

                        if (pf_initial_state[name] !== undefined && pf_initial_state[name] !== currentValue) {
                            hasChanged = true;
                            return false;
                        }
                    });

                    pf_has_changes = hasChanged;
                    pfUpdateNotificationBar();
                }

                function pfUpdateNotificationBar() {
                    var $bar = $('#pf_notification_bar');
                    var $warn = $bar.find('.pf-save-warn');

                    if (pf_has_changes) {
                        $bar.show();
                        $warn.show();
                    } else {
                        $bar.hide();
                        $warn.hide();
                    }
                }

                window.pfChanges = function() {
                    return pfCheckChanges();
                };

                // ============================================================
                // 2. Event listeners for changes
                // ============================================================
                $(document).on('change input', '#pfSettingsForm input, #pfSettingsForm select, #pfSettingsForm textarea', function() {
                    var name = $(this).attr('name');
                    if (name && (name.indexOf('pf_ajax_nonce') !== -1 || name.indexOf('opt_name') !== -1 || name.indexOf('instance_id') !== -1 || name.indexOf('tab') !== -1)) {
                        return;
                    }
                    pfCheckChanges();
                });

                $(document).on('pf-media-selected pf-media-removed pf-gallery-updated', function() {
                    pfCheckChanges();
                });

                $(document).on('pf-repeater-add pf-repeater-remove pf-repeater-clear pf-repeater-sort', function() {
                    pfCheckChanges();
                });

                // ============================================================
                // 3. Dismiss notification
                // ============================================================
                $(document).on('click', '.pf-dismiss-warn', function() {
                    $('#pf_notification_bar').hide();
                });

                // ============================================================
                // 4. Save Now button in notification bar
                // ============================================================
                $(document).on('click', '.pf-save-now-btn', function() {
                    $('.pfAjaxSaveBtn').click();
                });

                // ============================================================
                // 5. Override AJAX save success to reset change tracking
                // ============================================================
                $(document).ajaxSuccess(function(event, xhr, settings) {
                    if (settings.data && settings.data.indexOf('pf_ajax_save') !== -1) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                pf_has_changes = false;
                                pfCaptureInitialState();
                                pfUpdateNotificationBar();
                            }
                        } catch(e) {}
                    }
                });

                // ============================================================
                // 6. Initial setup
                // ============================================================
                $(document).ready(function() {
                    setTimeout(function() {
                        pfCaptureInitialState();
                        pf_has_changes = false;
                        pfUpdateNotificationBar();
                    }, 500);
                });

                // ============================================================
                // 7. Search settings
                // ============================================================
                $(document).on('input', '.pf-settings-search', function () {
                    var term = $(this).val().toLowerCase().trim();
                    $('.pf-field-wrapper').each(function () {
                        var $wrapper = $(this);
                        var text = $wrapper.text().toLowerCase();
                        var match = !term || text.indexOf(term) !== -1;
                        $wrapper.toggle(match);
                        if (match) {
                            $wrapper.closest('.pf-field-wrapper').show();
                        }
                    });
                });

                // ============================================================
                // 8. Tab switching
                // ============================================================
                $('.pf-sidebar-tab').on('click', function() {
                    var tabId = $(this).data('tab');
                    $('.pf-sidebar-tab').removeClass('active');
                    $(this).addClass('active');
                    $('.pf-tab-content').removeClass('active');
                    $('#pfTab-' + tabId).addClass('active');
                    $('#pfActiveTab').val(tabId);
                    var url = new URL(window.location.href);
                    url.searchParams.set('tab', tabId);
                    window.history.pushState({}, '', url);
                });

                var urlParams = new URLSearchParams(window.location.search);
                var tabParam = urlParams.get('tab');
                if (tabParam) {
                    var $targetTab = $('.pf-sidebar-tab[data-tab="' + tabParam + '"]');
                    if ($targetTab.length) {
                        $('.pf-sidebar-tab').removeClass('active');
                        $targetTab.addClass('active');
                        $('.pf-tab-content').removeClass('active');
                        $('#pfTab-' + tabParam).addClass('active');
                        $('#pfActiveTab').val(tabParam);
                    }
                } else {
                    var $firstTab = $('.pf-sidebar-tab:first');
                    if ($firstTab.length) {
                        $('#pfActiveTab').val($firstTab.data('tab'));
                    }
                }

                // ============================================================
                // 9. Dark Mode
                // ============================================================
                $('#pfThemeToggle').on('click', function() {
                    $('body').toggleClass('dark-mode');
                    var icon = $(this).find('.dashicons');
                    if ($('body').hasClass('dark-mode')) {
                        icon.removeClass('dashicons-admin-appearance').addClass('dashicons-lightbulb');
                        $(this).html('<i class="dashicons dashicons-lightbulb"></i> Light Mode');
                    } else {
                        icon.removeClass('dashicons-lightbulb').addClass('dashicons-admin-appearance');
                        $(this).html('<i class="dashicons dashicons-admin-appearance"></i> Dark Mode');
                    }
                });

                // ============================================================
                // 10. Browser back/forward
                // ============================================================
                window.addEventListener('popstate', function() {
                    var urlParams = new URLSearchParams(window.location.search);
                    var tabParam = urlParams.get('tab');
                    if (tabParam) {
                        var $targetTab = $('.pf-sidebar-tab[data-tab="' + tabParam + '"]');
                        if ($targetTab.length) {
                            $('.pf-sidebar-tab').removeClass('active');
                            $targetTab.addClass('active');
                            $('.pf-tab-content').removeClass('active');
                            $('#pfTab-' + tabParam).addClass('active');
                            $('#pfActiveTab').val(tabParam);
                        }
                    }
                });

                // ============================================================
                // 11. Export
                // ============================================================
                $('.pfExportBtn').on('click', function() {
                    var optName = $(this).data('optname');
                    window.location.href = '<?php echo admin_url('admin-post.php?action=pf_export'); ?>&opt_name=' + optName;
                });

                // ============================================================
                // 12. Import
                // ============================================================
                $('.pfImportBtn').on('click', function() {
                    var $btn = $(this);
                    var optName = $btn.data('optname');
                    var instanceId = $btn.data('instance');
                    var $file = $btn.closest('.pf-ie-box').find('.pfImportFile');
                    var file = $file[0].files[0];

                    if (!file) {
                        alert('Please select a file first!');
                        return;
                    }

                    var reader = new FileReader();
                    reader.onload = function(e) {
                        try {
                            var data = JSON.parse(e.target.result);
                            $.ajax({
                                url: ajaxurl,
                                type: 'POST',
                                data: {
                                    action: 'pf_import',
                                    data: data,
                                    opt_name: optName,
                                    instance_id: instanceId,
                                    nonce: '<?php echo wp_create_nonce('pf_ajax_nonce'); ?>'
                                },
                                success: function(response) {
                                    if (response.success) {
                                        alert('Import successful! Page will reload.');
                                        location.reload();
                                    } else {
                                        alert('Import failed: ' + response.data.message);
                                    }
                                }
                            });
                        } catch(e) {
                            alert('Invalid JSON file!');
                        }
                    };
                    reader.readAsText(file);
                });

                // ============================================================
                // 13. Reset All
                // ============================================================
                $('.pfResetBtn').on('click', function() {
                    var $btn = $(this);
                    var optName = $btn.data('optname');
                    var instanceId = $btn.data('instance');

                    if (confirm('<?php _e('Are you sure you want to reset ALL settings? This cannot be undone!', 'persian-framework'); ?>')) {
                        $btn.prop('disabled', true);
                        $btn.html('<span class="dashicons dashicons-update spin"></span> <?php _e('Resetting...', 'persian-framework'); ?>');

                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'pf_reset_options',
                                opt_name: optName,
                                instance_id: instanceId,
                                nonce: '<?php echo wp_create_nonce('pf_ajax_nonce'); ?>'
                            },
                            success: function(response) {
                                if (response.success) {
                                    alert('<?php _e('Settings reset to default! Page will reload.', 'persian-framework'); ?>');
                                    location.reload();
                                } else {
                                    alert('Reset failed: ' + response.data.message);
                                    $btn.prop('disabled', false);
                                    $btn.html('<i class="dashicons dashicons-image-rotate"></i> <?php _e('Reset All', 'persian-framework'); ?>');
                                }
                            }
                        });
                    }
                });

                // ============================================================
                // 14. Reset Section
                // ============================================================
                $(document).on('click', '.pfResetSectionBtn', function() {
                    var $btn = $(this);
                    var sectionId = $btn.data('section');
                    var optName = $btn.data('optname');
                    var instanceId = $btn.data('instance');
                    var sectionTitle = $btn.closest('.pf-section-header').find('h2').text() || sectionId;

                    if (confirm('<?php _e('Are you sure you want to reset the "', 'persian-framework'); ?>' + sectionTitle + '" <?php _e('section to default values? This cannot be undone!', 'persian-framework'); ?>')) {

                        $btn.prop('disabled', true);
                        $btn.html('<span class="dashicons dashicons-update spin"></span> <?php _e('Resetting...', 'persian-framework'); ?>');

                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'pf_reset_section',
                                section_id: sectionId,
                                opt_name: optName,
                                instance_id: instanceId,
                                nonce: '<?php echo wp_create_nonce('pf_ajax_nonce'); ?>'
                            },
                            success: function(response) {
                                if (response.success) {
                                    showNotification(response.data.message || '<?php _e('Section reset successfully!', 'persian-framework'); ?>');
                                    setTimeout(function() {
                                        location.reload();
                                    }, 1000);
                                } else {
                                    showNotification(response.data.message || '<?php _e('Error resetting section!', 'persian-framework'); ?>', true);
                                    $btn.prop('disabled', false);
                                    $btn.html('<i class="dashicons dashicons-image-rotate"></i> <?php _e('Reset Section', 'persian-framework'); ?>');
                                }
                            },
                            error: function() {
                                showNotification('<?php _e('Connection error! Please try again.', 'persian-framework'); ?>', true);
                                $btn.prop('disabled', false);
                                $btn.html('<i class="dashicons dashicons-image-rotate"></i> <?php _e('Reset Section', 'persian-framework'); ?>');
                            }
                        });
                    }
                });

                // ============================================================
                // 15. AJAX Save Settings
                // ============================================================
                $('.pfAjaxSaveBtn').on('click', function() {
                    var $btn = $(this);
                    var $form = $('#pfSettingsForm');
                    var optName = $btn.data('optname');
                    var instanceId = $btn.data('instance');
                    var nonce = $form.find('input[name="pf_ajax_nonce"]').val();

                    if (!optName) {
                        optName = $form.find('input[name="opt_name"]').val();
                    }
                    if (!instanceId) {
                        instanceId = $form.data('instance');
                    }

                    $btn.prop('disabled', true);
                    $btn.find('.pfSaveText').text('<?php _e('Saving...', 'persian-framework'); ?>');
                    $btn.find('.pfSaveSpinner').show();



                    var formArray = $form.serializeArray();
                    //console.log(formArray);

                    var optionsData = {};
                    var mediaData = {};
                    var galleryData = {};
                    var repeaterData = {};
                    var sortableData = {};
                    var sorterData = {};

                    // Build lists of field IDs
                    var sorterFieldIds = [];
                    $('.pf-sorter-container').each(function() {
                        var fieldId = $(this).data('field');
                        if (fieldId) {
                            sorterFieldIds.push(fieldId);
                        }
                    });

                    var mediaFieldIds = [];
                    $('.pf-media-container').each(function() {
                        var $idInput = $(this).find('.pf-media-id');
                        if ($idInput.length) {
                            var name = $idInput.attr('name');
                            if (name) {
                                var parts = name.match(/[^\[\]]+/g);
                                if (parts && parts.length >= 2) {
                                    if (parts.length === 3) {
                                        mediaFieldIds.push(parts[1]);
                                    } else if (parts.length >= 4) {
                                        mediaFieldIds.push(parts[3]);
                                    }
                                }
                            }
                        }
                    });

                    var galleryFieldIds = [];
                    $('.pf-gallery-container').each(function() {
                        var $inputs = $(this).find('.pf-gallery-list input[type="hidden"]');
                        if ($inputs.length) {
                            var name = $inputs.first().attr('name');
                            if (name) {
                                var match = name.match(/\[([^\]]+)\]/);
                                if (match) {
                                    galleryFieldIds.push(match[1]);
                                }
                            }
                        }
                    });

                    var repeaterFieldIds = [];
                    $('.pf-repeater-container').each(function() {
                        var $container = $(this);
                        var $item = $container.find('.pf-repeater-item').first();
                        if ($item.length) {
                            var $input = $item.find('input, textarea, select').first();
                            if ($input.length) {
                                var name = $input.attr('name');
                                if (name) {
                                    var match = name.match(/^[^\[]+\[([^\]]+)\]/);
                                    if (match) {
                                        repeaterFieldIds.push(match[1]);
                                    }
                                }
                            }
                        }
                    });

                    var multiTextFieldIds = [];
                    $('.pf-multi-text-items').each(function() {
                        var $input = $(this).find('.pf-multi-text-input').first();
                        if ($input.length) {
                            var name = $input.attr('name');
                            if (name) {
                                var match = name.match(/\[([^\]]+)\]/);
                                if (match) {
                                    multiTextFieldIds.push(match[1]);
                                }
                            }
                        }
                    });

                    function parseFieldName(name, optName) {
                        if (!name.startsWith(optName + '[')) {
                            return null;
                        }

                        var parts = name.match(/[^\[\]]+/g);
                        if (!parts || parts.length < 1) {
                            return null;
                        }

                        if (parts[0] !== optName) {
                            return null;
                        }

                        parts = parts.slice(1);
                        return parts;
                    }

                    formArray.forEach(function(item) {
                        var name = item.name;
                        var value = item.value;

                        if (name === 'pf_ajax_nonce' || name === 'opt_name' || name === 'instance_id' || name === 'tab') {
                            return;
                        }

                        var parts = parseFieldName(name, optName);
                        if (!parts || parts.length === 0) {
                            return;
                        }

                        var lastPart = parts[parts.length - 1];

                        // Check if this is a media field
                        if (lastPart === 'id' || lastPart === 'url') {
                            var fieldType = lastPart;
                            var mediaParts = parts.slice(0, -1);

                            if (mediaParts.length === 1) {
                                var mediaId = mediaParts[0];
                                if (!mediaData[mediaId]) {
                                    mediaData[mediaId] = {};
                                }
                                mediaData[mediaId][fieldType] = value;
                                return;
                            } else if (mediaParts.length === 3) {
                                var repId = mediaParts[0];
                                var idx = parseInt(mediaParts[1]);
                                var subField = mediaParts[2];

                                if (!mediaData[repId]) {
                                    mediaData[repId] = [];
                                }
                                if (!mediaData[repId][idx]) {
                                    mediaData[repId][idx] = {};
                                }
                                if (!mediaData[repId][idx][subField]) {
                                    mediaData[repId][idx][subField] = {};
                                }
                                mediaData[repId][idx][subField][fieldType] = value;
                                return;
                            } else {
                                var fieldId = parts[0];
                                var path = parts.slice(0, -1);
                                var key = path.join('_');

                                if (!mediaData[key]) {
                                    mediaData[key] = {};
                                }
                                mediaData[key][fieldType] = value;
                                return;
                            }
                        }

                        // Multi-text field
                        if (parts.length === 2 && !isNaN(parts[1])) {
                            var fieldId = parts[0];
                            var index = parseInt(parts[1]);
                            if (!optionsData[fieldId]) {
                                optionsData[fieldId] = [];
                            }
                            optionsData[fieldId][index] = value;
                            return;
                        }

                        // Gallery field
                        if (parts.length === 2 && parts[1] === '') {
                            var galleryId = parts[0];
                            if (!galleryData[galleryId]) {
                                galleryData[galleryId] = [];
                            }
                            galleryData[galleryId].push(parseInt(value));
                            return;
                        }

                        // Repeater field
                        if (parts.length === 3 && !isNaN(parts[1])) {
                            var repId = parts[0];
                            var idx = parseInt(parts[1]);
                            var subField = parts[2];

                            if (!repeaterData[repId]) {
                                repeaterData[repId] = [];
                            }
                            if (!repeaterData[repId][idx]) {
                                repeaterData[repId][idx] = {};
                            }
                            repeaterData[repId][idx][subField] = value;
                            return;
                        }

                        // Sorter field
                        var isSorterField = false;
                        var matchedSorterId = null;
                        var matchedColumn = null;
                        var matchedKey = null;

                        for (var i = 0; i < sorterFieldIds.length; i++) {
                            var sorterId = sorterFieldIds[i];
                            if (parts.length >= 3 && parts[0] === sorterId) {
                                isSorterField = true;
                                matchedSorterId = sorterId;
                                matchedColumn = parts[1];
                                matchedKey = parts[2];
                                break;
                            }
                        }

                        if (isSorterField && matchedSorterId) {
                            if (!sorterData[matchedSorterId]) {
                                sorterData[matchedSorterId] = {};
                            }
                            if (!sorterData[matchedSorterId][matchedColumn]) {
                                sorterData[matchedSorterId][matchedColumn] = {};
                            }
                            sorterData[matchedSorterId][matchedColumn][matchedKey] = value;
                            return;
                        }

                        // Regular field
                        if (parts.length === 1) {
                            var fieldId = parts[0];
                            if (sorterFieldIds.indexOf(fieldId) === -1 &&
                                mediaFieldIds.indexOf(fieldId) === -1 &&
                                galleryFieldIds.indexOf(fieldId) === -1 &&
                                repeaterFieldIds.indexOf(fieldId) === -1 &&
                                multiTextFieldIds.indexOf(fieldId) === -1) {
                                optionsData[fieldId] = value;
                            }
                            return;
                        }

                        // Fallback: Store as nested array
                        if (parts.length > 1) {
                            var fieldId = parts[0];
                            if (sorterFieldIds.indexOf(fieldId) === -1 &&
                                mediaFieldIds.indexOf(fieldId) === -1 &&
                                galleryFieldIds.indexOf(fieldId) === -1 &&
                                repeaterFieldIds.indexOf(fieldId) === -1 &&
                                multiTextFieldIds.indexOf(fieldId) === -1) {
                                var current = optionsData;
                                for (var i = 0; i < parts.length; i++) {
                                    var key = parts[i];
                                    if (i === parts.length - 1) {
                                        current[key] = value;
                                    } else {
                                        if (!current[key] || typeof current[key] !== 'object') {
                                            current[key] = {};
                                        }
                                        current = current[key];
                                    }
                                }
                            }
                        }
                    });

                    // Handle unchecked checkboxes
                    $form.find('input[type="checkbox"]:not(:checked)').each(function() {
                        var name = $(this).attr('name');
                        if (!name) return;

                        var parts = parseFieldName(name, optName);
                        if (!parts || parts.length !== 1) return;

                        var fieldId = parts[0];
                        if (sorterFieldIds.indexOf(fieldId) === -1 &&
                            mediaFieldIds.indexOf(fieldId) === -1 &&
                            galleryFieldIds.indexOf(fieldId) === -1 &&
                            repeaterFieldIds.indexOf(fieldId) === -1 &&
                            multiTextFieldIds.indexOf(fieldId) === -1 &&
                            !optionsData.hasOwnProperty(fieldId)) {
                            optionsData[fieldId] = '0';
                        }
                    });

                    // Filter out empty values from multi-text fields
                    for (var key in optionsData) {
                        if (optionsData.hasOwnProperty(key) && Array.isArray(optionsData[key])) {
                            optionsData[key] = optionsData[key].filter(function(item) {
                                return item !== '' && item !== null && item !== undefined;
                            });
                            if (optionsData[key].length === 0) {
                                delete optionsData[key];
                            }
                        }
                    }

                    // Merge REPEATER data
                    for (var repeaterId in repeaterData) {
                        if (!repeaterData.hasOwnProperty(repeaterId)) {
                            continue;
                        }

                        if (!optionsData[repeaterId]) {
                            optionsData[repeaterId] = [];
                        }

                        for (var idx in repeaterData[repeaterId]) {
                            if (!repeaterData[repeaterId].hasOwnProperty(idx)) {
                                continue;
                            }

                            if (!optionsData[repeaterId][idx]) {
                                optionsData[repeaterId][idx] = {};
                            }

                            for (var subField in repeaterData[repeaterId][idx]) {
                                if (repeaterData[repeaterId][idx].hasOwnProperty(subField)) {
                                    optionsData[repeaterId][idx][subField] = repeaterData[repeaterId][idx][subField];
                                }
                            }
                        }
                    }

                    // Merge MEDIA data
                    for (var mediaId in mediaData) {
                        if (!mediaData.hasOwnProperty(mediaId)) {
                            continue;
                        }

                        if (Array.isArray(mediaData[mediaId])) {
                            if (!optionsData[mediaId]) {
                                optionsData[mediaId] = [];
                            }

                            for (var idx in mediaData[mediaId]) {
                                if (!mediaData[mediaId].hasOwnProperty(idx)) {
                                    continue;
                                }

                                if (!optionsData[mediaId][idx]) {
                                    optionsData[mediaId][idx] = {};
                                }

                                for (var subfield in mediaData[mediaId][idx]) {
                                    if (mediaData[mediaId][idx].hasOwnProperty(subfield)) {
                                        optionsData[mediaId][idx][subfield] = mediaData[mediaId][idx][subfield];
                                    }
                                }
                            }
                        } else {
                            optionsData[mediaId] = mediaData[mediaId];
                        }
                    }

                    // Merge GALLERY data
                    for (var galleryId in galleryData) {
                        if (galleryData.hasOwnProperty(galleryId)) {
                            optionsData[galleryId] = galleryData[galleryId];
                        }
                    }

                    // Merge SORTER data
                    for (var sorterId in sorterData) {
                        if (sorterData.hasOwnProperty(sorterId)) {
                            optionsData[sorterId] = sorterData[sorterId];
                        }
                    }

                    // Merge SORTABLE data
                    $('.pf-sortable-container').each(function() {
                        var $container = $(this);
                        var fieldId = $container.data('field');
                        var items = {};

                        $container.find('.pf-sortable-item').each(function() {
                            var $item = $(this);
                            var key = $item.data('key');
                            var label = $item.find('.pf-sortable-label').text().trim();
                            if (key) {
                                items[key] = label;
                            }
                        });

                        if (Object.keys(items).length > 0) {
                            var filteredSortable = {};
                            for (var key in items) {
                                if (items[key] !== '' && items[key] !== null && items[key] !== undefined) {
                                    filteredSortable[key] = items[key];
                                }
                            }
                            if (Object.keys(filteredSortable).length > 0) {
                                optionsData[fieldId] = filteredSortable;
                            }
                        }
                    });

                    // ============================================================
                    // UNIVERSAL SOLUTION - Works with any opt_name and all field types
                    // ============================================================

                    var $form = $('#pfSettingsForm');
                    var optName = $form.find('input[name="opt_name"]').val() || 'persian_framework_options';
                    var formData = $form.serializeArray();
                    var optionsData = {};

                    console.log('Processing with opt_name:', optName);

                    formData.forEach(function(item) {
                        var name = item.name;
                        var value = item.value;

                        if (name === 'pf_ajax_nonce' || name === '_wp_http_referer' ||
                            name === 'opt_name' || name === 'instance_id' || name === 'tab') {
                            return;
                        }

                        if (!name.startsWith(optName + '[')) {
                            return;
                        }

                        var key = name.substring(optName.length + 1, name.length - 1);

                        if (key.endsWith('[]')) {
                            var cleanKey = key.slice(0, -2);
                            if (!optionsData[cleanKey]) {
                                optionsData[cleanKey] = [];
                            }
                            optionsData[cleanKey].push(value);
                            return;
                        }

                        if (key.includes('[')) {
                            var parts = key.match(/[^\[\]]+/g);
                            if (!parts) return;

                            var current = optionsData;
                            for (var i = 0; i < parts.length; i++) {
                                var part = parts[i];
                                if (i === parts.length - 1) {
                                    current[part] = value;
                                } else {
                                    if (!current[part] || typeof current[part] !== 'object') {
                                        var nextPart = parts[i + 1];
                                        if (nextPart && /^\d+$/.test(nextPart)) {
                                            current[part] = [];
                                        } else {
                                            current[part] = {};
                                        }
                                    }
                                    current = current[part];
                                }
                            }
                        } else {
                            optionsData[key] = value;
                        }
                    });

                    $form.find('input[type="checkbox"]:not(:checked)').each(function() {
                        var name = $(this).attr('name');
                        if (!name || !name.startsWith(optName + '[')) return;

                        var key = name.substring(optName.length + 1, name.length - 1);

                        if (key.endsWith('[]')) return;

                        if (!optionsData.hasOwnProperty(key)) {
                            optionsData[key] = '0';
                        }
                    });
                    console.log('Final Options Data:', optionsData);


                    var data = {
                        action: 'pf_ajax_save',
                        nonce: nonce,
                        opt_name: optName,
                        instance_id: instanceId,
                        options: optionsData
                    };

                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: data,
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showNotification(response.data.message || '<?php _e('Settings saved successfully!', 'persian-framework'); ?>');
                                $btn.find('.pfSaveText').text('<?php _e('Saved!', 'persian-framework'); ?>');
                            } else {
                                showNotification(response.data.message || '<?php _e('Error saving settings!', 'persian-framework'); ?>', true);
                                $btn.find('.pfSaveText').text('<?php _e('Error!', 'persian-framework'); ?>');
                            }
                            setTimeout(function() {
                                $btn.prop('disabled', false);
                                $btn.find('.pfSaveText').text('<?php _e('Save Settings', 'persian-framework'); ?>');
                                $btn.find('.pfSaveSpinner').hide();
                            }, 500);
                        },
                        error: function(xhr, status, error) {
                            showNotification('<?php _e('Connection error! Please try again.', 'persian-framework'); ?>', true);
                            $btn.prop('disabled', false);
                            $btn.find('.pfSaveText').text('<?php _e('Save Settings', 'persian-framework'); ?>');
                            $btn.find('.pfSaveSpinner').hide();
                        }
                    });
                });

                // ============================================================
                // 16. Notification
                // ============================================================
                function showNotification(message, isError) {
                    var $notification = $('#pfNotification');
                    var icon = $notification.find('.pf-notification-icon');
                    var text = $notification.find('.pf-notification-text');
                    icon.removeClass('dashicons-yes-alt dashicons-warning');
                    if (isError) {
                        icon.addClass('dashicons-warning');
                        $notification.addClass('error');
                    } else {
                        icon.addClass('dashicons-yes-alt');
                        $notification.removeClass('error');
                    }
                    text.text(message);
                    $notification.addClass('show');
                    clearTimeout($notification.data('timer'));
                    var timer = setTimeout(function() {
                        $notification.removeClass('show');
                    }, 3000);
                    $notification.data('timer', timer);
                }

                console.log('Persian Framework UI loaded!');
            })(jQuery);
        </script>
        <?php
    }

    public function ajax_save_options() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'pf_ajax_nonce')) {
            wp_send_json_error(array('message' => __('Invalid nonce!', 'persian-framework')));
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied!', 'persian-framework')));
        }

        $opt_name = isset($_POST['opt_name']) ? sanitize_text_field($_POST['opt_name']) : 'persian_framework_options';
        $options = isset($_POST['options']) ? $_POST['options'] : array();

        foreach ($options as $key => $value) {
            $options[$key] = $this->sanitize_field_value($value, $key);
        }

        update_option($opt_name, $options);

        wp_send_json_success(array(
                'message' => __('Settings saved successfully!', 'persian-framework'),
                'options' => $options
        ));
    }

    /**
     * Sanitize field value based on its type
     */
    private function sanitize_field_value($value, $field_key) {
        if (is_array($value)) {
            // Media field
            if (isset($value['id']) && isset($value['url'])) {
                $media_id = absint($value['id']);
                $media_url = esc_url_raw($value['url']);

                if ($media_id > 0 && empty($media_url)) {
                    $attachment_url = wp_get_attachment_url($media_id);
                    if ($attachment_url) {
                        $media_url = $attachment_url;
                    }
                }

                return array(
                        'id' => $media_id,
                        'url' => $media_url
                );
            }

            // Check if it's a repeater with media fields inside
            $is_repeater_with_media = false;
            if (!empty($value)) {
                foreach ($value as $k => $v) {
                    if (is_numeric($k) && is_array($v)) {
                        foreach ($v as $sub_key => $sub_value) {
                            if (is_array($sub_value) && isset($sub_value['id']) && isset($sub_value['url'])) {
                                $is_repeater_with_media = true;
                                break 2;
                            }
                        }
                    }
                }
            }

            if ($is_repeater_with_media) {
                $sanitized = array();
                foreach ($value as $index => $item) {
                    if (is_array($item)) {
                        $sanitized[$index] = array();
                        foreach ($item as $sub_key => $sub_value) {
                            if (is_array($sub_value) && isset($sub_value['id']) && isset($sub_value['url'])) {
                                $sanitized[$index][$sub_key] = array(
                                        'id' => absint($sub_value['id']),
                                        'url' => esc_url_raw($sub_value['url'])
                                );
                            } else {
                                $sanitized[$index][$sub_key] = $this->sanitize_field_value($sub_value, $sub_key);
                            }
                        }
                    }
                }
                return $sanitized;
            }

            // Gallery field
            if (!empty($value) && isset($value[0]) && is_numeric($value[0])) {
                $sanitized = array();
                foreach ($value as $item) {
                    if (is_numeric($item)) {
                        $sanitized[] = absint($item);
                    } elseif (is_array($item) && isset($item['id'])) {
                        $sanitized[] = absint($item['id']);
                    }
                }
                return $sanitized;
            }

            // Sortable field
            $is_sortable = false;
            if (!empty($value) && !isset($value['enabled']) && !isset($value['disabled']) && !isset($value['id'])) {
                $is_sortable = true;
                $has_numeric_keys = false;
                foreach ($value as $k => $v) {
                    if (is_numeric($k)) {
                        $has_numeric_keys = true;
                        break;
                    }
                    if (is_array($v)) {
                        $is_sortable = false;
                        break;
                    }
                }
                if ($has_numeric_keys) {
                    $is_sortable = false;
                }
            }

            if ($is_sortable) {
                $sanitized = array();
                foreach ($value as $k => $v) {
                    $sanitized[sanitize_text_field($k)] = sanitize_text_field($v);
                }
                return $sanitized;
            }

            // Multi-text field
            $is_multi_text = true;
            if (!empty($value)) {
                foreach ($value as $v) {
                    if (!is_string($v) && !is_numeric($v) && $v !== null && $v !== '') {
                        $is_multi_text = false;
                        break;
                    }
                }
            }

            if ($is_multi_text && !empty($value)) {
                $sanitized = array();
                foreach ($value as $k => $v) {
                    if (is_numeric($k)) {
                        $sanitized[intval($k)] = sanitize_text_field((string) $v);
                    } else {
                        $sanitized[sanitize_text_field($k)] = sanitize_text_field((string) $v);
                    }
                }
                ksort($sanitized);
                return array_values($sanitized);
            }

            // Sorter field
            if (isset($value['enabled']) || isset($value['disabled'])) {
                return $this->sanitize_sorter_array($value);
            }

            // Repeater field (without media)
            $is_repeater = false;
            if (!empty($value)) {
                foreach ($value as $k => $v) {
                    if (is_numeric($k) && is_array($v)) {
                        $is_repeater = true;
                        break;
                    }
                }
            }

            if ($is_repeater) {
                $sanitized = array();
                foreach ($value as $index => $item) {
                    if (is_array($item)) {
                        $sanitized[$index] = array();
                        foreach ($item as $sub_key => $sub_value) {
                            if (is_array($sub_value)) {
                                $sanitized[$index][sanitize_text_field($sub_key)] = $this->sanitize_field_value($sub_value, $sub_key);
                            } else {
                                $sanitized[$index][sanitize_text_field($sub_key)] = sanitize_text_field($sub_value);
                            }
                        }
                    }
                }
                return $sanitized;
            }

            // Nested arrays
            $sanitized = array();
            foreach ($value as $k => $v) {
                $sanitized[sanitize_text_field($k)] = $this->sanitize_field_value($v, $field_key);
            }
            return $sanitized;
        }

        return sanitize_text_field($value);
    }

    private function sanitize_sorter_array($array) {
        if (!is_array($array)) {
            return array();
        }
        $sanitized = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $sanitized[sanitize_text_field($key)] = $this->sanitize_sorter_array($value);
            } else {
                $sanitized[sanitize_text_field($key)] = sanitize_text_field($value);
            }
        }
        return $sanitized;
    }

    public function ajax_import() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'pf_ajax_nonce')) {
            wp_send_json_error(array('message' => __('Invalid nonce!', 'persian-framework')));
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied!', 'persian-framework')));
        }

        $data = isset($_POST['data']) ? $_POST['data'] : array();
        $opt_name = isset($_POST['opt_name']) ? sanitize_text_field($_POST['opt_name']) : 'persian_framework_options';

        if (!isset($data['options']) || !is_array($data['options'])) {
            wp_send_json_error(array('message' => __('Invalid data format!', 'persian-framework')));
        }

        $options = $data['options'];
        foreach ($options as $key => $value) {
            $options[$key] = $this->sanitize_field_value($value, $key);
        }

        update_option($opt_name, $options);

        wp_send_json_success(array(
                'message' => __('Import successful!', 'persian-framework')
        ));
    }

    public function ajax_reset_options() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'pf_ajax_nonce')) {
            wp_send_json_error(array('message' => __('Invalid nonce!', 'persian-framework')));
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied!', 'persian-framework')));
        }

        $opt_name = isset($_POST['opt_name']) ? sanitize_text_field($_POST['opt_name']) : 'persian_framework_options';

        $defaults_file = PERSIAN_FRAMEWORK_CONFIG . 'defaults.php';
        $defaults = array();
        if (file_exists($defaults_file)) {
            $defaults = include $defaults_file;
        }

        update_option($opt_name, $defaults);

        wp_send_json_success(array(
                'message' => __('Settings reset to default!', 'persian-framework')
        ));
    }

    public function save_options() {
        if (!isset($_POST['pf_nonce']) || !wp_verify_nonce($_POST['pf_nonce'], 'pf_save_options')) {
            wp_die('Invalid nonce!');
        }

        if (!current_user_can('manage_options')) {
            wp_die('Permission denied!');
        }

        $opt_name = isset($_POST['opt_name']) ? sanitize_text_field($_POST['opt_name']) : 'persian_framework_options';
        $options = isset($_POST[$opt_name]) ? $_POST[$opt_name] : array();

        if (isset($_POST['sections_order']) && is_array($_POST['sections_order'])) {
            $options['sections_order'] = $_POST['sections_order'];
        }

        foreach ($options as $key => $value) {
            $options[$key] = $this->sanitize_field_value($value, $key);
        }

        update_option($opt_name, $options);

        $tab = isset($_POST['tab']) ? sanitize_text_field($_POST['tab']) : '';

        wp_redirect(add_query_arg(array(
                'page' => isset($_GET['page']) ? sanitize_text_field($_GET['page']) : 'persian-framework',
                'tab' => $tab,
                'saved' => 'true'
        ), admin_url('admin.php')));
        exit;
    }

    public function show_notices() {
        if (isset($_GET['saved']) && $_GET['saved'] === 'true') {
            ?>
            <div class="notice notice-success is-dismissible" style="margin: 20px 20px 0 0;">
                <p><?php _e('Settings saved successfully!', 'persian-framework'); ?></p>
            </div>
            <?php
        }
    }

    public function enqueue_assets($hook) {
        // ✅ Try multiple ways to get menu_slug
        $menu_slug = '';

        // Way 1: From $this->framework
        if ($this->framework) {
            $config = $this->framework->get_config();
            $menu_slug = isset($config['menu_slug']) ? $config['menu_slug'] : '';
        }

        // Way 2: From global variable
        if (empty($menu_slug)) {
            global $persian_framework;
            if (isset($persian_framework)) {
                $config = $persian_framework->get_config();
                $menu_slug = isset($config['menu_slug']) ? $config['menu_slug'] : '';
            }
        }

        // Way 3: From static property (if you add it)
        if (empty($menu_slug) && class_exists('PersianFramework')) {
            if (method_exists('PersianFramework', 'get_current_config')) {
                $config = PersianFramework::get_current_config();
                $menu_slug = isset($config['menu_slug']) ? $config['menu_slug'] : '';
            }
        }

        // Way 4: Detect from screen ID
        if (empty($menu_slug)) {
            $screen = get_current_screen();
            if ($screen) {
                $screen_id = $screen->id;
                // Remove prefix to get menu_slug
                $prefixes = array('toplevel_page_', 'admin_page_', 'appearance_page_', 'settings_page_', 'tools_page_');
                foreach ($prefixes as $prefix) {
                    if (strpos($screen_id, $prefix) === 0) {
                        $menu_slug = substr($screen_id, strlen($prefix));
                        break;
                    }
                }
            }
        }

        $screen = get_current_screen();
        $is_pf_page = false;

        // Auto-detect framework page using menu_slug
        if (!empty($menu_slug)) {
            // Check screen ID
            if ($screen && strpos($screen->id, $menu_slug) !== false) {
                $is_pf_page = true;
            }

            // Check hook
            if (!$is_pf_page && strpos($hook, $menu_slug) !== false) {
                $is_pf_page = true;
            }

            // Check GET parameter
            if (!$is_pf_page && isset($_GET['page']) && $_GET['page'] === $menu_slug) {
                $is_pf_page = true;
            }
        }

        // Fallback for backward compatibility
        if (!$is_pf_page) {
            if ($screen && (strpos($screen->id, 'persian-framework') !== false ||
                            strpos($screen->id, 'theme-settings') !== false)) {
                $is_pf_page = true;
            }

            if (!$is_pf_page && (strpos($hook, 'persian-framework') !== false ||
                            strpos($hook, 'my-theme-framework') !== false)) {
                $is_pf_page = true;
            }
        }

        if (!$is_pf_page) {
            return;
        }

        // Enqueue assets...
        $sortable_js = PERSIAN_FRAMEWORK_URL . 'vendor/sortablejs/Sortable.min.js';
        if (file_exists(PERSIAN_FRAMEWORK_PATH . 'vendor/sortablejs/Sortable.min.js')) {
            wp_enqueue_script('sortablejs', $sortable_js, array(), '1.15.0', true);
        } else {
            wp_enqueue_script('sortablejs', 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js', array(), '1.15.0', true);
        }

        wp_enqueue_style('dashicons');
        wp_enqueue_style('pf-admin', PERSIAN_FRAMEWORK_ASSETS . 'css/admin.css', array(), PERSIAN_FRAMEWORK_VERSION);

        wp_localize_script('pf-admin', 'pf_ajax', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('pf_ajax_nonce')
        ));
    }
}