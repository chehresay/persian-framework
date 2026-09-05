<?php
/**
 * Persian Framework - Demo Activator Class
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Demo {

    private static $instance = null;
    private $demo_active = false;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', array($this, 'add_demo_page'));
        add_action('admin_init', array($this, 'handle_demo_activation'));
        add_action('admin_notices', array($this, 'show_demo_notice'));

        $this->demo_active = get_option('persian_framework_demo_active', false);
    }

    /**
     * Add demo page to WordPress admin
     */
    public function add_demo_page() {
        add_options_page(
                esc_html__('Persian Framework Demo', 'persian-framework'),
                esc_html__('PF Demo', 'persian-framework'),
                'manage_options',
                'persian-framework-demo',
                array($this, 'render_demo_page')
        );

        add_management_page(
                esc_html__('Persian Framework Demo', 'persian-framework'),
                esc_html__('PF Demo Activator', 'persian-framework'),
                'manage_options',
                'persian-framework-demo-tools',
                array($this, 'render_demo_page')
        );
    }

    /**
     * Render demo activation page
     */
    public function render_demo_page() {
        $is_active = get_option('persian_framework_demo_active', false);
        $demo_file = PERSIAN_FRAMEWORK_CONFIG . 'sections.php';
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Persian Framework Demo', 'persian-framework'); ?></h1>

            <div class="pf-demo-container" style="max-width:800px;margin:20px 0;">
                <div class="pf-demo-card" style="
                    background: #fff;
                    border: 1px solid #ccd0d4;
                    border-radius: 8px;
                    padding: 30px;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
                ">
                    <h2 style="margin-top:0;display:flex;align-items:center;gap:10px;">
                        <span class="dashicons dashicons-admin-generic" style="font-size:30px;width:30px;height:30px;color:#6366f1;"></span>
                        <?php esc_html_e('Demo Configuration', 'persian-framework'); ?>
                    </h2>

                    <p style="font-size:15px;color:#4a5568;line-height:1.6;">
                        <?php esc_html_e('Activate the demo configuration to see all 40+ field types in action. This will create a separate menu item with complete field examples.', 'persian-framework'); ?>
                    </p>

                    <div style="
                        background: #f8f9fa;
                        border-left: 4px solid #6366f1;
                        padding: 15px 20px;
                        margin: 20px 0;
                        border-radius: 4px;
                    ">
                        <strong><?php esc_html_e('Demo Features:', 'persian-framework'); ?></strong>
                        <ul style="margin:10px 0 0 20px;list-style:disc;">
                            <li><?php esc_html_e('40+ professional field types', 'persian-framework'); ?></li>
                            <li><?php esc_html_e('Live preview examples', 'persian-framework'); ?></li>
                            <li><?php esc_html_e('Repeater and sorter demos', 'persian-framework'); ?></li>
                            <li><?php esc_html_e('Media, gallery, and image fields', 'persian-framework'); ?></li>
                            <li><?php esc_html_e('Typography and color controls', 'persian-framework'); ?></li>
                            <li><?php esc_html_e('Date picker with Persian calendar', 'persian-framework'); ?></li>
                        </ul>
                    </div>

                    <?php if ($is_active): ?>
                        <div style="
                            background: #d1fae5;
                            border-left: 4px solid #10b981;
                            padding: 15px 20px;
                            margin: 20px 0;
                            border-radius: 4px;
                            display:flex;
                            align-items:center;
                            gap:12px;
                        ">
                            <span class="dashicons dashicons-yes-alt" style="color:#10b981;font-size:24px;width:24px;height:24px;"></span>
                            <span style="font-weight:600;color:#065f46;"><?php esc_html_e('Demo is currently ACTIVE', 'persian-framework'); ?></span>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=persian-framework-demo&deactivate_demo=1')); ?>"
                               class="button button-secondary"
                               style="margin-left:auto;"
                               onclick="return confirm('<?php esc_html_e('Are you sure you want to deactivate the demo?', 'persian-framework'); ?>');">
                                <?php esc_html_e('Deactivate Demo', 'persian-framework'); ?>
                            </a>
                        </div>

                        <div style="margin:20px 0;padding:15px 20px;background:#eff6ff;border-radius:4px;">
                            <strong><?php esc_html_e('Access Demo:', 'persian-framework'); ?></strong>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=persian-framework')); ?>"
                               class="button button-primary"
                               style="margin-left:10px;">
                                <span class="dashicons dashicons-external" style="font-size:16px;width:16px;height:16px;vertical-align:middle;"></span>
                                <?php esc_html_e('View Demo', 'persian-framework'); ?>
                            </a>
                        </div>

                    <?php else: ?>
                        <div style="
                            background: #fef3c7;
                            border-left: 4px solid #f59e0b;
                            padding: 15px 20px;
                            margin: 20px 0;
                            border-radius: 4px;
                        ">
                            <span class="dashicons dashicons-info" style="color:#f59e0b;font-size:20px;width:20px;height:20px;vertical-align:middle;"></span>
                            <?php esc_html_e('The demo is currently INACTIVE. Click the button below to activate it.', 'persian-framework'); ?>
                        </div>

                        <form method="post" action="" style="margin-top:20px;">
                            <?php wp_nonce_field('pf_demo_activation', 'pf_demo_nonce'); ?>
                            <input type="hidden" name="activate_demo" value="1">
                            <button type="submit" class="button button-primary button-hero" style="
                                background: #6366f1;
                                border-color: #6366f1;
                                padding: 12px 30px;
                                font-size: 16px;
                            ">
                                <span class="dashicons dashicons-yes" style="font-size:18px;width:18px;height:18px;vertical-align:middle;"></span>
                                <?php esc_html_e('Activate Demo', 'persian-framework'); ?>
                            </button>
                        </form>
                    <?php endif; ?>

                    <hr style="margin:30px 0;border-color:#e8edf4;">

                    <div style="color:#6b7a8f;font-size:13px;">
                        <p>
                            <span class="dashicons dashicons-info" style="font-size:16px;width:16px;height:16px;vertical-align:middle;"></span>
                            <?php esc_html_e('Activating the demo will create a separate menu item with all field types. You can deactivate it at any time.', 'persian-framework'); ?>
                        </p>
                        <p>
                            <span class="dashicons dashicons-admin-page" style="font-size:16px;width:16px;height:16px;vertical-align:middle;"></span>
                            <?php
                            printf(
                            /* translators: %s: File path */
                                    esc_html__('Demo file location: %s', 'persian-framework'),
                                    '<code>' . esc_html(PERSIAN_FRAMEWORK_CONFIG . 'sections.php') . '</code>'
                            );
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .pf-demo-card .button-hero {
                font-size: 16px !important;
                line-height: 2.5 !important;
                min-height: 50px !important;
                padding: 0 30px !important;
            }
            .pf-demo-card .button-hero .dashicons {
                margin-top: 4px;
            }
            .pf-demo-card ul li {
                margin-bottom: 4px;
            }
        </style>
        <?php
    }

    /**
     * Handle demo activation/deactivation
     */
    public function handle_demo_activation() {
        if (!isset($_GET['page']) || (strpos($_GET['page'], 'persian-framework-demo') === false)) {
            return;
        }

        if (isset($_POST['activate_demo']) && isset($_POST['pf_demo_nonce'])) {
            if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pf_demo_nonce'])), 'pf_demo_activation')) {
                wp_die(esc_html__('Invalid nonce!', 'persian-framework'));
            }

            if (!current_user_can('manage_options')) {
                wp_die(esc_html__('Permission denied!', 'persian-framework'));
            }

            $this->activate_demo();
            wp_safe_redirect(add_query_arg('demo_activated', '1', wp_get_referer()));
            exit;
        }

        if (isset($_GET['deactivate_demo'])) {
            if (!current_user_can('manage_options')) {
                wp_die(esc_html__('Permission denied!', 'persian-framework'));
            }

            $this->deactivate_demo();
            wp_safe_redirect(remove_query_arg('deactivate_demo', wp_get_referer()));
            exit;
        }

        if (isset($_GET['demo_activated'])) {
            add_action('admin_notices', array($this, 'show_activation_notice'));
        }
    }

    /**
     * Activate demo
     */
    private function activate_demo() {
        update_option('persian_framework_demo_active', true);
        $this->register_demo_framework();
    }

    /**
     * Deactivate demo
     */
    private function deactivate_demo() {
        delete_option('persian_framework_demo_active');
        delete_option('persian_framework_demo_options');
        delete_transient('persian_framework_demo_active');
    }

    /**
     * Register demo framework instance
     */
    private function register_demo_framework() {
        if (class_exists('PersianFramework')) {
            $demo_framework = PersianFramework::get_instance('demo-framework');

            $sections_file = PERSIAN_FRAMEWORK_CONFIG . 'sections.php';
            if (file_exists($sections_file)) {
                include_once $sections_file;
            }
        }
    }

    /**
     * Show activation success notice
     */
    public function show_activation_notice() {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <strong><?php esc_html_e('Persian Framework Demo Activated!', 'persian-framework'); ?></strong>
                <?php esc_html_e('You can now view the demo from the menu.', 'persian-framework'); ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=persian-framework')); ?>">
                    <?php esc_html_e('View Demo →', 'persian-framework'); ?>
                </a>
            </p>
        </div>
        <?php
    }

    /**
     * Show notice on admin if demo is active
     */
    public function show_demo_notice() {
        if (!$this->demo_active) {
            return;
        }

        $screen = get_current_screen();
        if ($screen && strpos($screen->id, 'persian-framework-demo') !== false) {
            return;
        }

        ?>
        <div class="notice notice-info is-dismissible" style="border-left-color:#6366f1;">
            <p>
                <strong><?php esc_html_e('Persian Framework Demo is Active', 'persian-framework'); ?></strong>
                <span style="margin:0 12px;">|</span>
                <a href="<?php echo esc_url(admin_url('admin.php?page=persian-framework')); ?>">
                    <?php esc_html_e('View Demo', 'persian-framework'); ?>
                </a>
                <span style="margin:0 8px;">|</span>
                <a href="<?php echo esc_url(admin_url('options-general.php?page=persian-framework-demo')); ?>" style="color:#ef4444;">
                    <?php esc_html_e('Deactivate Demo', 'persian-framework'); ?>
                </a>
            </p>
        </div>
        <?php
    }

    /**
     * Check if demo is active
     */
    public function is_demo_active() {
        return $this->demo_active;
    }
}