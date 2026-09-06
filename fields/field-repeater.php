<?php
/**
 * Persian Framework - Repeater Field
 * Create repeatable groups of fields with drag & drop sorting
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Repeater {

    private $field;
    private $value;
    private static $instance_counter = 0;

    public function __construct($field, $value = null) {
        $this->field = $field;
        $this->value = $value;
        self::$instance_counter++;
    }

    public function render() {
        $id = isset($this->field['id']) ? $this->field['id'] : '';
        $name = isset($this->field['name']) ? $this->field['name'] : $id;
        $fields = isset($this->field['fields']) ? $this->field['fields'] : array();

        $value = $this->normalize_value($this->value);

        $max_items = isset($this->field['max']) ? intval($this->field['max']) : 0;
        $min_items = isset($this->field['min']) ? intval($this->field['min']) : 0;
        $button_text = isset($this->field['button_text']) ? $this->field['button_text'] : esc_html__('Add Item', 'persian-framework');
        $sortable = isset($this->field['sortable']) ? $this->field['sortable'] : true;
        $collapsible = isset($this->field['collapsible']) ? $this->field['collapsible'] : true;
        $title_field = isset($this->field['title_field']) ? $this->field['title_field'] : '';

        foreach ($fields as &$field_config) {
            if (isset($field_config['type']) && $field_config['type'] === 'slider') {
                if (!isset($field_config['default']) && isset($field_config['min'])) {
                    $field_config['default'] = $field_config['min'];
                }
            }
        }
        unset($field_config);

        $instance_id = 'repeater-' . $id . '-' . self::$instance_counter;
        $unique_id = $instance_id;

        if (empty($value) && $min_items > 0) {
            $value = array_fill(0, $min_items, array());
        }

        $field_options_cache = array();
        foreach ($fields as $field_config) {
            if (isset($field_config['type']) && $field_config['type'] === 'select') {
                $field_id = $field_config['id'];
                $field_options_cache[$field_id] = $this->get_select_options($field_config);
            }
        }

        ?>
        <div class="pf-field-wrapper pf-field-repeater"
             data-id="<?php echo esc_attr($id); ?>"
             data-instance="<?php echo esc_attr(self::$instance_counter); ?>"
             data-max="<?php echo esc_attr($max_items); ?>"
             data-min="<?php echo esc_attr($min_items); ?>"
             data-sortable="<?php echo $sortable ? 'true' : 'false'; ?>"
             data-collapsible="<?php echo $collapsible ? 'true' : 'false'; ?>"
             data-field-options="<?php echo esc_attr(json_encode($field_options_cache)); ?>">

            <?php if (isset($this->field['title'])): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                    <?php if ($max_items > 0): ?>
                        <span class="pf-repeater-max-label">
                            <?php
                            /* translators: %d: maximum number of items allowed */
                            printf( esc_html__( '(Max: %d)', 'persian-framework' ), esc_html( $max_items ) );
                            ?>
                        </span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-repeater-container">
                <div class="pf-repeater-items" id="<?php echo esc_attr($unique_id); ?>">
                    <?php
                    $index = 0;
                    foreach ($value as $item):
                        if (!is_array($item)) {
                            $item = array();
                        }
                        ?>
                        <div class="pf-repeater-item" data-index="<?php echo esc_attr($index); ?>">
                            <div class="pf-repeater-item-header">
                                <?php if ($sortable): ?>
                                    <span class="pf-repeater-handle dashicons dashicons-move"></span>
                                <?php endif; ?>
                                <span class="pf-repeater-item-title">
                                    <?php
                                    if ($title_field && isset($item[$title_field]) && !empty($item[$title_field])) {
                                        echo esc_html($item[$title_field]);
                                    } else {
                                        /* translators: %d: item number */
                                        printf( esc_html__( 'Item %d', 'persian-framework' ), esc_html( $index + 1 ) );
                                    }
                                    ?>
                                </span>
                                <?php if ($collapsible): ?>
                                    <button type="button" class="pf-repeater-toggle">
                                        <span class="dashicons dashicons-arrow-down-alt2"></span>
                                    </button>
                                <?php endif; ?>
                                <button type="button" class="pf-repeater-remove" aria-label="<?php esc_attr_e('Remove', 'persian-framework'); ?>">
                                    <span class="dashicons dashicons-no-alt"></span>
                                </button>
                            </div>
                            <div class="pf-repeater-item-body" <?php echo $collapsible ? 'style="display:block;"' : ''; ?>>
                                <?php
                                foreach ($fields as $field_config):
                                    $field_id = $field_config['id'];
                                    $field_name = $name . '[' . $index . '][' . $field_id . ']';
                                    $field_unique_id = $id . '_' . $index . '_' . $field_id . '_' . self::$instance_counter . '_' . uniqid();
                                    $field_value = isset($item[$field_id]) ? $item[$field_id] : (isset($field_config['default']) ? $field_config['default'] : '');

                                    $field_class = isset($field_config['class']) ? $field_config['class'] : '';
                                    $field_type = isset($field_config['type']) ? $field_config['type'] : 'text';
                                    $field_class_name = 'PersianFramework_Field_' . ucfirst(str_replace('-', '_', $field_type));

                                    $field_config['class'] = trim($field_class . ' pf-repeater-subfield');
                                    $field_config['name'] = $field_name;
                                    $field_config['id'] = $field_unique_id;

                                    if ($field_type === 'select' && isset($field_options_cache[$field_id])) {
                                        $field_config['options'] = $field_options_cache[$field_id];
                                    }

                                    echo '<div class="pf-repeater-field pf-repeater-field-' . esc_attr($field_type) . '">';

                                    if (class_exists($field_class_name)) {
                                        $field_obj = new $field_class_name($field_config, $field_value);
                                        $field_obj->render();
                                    } else {
                                        $this->render_fallback_field($field_config, $field_value);
                                    }

                                    echo '</div>';
                                endforeach;
                                ?>
                            </div>
                        </div>
                        <?php
                        $index++;
                    endforeach;
                    ?>
                </div>

                <div class="pf-repeater-actions">
                    <button type="button" class="pf-btn pf-btn-primary pf-repeater-add"
                            data-target="<?php echo esc_attr($unique_id); ?>">
                        <span class="dashicons dashicons-plus-alt"></span>
                        <?php echo esc_html($button_text); ?>
                    </button>
                    <?php if (!empty($value) && count($value) > $min_items): ?>
                        <button type="button" class="pf-btn pf-btn-danger pf-repeater-clear"
                                data-target="<?php echo esc_attr($unique_id); ?>">
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
        $item_count = is_array($value) ? count($value) : 0;
        $this->enqueue_scripts($id, $fields, $sortable, $collapsible, $title_field, $item_count, $unique_id, self::$instance_counter, $field_options_cache);
    }

    private function render_fallback_field($field_config, $value) {
        $type = isset($field_config['type']) ? $field_config['type'] : 'text';
        $id = isset($field_config['id']) ? $field_config['id'] : '';
        $name = isset($field_config['name']) ? $field_config['name'] : '';
        $title = isset($field_config['title']) ? $field_config['title'] : '';

        echo '<div class="pf-field-wrapper">';
        if ($title) {
            echo '<label class="pf-field-label" for="' . esc_attr($id) . '">' . esc_html($title) . '</label>';
        }

        switch ($type) {
            case 'slider':
                $min = isset($field_config['min']) ? $field_config['min'] : 0;
                $max = isset($field_config['max']) ? $field_config['max'] : 100;
                $step = isset($field_config['step']) ? $field_config['step'] : 1;
                $default = isset($field_config['default']) ? $field_config['default'] : $min;
                $current_value = $value !== '' ? $value : $default;
                ?>
                <div class="pf-slider-wrapper">
                    <input type="range" id="<?php echo esc_attr($id); ?>"
                           name="<?php echo esc_attr($name); ?>"
                           value="<?php echo esc_attr($current_value); ?>"
                           min="<?php echo esc_attr($min); ?>"
                           max="<?php echo esc_attr($max); ?>"
                           step="<?php echo esc_attr($step); ?>"
                           class="pf-slider-input">
                    <span class="pf-slider-value"><?php echo esc_html($current_value); ?></span>
                </div>
                <?php
                break;

            case 'textarea':
                ?>
                <textarea id="<?php echo esc_attr($id); ?>"
                          name="<?php echo esc_attr($name); ?>"
                          class="pf-field-input pf-textarea-input"
                          rows="3"><?php echo esc_textarea($value); ?></textarea>
                <?php
                break;

            case 'select':
                $options = isset($field_config['options']) ? $field_config['options'] : array();
                $placeholder = isset($field_config['placeholder']) ? $field_config['placeholder'] : esc_html__('Select an option', 'persian-framework');
                ?>
                <select id="<?php echo esc_attr($id); ?>"
                        name="<?php echo esc_attr($name); ?>"
                        class="pf-field-input">
                    <option value=""><?php echo esc_html($placeholder); ?></option>
                    <?php foreach ($options as $key => $label): ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php selected($value, $key); ?>>
                            <?php echo esc_html($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php
                break;

            case 'switch':
                $checked = $value ? 'checked' : '';
                ?>
                <div class="pf-switch-wrapper">
                    <input type="hidden" name="<?php echo esc_attr($name); ?>" value="0">
                    <input type="checkbox" id="<?php echo esc_attr($id); ?>"
                           name="<?php echo esc_attr($name); ?>"
                           value="1" <?php echo wp_kses_data($checked); ?>>
                    <label for="<?php echo esc_attr($id); ?>" class="pf-switch-label">
                        <span class="pf-switch-slider"></span>
                    </label>
                    <span class="pf-switch-status"><?php echo $checked ? 'ON' : 'OFF'; ?></span>
                </div>
                <?php
                break;

            default:
                ?>
                <input type="text" id="<?php echo esc_attr($id); ?>"
                       name="<?php echo esc_attr($name); ?>"
                       value="<?php echo esc_attr($value); ?>"
                       class="pf-field-input">
                <?php
                break;
        }
        echo '</div>';
    }

    private function get_select_options($field_config) {
        $options = array();

        if (isset($field_config['options']) && is_array($field_config['options'])) {
            return $field_config['options'];
        }

        $data_type = isset($field_config['data']) ? $field_config['data'] : '';
        $args = isset($field_config['args']) ? $field_config['args'] : array();

        if (empty($data_type)) {
            return $options;
        }

        switch ($data_type) {
            case 'terms':
            case 'categories':
                $taxonomy = isset($args['taxonomy']) ? $args['taxonomy'] : 'category';
                $hide_empty = isset($args['hide_empty']) ? $args['hide_empty'] : false;
                $parent = isset($args['parent']) ? $args['parent'] : 0;

                $terms = get_terms(array(
                        'taxonomy' => $taxonomy,
                        'hide_empty' => $hide_empty,
                        'parent' => $parent,
                ));

                if (!is_wp_error($terms) && !empty($terms)) {
                    foreach ($terms as $term) {
                        $options[$term->term_id] = $term->name;
                    }
                }
                break;

            case 'posts':
            case 'pages':
                $post_type = isset($args['post_type']) ? $args['post_type'] : 'post';
                $posts_per_page = isset($args['posts_per_page']) ? $args['posts_per_page'] : -1;

                $posts = get_posts(array(
                        'post_type' => $post_type,
                        'posts_per_page' => $posts_per_page,
                        'post_status' => 'publish',
                ));

                if (!empty($posts)) {
                    foreach ($posts as $post) {
                        $options[$post->ID] = $post->post_title;
                    }
                }
                break;

            case 'users':
                $role = isset($args['role']) ? $args['role'] : '';

                $users = get_users(array(
                        'role' => $role,
                ));

                if (!empty($users)) {
                    foreach ($users as $user) {
                        $options[$user->ID] = $user->display_name . ' (' . $user->user_email . ')';
                    }
                }
                break;

            case 'menus':
                $menus = wp_get_nav_menus();
                if (!empty($menus)) {
                    foreach ($menus as $menu) {
                        $options[$menu->term_id] = $menu->name;
                    }
                }
                break;

            case 'sidebar':
                global $wp_registered_sidebars;
                if (!empty($wp_registered_sidebars)) {
                    foreach ($wp_registered_sidebars as $sidebar) {
                        $options[$sidebar['id']] = $sidebar['name'];
                    }
                }
                break;

            case 'callback':
                if (isset($args['callback']) && is_callable($args['callback'])) {
                    $options = call_user_func($args['callback']);
                }
                break;
        }

        return $options;
    }

    private function normalize_value($value) {
        if (is_null($value)) {
            return array();
        }

        if (is_array($value)) {
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

        if (is_string($value) && !empty($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return array();
    }

    private function enqueue_scripts($id, $fields, $sortable, $collapsible, $title_field, $item_count, $unique_id, $instance_id, $field_options_cache = array()) {
        static $pf_repeater_enqueued = false;
        static $scripts_printed = array();

        $script_key = $id . '_' . $instance_id;
        if (in_array($script_key, $scripts_printed)) {
            return;
        }
        $scripts_printed[] = $script_key;

        if (!$pf_repeater_enqueued) {
            ?>
            <style>
                .pf-repeater-container {
                    margin-top: 8px;
                }

                .pf-repeater-items {
                    display: flex;
                    flex-direction: column;
                    gap: 12px;
                    margin-bottom: 12px;
                    min-height: 50px;
                }

                .pf-repeater-items-empty {
                    padding: 20px;
                    text-align: center;
                    color: #94a3b8;
                    border: 2px dashed #e8edf4;
                    border-radius: 8px;
                }
                body.dark-mode .pf-repeater-items-empty {
                    border-color: #334155;
                }

                .pf-repeater-item {
                    border: 1px solid #e8edf4;
                    border-radius: 12px;
                    background: white;
                    transition: all 0.2s ease;
                    overflow: hidden;
                }
                body.dark-mode .pf-repeater-item {
                    background: #1e293b;
                    border-color: #334155;
                }

                .pf-repeater-item:hover {
                    border-color: #94a3b8;
                }
                body.dark-mode .pf-repeater-item:hover {
                    border-color: #475569;
                }

                .pf-repeater-item.sortable-ghost {
                    opacity: 0.4;
                    border-color: #6366f1;
                }

                .pf-repeater-item.sortable-chosen {
                    border-color: #6366f1;
                    box-shadow: 0 8px 30px rgba(99, 102, 241, 0.3);
                    transform: scale(1.02);
                }

                .pf-repeater-item-header {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    padding: 12px 16px;
                    background: #f8f9fa;
                    border-bottom: 1px solid #e8edf4;
                    cursor: default;
                }
                body.dark-mode .pf-repeater-item-header {
                    background: #0f172a;
                    border-bottom-color: #334155;
                }

                .pf-repeater-item-header .pf-repeater-handle {
                    color: #94a3b8;
                    cursor: grab;
                    font-size: 18px;
                    flex-shrink: 0;
                }
                .pf-repeater-item-header .pf-repeater-handle:hover {
                    color: #6366f1;
                }

                .pf-repeater-item-title {
                    flex: 1;
                    font-weight: 600;
                    font-size: 14px;
                    color: #1a2332;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                }
                body.dark-mode .pf-repeater-item-title {
                    color: #e2e8f0;
                }

                .pf-repeater-toggle {
                    background: none;
                    border: none;
                    cursor: pointer;
                    color: #94a3b8;
                    padding: 4px;
                    transition: transform 0.3s ease;
                }
                .pf-repeater-toggle:hover {
                    color: #6366f1;
                }
                .pf-repeater-toggle .dashicons {
                    font-size: 18px;
                    width: 18px;
                    height: 18px;
                }
                .pf-repeater-toggle.collapsed .dashicons {
                    transform: rotate(-90deg);
                }

                .pf-repeater-remove {
                    background: none;
                    border: none;
                    cursor: pointer;
                    color: #94a3b8;
                    padding: 4px;
                    transition: all 0.2s ease;
                }
                .pf-repeater-remove:hover {
                    color: #ef4444;
                    transform: scale(1.2);
                }
                .pf-repeater-remove .dashicons {
                    font-size: 18px;
                    width: 18px;
                    height: 18px;
                }

                .pf-repeater-item-body {
                    padding: 16px;
                    transition: all 0.3s ease;
                }

                .pf-repeater-item-body.collapsed {
                    display: none !important;
                }

                .pf-repeater-field {
                    margin-bottom: 8px;
                }
                .pf-repeater-field:last-child {
                    margin-bottom: 0;
                }

                .pf-repeater-field .pf-field-wrapper {
                    margin-bottom: 20px;
                }

                .pf-repeater-actions {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 10px;
                }

                .pf-repeater-actions .pf-btn .dashicons {
                    font-size: 16px;
                    width: 16px;
                    height: 16px;
                }

                .pf-repeater-max-label {
                    font-size: 12px;
                    font-weight: 400;
                    color: #94a3b8;
                    margin-left: 6px;
                }

                .pf-repeater-field .pf-field-wrapper {
                    padding: 4px 0;
                }

                .pf-repeater-field .pf-field-label {
                    font-size: 13px;
                    margin-bottom: 4px;
                }

                .pf-slider-wrapper {
                    display: flex;
                    align-items: center;
                    gap: 15px;
                    margin-top: 5px;
                }

                .pf-slider-wrapper .pf-slider-input {
                    flex: 1;
                    height: 6px;
                    -webkit-appearance: none;
                    appearance: none;
                    background: #e8edf4;
                    border-radius: 3px;
                    outline: none;
                    cursor: pointer;
                }
                .pf-slider-wrapper .pf-slider-input::-webkit-slider-thumb {
                    -webkit-appearance: none;
                    appearance: none;
                    width: 18px;
                    height: 18px;
                    border-radius: 50%;
                    background: #6366f1;
                    cursor: pointer;
                    transition: all 0.2s;
                }
                .pf-slider-wrapper .pf-slider-input::-webkit-slider-thumb:hover {
                    transform: scale(1.15);
                    background: #4f46e5;
                }
                .pf-slider-wrapper .pf-slider-input::-moz-range-thumb {
                    width: 18px;
                    height: 18px;
                    border-radius: 50%;
                    background: #6366f1;
                    cursor: pointer;
                    border: none;
                }

                .pf-slider-wrapper .pf-slider-value {
                    min-width: 40px;
                    text-align: center;
                    font-weight: 600;
                    font-size: 14px;
                    color: #1a2332;
                    background: #f8f9fa;
                    padding: 4px 10px;
                    border-radius: 6px;
                    border: 1px solid #e8edf4;
                }
                body.dark-mode .pf-slider-wrapper .pf-slider-value {
                    background: #0f172a;
                    border-color: #334155;
                    color: #e2e8f0;
                }

                @media (max-width: 768px) {
                    .pf-repeater-item-header {
                        flex-wrap: wrap;
                        gap: 6px;
                    }
                    .pf-repeater-item-title {
                        order: 1;
                        width: 100%;
                    }
                    .pf-repeater-toggle {
                        order: 2;
                    }
                    .pf-repeater-remove {
                        order: 3;
                    }
                    .pf-repeater-handle {
                        order: 0;
                    }
                    .pf-repeater-item-body {
                        padding: 12px;
                    }
                    .pf-repeater-actions {
                        flex-direction: column;
                    }
                    .pf-repeater-actions .pf-btn {
                        justify-content: center;
                    }
                }
            </style>

            <?php
            $pf_repeater_enqueued = true;
        }

        ?>
        <script>
            (function($) {
                'use strict';

                var containerId = '<?php echo esc_js($unique_id); ?>';
                var fieldId = '<?php echo esc_js($id); ?>';
                var fieldName = '<?php echo esc_js($this->field['name']); ?>';
                var fieldsConfig = <?php echo json_encode($fields); ?>;
                var maxItems = <?php echo intval($this->field['max'] ?? 0); ?>;
                var minItems = <?php echo intval($this->field['min'] ?? 0); ?>;
                var sortableEnabled = <?php echo $sortable ? 'true' : 'false'; ?>;
                var collapsibleEnabled = <?php echo $collapsible ? 'true' : 'false'; ?>;
                var titleField = '<?php echo esc_js($title_field); ?>';
                var instanceId = <?php echo intval($instance_id); ?>;
                var fieldOptionsCache = <?php echo json_encode($field_options_cache); ?>;

                var $container = $('#' + containerId);

                var itemCounter = 0;
                var existingItems = $container.find('.pf-repeater-item');
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
                } else {
                    itemCounter = <?php echo intval($item_count); ?>;
                    if (itemCounter < minItems) {
                        itemCounter = minItems;
                    }
                }

                var initializedMediaFields = {};

                function initMediaFieldsInItem($item) {
                    $item.find('.pf-media-container').each(function() {
                        var $container = $(this);
                        var uniqueId = $container.data('unique-id');

                        if (uniqueId && initializedMediaFields[uniqueId]) {
                            return;
                        }

                        if (uniqueId) {
                            initializedMediaFields[uniqueId] = true;
                        }

                        var $chooseBtn = $container.find('.pf-media-choose');
                        var $removeBtn = $container.find('.pf-media-remove');
                        var $idInput = $container.find('.pf-media-id');
                        var $urlInput = $container.find('.pf-media-url');

                        $chooseBtn.off('click').on('click', function(e) {
                            e.preventDefault();
                            var uid = $(this).data('unique-id');
                            if (uid && typeof pfOpenMediaFrame === 'function') {
                                pfOpenMediaFrame(uid);
                            }
                        });

                        $removeBtn.off('click').on('click', function(e) {
                            e.preventDefault();
                            var uid = $(this).data('unique-id');
                            if (!uid) return;

                            var $c = $('.pf-media-container[data-unique-id="' + uid + '"]');
                            if (!$c.length) return;

                            var $id = $c.find('.pf-media-id');
                            var $url = $c.find('.pf-media-url');
                            var $preview = $c.find('.pf-media-preview');

                            $id.val('0');
                            $url.val('');
                            $preview.html(
                                '<span class="dashicons dashicons-format-image"></span>' +
                                '<span class="pf-media-placeholder"><?php echo esc_html_e('No media selected', 'persian-framework'); ?></span>'
                            );
                            $(this).hide();

                            $id.trigger('change');
                            $url.trigger('change');
                            $c.trigger('pf-media-removed');
                        });
                    });
                }

                function renderField(fieldConfig, value) {
                    var type = fieldConfig.type || 'text';
                    var html = '';

                    if (type === 'slider' && (value === undefined || value === '')) {
                        value = fieldConfig.default !== undefined ? fieldConfig.default : (fieldConfig.min || 0);
                    }

                    if (type === 'text' || type === 'url' || type === 'email') {
                        html += '<div class="pf-field-wrapper">';
                        if (fieldConfig.title) {
                            html += '<label class="pf-field-label" for="' + fieldConfig.id + '">' + fieldConfig.title + '</label>';
                        }
                        if (fieldConfig.subtitle) {
                            html += '<span class="pf-subtitle">' + fieldConfig.subtitle + '</span>';
                        }
                        html += '<input type="text" id="' + fieldConfig.id + '" name="' + fieldConfig.name + '" value="' + (value || '') + '" class="pf-field-input">';
                        html += '</div>';
                    } else if (type === 'textarea') {
                        html += '<div class="pf-field-wrapper">';
                        if (fieldConfig.title) {
                            html += '<label class="pf-field-label" for="' + fieldConfig.id + '">' + fieldConfig.title + '</label>';
                        }
                        if (fieldConfig.subtitle) {
                            html += '<span class="pf-subtitle">' + fieldConfig.subtitle + '</span>';
                        }
                        html += '<textarea id="' + fieldConfig.id + '" name="' + fieldConfig.name + '" class="pf-field-input pf-textarea-input" rows="3">' + (value || '') + '</textarea>';
                        html += '</div>';
                    } else if (type === 'switch') {
                        var checked = value ? 'checked' : '';
                        html += '<div class="pf-field-wrapper pf-field-switch">';
                        if (fieldConfig.title) {
                            html += '<label class="pf-field-label">' + fieldConfig.title + '</label>';
                        }
                        if (fieldConfig.subtitle) {
                            html += '<span class="pf-subtitle">' + fieldConfig.subtitle + '</span>';
                        }
                        html += '<div class="pf-switch-wrapper">';
                        html += '<input type="hidden" name="' + fieldConfig.name + '" value="0">';
                        html += '<input type="checkbox" id="' + fieldConfig.id + '" name="' + fieldConfig.name + '" value="1" ' + checked + '>';
                        html += '<label for="' + fieldConfig.id + '" class="pf-switch-label"><span class="pf-switch-slider"></span></label>';
                        html += '<span class="pf-switch-status">' + (checked ? 'ON' : 'OFF') + '</span>';
                        html += '</div></div>';
                    } else if (type === 'select') {
                        html += '<div class="pf-field-wrapper">';
                        if (fieldConfig.title) {
                            html += '<label class="pf-field-label" for="' + fieldConfig.id + '">' + fieldConfig.title + '</label>';
                        }
                        if (fieldConfig.subtitle) {
                            html += '<span class="pf-subtitle">' + fieldConfig.subtitle + '</span>';
                        }
                        html += '<select id="' + fieldConfig.id + '" name="' + fieldConfig.name + '" class="pf-field-input">';
                        if (fieldConfig.placeholder) {
                            html += '<option value="">' + fieldConfig.placeholder + '</option>';
                        }
                        if (fieldConfig.options) {
                            for (var key in fieldConfig.options) {
                                html += '<option value="' + key + '" ' + (value == key ? 'selected' : '') + '>' + fieldConfig.options[key] + '</option>';
                            }
                        }
                        html += '</select></div>';
                    } else if (type === 'slider') {
                        var min = fieldConfig.min || 0;
                        var max = fieldConfig.max || 100;
                        var step = fieldConfig.step || 1;
                        var currentValue = value !== undefined && value !== '' ? value : fieldConfig.default || min;

                        html += '<div class="pf-field-wrapper pf-field-slider">';
                        if (fieldConfig.title) {
                            html += '<label class="pf-field-label" for="' + fieldConfig.id + '">' + fieldConfig.title;
                            if (fieldConfig.subtitle) {
                                html += '<span class="pf-subtitle">' + fieldConfig.subtitle + '</span>';
                            }
                            html += '</label>';
                        }
                        html += '<div class="pf-slider-wrapper">';
                        html += '<input type="range" id="' + fieldConfig.id + '" name="' + fieldConfig.name + '" value="' + currentValue + '" min="' + min + '" max="' + max + '" step="' + step + '" class="pf-slider-input">';
                        html += '<span class="pf-slider-value">' + currentValue + '</span>';
                        html += '</div></div>';
                    } else if (type === 'color') {
                        html += '<div class="pf-field-wrapper pf-field-color">';
                        if (fieldConfig.title) {
                            html += '<label class="pf-field-label" for="' + fieldConfig.id + '">' + fieldConfig.title + '</label>';
                        }
                        if (fieldConfig.subtitle) {
                            html += '<span class="pf-subtitle">' + fieldConfig.subtitle + '</span>';
                        }
                        html += '<input type="color" id="' + fieldConfig.id + '" name="' + fieldConfig.name + '" value="' + (value || '#ffffff') + '" class="pf-field-input pf-color-input">';
                        html += '</div>';
                    } else if (type === 'image' || type === 'media') {
                        var mediaId = (value && value.id) ? value.id : 0;
                        var mediaUrl = (value && value.url) ? value.url : '';
                        var previewUrl = mediaUrl;
                        var title = '';

                        html += '<div class="pf-field-wrapper pf-field-media">';
                        if (fieldConfig.title) {
                            html += '<label class="pf-field-label" for="' + fieldConfig.id + '">' + fieldConfig.title + '</label>';
                        }
                        if (fieldConfig.subtitle) {
                            html += '<span class="pf-subtitle">' + fieldConfig.subtitle + '</span>';
                        }
                        html += '<div class="pf-media-container" data-unique-id="' + fieldConfig.id + '">';

                        html += '<div class="pf-media-preview">';
                        if (previewUrl) {
                            html += '<img src="' + previewUrl + '" alt="' + (title || '') + '" />';
                            if (mediaId) {
                                html += '<div class="pf-media-badge"><span class="dashicons dashicons-yes-alt"></span></div>';
                            }
                        } else {
                            html += '<div class="pf-media-empty" style="text-align: center;">';
                            html += '<span class="dashicons dashicons-format-image"></span>';
                            html += '<span class="pf-media-placeholder"><?php echo esc_html_e('No media selected', 'persian-framework'); ?></span>';
                            html += '</div>';
                        }
                        html += '</div>';

                        html += '<div class="pf-media-fields-wrapper" style="width: 100%; display: inline-block;">';
                        html += '<div class="pf-media-fields">';
                        html += '<div class="pf-media-field-row">';
                        html += '<input type="hidden" class="pf-media-id" name="' + fieldConfig.name + '[id]" value="' + mediaId + '" />';
                        html += '</div>';
                        html += '<div class="pf-media-field-row">';
                        html += '<input type="text" class="pf-field-input pf-media-url" name="' + fieldConfig.name + '[url]" value="' + mediaUrl + '" placeholder="<?php esc_attr_e('Media URL', 'persian-framework'); ?>" />';
                        html += '</div></div>';

                        html += '<div class="pf-media-actions">';
                        html += '<button type="button" class="pf-btn pf-btn-primary pf-media-choose" data-unique-id="' + fieldConfig.id + '">';
                        html += '<span class="dashicons dashicons-edit"></span> ' + (mediaId ? '<?php esc_html_e('Replace', 'persian-framework'); ?>' : '<?php esc_html_e('Choose', 'persian-framework'); ?>');
                        html += '</button>';
                        html += '<button type="button" class="pf-btn pf-btn-danger pf-media-remove" data-unique-id="' + fieldConfig.id + '" ' + (!mediaId ? 'style="display:none;"' : '') + '>';
                        html += '<span class="dashicons dashicons-no-alt"></span> <?php esc_html_e('Remove', 'persian-framework'); ?>';
                        html += '</button>';
                        if (mediaUrl) {
                            html += '<a href="' + mediaUrl + '" target="_blank" class="pf-btn pf-btn-secondary pf-media-view">';
                            html += '<span class="dashicons dashicons-external"></span> <?php esc_html_e('View', 'persian-framework'); ?>';
                            html += '</a>';
                        }
                        html += '</div></div></div>';

                        if (fieldConfig.desc) {
                            html += '<p class="pf-field-desc">' + fieldConfig.desc + '</p>';
                        }
                        html += '</div>';
                    } else if (type === 'number') {
                        html += '<div class="pf-field-wrapper">';
                        if (fieldConfig.title) {
                            html += '<label class="pf-field-label" for="' + fieldConfig.id + '">' + fieldConfig.title + '</label>';
                        }
                        if (fieldConfig.subtitle) {
                            html += '<span class="pf-subtitle">' + fieldConfig.subtitle + '</span>';
                        }
                        var min = fieldConfig.min || '';
                        var max = fieldConfig.max || '';
                        html += '<input type="number" id="' + fieldConfig.id + '" name="' + fieldConfig.name + '" value="' + (value || '') + '" min="' + min + '" max="' + max + '" class="pf-field-input">';
                        html += '</div>';
                    } else {
                        html += '<div class="pf-field-wrapper">';
                        if (fieldConfig.title) {
                            html += '<label class="pf-field-label" for="' + fieldConfig.id + '">' + fieldConfig.title + '</label>';
                        }
                        if (fieldConfig.subtitle) {
                            html += '<span class="pf-subtitle">' + fieldConfig.subtitle + '</span>';
                        }
                        html += '<input type="text" id="' + fieldConfig.id + '" name="' + fieldConfig.name + '" value="' + (value || '') + '" class="pf-field-input">';
                        html += '</div>';
                    }

                    return html;
                }

                function generateRepeaterItem(index) {
                    var html = '<div class="pf-repeater-item" data-index="' + index + '">';
                    html += '<div class="pf-repeater-item-header">';
                    if (sortableEnabled) {
                        html += '<span class="pf-repeater-handle dashicons dashicons-move"></span>';
                    }
                    html += '<span class="pf-repeater-item-title">';
                    html += '<?php echo esc_html__('Item', 'persian-framework'); ?> ' + (index + 1);
                    html += '</span>';
                    if (collapsibleEnabled) {
                        html += '<button type="button" class="pf-repeater-toggle"><span class="dashicons dashicons-arrow-down-alt2"></span></button>';
                    }
                    html += '<button type="button" class="pf-repeater-remove" aria-label="<?php esc_attr_e('Remove', 'persian-framework'); ?>"><span class="dashicons dashicons-no-alt"></span></button>';
                    html += '</div>';
                    html += '<div class="pf-repeater-item-body">';

                    fieldsConfig.forEach(function(field) {
                        var fieldId = field.id;
                        var fieldType = field.type || 'text';
                        var randomSuffix = Math.random().toString(36).substring(2, 8);
                        var uniqueFieldId = '<?php echo esc_js($id); ?>_' + index + '_' + fieldId + '_' + instanceId + '_' + randomSuffix;
                        var fieldName = '<?php echo esc_js($this->field['name']); ?>[' + index + '][' + fieldId + ']';
                        var fieldConfig = $.extend(true, {}, field, {
                            name: fieldName,
                            id: uniqueFieldId,
                            class: (field.class || '') + ' pf-repeater-subfield'
                        });

                        if (fieldType === 'select' && fieldOptionsCache[fieldId]) {
                            fieldConfig.options = fieldOptionsCache[fieldId];
                        }

                        if (fieldType === 'slider' && fieldConfig.default === undefined) {
                            fieldConfig.default = fieldConfig.min || 0;
                        }

                        html += '<div class="pf-repeater-field pf-repeater-field-' + fieldType + '">';
                        html += renderField(fieldConfig, '');
                        html += '</div>';
                    });

                    html += '</div></div>';
                    return html;
                }

                function updateItemIndexes() {
                    $container.find('.pf-repeater-item').each(function(index) {
                        var $item = $(this);

                        $item.data('index', index);
                        $item.attr('data-index', index);

                        $item.find('input, textarea, select').each(function() {
                            var $field = $(this);
                            var name = $field.attr('name');
                            if (name) {
                                var newName = name.replace(/\[\d+\]/, '[' + index + ']');
                                $field.attr('name', newName);
                            }
                        });

                        $item.find('.pf-field-wrapper').each(function() {
                            var $wrapper = $(this);
                            var $input = $wrapper.find('input, textarea, select').first();
                            var id = $input.attr('id');
                            if (id) {
                                var newId = id.replace(/_\d+_/, '_' + index + '_');
                                $input.attr('id', newId);
                                $wrapper.find('label').attr('for', newId);
                                $wrapper.find('.pf-switch-label').attr('for', newId);
                            }
                        });

                        var $title = $item.find('.pf-repeater-item-title');
                        if (titleField) {
                            var $titleInput = $item.find('input[name*="[' + titleField + ']"]');
                            if ($titleInput.length && $titleInput.val()) {
                                $title.text($titleInput.val());
                            } else {
                                $title.text('<?php echo esc_html__('Item', 'persian-framework'); ?> ' + (index + 1));
                            }
                        } else {
                            $title.text('<?php echo esc_html__('Item', 'persian-framework'); ?> ' + (index + 1));
                        }
                    });
                }

                function updateRemoveButtons() {
                    var count = $container.find('.pf-repeater-item').length;
                    $container.find('.pf-repeater-remove').toggle(count > minItems);
                }

                function updateTitleFields() {
                    if (!titleField) return;

                    $container.find('.pf-repeater-item').each(function(index) {
                        var $item = $(this);
                        var $title = $item.find('.pf-repeater-item-title');
                        var $field = $item.find('input[name*="[' + titleField + ']"]');
                        if ($field.length) {
                            var value = $field.val() || '<?php echo esc_html__('Item', 'persian-framework'); ?> ' + (index + 1);
                            $title.text(value);
                        }
                    });
                }

                function initSliderEvents() {
                    $container.find('.pf-slider-input').off('input').on('input', function() {
                        var $this = $(this);
                        var $wrapper = $this.closest('.pf-slider-wrapper');
                        var $valueDisplay = $wrapper.find('.pf-slider-value');
                        var val = $this.val();
                        $valueDisplay.text(val);
                    });

                    $container.find('.pf-slider-input').each(function() {
                        var $this = $(this);
                        var $wrapper = $this.closest('.pf-slider-wrapper');
                        var $valueDisplay = $wrapper.find('.pf-slider-value');
                        var val = $this.val();
                        if ($valueDisplay.length && val !== undefined) {
                            $valueDisplay.text(val);
                        }
                    });
                }

                function initSortable() {
                    if (!sortableEnabled || typeof Sortable === 'undefined') return;

                    var container = document.getElementById(containerId);
                    if (!container) return;

                    if (container.sortableInstance) {
                        try {
                            container.sortableInstance.destroy();
                        } catch(e) {}
                    }

                    try {
                        var sortable = new Sortable(container, {
                            animation: 150,
                            handle: '.pf-repeater-handle',
                            ghostClass: 'sortable-ghost',
                            chosenClass: 'sortable-chosen',
                            onEnd: function() {
                                updateItemIndexes();
                                updateTitleFields();
                                $(document).trigger('pf-repeater-sort', [fieldId, instanceId]);
                                pfChanges();
                            }
                        });

                        container.sortableInstance = sortable;
                    } catch(e) {
                        console.warn('Error initializing Sortable:', e);
                    }
                }

                var $addBtn = $('.pf-repeater-add[data-target="' + containerId + '"]');
                var $clearBtn = $('.pf-repeater-clear[data-target="' + containerId + '"]');

                $addBtn.on('click', function() {
                    if (maxItems > 0 && $container.find('.pf-repeater-item').length >= maxItems) {
                        alert('<?php esc_html_e('Maximum number of items reached.', 'persian-framework'); ?>');
                        return;
                    }

                    if (collapsibleEnabled) {
                        $container.find('.pf-repeater-item-body').slideUp(300);
                        $container.find('.pf-repeater-toggle').addClass('collapsed');
                    }

                    var index = itemCounter++;
                    var $item = $(generateRepeaterItem(index));
                    $container.append($item);

                    if (collapsibleEnabled) {
                        var $newBody = $item.find('.pf-repeater-item-body');
                        var $newToggle = $item.find('.pf-repeater-toggle');
                        $newBody.slideDown(300);
                        $newToggle.removeClass('collapsed');
                    }

                    setTimeout(function() {
                        initMediaFieldsInItem($item);
                    }, 50);

                    updateItemIndexes();
                    updateRemoveButtons();
                    updateTitleFields();
                    setTimeout(initSliderEvents, 100);
                    $(document).trigger('pf-repeater-add', [fieldId, instanceId, $item]);

                    if (sortableEnabled) {
                        setTimeout(initSortable, 100);
                    }

                    $clearBtn.show();
                });

                $(document).on('click', '.pf-repeater-remove', function() {
                    var $item = $(this).closest('.pf-repeater-item');
                    if ($item.closest('#' + containerId).length === 0) return;

                    var count = $container.find('.pf-repeater-item').length;

                    if (count <= minItems) {
                        alert('<?php esc_html_e('Minimum number of items required.', 'persian-framework'); ?>');
                        return;
                    }

                    if (confirm('<?php esc_html_e('Remove this item?', 'persian-framework'); ?>')) {
                        $item.fadeOut(300, function() {
                            $item.remove();
                            updateItemIndexes();
                            updateRemoveButtons();
                            updateTitleFields();

                            if ($container.find('.pf-repeater-item').length === 0) {
                                $clearBtn.hide();
                            }

                            $(document).trigger('pf-repeater-remove', [fieldId, instanceId]);
                        });
                    }
                });

                $clearBtn.on('click', function() {
                    var count = $container.find('.pf-repeater-item').length;
                    if (count <= minItems) {
                        alert('<?php esc_html_e('Minimum number of items required.', 'persian-framework'); ?>');
                        return;
                    }

                    if (confirm('<?php esc_html_e('Remove all items?', 'persian-framework'); ?>')) {
                        $container.find('.pf-repeater-item').fadeOut(300, function() {
                            $container.empty();
                            for (var i = 0; i < minItems; i++) {
                                var index = itemCounter++;
                                var $item = $(generateRepeaterItem(index));
                                $container.append($item);
                            }
                            updateItemIndexes();
                            updateRemoveButtons();
                            updateTitleFields();
                            $clearBtn.hide();

                            setTimeout(initSliderEvents, 100);
                            $(document).trigger('pf-repeater-clear', [fieldId, instanceId]);

                            if (sortableEnabled) {
                                setTimeout(initSortable, 100);
                            }
                        });
                    }
                });

                $(document).on('click', '.pf-repeater-toggle', function() {
                    if (!collapsibleEnabled) return;

                    var $item = $(this).closest('.pf-repeater-item');
                    if ($item.closest('#' + containerId).length === 0) return;

                    var $body = $item.find('.pf-repeater-item-body');
                    $body.slideToggle(300);
                    $(this).toggleClass('collapsed');
                });

                $(document).on('input', '.pf-repeater-item input, .pf-repeater-item textarea', function() {
                    if (!titleField) return;

                    var $item = $(this).closest('.pf-repeater-item');
                    if ($item.closest('#' + containerId).length === 0) return;

                    var $field = $item.find('input[name*="[' + titleField + ']"]');
                    if ($field.length && $field.is(this)) {
                        var $title = $item.find('.pf-repeater-item-title');
                        var value = $(this).val() || '<?php echo esc_html__('Item', 'persian-framework'); ?> ' + ($item.data('index') + 1);
                        $title.text(value);
                    }
                });

                $(document).ready(function() {
                    var existingItems = $container.find('.pf-repeater-item');
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

                        setTimeout(function () {
                            $container.find('.pf-repeater-toggle').trigger("click");
                        }, 100);
                    }

                    $container.find('.pf-repeater-item').each(function() {
                        initMediaFieldsInItem($(this));
                    });

                    updateItemIndexes();
                    updateRemoveButtons();
                    updateTitleFields();

                    setTimeout(initSliderEvents, 200);

                    if (sortableEnabled) {
                        setTimeout(initSortable, 300);
                    }

                    if ($container.find('.pf-repeater-item').length <= minItems) {
                        $clearBtn.hide();
                    }
                });

                $(document).on('pf-tab-switch', function() {
                    if (sortableEnabled) {
                        setTimeout(initSortable, 400);
                    }
                    setTimeout(initSliderEvents, 400);
                });

            })(jQuery);
        </script>
        <?php
    }

    public function sanitize($value) {
        if (!is_array($value)) {
            return array();
        }

        $sanitized = array();
        foreach ($value as $index => $item) {
            if (is_array($item)) {
                $sanitized[$index] = array();
                foreach ($item as $sub_key => $sub_value) {
                    if (is_array($sub_value)) {
                        $sanitized[$index][sanitize_text_field($sub_key)] = $this->sanitize($sub_value);
                    } else {
                        $sanitized[$index][sanitize_text_field($sub_key)] = sanitize_text_field($sub_value);
                    }
                }
            }
        }

        return $sanitized;
    }
}