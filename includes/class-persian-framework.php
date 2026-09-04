<?php
/**
 * Persian Framework - Main Class (Multi-Instance)
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework {

    // ✅ Store multiple instances
    private static $instances = array();

    // ✅ Each instance has its own properties
    private $config = array();
    private $sections = array();
    private $loaded = false;
    private $instance_id = '';

    const VERSION = '3.0.0';
    const OPTION_NAME = 'persian_framework_options';
    const THEME_OPTION = 'persian_framework_theme';

    /**
     * Get instance by ID
     *
     * @param string $instance_id
     * @return PersianFramework
     */
    public static function get_instance($instance_id = 'default') {
        if (!isset(self::$instances[$instance_id])) {
            self::$instances[$instance_id] = new self($instance_id);
        }
        return self::$instances[$instance_id];
    }

    /**
     * Constructor - Private
     *
     * @param string $instance_id
     */
    private function __construct($instance_id) {
        $this->instance_id = $instance_id;
        $this->define_constants();
        $this->load_dependencies();
        $this->init_hooks();
        $this->init_components();
    }

    /**
     * Get instance ID
     */
    public function get_instance_id() {
        return $this->instance_id;
    }

    private function define_constants() {
        if (!defined('PERSIAN_FRAMEWORK_VERSION')) {
            define('PERSIAN_FRAMEWORK_VERSION', self::VERSION);
        }
        if (!defined('PERSIAN_FRAMEWORK_PATH')) {
            define('PERSIAN_FRAMEWORK_PATH', plugin_dir_path(dirname(__FILE__)));
        }
        if (!defined('PERSIAN_FRAMEWORK_URL')) {
            define('PERSIAN_FRAMEWORK_URL', plugin_dir_url(dirname(__FILE__)));
        }
        if (!defined('PERSIAN_FRAMEWORK_INCLUDES')) {
            define('PERSIAN_FRAMEWORK_INCLUDES', PERSIAN_FRAMEWORK_PATH . 'includes/');
        }
        if (!defined('PERSIAN_FRAMEWORK_FIELDS')) {
            define('PERSIAN_FRAMEWORK_FIELDS', PERSIAN_FRAMEWORK_PATH . 'fields/');
        }
        if (!defined('PERSIAN_FRAMEWORK_TEMPLATES')) {
            define('PERSIAN_FRAMEWORK_TEMPLATES', PERSIAN_FRAMEWORK_PATH . 'templates/');
        }
        if (!defined('PERSIAN_FRAMEWORK_CONFIG')) {
            define('PERSIAN_FRAMEWORK_CONFIG', PERSIAN_FRAMEWORK_PATH . 'config/');
        }
        if (!defined('PERSIAN_FRAMEWORK_ASSETS')) {
            define('PERSIAN_FRAMEWORK_ASSETS', PERSIAN_FRAMEWORK_URL . 'assets/');
        }
        if (!defined('PERSIAN_FRAMEWORK_LANGUAGES')) {
            define('PERSIAN_FRAMEWORK_LANGUAGES', PERSIAN_FRAMEWORK_PATH . 'languages/');
        }
    }

    private function load_dependencies() {
        $files = array(
            'class-framework-loader.php',
            'class-framework-admin.php',
            'class-framework-fields.php',
            'class-framework-sanitize.php',
            'class-framework-ajax.php',
            'class-framework-export-import.php',
            'class-framework-theme.php',
            'class-framework-translations.php',
            'class-framework-api.php',
            'class-framework-demo.php',
            'class-framework-required.php',
        );

        foreach ($files as $file) {
            $path = PERSIAN_FRAMEWORK_INCLUDES . $file;
            if (file_exists($path)) {
                require_once $path;
            }
        }
    }

    private function init_hooks() {
        register_activation_hook(PERSIAN_FRAMEWORK_PATH . 'persian-framework.php', array($this, 'activate'));
        register_deactivation_hook(PERSIAN_FRAMEWORK_PATH . 'persian-framework.php', array($this, 'deactivate'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('init', array($this, 'load_config'), 5);
        add_action('admin_menu', array($this, 'register_admin_menu'), 10);
    }

    private function init_components() {
        if (class_exists('PersianFramework_Loader')) {
            PersianFramework_Loader::get_instance();
        }
        if (class_exists('PersianFramework_Admin')) {
            PersianFramework_Admin::get_instance();
        }
        if (class_exists('PersianFramework_Fields')) {
            PersianFramework_Fields::get_instance();
        }
        if (class_exists('PersianFramework_Translations')) {
            PersianFramework_Translations::get_instance();
        }
        if (class_exists('PersianFramework_Export_Import')) {
            PersianFramework_Export_Import::get_instance();
        }
        if (class_exists('PersianFramework_Demo')) {
            PersianFramework_Demo::get_instance();
        }
        if (class_exists('PersianFramework_Required')) {
            PersianFramework_Required::enqueue_scripts();
        }
    }

    public function load_textdomain() {
        load_plugin_textdomain('persian-framework', false, dirname(plugin_basename(PERSIAN_FRAMEWORK_PATH)) . '/languages/');
    }

    public function activate() {
        $opt_name = $this->get_opt_name();
        if (!get_option($opt_name)) {
            update_option($opt_name, $this->get_default_options());
        }
        do_action('persian_framework_activate', $this->instance_id);
    }

    public function deactivate() {
        do_action('persian_framework_deactivate', $this->instance_id);
    }

    public function load_config() {
        if ($this->loaded) {
            return;
        }

        // Only load default config if this is the default instance and config is empty
        if ($this->instance_id === 'default' && empty($this->config) && empty($this->sections)) {
            $sections_file = PERSIAN_FRAMEWORK_CONFIG . 'sections.php';
            if (file_exists($sections_file)) {
                include_once $sections_file;
            }
        }

        $this->loaded = true;
    }

    private function get_default_options() {
        $defaults = array();
        $defaults_file = PERSIAN_FRAMEWORK_CONFIG . 'defaults.php';
        if (file_exists($defaults_file)) {
            $defaults = include $defaults_file;
        }
        return apply_filters('persian_framework_default_options', $defaults, $this->instance_id);
    }

    /**
     * Set configuration arguments
     */
    public function set_args($args) {
        $this->config = wp_parse_args($args, array(
            'opt_name' => 'persian_framework_options_' . $this->instance_id,
            'display_name' => 'Persian Framework',
            'display_version' => self::VERSION,
            'menu_title' => 'Settings',
            'page_title' => 'Settings',
            'menu_slug' => 'persian-framework-' . $this->instance_id,
            'menu_icon' => 'dashicons-admin-generic',
            'menu_position' => null,
            'capability' => 'manage_options',
            'show_import_export' => true,
            'show_backup' => true,
            'dev_mode' => false,
            'global_variable' => null,
        ));

        // Initialize global variable if configured
        $this->setup_global_variable();
    }

    /**
     * Setup global variable for fast option access
     */
    private function setup_global_variable() {
        $global_var = $this->config['global_variable'];

        if (empty($global_var) || !is_string($global_var)) {
            return;
        }

        // Load options into global variable
        $GLOBALS[$global_var] = $this->get_all_options();

        // Auto-sync global variable on option updates
        add_action('update_option_' . $this->get_opt_name(), function($old_value, $new_value) use ($global_var) {
            $GLOBALS[$global_var] = $new_value;
        }, 10, 2);
    }

    /**
     * Get option from global variable (fallback to database)
     */
    public function get_global_option($key, $default = null) {
        $global_var = $this->config['global_variable'];

        // Try global variable first
        if (!empty($global_var) && isset($GLOBALS[$global_var]) && is_array($GLOBALS[$global_var])) {
            return isset($GLOBALS[$global_var][$key]) ? $GLOBALS[$global_var][$key] : $default;
        }

        // Fallback to database
        return $this->get_option($key, $default);
    }

    /**
     * Set option and sync global variable
     */
    public function set_option($key, $value) {
        $opt_name = $this->get_opt_name();
        $options = get_option($opt_name, array());
        $options[$key] = $value;
        $result = update_option($opt_name, $options);

        // Update global variable if saved
        if ($result) {
            $global_var = $this->config['global_variable'];
            if (!empty($global_var) && is_string($global_var)) {
                $GLOBALS[$global_var] = $options;
            }
        }

        return $result;
    }

    /**
     * Add a section
     */
    public function add_section($section) {
        $this->sections[] = wp_parse_args($section, array(
            'id' => '',
            'title' => '',
            'icon' => '',
            'fields' => array(),
        ));
    }

    /**
     * Get configuration
     */
    public function get_config() {
        return $this->config;
    }

    /**
     * Get sections
     */
    public function get_sections() {
        return $this->sections;
    }

    /**
     * Get option name
     */
    public function get_opt_name() {
        return isset($this->config['opt_name']) ? $this->config['opt_name'] : 'persian_framework_options_' . $this->instance_id;
    }

    /**
     * Get a single option value
     */
    public function get_option($key, $default = null) {
        $opt_name = $this->get_opt_name();
        $options = get_option($opt_name, array());
        return isset($options[$key]) ? $options[$key] : $default;
    }

    /**
     * Get all options
     */
    public function get_all_options() {
        $opt_name = $this->get_opt_name();
        return get_option($opt_name, array());
    }

    /**
     * Register admin menu
     */
    public function register_admin_menu() {
        $config = $this->get_config();

        if (empty($config)) {
            return;
        }

        // ✅ Check if this is the main framework instance and demo is not active
        $is_main_instance = ($this->instance_id === 'persian-framework' || $this->instance_id === 'default');
        $is_demo_active = get_option('persian_framework_demo_active', false);

        // ✅ If demo is not active, hide the main menu
        if ($is_main_instance && !$is_demo_active) {
            // Still register but with 'none' capability to hide it
            add_menu_page(
                $config['page_title'] ?? 'Persian Framework',
                $config['menu_title'] ?? 'Persian Framework',
                'manage_options', // Still need capability for direct access
                $config['menu_slug'] ?? 'persian-framework',
                array($this, 'render_admin_page'),
                $config['menu_icon'] ?? 'dashicons-admin-generic',
                $config['menu_position'] ?? 59
            );

            // ✅ Hide the menu by removing it from the global menu array
            add_action('admin_menu', array($this, 'hide_menu'), 999);
            return;
        }

        // Normal registration when demo is active
        add_menu_page(
            $config['page_title'] ?? 'Persian Framework',
            $config['menu_title'] ?? 'Persian Framework',
            $config['capability'] ?? 'manage_options',
            $config['menu_slug'] ?? 'persian-framework',
            array($this, 'render_admin_page'),
            $config['menu_icon'] ?? 'dashicons-admin-generic',
            $config['menu_position'] ?? 59
        );
    }

    /**
     * ✅ Hide the main menu from admin sidebar
     */
    public function hide_menu() {
        global $menu, $submenu;

        $menu_slug = $this->config['menu_slug'] ?? 'persian-framework';

        // Remove from main menu
        foreach ($menu as $key => $item) {
            if (isset($item[2]) && $item[2] === $menu_slug) {
                unset($menu[$key]);
                break;
            }
        }

        // Remove from submenu as well
        foreach ($submenu as $parent => $items) {
            foreach ($items as $key => $item) {
                if (isset($item[2]) && $item[2] === $menu_slug) {
                    unset($submenu[$parent][$key]);
                    break;
                }
            }
        }
    }

    /**
     * ✅ Override render_admin_page to show activation notice if not active
     */
    public function render_admin_page() {
        $is_demo_active = get_option('persian_framework_demo_active', false);
        $is_main_instance = ($this->instance_id === 'persian-framework' || $this->instance_id === 'default');

        // ✅ If demo is not active, show activation notice instead of the settings page
        if ($is_main_instance && !$is_demo_active) {
            $this->render_activation_notice();
            return;
        }

        // Normal rendering when demo is active
        if (class_exists('PersianFramework_Admin')) {
            $admin = PersianFramework_Admin::get_instance();
            $admin->render_page($this);
        }
    }

    /**
     * ✅ Render activation notice when demo is not active
     */
    private function render_activation_notice() {
        ?>
        <div class="wrap pf-demo-activation-notice" style="max-width:800px;margin:40px auto;">
            <div style="
            background: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            text-align: center;
        ">
                <div style="
                width: 80px;
                height: 80px;
                background: #f3f4f6;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
            ">
                    <span class="dashicons dashicons-admin-generic" style="font-size:48px;width:48px;height:48px;color:#6366f1;"></span>
                </div>

                <h1 style="font-size:28px;font-weight:700;color:#1a2332;margin:0 0 12px;">
                    <?php _e('Welcome to Persian Framework!', 'persian-framework'); ?>
                </h1>

                <p style="font-size:16px;color:#4a5568;line-height:1.6;max-width:500px;margin:0 auto 30px;">
                    <?php _e('Activate the demo to explore all 40+ professional field types and see the framework in action.', 'persian-framework'); ?>
                </p>

                <div style="
                background: #f8f9fa;
                border-left: 4px solid #6366f1;
                padding: 20px 24px;
                margin: 0 auto 30px;
                border-radius: 4px;
                text-align:left;
                max-width:500px;
            ">
                    <strong style="display:block;margin-bottom:8px;"><?php _e('Demo Features:', 'persian-framework'); ?></strong>
                    <ul style="margin:0;padding-left:20px;list-style:disc;color:#4a5568;">
                        <li><?php _e('40+ professional field types', 'persian-framework'); ?></li>
                        <li><?php _e('Live preview examples', 'persian-framework'); ?></li>
                        <li><?php _e('Repeater and sorter demos', 'persian-framework'); ?></li>
                        <li><?php _e('Media, gallery, and image fields', 'persian-framework'); ?></li>
                        <li><?php _e('Typography and color controls', 'persian-framework'); ?></li>
                    </ul>
                </div>

                <a href="<?php echo admin_url('options-general.php?page=persian-framework-demo'); ?>"
                   class="button button-primary button-hero"
                   style="
                   background: #6366f1;
                   border-color: #6366f1;
                   padding: 12px 40px;
                   font-size: 16px;
                   min-height: 50px;
                   line-height: 2.5;
               ">
                    <span class="dashicons dashicons-yes" style="font-size:18px;width:18px;height:18px;vertical-align:middle;margin-top:-2px;"></span>
                    <?php _e('Activate Demo', 'persian-framework'); ?>
                </a>

                <p style="margin-top:20px;font-size:13px;color:#94a3b8;">
                    <?php _e('Demo activation will create a separate menu item with complete field examples.', 'persian-framework'); ?>
                </p>
            </div>
        </div>
        <style>
            .pf-demo-activation-notice .button-hero {
                font-size: 16px !important;
                line-height: 2.5 !important;
                min-height: 50px !important;
                padding: 0 40px !important;
            }
            .pf-demo-activation-notice ul li {
                margin-bottom: 4px;
            }
        </style>
        <?php
    }
}