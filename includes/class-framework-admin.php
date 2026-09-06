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

            <div id="pf_notification_bar" style="display:none;">
                <div class="pf-save-warn notice-yellow">
                    <span class="dashicons dashicons-warning"></span>
                    <strong><?php esc_html_e('Settings have changed, you should save them!', 'persian-framework'); ?></strong>
                    <button type="button" class="pf-btn pf-btn-success pfAjaxSaveBtn pf-save-now-btn"
                            data-instance="<?php echo esc_attr($instance_id); ?>"
                            data-optname="<?php echo esc_attr($opt_name); ?>">
                        <i class="dashicons dashicons-yes"></i>
                        <span class="pfSaveText"><?php esc_html_e('Save Settings', 'persian-framework'); ?></span>
                    </button>
                    <button type="button" class="pf-btn pf-btn-secondary pf-dismiss-warn">
                        <span class="dashicons dashicons-no-alt"></span>
                    </button>
                </div>
                <div class="pf-field-errors notice-red" style="display:none;">
                    <strong><span class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Error(s) found!', 'persian-framework'); ?></strong>
                </div>
                <div class="pf-field-warnings notice-yellow" style="display:none;">
                    <strong><span class="dashicons dashicons-warning"></span> <?php esc_html_e('Warning(s) found!', 'persian-framework'); ?></strong>
                </div>
            </div>

            <header class="pf-header">
                <div class="pf-header-brand">
                    <div class="pf-brand-icon">
                        <img src="<?php echo esc_url(PERSIAN_FRAMEWORK_URL); ?>assets/images/logo.svg" width="50px">
                    </div>
                    <div>
                        <div class="pf-brand-title">
                            <?php
                            if (isset($config['display_name'])) {
                                echo esc_html($config['display_name']);
                            } else {
                                esc_html_e('Persian Framework', 'persian-framework');
                            }
                            ?>
                        </div>
                        <div class="pf-brand-subtitle">
                            v<?php echo esc_html($config['display_version'] ?? PERSIAN_FRAMEWORK_VERSION); ?>
                            | <?php esc_html_e('Advanced Options Framework', 'persian-framework'); ?>
                        </div>
                    </div>
                </div>
                <div class="pf-header-actions">
                    <div class="pf-search">
                        <span class="dashicons dashicons-search"></span>
                        <input type="search" class="pf-settings-search" placeholder="<?php esc_attr_e('Search settings…', 'persian-framework'); ?>">
                    </div>

                    <button type="button" class="pf-btn pf-btn-secondary" id="pfThemeToggle">
                        <i class="dashicons dashicons-admin-appearance"></i> <?php esc_html_e('Dark Mode', 'persian-framework'); ?>
                    </button>

                    <button type="button" class="pf-btn pf-btn-danger pfResetBtn"
                            data-optname="<?php echo esc_attr($opt_name); ?>"
                            data-instance="<?php echo esc_attr($instance_id); ?>">
                        <i class="dashicons dashicons-image-rotate"></i> <?php esc_html_e('Reset All', 'persian-framework'); ?>
                    </button>

                    <button type="button" class="pf-btn pf-btn-success pfAjaxSaveBtn"
                            data-instance="<?php echo esc_attr($instance_id); ?>"
                            data-optname="<?php echo esc_attr($opt_name); ?>">
                        <i class="dashicons dashicons-yes"></i>
                        <span class="pfSaveText"><?php esc_html_e('Save Settings', 'persian-framework'); ?></span>
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
                        <span><?php esc_html_e('Import / Export', 'persian-framework'); ?></span>
                    </button>

                    <button class="pf-sidebar-tab" data-tab="backup">
                        <i class="dashicons dashicons-backup"></i>
                        <span><?php esc_html_e('Backup', 'persian-framework'); ?></span>
                    </button>

                    <button class="pf-sidebar-tab" data-tab="donate">
                        <i class="dashicons dashicons-heart"></i>
                        <span><?php esc_html_e('Donate', 'persian-framework'); ?></span>
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
                                            <?php esc_html_e('Reset Section', 'persian-framework'); ?>
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
                                                $field_html = PersianFramework_Fields::render_field($field, $value);
                                                echo wp_kses_post($field_html);
                                            }
                                            ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="pf-tab-content" id="pfTab-import-export">
                            <h2><?php esc_html_e('Import / Export', 'persian-framework'); ?></h2>
                            <p class="pf-tab-description"><?php esc_html_e('Import or export your settings as JSON file.', 'persian-framework'); ?></p>
                            <div class="pf-import-export-grid">
                                <div class="pf-ie-box">
                                    <h3><?php esc_html_e('Export Settings', 'persian-framework'); ?></h3>
                                    <p><?php esc_html_e('Download your current settings as a JSON file.', 'persian-framework'); ?></p>
                                    <button type="button" class="pf-btn pf-btn-secondary pfExportBtn" data-optname="<?php echo esc_attr($opt_name); ?>">
                                        <i class="dashicons dashicons-download"></i> <?php esc_html_e('Export JSON', 'persian-framework'); ?>
                                    </button>
                                </div>
                                <div class="pf-ie-box">
                                    <h3><?php esc_html_e('Import Settings', 'persian-framework'); ?></h3>
                                    <p><?php esc_html_e('Upload a JSON file to import settings.', 'persian-framework'); ?></p>
                                    <input type="file" class="pfImportFile" accept=".json" style="display:block;margin-bottom:10px;">
                                    <button type="button" class="pf-btn pf-btn-primary pfImportBtn" data-optname="<?php echo esc_attr($opt_name); ?>" data-instance="<?php echo esc_attr($instance_id); ?>">
                                        <i class="dashicons dashicons-upload"></i> <?php esc_html_e('Import', 'persian-framework'); ?>
                                    </button>
                                </div>
                                <div class="pf-ie-box pf-ie-danger">
                                    <h3><?php esc_html_e('Reset All Settings', 'persian-framework'); ?></h3>
                                    <p><?php esc_html_e('⚠️ This will delete all current settings.', 'persian-framework'); ?></p>
                                    <button type="button" class="pf-btn pf-btn-danger pfResetBtn" data-optname="<?php echo esc_attr($opt_name); ?>" data-instance="<?php echo esc_attr($instance_id); ?>">
                                        <i class="dashicons dashicons-trash"></i> <?php esc_html_e('Reset All', 'persian-framework'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="pf-tab-content" id="pfTab-backup">
                            <h2><?php esc_html_e('Backup', 'persian-framework'); ?></h2>
                            <p class="pf-tab-description"><?php esc_html_e('Create and manage backups of your settings.', 'persian-framework'); ?></p>
                            <div class="pf-backup-actions">
                                <button type="button" class="pf-btn pf-btn-primary pfCreateBackupBtn" data-optname="<?php echo esc_attr($opt_name); ?>" data-instance="<?php echo esc_attr($instance_id); ?>">
                                    <i class="dashicons dashicons-backup"></i> <?php esc_html_e('Create Backup', 'persian-framework'); ?>
                                </button>
                            </div>
                            <div id="pfBackupList">
                                <p style="color:#94a3b8;text-align:center;padding:40px 0;">
                                    <?php esc_html_e('No backups found.', 'persian-framework'); ?>
                                </p>
                            </div>
                        </div>


                        <div class="pf-tab-content" id="pfTab-donate">
                            <h2><?php esc_html_e('Support Persian Framework', 'persian-framework'); ?></h2>
                            <p class="pf-tab-description">
                                <?php esc_html_e('If you find this framework useful, please consider supporting its development.', 'persian-framework'); ?>
                            </p>

                            <div class="pf-donate-author" style="
                                background: #f8fafc;
                                border: 1px solid #e8edf4;
                                border-radius: 16px;
                                padding: 20px 24px;
                                margin-bottom: 24px;
                                display: flex;
                                align-items: center;
                                gap: 20px;
                                flex-wrap: wrap;
                            ">
                                <div style="
                                    width: 60px;
                                    height: 60px;
                                    background: linear-gradient(135deg, #6366f1, #8b5cf6);
                                    border-radius: 50%;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-size: 28px;
                                    color: white;
                                    flex-shrink: 0;
                                ">
                                <span class="dashicons dashicons-admin-users"></span>
                                </div>
                                <div>
                                    <div style="font-weight: 700; font-size: 18px; color: #1a2332;">
                                        Morad Chehresay
                                    </div>
                                    <div style="color: #64748b; font-size: 14px; display: flex; flex-wrap: wrap; gap: 12px; margin-top: 4px;">
                                        <span>
                                            <span class="dashicons dashicons-email" style="font-size: 14px; width: 14px; height: 14px; vertical-align: middle;"></span>
                                            <a href="mailto:chehresay@gmail.com" style="color: #6366f1; text-decoration: none;">chehresay@gmail.com</a>
                                        </span>
                                                                <span>
                                            <span class="dashicons dashicons-admin-site" style="font-size: 14px; width: 14px; height: 14px; vertical-align: middle;"></span>
                                            <a href="https://parswp.ir" target="_blank" style="color: #6366f1; text-decoration: none;">parswp.ir</a>
                                        </span>
                                                                <span>
                                            <span class="dashicons dashicons-github" style="font-size: 14px; width: 14px; height: 14px; vertical-align: middle;"></span>
                                            <a href="https://github.com/chehresay" target="_blank" style="color: #6366f1; text-decoration: none;">github.com/chehresay</a>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="pf-donate-grid">
                                <div class="pf-donate-box">
                                    <h3><?php esc_html_e('Cryptocurrency', 'persian-framework'); ?></h3>

                                    <div class="pf-donate-item">
                                        <strong>Bitcoin (BTC)</strong>
                                        <code>bc1q0r3gzt5xtlglerst36vh6567023thpv5huthrl</code>
                                        <button class="pf-btn pf-btn-secondary pf-copy-btn" data-copy="bc1q0r3gzt5xtlglerst36vh6567023thpv5huthrl">
                                            <span class="dashicons dashicons-clipboard"></span>
                                            <?php esc_html_e('Copy', 'persian-framework'); ?>
                                        </button>
                                    </div>

                                    <div class="pf-donate-item">
                                        <strong>Ethereum (ETH)</strong>
                                        <code>0xd77935cb0f1b03054720de9cb94c3d7df12b9d0e</code>
                                        <button class="pf-btn pf-btn-secondary pf-copy-btn" data-copy="0xd77935cb0f1b03054720de9cb94c3d7df12b9d0e">
                                            <span class="dashicons dashicons-clipboard"></span>
                                            <?php esc_html_e('Copy', 'persian-framework'); ?>
                                        </button>
                                    </div>
                                </div>

                                <div class="pf-donate-box">
                                    <h3><?php esc_html_e('Other Ways to Support', 'persian-framework'); ?></h3>
                                    <ul>
                                        <li>⭐ <?php esc_html_e('Star the project on GitHub', 'persian-framework'); ?></li>
                                        <li>🐛 <?php esc_html_e('Report bugs and suggest features', 'persian-framework'); ?></li>
                                        <li>📝 <?php esc_html_e('Write documentation or tutorials', 'persian-framework'); ?></li>
                                        <li>🔀 <?php esc_html_e('Contribute code via pull requests', 'persian-framework'); ?></li>
                                    </ul>
                                </div>
                            </div>

                            <div style="
                                margin-top: 24px;
                                padding: 16px 20px;
                                background: #f0fdf4;
                                border-left: 4px solid #22c55e;
                                border-radius: 8px;
                                color: #166534;
                                font-size: 14px;
                            ">
                                <span class="dashicons dashicons-heart" style="color: #22c55e; font-size: 18px; width: 18px; height: 18px; vertical-align: middle;"></span>
                                <?php esc_html_e('Thank you for using Persian Framework! Your support means the world to me. ❤️', 'persian-framework'); ?>
                            </div>

                        </div>

                        <div class="pf-actions">
                            <button type="button" class="pf-btn pf-btn-success pfAjaxSaveBtn"
                                    data-optname="<?php echo esc_attr($opt_name); ?>"
                                    data-instance="<?php echo esc_attr($instance_id); ?>">
                                <i class="dashicons dashicons-yes"></i>
                                <span class="pfSaveText"><?php esc_html_e('Save Settings', 'persian-framework'); ?></span>
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

    public function ajax_reset_section() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'pf_ajax_nonce')) {
            wp_send_json_error(array('message' => esc_html__('Invalid nonce!', 'persian-framework')));
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => esc_html__('Permission denied!', 'persian-framework')));
        }

        $opt_name = isset($_POST['opt_name']) ? sanitize_text_field(wp_unslash($_POST['opt_name'])) : 'persian_framework_options';
        $section_id = isset($_POST['section_id']) ? sanitize_text_field(wp_unslash($_POST['section_id'])) : '';
        $instance_id = isset($_POST['instance_id']) ? sanitize_text_field(wp_unslash($_POST['instance_id'])) : 'default';

        if (empty($section_id)) {
            wp_send_json_error(array('message' => esc_html__('Section ID is required!', 'persian-framework')));
        }

        $framework = null;

        if (class_exists('PersianFramework')) {
            try {
                $framework = PersianFramework::get_instance($instance_id);
                if (empty($framework->get_config())) {
                    $framework->load_config();
                }
            } catch (Exception $e) {
                // Fallback
            }
        }

        if ($framework === null) {
            global $persian_framework;
            if (isset($persian_framework)) {
                $framework = $persian_framework;
            }
        }

        if ($framework === null && $this->framework !== null) {
            $framework = $this->framework;
        }

        $defaults = array();

        if ($framework !== null) {
            $sections = $framework->get_sections();

            foreach ($sections as $section) {
                if ($section['id'] === $section_id && isset($section['fields'])) {
                    foreach ($section['fields'] as $field) {
                        if (isset($field['id'])) {
                            $field_id = $field['id'];

                            if (isset($field['type']) && $field['type'] === 'repeater') {
                                if (isset($field['default']) && is_array($field['default'])) {
                                    $defaults[$field_id] = $field['default'];
                                } else {
                                    $defaults[$field_id] = array();
                                }
                            } else {
                                if (isset($field['default'])) {
                                    $defaults[$field_id] = $field['default'];
                                } else {
                                    $defaults[$field_id] = null;
                                }
                            }
                        }
                    }
                    break;
                }
            }
        }

        if (empty($defaults)) {
            $defaults_file = PERSIAN_FRAMEWORK_CONFIG . 'defaults.php';
            if (file_exists($defaults_file)) {
                $all_defaults = include $defaults_file;
                if (isset($all_defaults[$section_id])) {
                    $defaults = $all_defaults[$section_id];
                }
            }
        }

        $current_options = get_option($opt_name, array());

        foreach ($defaults as $field_id => $default_value) {
            if ($default_value === null) {
                if (isset($current_options[$field_id])) {
                    unset($current_options[$field_id]);
                }
            } else {
                $current_options[$field_id] = $default_value;
            }
        }

        update_option($opt_name, $current_options);

        wp_send_json_success(array(
                'message' => sprintf(
                /* translators: %s: Section ID */
                        esc_html__('Section "%s" has been reset to defaults!', 'persian-framework'),
                        $section_id
                ),
                'section_id' => $section_id
        ));
    }

    private function enqueue_styles_and_scripts() {
        ?>
        <style>
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

            @keyframes pfSpin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }

            .pf-section-actions .dashicons.spin,
            .pf-btn .dashicons.spin {
                animation: pfSpin 1s linear infinite;
            }

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

                $(document).on('click', '.pf-dismiss-warn', function() {
                    $('#pf_notification_bar').hide();
                });

                $(document).on('click', '.pf-save-now-btn', function() {
                    $('.pfAjaxSaveBtn').click();
                });

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

                $(document).ready(function() {
                    setTimeout(function() {
                        pfCaptureInitialState();
                        pf_has_changes = false;
                        pfUpdateNotificationBar();
                    }, 500);
                });

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

                $('.pfExportBtn').on('click', function() {
                    var optName = $(this).data('optname');
                    window.location.href = '<?php echo esc_url(admin_url('admin-post.php?action=pf_export')); ?>&opt_name=' + optName;
                });

                $('.pfImportBtn').on('click', function() {
                    var $btn = $(this);
                    var optName = $btn.data('optname');
                    var instanceId = $btn.data('instance');
                    var $file = $btn.closest('.pf-ie-box').find('.pfImportFile');
                    var file = $file[0].files[0];

                    if (!file) {
                        alert('<?php esc_html_e('Please select a file first!', 'persian-framework'); ?>');
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
                                    nonce: '<?php echo esc_js(wp_create_nonce('pf_ajax_nonce')); ?>'
                                },
                                success: function(response) {
                                    if (response.success) {
                                        alert('<?php esc_html_e('Import successful! Page will reload.', 'persian-framework'); ?>');
                                        location.reload();
                                    } else {
                                        alert('<?php esc_html_e('Import failed: ', 'persian-framework'); ?>' + response.data.message);
                                    }
                                }
                            });
                        } catch(e) {
                            alert('<?php esc_html_e('Invalid JSON file!', 'persian-framework'); ?>');
                        }
                    };
                    reader.readAsText(file);
                });

                $('.pfResetBtn').on('click', function() {
                    var $btn = $(this);
                    var optName = $btn.data('optname');
                    var instanceId = $btn.data('instance');

                    if (confirm('<?php esc_html_e('Are you sure you want to reset all settings? This cannot be undone!', 'persian-framework'); ?>')) {
                        $btn.prop('disabled', true);
                        $btn.html('<span class="dashicons dashicons-update spin"></span> <?php esc_html_e('Resetting...', 'persian-framework'); ?>');

                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'pf_reset_options',
                                opt_name: optName,
                                instance_id: instanceId,
                                nonce: '<?php echo esc_js(wp_create_nonce('pf_ajax_nonce')); ?>'
                            },
                            success: function(response) {
                                if (response.success) {
                                    alert('<?php esc_html_e('Settings reset to default! Page will reload.', 'persian-framework'); ?>');
                                    location.reload();
                                } else {
                                    alert('<?php esc_html_e('Reset failed: ', 'persian-framework'); ?>' + response.data.message);
                                    $btn.prop('disabled', false);
                                    $btn.html('<i class="dashicons dashicons-image-rotate"></i> <?php esc_html_e('Reset All', 'persian-framework'); ?>');
                                }
                            }
                        });
                    }
                });

                $(document).on('click', '.pfResetSectionBtn', function() {
                    var $btn = $(this);
                    var sectionId = $btn.data('section');
                    var optName = $btn.data('optname');
                    var instanceId = $btn.data('instance');
                    var sectionTitle = $btn.closest('.pf-section-header').find('h2').text() || sectionId;

                    if (confirm('<?php esc_html_e('Are you sure you want to reset the "', 'persian-framework'); ?>' + sectionTitle + '" <?php esc_html_e('section to default values? This cannot be undone!', 'persian-framework'); ?>')) {

                        $btn.prop('disabled', true);
                        $btn.html('<span class="dashicons dashicons-update spin"></span> <?php esc_html_e('Resetting...', 'persian-framework'); ?>');

                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'pf_reset_section',
                                section_id: sectionId,
                                opt_name: optName,
                                instance_id: instanceId,
                                nonce: '<?php echo esc_js(wp_create_nonce('pf_ajax_nonce')); ?>'
                            },
                            success: function(response) {
                                if (response.success) {
                                    showNotification(response.data.message || '<?php esc_html_e('Section reset successfully!', 'persian-framework'); ?>');
                                    setTimeout(function() {
                                        location.reload();
                                    }, 1000);
                                } else {
                                    showNotification(response.data.message || '<?php esc_html_e('Error resetting section!', 'persian-framework'); ?>', true);
                                    $btn.prop('disabled', false);
                                    $btn.html('<i class="dashicons dashicons-image-rotate"></i> <?php esc_html_e('Reset Section', 'persian-framework'); ?>');
                                }
                            },
                            error: function() {
                                showNotification('<?php esc_html_e('Connection error! Please try again.', 'persian-framework'); ?>', true);
                                $btn.prop('disabled', false);
                                $btn.html('<i class="dashicons dashicons-image-rotate"></i> <?php esc_html_e('Reset Section', 'persian-framework'); ?>');
                            }
                        });
                    }
                });

                $('.pfAjaxSaveBtn').on('click', function() {
                    var $btn = $(this);
                    var $form = $('#pfSettingsForm');
                    var optName = $btn.data('optname') || $form.find('input[name="opt_name"]').val() || 'persian_framework_options';
                    var instanceId = $btn.data('instance') || $form.data('instance');
                    var nonce = $form.find('input[name="pf_ajax_nonce"]').val();

                    $btn.prop('disabled', true);
                    $btn.find('.pfSaveText').text('<?php esc_html_e('Saving...', 'persian-framework'); ?>');
                    $btn.find('.pfSaveSpinner').show();

                    if (typeof tinyMCE !== 'undefined') {
                        var editors = tinyMCE.editors;
                        for (var editorId in editors) {
                            if (editors.hasOwnProperty(editorId)) {
                                var editor = editors[editorId];
                                if (editor && editor.id) {
                                    editor.save();
                                }
                            }
                        }
                    }

                    var formData = $form.serializeArray();
                    var optionsData = {};

                    // ------------------------------------------------------------
                    // Parse option field names safely.
                    // Example:
                    // persian_framework_options[gallery_field][]
                    // becomes parts = ['gallery_field'], isArray = true.
                    //
                    // The old substring(..., name.length - 1) logic removed the
                    // wrong bracket and turned gallery_field[] into a malformed
                    // key. As a result, every gallery value overwrote the previous
                    // value and only the last image was saved.
                    // ------------------------------------------------------------
                    function pfParseOptionName(name) {
                        if (!name || name.indexOf(optName + '[') !== 0) {
                            return null;
                        }

                        var raw = name.substring(optName.length);
                        var isArray = raw.slice(-2) === '[]';
                        var parts = raw.match(/[^\[\]]+/g);

                        if (!parts || !parts.length) {
                            return null;
                        }

                        return {
                            parts: parts,
                            isArray: isArray
                        };
                    }

                    function pfSetOptionValue(target, parts, value, append) {
                        var current = target;

                        for (var i = 0; i < parts.length; i++) {
                            var part = parts[i];
                            var isLast = i === parts.length - 1;

                            if (isLast) {
                                if (append) {
                                    if (!Array.isArray(current[part])) {
                                        current[part] = [];
                                    }

                                    if (value !== '' && value !== null && value !== undefined) {
                                        current[part].push(value);
                                    }
                                } else {
                                    current[part] = value;
                                }
                                return;
                            }

                            if (!current[part] || typeof current[part] !== 'object') {
                                var nextPart = parts[i + 1];
                                current[part] = nextPart && /^\d+$/.test(nextPart) ? [] : {};
                            }

                            current = current[part];
                        }
                    }

                    function pfEnsureArrayOption(target, parts) {
                        var current = target;

                        for (var i = 0; i < parts.length; i++) {
                            var part = parts[i];
                            var isLast = i === parts.length - 1;

                            if (isLast) {
                                if (!Array.isArray(current[part])) {
                                    current[part] = [];
                                }
                                return;
                            }

                            if (!current[part] || typeof current[part] !== 'object') {
                                var nextPart = parts[i + 1];
                                current[part] = nextPart && /^\d+$/.test(nextPart) ? [] : {};
                            }

                            current = current[part];
                        }
                    }

                    formData.forEach(function(item) {
                        var name = item.name;
                        var value = item.value;

                        if (name === 'pf_ajax_nonce' || name === '_wp_http_referer' ||
                            name === 'opt_name' || name === 'instance_id' || name === 'tab') {
                            return;
                        }

                        var parsed = pfParseOptionName(name);
                        if (!parsed) {
                            return;
                        }

                        pfSetOptionValue(optionsData, parsed.parts, value, parsed.isArray);
                    });

                    // ------------------------------------------------------------
                    // Make empty array fields explicit. This is important when a
                    // gallery is cleared completely: serializeArray() then has no
                    // gallery inputs, so without this step the old gallery would
                    // remain in the database.
                    // ------------------------------------------------------------
                    $form.find('[name]').each(function() {
                        var name = $(this).attr('name');
                        var parsed = pfParseOptionName(name);

                        if (parsed && parsed.isArray) {
                            pfEnsureArrayOption(optionsData, parsed.parts);
                        }
                    });

                    // ============================================================
                    // FIX: Handle unchecked checkboxes
                    // ============================================================
                    $form.find('input[type="checkbox"]:not(:checked)').each(function() {
                        var name = $(this).attr('name');
                        var parsed = pfParseOptionName(name);

                        if (!parsed || parsed.isArray) {
                            return;
                        }

                        // Do not overwrite an already submitted value.
                        var current = optionsData;
                        for (var i = 0; i < parsed.parts.length; i++) {
                            var part = parsed.parts[i];
                            if (i === parsed.parts.length - 1) {
                                if (!Object.prototype.hasOwnProperty.call(current, part)) {
                                    current[part] = '0';
                                }
                            } else {
                                if (!current[part] || typeof current[part] !== 'object') {
                                    current[part] = {};
                                }
                                current = current[part];
                            }
                        }
                    });

                    // ============================================================
                    // FIX: Ensure multi-select values are arrays
                    // ============================================================
                    $form.find('select[multiple]').each(function() {
                        var $select = $(this);
                        var name = $select.attr('name');
                        var parsed = pfParseOptionName(name);

                        if (!parsed) {
                            return;
                        }

                        var selectedValues = $select.val() || [];
                        if (!Array.isArray(selectedValues)) {
                            selectedValues = [selectedValues];
                        }

                        selectedValues = selectedValues.filter(function(v) {
                            return v !== '' && v !== null && v !== undefined;
                        });

                        // Multiple selects are arrays even if their HTML name
                        // does not explicitly end with [].
                        var current = optionsData;
                        for (var i = 0; i < parsed.parts.length; i++) {
                            var part = parsed.parts[i];
                            if (i === parsed.parts.length - 1) {
                                current[part] = selectedValues;
                            } else {
                                if (!current[part] || typeof current[part] !== 'object') {
                                    var nextPart = parsed.parts[i + 1];
                                    current[part] = nextPart && /^\d+$/.test(nextPart) ? [] : {};
                                }
                                current = current[part];
                            }
                        }
                    });

                    console.log('formData:', formData);
                    console.log('Processing with opt_name:', optName);
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
                                showNotification(response.data.message || '<?php esc_html_e('Settings saved successfully!', 'persian-framework'); ?>');
                                $btn.find('.pfSaveText').text('<?php esc_html_e('Saved!', 'persian-framework'); ?>');
                            } else {
                                showNotification(response.data.message || '<?php esc_html_e('Error saving settings!', 'persian-framework'); ?>', true);
                                $btn.find('.pfSaveText').text('<?php esc_html_e('Error!', 'persian-framework'); ?>');
                            }
                            setTimeout(function() {
                                $btn.prop('disabled', false);
                                $btn.find('.pfSaveText').text('<?php esc_html_e('Save Settings', 'persian-framework'); ?>');
                                $btn.find('.pfSaveSpinner').hide();
                            }, 500);
                        },
                        error: function(xhr, status, error) {
                            showNotification('<?php esc_html_e('Connection error! Please try again.', 'persian-framework'); ?>', true);
                            $btn.prop('disabled', false);
                            $btn.find('.pfSaveText').text('<?php esc_html_e('Save Settings', 'persian-framework'); ?>');
                            $btn.find('.pfSaveSpinner').hide();
                        }
                    });
                });

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

                $(document).on('click', '.pf-copy-btn', function() {
                    var text = $(this).data('copy');
                    navigator.clipboard.writeText(text).then(function() {
                        var $btn = $(this);
                        var originalText = $btn.html();
                        $btn.html('<span class="dashicons dashicons-yes"></span> Copied!');
                        setTimeout(function() {
                            $btn.html(originalText);
                        }, 2000);
                    }.bind(this));
                });

                console.log('Persian Framework UI loaded!');
            })(jQuery);
        </script>
        <?php
    }

    public function ajax_save_options() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'pf_ajax_nonce')) {
            wp_send_json_error(array('message' => esc_html__('Invalid nonce!', 'persian-framework')));
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => esc_html__('Permission denied!', 'persian-framework')));
        }

        $opt_name = isset($_POST['opt_name']) ? sanitize_text_field(wp_unslash($_POST['opt_name'])) : 'persian_framework_options';
        $options = isset($_POST['options']) ? wp_unslash($_POST['options']) : array();

        foreach ($options as $key => $value) {
            $options[$key] = $this->sanitize_field_value($value, $key);
        }

        update_option($opt_name, $options);

        wp_send_json_success(array(
                'message' => esc_html__('Settings saved successfully!', 'persian-framework'),
                'options' => $options
        ));
    }

    private function sanitize_field_value($value, $field_key) {
        // ============================================================
        // FIX: Check for gallery field FIRST (before any other processing)
        // ============================================================
        if (strpos($field_key, 'gallery') !== false) {
            // If it's a string (single value), return as array with single value
            if (is_string($value) && !empty($value)) {
                return array(absint($value));
            }
            // If it's already an array, sanitize each item
            if (is_array($value)) {
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
            // Empty or null -> return empty array
            return array();
        }

        $wp_editor_fields = array('wp_editor', 'editor', 'wysiwyg', 'content');

        $is_wp_editor = false;
        foreach ($wp_editor_fields as $field) {
            if (strpos($field_key, $field) !== false) {
                $is_wp_editor = true;
                break;
            }
        }

        if ($is_wp_editor && is_string($value)) {
            return wp_kses_post($value);
        }

        // ============================================================
        // Check for array-type fields
        // ============================================================
        $array_fields = array(
                'multi_select', 'multi-select', 'checkbox', 'multi_text', 'multi-text',
                'checkbox_multiple', 'multiple'
        );
        $is_array_field = false;
        foreach ($array_fields as $field_type) {
            if (strpos($field_key, $field_type) !== false) {
                $is_array_field = true;
                break;
            }
        }

        if ($is_array_field) {
            if (is_array($value)) {
                return array_map('sanitize_text_field', $value);
            }
            if (is_string($value) && $value !== '') {
                if (strpos($value, ',') !== false) {
                    return array_map('sanitize_text_field', explode(',', $value));
                }
                return array(sanitize_text_field($value));
            }
            return array();
        }

        // ============================================================
        // Handle regular arrays (repeater, media, sorter, etc.)
        // ============================================================
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

            // Numeric array (gallery, etc.) - but gallery is already handled above
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
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'pf_ajax_nonce')) {
            wp_send_json_error(array('message' => esc_html__('Invalid nonce!', 'persian-framework')));
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => esc_html__('Permission denied!', 'persian-framework')));
        }

        $data = isset($_POST['data']) ? map_deep(wp_unslash($_POST['data']), 'sanitize_text_field') : array();
        $opt_name = isset($_POST['opt_name']) ? sanitize_text_field(wp_unslash($_POST['opt_name'])) : 'persian_framework_options';

        if (!isset($data['options']) || !is_array($data['options'])) {
            wp_send_json_error(array('message' => esc_html__('Invalid data format!', 'persian-framework')));
        }

        $options = $data['options'];
        foreach ($options as $key => $value) {
            $options[$key] = $this->sanitize_field_value($value, $key);
        }

        update_option($opt_name, $options);

        wp_send_json_success(array(
                'message' => esc_html__('Import successful!', 'persian-framework')
        ));
    }

    public function ajax_reset_options() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'pf_ajax_nonce')) {
            wp_send_json_error(array('message' => esc_html__('Invalid nonce!', 'persian-framework')));
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => esc_html__('Permission denied!', 'persian-framework')));
        }

        $opt_name = isset($_POST['opt_name']) ? sanitize_text_field(wp_unslash($_POST['opt_name'])) : 'persian_framework_options';

        $defaults_file = PERSIAN_FRAMEWORK_CONFIG . 'defaults.php';
        $defaults = array();
        if (file_exists($defaults_file)) {
            $defaults = include $defaults_file;
        }

        update_option($opt_name, $defaults);

        wp_send_json_success(array(
                'message' => esc_html__('Settings reset to default!', 'persian-framework')
        ));
    }

    public function save_options() {
        if (!isset($_POST['pf_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pf_nonce'])), 'pf_save_options')) {
            wp_die(esc_html__('Invalid nonce!', 'persian-framework'));
        }

        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Permission denied!', 'persian-framework'));
        }

        $opt_name = isset($_POST['opt_name']) ? sanitize_text_field(wp_unslash($_POST['opt_name'])) : 'persian_framework_options';
        $options = isset($_POST[$opt_name]) ? wp_unslash($_POST[$opt_name]) : array();

        if (isset($_POST['sections_order']) && is_array($_POST['sections_order'])) {
            $options['sections_order'] = map_deep(wp_unslash($_POST['sections_order']), 'sanitize_text_field');
        }

        foreach ($options as $key => $value) {
            $options[$key] = $this->sanitize_field_value($value, $key);
        }

        update_option($opt_name, $options);

        $tab = isset($_POST['tab']) ? sanitize_text_field(wp_unslash($_POST['tab'])) : '';

        wp_safe_redirect(add_query_arg(array(
                'page' => isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : 'persian-framework',
                'tab' => $tab,
                'saved' => 'true'
        ), admin_url('admin.php')));
        exit;
    }

    public function show_notices() {
        if (isset($_GET['saved']) && $_GET['saved'] === 'true') {
            ?>
            <div class="notice notice-success is-dismissible" style="margin: 20px 20px 0 0;">
                <p><?php esc_html_e('Settings saved successfully!', 'persian-framework'); ?></p>
            </div>
            <?php
        }
    }

    public function enqueue_assets($hook) {
        $menu_slug = '';

        if ($this->framework) {
            $config = $this->framework->get_config();
            $menu_slug = isset($config['menu_slug']) ? $config['menu_slug'] : '';
        }

        if (empty($menu_slug)) {
            global $persian_framework;
            if (isset($persian_framework)) {
                $config = $persian_framework->get_config();
                $menu_slug = isset($config['menu_slug']) ? $config['menu_slug'] : '';
            }
        }

        if (empty($menu_slug) && class_exists('PersianFramework')) {
            if (method_exists('PersianFramework', 'get_current_config')) {
                $config = PersianFramework::get_current_config();
                $menu_slug = isset($config['menu_slug']) ? $config['menu_slug'] : '';
            }
        }

        if (empty($menu_slug)) {
            $screen = get_current_screen();
            if ($screen) {
                $screen_id = $screen->id;
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

        if (!empty($menu_slug)) {
            if ($screen && strpos($screen->id, $menu_slug) !== false) {
                $is_pf_page = true;
            }

            if (!$is_pf_page && strpos($hook, $menu_slug) !== false) {
                $is_pf_page = true;
            }

            if (!$is_pf_page && isset($_GET['page']) && $_GET['page'] === $menu_slug) {
                $is_pf_page = true;
            }
        }

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

        $sortable_js = PERSIAN_FRAMEWORK_URL . 'vendor/sortablejs/Sortable.min.js';
        wp_enqueue_script('sortablejs', $sortable_js, array(), '1.15.0', true);

        wp_enqueue_style('dashicons');
        wp_enqueue_style('pf-admin', PERSIAN_FRAMEWORK_ASSETS . 'css/admin.css', array(), PERSIAN_FRAMEWORK_VERSION);

        wp_localize_script('pf-admin', 'pf_ajax', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('pf_ajax_nonce')
        ));
    }
}