<?php
/**
 * Persian Framework - Date Field
 * Supports both Gregorian and Jalali (Persian) dates
 * Uses PersianDatepicker library: http://babakhani.github.io/PersianWebToolkit/
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Date {

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
        $calendar = isset($this->field['calendar']) ? $this->field['calendar'] : '';

        $settings = $this->get_field_settings();
        extract($settings);

        $wrapper_id = $id . '_wrapper';
        $hidden_id = $id . '_hidden';
        $display_id = $id . '_display';

        $options = $this->build_picker_options($settings);
        $options_json = wp_json_encode($options);

        ?>
        <div class="pf-field-wrapper pf-field-date pf-date-<?php echo esc_attr($calendar_type); ?>">
            <?php if (isset($this->field['title'])): ?>
                <label for="<?php echo esc_attr($id); ?>" class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                    <?php if ($required): ?>
                        <span class="pf-required">*</span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-date-wrapper" id="<?php echo esc_attr($wrapper_id); ?>">

                <?php if ($calendar != 'persian'): ?>
                    <input type="date"
                           name="<?php echo esc_attr($name); ?>"
                           class="pf-field-input pf-date-input"
                           placeholder="<?php echo esc_attr($placeholder); ?>"
                           value="<?php echo esc_attr($value); ?>"
                           autocomplete="off" />
                <?php else: ?>

                    <input type="hidden"
                           id="<?php echo esc_attr($hidden_id); ?>"
                           name="<?php echo esc_attr($name); ?>"
                           value="<?php echo esc_attr($value); ?>"
                           class="pf-date-input pf-date-hidden"
                           data-calendar="<?php echo esc_attr($calendar_type); ?>"
                            <?php echo wp_kses_data($required); ?> />

                    <?php if ($inline): ?>
                        <div class="pf-date-inline-container" id="<?php echo esc_attr($id); ?>_inline"></div>

                        <input type="text"
                               id="<?php echo esc_attr($display_id); ?>"
                               class="pf-date-alt"
                               placeholder="<?php echo esc_attr($placeholder); ?>"
                               readonly />
                    <?php else: ?>
                        <input type="text"
                               id="<?php echo esc_attr($display_id); ?>"
                               class="pf-date-display pf-date-input"
                               placeholder="<?php echo esc_attr($placeholder); ?>"
                               value="<?php echo esc_attr($this->format_display_value($value, $calendar_type)); ?>"
                               autocomplete="off"
                               readonly />
                    <?php endif; ?>

                <?php endif; ?>

                <?php if (isset($this->field['desc'])): ?>
                    <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php
        $this->enqueue_scripts($id, $hidden_id, $display_id, $options_json, $inline);
    }

    private function get_field_settings() {
        return array(
                'calendar_type' => isset($this->field['calendar']) ? $this->field['calendar'] : 'persian',
                'format' => isset($this->field['format']) ? $this->field['format'] :
                        (isset($this->field['calendar']) && $this->field['calendar'] === 'persian' ? 'YYYY/MM/DD' : 'YYYY-MM-DD'),
                'inline' => isset($this->field['inline']) && $this->field['inline'],
                'timepicker' => isset($this->field['timepicker']) && $this->field['timepicker'],
                'only_time' => isset($this->field['only_time']) && $this->field['only_time'],
                'auto_close' => isset($this->field['auto_close']) && $this->field['auto_close'],
                'observer' => isset($this->field['observer']) && $this->field['observer'],
                'view_mode' => isset($this->field['view_mode']) ? $this->field['view_mode'] : 'day',
                'min_date' => isset($this->field['min_date']) ? $this->field['min_date'] : null,
                'max_date' => isset($this->field['max_date']) ? $this->field['max_date'] : null,
                'required' => isset($this->field['required']) && $this->field['required'] ? 'required' : '',
                'placeholder' => isset($this->field['placeholder']) ? $this->field['placeholder'] : esc_html__('Select date', 'persian-framework'),
                'locale' => isset($this->field['locale']) ? $this->field['locale'] : 'fa',
                'leap_year_mode' => isset($this->field['leap_year_mode']) ? $this->field['leap_year_mode'] : 'astronomical'
        );
    }

    private function build_picker_options($settings) {
        extract($settings);

        $options = array(
                'format' => $format,
                'observer' => $observer,
                'autoClose' => $auto_close,
                'viewMode' => $view_mode,
                'initialValue' => !empty($this->value),
                'initialValueType' => $calendar_type === 'persian' ? 'persian' : 'gregorian',
                'calendarType' => $calendar_type,
                'calendar' => array(
                        'persian' => array(
                                'leapYearMode' => $leap_year_mode
                        )
                ),
                'timePicker' => array(
                        'enabled' => $timepicker
                ),
                'toolbox' => array(
                        'calendarSwitch' => array(
                                'enabled' => isset($this->field['calendar_switch']) && $this->field['calendar_switch']
                        )
                ),
                'navigator' => array(
                        'scroll' => array(
                                'enabled' => isset($this->field['scroll_nav']) ? $this->field['scroll_nav'] : true
                        )
                )
        );

        if ($only_time) {
            $options['onlyTimePicker'] = true;
        }

        if ($inline) {
            $options['inline'] = true;
        }

        if ($min_date) {
            $options['minDate'] = $this->parse_date($min_date, $calendar_type);
        }
        if ($max_date) {
            $options['maxDate'] = $this->parse_date($max_date, $calendar_type);
        }

        if (isset($this->field['formatter']) && is_callable($this->field['formatter'])) {
            $options['formatter'] = $this->field['formatter'];
        }

        if (isset($this->field['on_select']) && is_callable($this->field['on_select'])) {
            $options['onSelect'] = $this->field['on_select'];
        }

        if ($locale === 'en') {
            $options['calendar']['persian']['locale'] = 'en';
        }

        foreach (array('check_date', 'check_month', 'check_year') as $callback) {
            if (isset($this->field[$callback]) && is_callable($this->field[$callback])) {
                $options[$callback] = $this->field[$callback];
            }
        }

        return $options;
    }

    private function format_display_value($value, $calendar_type) {
        if (empty($value)) {
            return '';
        }

        if ($calendar_type === 'persian') {
            return $value;
        } else {
            return date_i18n(get_option('date_format'), $value);
        }
    }

    private function parse_date($date, $calendar_type) {
        if (is_numeric($date)) {
            return intval($date);
        }

        if ($calendar_type === 'persian') {
            $pattern = '/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/';
            if (preg_match($pattern, $date, $matches)) {
                $pDate = new \persianDate();
                $pDate->date($matches[1], intval($matches[2]) - 1, $matches[3]);
                return $pDate->valueOf();
            }
        } else {
            $timestamp = strtotime($date);
            if ($timestamp !== false) {
                return $timestamp * 1000;
            }
        }

        return null;
    }

    private function enqueue_scripts($id, $hidden_id, $display_id, $options_json, $inline) {
        static $pf_date_enqueued = false;

        if (!$pf_date_enqueued) {
            wp_enqueue_script(
                    'persian-datepicker',
                    PERSIAN_FRAMEWORK_ASSETS . 'js/persian-datepicker-x.js',
                    array('jquery'),
                    '1.0.0',
                    true
            );
            $pf_date_enqueued = true;
        }

        ?>
        <script>
            (function($) {
                'use strict';

                function initDatepicker() {
                    var $hiddenInput = $('#<?php echo esc_js($hidden_id); ?>');
                    var $displayInput = $('#<?php echo esc_js($display_id); ?>');
                    var options = <?php echo wp_kses_data($options_json); ?>;

                    if (!$displayInput.length || typeof $.fn.persianDatepicker === 'undefined') {
                        return;
                    }

                    var onSelectHandler = function(unix) {
                        var state = this.getState ? this.getState() : null;
                        var formatted = state && state.selected ? state.selected.formatted : '';

                        $hiddenInput.val(unix).trigger('change');

                        if (formatted) {
                            $displayInput.val(formatted);
                        }

                        $(document).trigger('pf-date-select', [unix, formatted, '<?php echo esc_js($id); ?>']);

                        <?php if (isset($this->field['on_select']) && is_callable($this->field['on_select'])): ?>
                        if (typeof options.onSelect === 'function') {
                            options.onSelect(unix);
                        }
                        <?php endif; ?>
                    };

                    <?php if ($inline): ?>
                    var $container = $('#<?php echo esc_js($id); ?>_inline');
                    if ($container.length) {
                        var picker = $container.persianDatepicker($.extend({}, options, {
                            onSelect: onSelectHandler,
                            altField: '#' + '<?php echo esc_js($display_id); ?>'
                        }));

                        <?php if (!empty($this->value)): ?>
                        setInitialDate(picker, '<?php echo esc_js($this->value); ?>', options);
                        <?php endif; ?>
                    }

                    <?php else: ?>
                    var picker = $displayInput.persianDatepicker($.extend({}, options, {
                        onSelect: onSelectHandler
                    }));

                    <?php if (!empty($this->value)): ?>
                    setInitialDate(picker, '<?php echo esc_js($this->value); ?>', options);
                    <?php endif; ?>
                    <?php endif; ?>
                }

                function setInitialDate(picker, value, options) {
                    if (!value) return;

                    try {
                        if (options.calendarType === 'persian') {
                            var parts = value.split(/[\/\-]/);
                            if (parts.length === 3) {
                                var dateObj = new persianDate();
                                dateObj.date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                                picker.setDate(dateObj.valueOf());
                            }
                        } else if (!isNaN(value)) {
                            picker.setDate(parseInt(value));
                        }
                    } catch(e) {
                        console.warn('Could not set initial date:', e);
                    }
                }

                $(document).ready(initDatepicker);

                $(document).on('pf-repeater-add', function() {
                    setTimeout(initDatepicker, 300);
                });

            })(jQuery);
        </script>
        <?php
    }

    public function sanitize($value) {
        $value = wp_unslash($value);
        return sanitize_text_field($value);
    }
}