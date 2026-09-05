<?php
/**
 * Persian Framework - Theme Class
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Theme {

    private static $instance = null;
    private $themes = array();
    private $current_theme = 'default';

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->register_themes();
        $this->load_current_theme();
        add_action('admin_enqueue_scripts', array($this, 'enqueue_theme_style'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_theme_style'));
        add_filter('persian_framework_theme_classes', array($this, 'get_theme_classes'));
    }

    private function register_themes() {
        $this->themes = array(
            'default' => array(
                'name' => esc_html__('Default Theme', 'persian-framework'),
                'description' => esc_html__('Clean and modern design', 'persian-framework'),
                'css' => 'default.css',
                'colors' => array(
                    'primary' => '#6366f1',
                    'secondary' => '#8b5cf6',
                    'background' => '#ffffff',
                    'text' => '#1a2332',
                )
            ),
            'dark' => array(
                'name' => esc_html__('Dark Theme', 'persian-framework'),
                'description' => esc_html__('Dark mode design', 'persian-framework'),
                'css' => 'dark.css',
                'colors' => array(
                    'primary' => '#818cf8',
                    'secondary' => '#a78bfa',
                    'background' => '#0f172a',
                    'text' => '#e2e8f0',
                )
            ),
            'ocean' => array(
                'name' => esc_html__('Ocean Theme', 'persian-framework'),
                'description' => esc_html__('Ocean blue design', 'persian-framework'),
                'css' => 'ocean.css',
                'colors' => array(
                    'primary' => '#0284c7',
                    'secondary' => '#0ea5e9',
                    'background' => '#f0f9ff',
                    'text' => '#0c4a6e',
                )
            ),
            'forest' => array(
                'name' => esc_html__('Forest Theme', 'persian-framework'),
                'description' => esc_html__('Natural green design', 'persian-framework'),
                'css' => 'forest.css',
                'colors' => array(
                    'primary' => '#059669',
                    'secondary' => '#10b981',
                    'background' => '#ecfdf5',
                    'text' => '#064e3b',
                )
            ),
            'sunset' => array(
                'name' => esc_html__('Sunset Theme', 'persian-framework'),
                'description' => esc_html__('Warm sunset design', 'persian-framework'),
                'css' => 'sunset.css',
                'colors' => array(
                    'primary' => '#db2777',
                    'secondary' => '#f472b6',
                    'background' => '#fdf2f8',
                    'text' => '#9d174d',
                )
            ),
            'minimal' => array(
                'name' => esc_html__('Minimal Theme', 'persian-framework'),
                'description' => esc_html__('Minimalist design', 'persian-framework'),
                'css' => 'minimal.css',
                'colors' => array(
                    'primary' => '#475569',
                    'secondary' => '#94a3b8',
                    'background' => '#f8fafc',
                    'text' => '#1e293b',
                )
            ),
        );
    }

    private function load_current_theme() {
        $theme = get_option(PERSIAN_FRAMEWORK_THEME, 'default');
        if (isset($this->themes[$theme])) {
            $this->current_theme = $theme;
        } else {
            $this->current_theme = 'default';
        }
    }

    public function enqueue_theme_style() {
        $theme = $this->get_current_theme();
        if (isset($this->themes[$theme])) {
            $css_file = $this->themes[$theme]['css'];
            $css_path = PERSIAN_FRAMEWORK_ASSETS . 'css/themes/' . $css_file;

            if (file_exists(PERSIAN_FRAMEWORK_PATH . 'assets/css/themes/' . $css_file)) {
                wp_enqueue_style(
                    'pf-theme-' . $theme,
                    $css_path,
                    array(),
                    PERSIAN_FRAMEWORK_VERSION
                );
            }
        }
    }

    public function get_theme_classes($classes = '') {
        $theme = $this->get_current_theme();
        $classes .= ' pf-theme-' . $theme;
        return $classes;
    }

    public function get_current_theme() {
        return $this->current_theme;
    }

    public function get_themes() {
        return $this->themes;
    }

    public function get_theme_colors() {
        if (isset($this->themes[$this->current_theme])) {
            return $this->themes[$this->current_theme]['colors'];
        }
        return $this->themes['default']['colors'];
    }

    public function switch_theme($theme_slug) {
        if (isset($this->themes[$theme_slug])) {
            update_option(PERSIAN_FRAMEWORK_THEME, $theme_slug);
            $this->current_theme = $theme_slug;
            return true;
        }
        return false;
    }
}