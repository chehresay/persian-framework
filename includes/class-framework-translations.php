<?php
/**
 * Persian Framework - Translations Class
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Translations {

    private static $instance = null;
    private $translations = array();
    private $current_language = 'en_US';

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->detect_language();
        $this->load_translations();
        $this->setup_filters();
    }

    private function detect_language() {
        $locale = get_locale();
        $this->current_language = $locale;
    }

    private function load_translations() {
        $file = PERSIAN_FRAMEWORK_LANGUAGES . 'translations-' . $this->current_language . '.php';
        if (file_exists($file)) {
            $this->translations = include $file;
        }
    }

    private function setup_filters() {
        add_filter('gettext', array($this, 'translate_text'), 10, 3);
        add_filter('gettext_with_context', array($this, 'translate_with_context'), 10, 4);
        add_filter('ngettext', array($this, 'translate_plural'), 10, 5);
    }

    public function translate_text($translated, $text, $domain) {
        if ($domain !== 'persian-framework') {
            return $translated;
        }
        if (isset($this->translations[$text])) {
            return $this->translations[$text];
        }
        return $translated;
    }

    public function translate_with_context($translated, $text, $context, $domain) {
        if ($domain !== 'persian-framework') {
            return $translated;
        }
        $key = $context . ':' . $text;
        if (isset($this->translations[$key])) {
            return $this->translations[$key];
        }
        return $this->translate_text($translated, $text, $domain);
    }

    public function translate_plural($translated, $single, $plural, $number, $domain) {
        if ($domain !== 'persian-framework') {
            return $translated;
        }
        if ($number == 1 && isset($this->translations[$single])) {
            return $this->translations[$single];
        } elseif ($number > 1 && isset($this->translations[$plural])) {
            return $this->translations[$plural];
        }
        return $translated;
    }
}