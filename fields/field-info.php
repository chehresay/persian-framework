<?php
/**
 * Persian Framework - Info Field
 * Display system information, notices, or static content
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Info {

    private $field;
    private $value;

    public function __construct($field, $value = null) {
        $this->field = $field;
        $this->value = $value;
    }

    public function render() {

        $id = isset($this->field['id']) ? $this->field['id'] : '';
        $title = isset($this->field['title']) ? $this->field['title'] : '';
        $desc = isset($this->field['desc']) ? $this->field['desc'] : '';
        $type = isset($this->field['info_type']) ? $this->field['info_type'] : 'info'; // info, warning, error, success
        $icon = isset($this->field['icon']) ? $this->field['icon'] : 'dashicons-info';

        // Style based on type
        $styles = array(
                'info' => array(
                        'bg' => '#eff6ff',
                        'border' => '#3b82f6',
                        'text' => '#1e40af',
                        'icon' => 'dashicons-info'
                ),
                'warning' => array(
                        'bg' => '#fffbeb',
                        'border' => '#f59e0b',
                        'text' => '#92400e',
                        'icon' => 'dashicons-warning'
                ),
                'error' => array(
                        'bg' => '#fef2f2',
                        'border' => '#ef4444',
                        'text' => '#991b1b',
                        'icon' => 'dashicons-dismiss'
                ),
                'success' => array(
                        'bg' => '#f0fdf4',
                        'border' => '#22c55e',
                        'text' => '#166534',
                        'icon' => 'dashicons-yes-alt'
                )
        );

        $style = isset($styles[$type]) ? $styles[$type] : $styles['info'];
        $icon_class = isset($this->field['icon']) ? $this->field['icon'] : $style['icon'];

        ?>
        <div class="pf-field-wrapper pf-field-info pf-info-type-<?php echo esc_attr($type); ?>">
            <?php if ($title): ?>
                <div class="pf-info-header">
                    <span class="dashicons <?php echo esc_attr($icon_class); ?>"></span>
                    <h3 class="pf-info-title"><?php echo esc_html($title); ?></h3>
                </div>
            <?php endif; ?>

            <div class="pf-info-content" style="
                    background: <?php echo esc_attr($style['bg']); ?>;
                    border-left: 4px solid <?php echo esc_attr($style['border']); ?>;
                    color: <?php echo esc_attr($style['text']); ?>;
                    padding: 16px 20px;
                    border-radius: 8px;
                    margin: 8px 0;
                    ">
                <?php echo wp_kses_post($desc); ?>
            </div>

            <?php if (isset($this->field['desc_bottom'])): ?>
                <p class="pf-field-desc"><?php echo esc_html($this->field['desc_bottom']); ?></p>
            <?php endif; ?>
        </div>

        <style>
            .pf-field-info {
                margin: 4px 0;
            }
            .pf-info-header {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 8px;
            }
            .pf-info-header .dashicons {
                font-size: 24px;
                width: 24px;
                height: 24px;
                color: #3b82f6;
            }
            .pf-info-title {
                margin: 0;
                font-size: 16px;
                font-weight: 600;
                color: #1a2332;
            }
            .pf-info-content {
                font-size: 14px;
                line-height: 1.6;
            }
            .pf-info-content table {
                width: 100%;
                border-collapse: collapse;
                margin: 0;
            }
            .pf-info-content table tr {
                border-bottom: 1px solid rgba(0,0,0,0.06);
            }
            .pf-info-content table tr:last-child {
                border-bottom: none;
            }
            .pf-info-content table td {
                padding: 6px 12px;
                vertical-align: top;
            }
            .pf-info-content table td:first-child {
                font-weight: 600;
                width: 40%;
            }
            .pf-info-content code {
                background: rgba(0,0,0,0.06);
                padding: 2px 6px;
                border-radius: 4px;
                font-size: 13px;
            }
            .pf-info-content .pf-info-badge {
                display: inline-block;
                padding: 2px 10px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
            }
            .pf-info-content .pf-info-badge.success {
                background: #22c55e;
                color: white;
            }
            .pf-info-content .pf-info-badge.warning {
                background: #f59e0b;
                color: white;
            }
            .pf-info-content .pf-info-badge.error {
                background: #ef4444;
                color: white;
            }
            .pf-info-content .pf-info-badge.info {
                background: #3b82f6;
                color: white;
            }

            /* Dark mode */
            body.dark-mode .pf-info-title {
                color: #e2e8f0;
            }
            body.dark-mode .pf-info-content {
                border-color: #334155 !important;
            }
            body.dark-mode .pf-info-content table tr {
                border-color: #334155;
            }
            body.dark-mode .pf-info-content code {
                background: rgba(255,255,255,0.08);
                color: #e2e8f0;
            }
            body.dark-mode .pf-info-header .dashicons {
                color: #818cf8;
            }

            /* Info type styles */
            .pf-info-type-warning .pf-info-header .dashicons {
                color: #f59e0b;
            }
            .pf-info-type-error .pf-info-header .dashicons {
                color: #ef4444;
            }
            .pf-info-type-success .pf-info-header .dashicons {
                color: #22c55e;
            }
        </style>

        <?php
        $this->enqueue_scripts();
    }

    private function enqueue_scripts() {
        static $enqueued = false;

        if (!$enqueued) {
            ?>
            <script>
                (function($) {
                    'use strict';

                    // Add any interactive behavior here if needed
                    // For example, auto-refresh system info
                    if ($('.pf-field-info').length) {
                        // System info is static, no action needed
                        //console.log('Info field loaded successfully.');
                    }

                })(jQuery);
            </script>
            <?php
            $enqueued = true;
        }
    }
}