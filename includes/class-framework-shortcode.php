<?php
/**
 * Persian Framework - Shortcode Class
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Shortcode {

    private static $instance = null;
    private $shortcodes = array();
    private $default_shortcodes = array();

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->register_default_shortcodes();
        add_action('init', array($this, 'register_shortcodes'));
        add_filter('widget_text', 'do_shortcode');
        add_filter('the_content', array($this, 'process_shortcodes_in_content'));
    }

    /**
     * Register default shortcodes
     */
    private function register_default_shortcodes() {
        $this->default_shortcodes = array(
            'pf_option' => array(
                'callback' => array($this, 'shortcode_option'),
                'description' => esc_html__('Display an option value', 'persian-framework'),
                'attributes' => array(
                    'key' => array(
                        'required' => true,
                        'description' => esc_html__('Option key to display', 'persian-framework')
                    ),
                    'default' => array(
                        'default' => '',
                        'description' => esc_html__('Default value if option is empty', 'persian-framework')
                    )
                )
            ),
            'pf_if' => array(
                'callback' => array($this, 'shortcode_if'),
                'description' => esc_html__('Conditional content based on option value', 'persian-framework'),
                'attributes' => array(
                    'key' => array(
                        'required' => true,
                        'description' => esc_html__('Option key to check', 'persian-framework')
                    ),
                    'value' => array(
                        'required' => true,
                        'description' => esc_html__('Value to compare against', 'persian-framework')
                    ),
                    'operator' => array(
                        'default' => '=',
                        'description' => esc_html__('Comparison operator: =, !=, >, <, >=, <=', 'persian-framework')
                    )
                )
            ),
            'pf_if_not' => array(
                'callback' => array($this, 'shortcode_if_not'),
                'description' => esc_html__('Conditional content if option value is not equal', 'persian-framework'),
                'attributes' => array(
                    'key' => array(
                        'required' => true,
                        'description' => esc_html__('Option key to check', 'persian-framework')
                    ),
                    'value' => array(
                        'required' => true,
                        'description' => esc_html__('Value to compare against', 'persian-framework')
                    )
                )
            ),
            'pf_repeater' => array(
                'callback' => array($this, 'shortcode_repeater'),
                'description' => esc_html__('Loop through repeater field items', 'persian-framework'),
                'attributes' => array(
                    'key' => array(
                        'required' => true,
                        'description' => esc_html__('Repeater option key', 'persian-framework')
                    ),
                    'field' => array(
                        'required' => true,
                        'description' => esc_html__('Field to display from repeater', 'persian-framework')
                    ),
                    'separator' => array(
                        'default' => ', ',
                        'description' => esc_html__('Separator between items', 'persian-framework')
                    )
                )
            ),
            'pf_sorter' => array(
                'callback' => array($this, 'shortcode_sorter'),
                'description' => esc_html__('Display sorter field items', 'persian-framework'),
                'attributes' => array(
                    'key' => array(
                        'required' => true,
                        'description' => esc_html__('Sorter option key', 'persian-framework')
                    ),
                    'column' => array(
                        'required' => true,
                        'description' => esc_html__('Column to display from sorter', 'persian-framework')
                    ),
                    'separator' => array(
                        'default' => ', ',
                        'description' => esc_html__('Separator between items', 'persian-framework')
                    )
                )
            ),
            'pf_map' => array(
                'callback' => array($this, 'shortcode_map'),
                'description' => esc_html__('Display a Google Map', 'persian-framework'),
                'attributes' => array(
                    'key' => array(
                        'required' => true,
                        'description' => esc_html__('Map option key', 'persian-framework')
                    ),
                    'width' => array(
                        'default' => '100%',
                        'description' => esc_html__('Map width', 'persian-framework')
                    ),
                    'height' => array(
                        'default' => '400px',
                        'description' => esc_html__('Map height', 'persian-framework')
                    ),
                    'zoom' => array(
                        'default' => '14',
                        'description' => esc_html__('Map zoom level', 'persian-framework')
                    )
                )
            ),
            'pf_gallery' => array(
                'callback' => array($this, 'shortcode_gallery'),
                'description' => esc_html__('Display a gallery', 'persian-framework'),
                'attributes' => array(
                    'key' => array(
                        'required' => true,
                        'description' => esc_html__('Gallery option key', 'persian-framework')
                    ),
                    'columns' => array(
                        'default' => '3',
                        'description' => esc_html__('Number of columns', 'persian-framework')
                    ),
                    'size' => array(
                        'default' => 'medium',
                        'description' => esc_html__('Image size: thumbnail, medium, large, full', 'persian-framework')
                    ),
                    'link' => array(
                        'default' => 'file',
                        'description' => esc_html__('Link to: file, attachment, none', 'persian-framework')
                    )
                )
            ),
            'pf_image' => array(
                'callback' => array($this, 'shortcode_image'),
                'description' => esc_html__('Display an image', 'persian-framework'),
                'attributes' => array(
                    'key' => array(
                        'required' => true,
                        'description' => esc_html__('Image option key', 'persian-framework')
                    ),
                    'size' => array(
                        'default' => 'medium',
                        'description' => esc_html__('Image size', 'persian-framework')
                    ),
                    'class' => array(
                        'default' => '',
                        'description' => esc_html__('CSS class', 'persian-framework')
                    )
                )
            ),
            'pf_color' => array(
                'callback' => array($this, 'shortcode_color'),
                'description' => esc_html__('Display a color value with style', 'persian-framework'),
                'attributes' => array(
                    'key' => array(
                        'required' => true,
                        'description' => esc_html__('Color option key', 'persian-framework')
                    ),
                    'type' => array(
                        'default' => 'text',
                        'description' => esc_html__('Output type: text, background, color, box', 'persian-framework')
                    )
                )
            )
        );
    }

    /**
     * Register a custom shortcode
     */
    public function register($tag, $callback, $description = '', $attributes = array()) {
        $this->shortcodes[$tag] = array(
            'callback' => $callback,
            'description' => $description,
            'attributes' => $attributes
        );
        return $this;
    }

    /**
     * Register all shortcodes
     */
    public function register_shortcodes() {
        $all_shortcodes = array_merge($this->default_shortcodes, $this->shortcodes);

        foreach ($all_shortcodes as $tag => $shortcode) {
            add_shortcode($tag, $shortcode['callback']);
        }
    }

    /**
     * Process shortcodes in content
     */
    public function process_shortcodes_in_content($content) {
        $content = preg_replace_callback(
            '/\[pf_option\s+([^\]]+)\]/',
            array($this, 'process_option_shortcode'),
            $content
        );
        return $content;
    }

    /**
     * Process option shortcode
     */
    private function process_option_shortcode($matches) {
        $atts = shortcode_parse_atts($matches[1]);
        if (isset($atts['key'])) {
            return pf_get($atts['key'], isset($atts['default']) ? $atts['default'] : '');
        }
        return $matches[0];
    }

    /**
     * Shortcode: pf_option
     */
    public function shortcode_option($atts) {
        $atts = shortcode_atts(array(
            'key' => '',
            'default' => ''
        ), $atts);

        if (empty($atts['key'])) {
            return '';
        }

        return pf_get($atts['key'], $atts['default']);
    }

    /**
     * Shortcode: pf_if
     */
    public function shortcode_if($atts, $content = null) {
        $atts = shortcode_atts(array(
            'key' => '',
            'value' => '',
            'operator' => '='
        ), $atts);

        if (empty($atts['key']) || empty($content)) {
            return '';
        }

        $option = pf_get($atts['key']);
        $value = $atts['value'];

        $condition = false;
        switch ($atts['operator']) {
            case '=':
                $condition = ($option == $value);
                break;
            case '!=':
                $condition = ($option != $value);
                break;
            case '>':
                $condition = ($option > $value);
                break;
            case '<':
                $condition = ($option < $value);
                break;
            case '>=':
                $condition = ($option >= $value);
                break;
            case '<=':
                $condition = ($option <= $value);
                break;
        }

        return $condition ? do_shortcode($content) : '';
    }

    /**
     * Shortcode: pf_if_not
     */
    public function shortcode_if_not($atts, $content = null) {
        $atts = shortcode_atts(array(
            'key' => '',
            'value' => ''
        ), $atts);

        if (empty($atts['key']) || empty($content)) {
            return '';
        }

        $option = pf_get($atts['key']);

        return ($option != $atts['value']) ? do_shortcode($content) : '';
    }

    /**
     * Shortcode: pf_repeater
     */
    public function shortcode_repeater($atts) {
        $atts = shortcode_atts(array(
            'key' => '',
            'field' => '',
            'separator' => ', '
        ), $atts);

        if (empty($atts['key']) || empty($atts['field'])) {
            return '';
        }

        $repeater = pf_get($atts['key'], array());
        if (!is_array($repeater) || empty($repeater)) {
            return '';
        }

        $items = array();
        foreach ($repeater as $item) {
            if (isset($item[$atts['field']])) {
                $items[] = $item[$atts['field']];
            }
        }

        return implode($atts['separator'], $items);
    }

    /**
     * Shortcode: pf_sorter
     */
    public function shortcode_sorter($atts) {
        $atts = shortcode_atts(array(
            'key' => '',
            'column' => '',
            'separator' => ', '
        ), $atts);

        if (empty($atts['key']) || empty($atts['column'])) {
            return '';
        }

        $sorter = pf_get($atts['key'], array());
        if (!is_array($sorter) || empty($sorter)) {
            return '';
        }

        if (isset($sorter[$atts['column']]) && is_array($sorter[$atts['column']])) {
            $items = array_keys($sorter[$atts['column']]);
            return implode($atts['separator'], $items);
        }

        return '';
    }

    /**
     * Shortcode: pf_map
     */
    public function shortcode_map($atts) {
        $atts = shortcode_atts(array(
            'key' => '',
            'width' => '100%',
            'height' => '400px',
            'zoom' => '14'
        ), $atts);

        if (empty($atts['key'])) {
            return '';
        }

        $map = pf_get($atts['key']);
        if (empty($map) || !isset($map['lat']) || !isset($map['lng'])) {
            return '';
        }

        $lat = $map['lat'];
        $lng = $map['lng'];

        $output = '<div class="pf-map-container" style="width:' . esc_attr($atts['width']) . ';height:' . esc_attr($atts['height']) . ';">';
        $output .= '<iframe 
            width="100%" 
            height="100%" 
            frameborder="0" 
            style="border:0;border-radius:12px;"
            src="https://www.google.com/maps/embed/v1/place?key=' . esc_attr($this->get_google_api_key()) . '&q=' . urlencode($lat . ',' . $lng) . '&zoom=' . intval($atts['zoom']) . '"
            allowfullscreen>
        </iframe>';
        $output .= '</div>';

        return $output;
    }

    /**
     * Get Google API key
     */
    private function get_google_api_key() {
        return apply_filters('pf_google_api_key', '');
    }

    /**
     * Shortcode: pf_gallery
     */
    public function shortcode_gallery($atts) {
        $atts = shortcode_atts(array(
            'key' => '',
            'columns' => '3',
            'size' => 'medium',
            'link' => 'file'
        ), $atts);

        if (empty($atts['key'])) {
            return '';
        }

        $gallery = pf_get($atts['key'], array());
        if (!is_array($gallery) || empty($gallery)) {
            return '';
        }

        $output = '<div class="pf-gallery" style="display:grid;grid-template-columns:repeat(' . intval($atts['columns']) . ',1fr);gap:16px;">';

        foreach ($gallery as $image) {
            if (is_array($image) && isset($image['id'])) {
                $img = wp_get_attachment_image_src($image['id'], $atts['size']);
                if ($img) {
                    $url = $img[0];
                    $full = wp_get_attachment_image_src($image['id'], 'full');
                    $full_url = $full ? $full[0] : $url;

                    $output .= '<div class="pf-gallery-item">';
                    if ($atts['link'] === 'file') {
                        $output .= '<a href="' . esc_url($full_url) . '" target="_blank">';
                    } elseif ($atts['link'] === 'attachment') {
                        $output .= '<a href="' . get_attachment_link($image['id']) . '">';
                    }
                    $output .= '<img src="' . esc_url($url) . '" alt="" style="width:100%;height:auto;border-radius:12px;display:block;">';
                    if ($atts['link'] !== 'none') {
                        $output .= '</a>';
                    }
                    $output .= '</div>';
                }
            }
        }

        $output .= '</div>';
        return $output;
    }

    /**
     * Shortcode: pf_image
     */
    public function shortcode_image($atts) {
        $atts = shortcode_atts(array(
            'key' => '',
            'size' => 'medium',
            'class' => ''
        ), $atts);

        if (empty($atts['key'])) {
            return '';
        }

        $image = pf_get($atts['key']);
        if (empty($image)) {
            return '';
        }

        if (is_array($image) && isset($image['id'])) {
            $img = wp_get_attachment_image($image['id'], $atts['size'], false, array('class' => $atts['class']));
            if ($img) {
                return $img;
            }
            if (isset($image['url'])) {
                return '<img src="' . esc_url($image['url']) . '" class="' . esc_attr($atts['class']) . '" alt="">';
            }
        }

        if (is_string($image) && filter_var($image, FILTER_VALIDATE_URL)) {
            return '<img src="' . esc_url($image) . '" class="' . esc_attr($atts['class']) . '" alt="">';
        }

        return '';
    }

    /**
     * Shortcode: pf_color
     */
    public function shortcode_color($atts) {
        $atts = shortcode_atts(array(
            'key' => '',
            'type' => 'text'
        ), $atts);

        if (empty($atts['key'])) {
            return '';
        }

        $color = pf_get($atts['key']);
        if (empty($color)) {
            return '';
        }

        switch ($atts['type']) {
            case 'background':
                return '<span style="display:inline-block;width:20px;height:20px;background:' . esc_attr($color) . ';border-radius:4px;"></span>';
            case 'color':
                return '<span style="color:' . esc_attr($color) . ';">' . esc_html($color) . '</span>';
            case 'box':
                return '<div style="width:100px;height:100px;background:' . esc_attr($color) . ';border-radius:12px;border:2px solid #e8edf4;"></div>';
            default:
                return esc_html($color);
        }
    }

    /**
     * Get shortcode documentation
     */
    public function get_documentation() {
        $docs = array();
        $all_shortcodes = array_merge($this->default_shortcodes, $this->shortcodes);

        foreach ($all_shortcodes as $tag => $shortcode) {
            $docs[$tag] = array(
                'tag' => $tag,
                'description' => $shortcode['description'],
                'attributes' => isset($shortcode['attributes']) ? $shortcode['attributes'] : array(),
                'example' => '[' . $tag . ' ' . $this->build_example_attributes($shortcode) . ']'
            );
        }

        return $docs;
    }

    /**
     * Build example attributes
     */
    private function build_example_attributes($shortcode) {
        $attrs = array();
        if (isset($shortcode['attributes'])) {
            foreach ($shortcode['attributes'] as $key => $attr) {
                if (isset($attr['default'])) {
                    $attrs[] = $key . '="' . $attr['default'] . '"';
                } elseif (isset($attr['required']) && $attr['required']) {
                    $attrs[] = $key . '="' . $key . '"';
                }
            }
        }
        return implode(' ', $attrs);
    }
}