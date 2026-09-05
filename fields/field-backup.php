<?php
/**
 * Persian Framework - Backup Field
 * Create and manage backups
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Backup {

    private $field;
    private $value;

    public function __construct($field, $value = null) {
        $this->field = $field;
        $this->value = $value;
    }

    public function render() {
        $id = isset($this->field['id']) ? $this->field['id'] : '';
        $name = isset($this->field['name']) ? $this->field['name'] : $id;
        $instance_id = isset($this->field['instance_id']) ? $this->field['instance_id'] : '';
        $opt_name = isset($this->field['opt_name']) ? $this->field['opt_name'] : '';

        $backups = get_option($opt_name . '_backups', array());

        ?>
        <div class="pf-field-wrapper pf-field-backup">
            <?php if (isset($this->field['title'])): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-backup-container">
                <div class="pf-backup-actions">
                    <button type="button" class="pf-btn pf-btn-primary pf-create-backup-btn"
                            data-optname="<?php echo esc_attr($opt_name); ?>"
                            data-instance="<?php echo esc_attr($instance_id); ?>">
                        <span class="dashicons dashicons-backup"></span>
                        <?php esc_html_e('Create Backup', 'persian-framework'); ?>
                    </button>
                    <span class="pf-backup-spinner" style="display:none;">
                        <span class="dashicons dashicons-update spin"></span>
                    </span>
                </div>

                <div class="pf-backup-list">
                    <h4><?php esc_html_e('Available Backups', 'persian-framework'); ?></h4>
                    <?php if (empty($backups)): ?>
                        <p class="pf-backup-empty"><?php esc_html_e('No backups found.', 'persian-framework'); ?></p>
                    <?php else: ?>
                        <div class="pf-backup-items">
                            <?php foreach (array_reverse($backups) as $timestamp => $data): ?>
                                <div class="pf-backup-item" data-timestamp="<?php echo esc_attr($timestamp); ?>">
                                    <div class="pf-backup-info">
                                        <span class="pf-backup-date">
                                            <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $timestamp)); ?>
                                        </span>
                                        <span class="pf-backup-size">
                                            <?php echo esc_html(size_format(strlen(wp_json_encode($data)))); ?>
                                        </span>
                                    </div>
                                    <div class="pf-backup-actions">
                                        <button type="button" class="pf-btn pf-btn-secondary pf-restore-backup-btn"
                                                data-optname="<?php echo esc_attr($opt_name); ?>"
                                                data-timestamp="<?php echo esc_attr($timestamp); ?>">
                                            <span class="dashicons dashicons-restore"></span>
                                            <?php esc_html_e('Restore', 'persian-framework'); ?>
                                        </button>
                                        <button type="button" class="pf-btn pf-btn-danger pf-delete-backup-btn"
                                                data-optname="<?php echo esc_attr($opt_name); ?>"
                                                data-timestamp="<?php echo esc_attr($timestamp); ?>">
                                            <span class="dashicons dashicons-trash"></span>
                                            <?php esc_html_e('Delete', 'persian-framework'); ?>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (isset($this->field['desc'])): ?>
                <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php
        $this->enqueue_scripts($opt_name, $instance_id);
    }

    private function enqueue_scripts($opt_name, $instance_id) {
        static $pf_backup_enqueued = false;

        if (!$pf_backup_enqueued) {
            ?>
            <style>
                .pf-backup-container {
                    margin-top: 8px;
                }

                .pf-backup-actions {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    margin-bottom: 16px;
                }

                .pf-backup-spinner .dashicons {
                    animation: pfSpin 1s linear infinite;
                }

                .pf-backup-list h4 {
                    margin: 0 0 12px 0;
                    font-size: 14px;
                    color: #1a2332;
                }
                body.dark-mode .pf-backup-list h4 {
                    color: #e2e8f0;
                }

                .pf-backup-empty {
                    color: #94a3b8;
                    text-align: center;
                    padding: 20px 0;
                }

                .pf-backup-items {
                    display: flex;
                    flex-direction: column;
                    gap: 8px;
                }

                .pf-backup-item {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 12px 16px;
                    background: white;
                    border: 1px solid #e8edf4;
                    border-radius: 8px;
                    transition: all 0.2s ease;
                }
                body.dark-mode .pf-backup-item {
                    background: #1e293b;
                    border-color: #334155;
                }

                .pf-backup-item:hover {
                    border-color: #6366f1;
                    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.1);
                }

                .pf-backup-info {
                    display: flex;
                    align-items: center;
                    gap: 16px;
                }

                .pf-backup-date {
                    font-weight: 600;
                    color: #1a2332;
                }
                body.dark-mode .pf-backup-date {
                    color: #e2e8f0;
                }

                .pf-backup-size {
                    font-size: 13px;
                    color: #94a3b8;
                }

                .pf-backup-actions {
                    display: flex;
                    gap: 6px;
                    margin-bottom: 0;
                }

                .pf-backup-actions .pf-btn {
                    padding: 6px 12px;
                    font-size: 12px;
                }
                .pf-backup-actions .pf-btn .dashicons {
                    font-size: 14px;
                    width: 14px;
                    height: 14px;
                }

                @keyframes pfSpin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }

                @media (max-width: 768px) {
                    .pf-backup-item {
                        flex-direction: column;
                        align-items: stretch;
                        gap: 10px;
                    }
                    .pf-backup-info {
                        justify-content: space-between;
                    }
                    .pf-backup-actions {
                        justify-content: flex-end;
                    }
                }
            </style>

            <script>
                (function($) {
                    'use strict';

                    var optName = '<?php echo esc_js($opt_name); ?>';
                    var instanceId = '<?php echo esc_js($instance_id); ?>';

                    $(document).on('click', '.pf-create-backup-btn', function() {
                        var $btn = $(this);
                        var $spinner = $btn.closest('.pf-backup-actions').find('.pf-backup-spinner');

                        $btn.prop('disabled', true);
                        $spinner.show();

                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'pf_create_backup',
                                opt_name: optName,
                                instance_id: instanceId,
                                nonce: pf_ajax.nonce
                            },
                            success: function(response) {
                                if (response.success) {
                                    location.reload();
                                } else {
                                    alert(response.data.message || '<?php esc_html_e('Failed to create backup.', 'persian-framework'); ?>');
                                }
                            },
                            error: function() {
                                alert('<?php esc_html_e('Connection error.', 'persian-framework'); ?>');
                            },
                            complete: function() {
                                $btn.prop('disabled', false);
                                $spinner.hide();
                            }
                        });
                    });

                    $(document).on('click', '.pf-restore-backup-btn', function() {
                        var $btn = $(this);
                        var timestamp = $btn.data('timestamp');

                        if (confirm('<?php esc_html_e('Restore this backup? This will overwrite all current settings.', 'persian-framework'); ?>')) {
                            $.ajax({
                                url: ajaxurl,
                                type: 'POST',
                                data: {
                                    action: 'pf_restore_backup',
                                    opt_name: optName,
                                    timestamp: timestamp,
                                    nonce: pf_ajax.nonce
                                },
                                success: function(response) {
                                    if (response.success) {
                                        alert(response.data.message || '<?php esc_html_e('Backup restored successfully.', 'persian-framework'); ?>');
                                        location.reload();
                                    } else {
                                        alert(response.data.message || '<?php esc_html_e('Failed to restore backup.', 'persian-framework'); ?>');
                                    }
                                },
                                error: function() {
                                    alert('<?php esc_html_e('Connection error.', 'persian-framework'); ?>');
                                }
                            });
                        }
                    });

                    $(document).on('click', '.pf-delete-backup-btn', function() {
                        var $btn = $(this);
                        var timestamp = $btn.data('timestamp');

                        if (confirm('<?php esc_html_e('Delete this backup?', 'persian-framework'); ?>')) {
                            $.ajax({
                                url: ajaxurl,
                                type: 'POST',
                                data: {
                                    action: 'pf_delete_backup',
                                    opt_name: optName,
                                    timestamp: timestamp,
                                    nonce: pf_ajax.nonce
                                },
                                success: function(response) {
                                    if (response.success) {
                                        $btn.closest('.pf-backup-item').fadeOut(300, function() {
                                            $(this).remove();
                                            if (!$('.pf-backup-item').length) {
                                                $('.pf-backup-list').append('<p class="pf-backup-empty"><?php esc_html_e('No backups found.', 'persian-framework'); ?></p>');
                                            }
                                        });
                                    } else {
                                        alert(response.data.message || '<?php esc_html_e('Failed to delete backup.', 'persian-framework'); ?>');
                                    }
                                },
                                error: function() {
                                    alert('<?php esc_html_e('Connection error.', 'persian-framework'); ?>');
                                }
                            });
                        }
                    });

                })(jQuery);
            </script>
            <?php
            $pf_backup_enqueued = true;
        }
    }
}