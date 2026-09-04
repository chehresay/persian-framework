<?php
/**
 * Persian Framework - Required Fields Handler
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Required {

    /**
     * Get data attributes for conditional logic
     *
     * @param array $required Required rules
     * @return string HTML attributes
     */
    public static function get_attributes($required) {
        if (empty($required) || !is_array($required) || count($required) < 3) {
            return '';
        }

        $field_id = $required[0];
        $operator = $required[1];
        $value = $required[2];

        // Sanitize
        $field_id = esc_attr($field_id);
        $operator = esc_attr($operator);
        $value = esc_attr($value);

        return sprintf(
                ' data-required="%s" data-operator="%s" data-required-value="%s"',
                $field_id,
                $operator,
                $value
        );
    }

    /**
     * Check if field should be visible based on required rules
     *
     * @param array $required Required rules
     * @param array $values All field values
     * @return bool
     */
    public static function check($required, $values) {
        if (empty($required) || !is_array($required) || count($required) < 3) {
            return true;
        }

        $field_id = $required[0];
        $operator = $required[1];
        $required_value = $required[2];

        if (!isset($values[$field_id])) {
            return false;
        }

        $current_value = $values[$field_id];

        switch ($operator) {
            case '=':
            case '==':
                return $current_value == $required_value;
            case '!=':
            case '<>':
                return $current_value != $required_value;
            case '>':
                return $current_value > $required_value;
            case '<':
                return $current_value < $required_value;
            case '>=':
                return $current_value >= $required_value;
            case '<=':
                return $current_value <= $required_value;
            case 'contains':
                if (is_array($current_value)) {
                    return in_array($required_value, $current_value);
                }
                return strpos($current_value, $required_value) !== false;
            case 'not-contains':
                if (is_array($current_value)) {
                    return !in_array($required_value, $current_value);
                }
                return strpos($current_value, $required_value) === false;
            default:
                return true;
        }
    }

    /**
     * Enqueue required field JavaScript
     */
    public static function enqueue_scripts() {
        static $enqueued = false;

        if (!$enqueued) {
            // ✅ ثبت اسکریپت با وابستگی به jQuery
            add_action('admin_enqueue_scripts', function() {
                wp_enqueue_script(
                        'pf-required-fields',
                        false, // بدون فایل خارجی
                        array('jquery'), // ✅ وابستگی به jQuery
                        PERSIAN_FRAMEWORK_VERSION,
                        true // در فوتر بارگذاری شود
                );

                // ✅ اضافه کردن اسکریپت inline با jQuery
                wp_add_inline_script('jquery', self::get_script());
            });

            $enqueued = true;
        }
    }

    /**
     * Get JavaScript code for required fields
     *
     * @return string
     */
    private static function get_script() {
        return '
            (function($) {
                "use strict";

                /**
                 * Check if a field should be visible
                 */
                function checkRequired($field) {
                    var required = $field.data("required");
                    if (!required) {
                        return true;
                    }

                    var operator = $field.data("operator") || "=";
                    var requiredValue = $field.data("required-value");

                    // Find the source field
                    var $sourceField = $("#" + required);
                    if (!$sourceField.length) {
                        return true;
                    }

                    var currentValue = getFieldValue($sourceField);

                    // Check condition
                    var isVisible = compareValues(currentValue, operator, requiredValue);

                    // Show/hide the field
                    if (isVisible) {
                        $field.show();
                    } else {
                        $field.hide();
                    }

                    return isVisible;
                }

                /**
                 * Get field value based on field type
                 */
                function getFieldValue($field) {
                    var type = $field.attr("type") || $field.prop("tagName").toLowerCase();

                    if (type === "checkbox" && $field.is(":checkbox")) {
                        return $field.is(":checked") ? 1 : 0;
                    }

                    if (type === "radio" && $field.is(":radio")) {
                        return $field.is(":checked") ? $field.val() : "";
                    }

                    if (type === "select-multiple" || $field.is("select")) {
                        return $field.val();
                    }

                    return $field.val();
                }

                /**
                 * Compare two values with operator
                 */
                function compareValues(current, operator, required) {
                    // Convert to string for comparison if needed
                    if (operator === "=" || operator === "==") {
                        return current == required;
                    }

                    if (operator === "!=" || operator === "<>") {
                        return current != required;
                    }

                    if (operator === ">") {
                        return parseFloat(current) > parseFloat(required);
                    }

                    if (operator === "<") {
                        return parseFloat(current) < parseFloat(required);
                    }

                    if (operator === ">=") {
                        return parseFloat(current) >= parseFloat(required);
                    }

                    if (operator === "<=") {
                        return parseFloat(current) <= parseFloat(required);
                    }

                    if (operator === "contains") {
                        if (Array.isArray(current)) {
                            return current.includes(required);
                        }
                        return current.toString().includes(required);
                    }

                    if (operator === "not-contains") {
                        if (Array.isArray(current)) {
                            return !current.includes(required);
                        }
                        return !current.toString().includes(required);
                    }

                    return true;
                }

                /**
                 * Initialize all required fields
                 */
                function initRequiredFields() {
                    $(".pf-field-wrapper[data-required]").each(function() {
                        var $field = $(this);
                        checkRequired($field);
                    });
                }

                /**
                 * Watch for changes on source fields
                 */
                function watchRequiredFields() {
                    $(".pf-field-wrapper[data-required]").each(function() {
                        var required = $(this).data("required");
                        if (required) {
                            var $source = $("#" + required);
                            if ($source.length) {
                                $source.on("change keyup", function() {
                                    $(".pf-field-wrapper[data-required=\"" + required + "\"]").each(function() {
                                        checkRequired($(this));
                                    });
                                });
                            }
                        }
                    });
                }

                // Initialize on document ready
                $(document).ready(function() {
                    initRequiredFields();
                    watchRequiredFields();

                    // Re-initialize after AJAX
                    $(document).on("pf-field-rendered", function() {
                        initRequiredFields();
                        watchRequiredFields();
                    });
                });

            })(jQuery);
        ';
    }
}