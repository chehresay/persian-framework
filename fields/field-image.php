<?php
/**
 * Persian Framework - Image Field
 * Single image selection with WordPress Media Library
 * Extends Media field with image-specific features
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Image extends PersianFramework_Field_Media {

    // This class extends the Media field
    // It inherits all functionality from PersianFramework_Field_Media
    // and adds image-specific features if needed

    public function __construct($field, $value = null) {
        parent::__construct($field, $value);
    }

    public function render() {
        // Add image-specific attributes
        $this->field['image_only'] = true;

        // Add image size options if not set
        if (!isset($this->field['preview_size'])) {
            $this->field['preview_size'] = 'medium';
        }

        // Call parent render
        parent::render();

        // Additional image-specific styling
        $this->enqueue_scripts();
    }

    private function enqueue_scripts() {
        static $enqueued = false;

        if (!$enqueued) {
            ?>
            <style>
                .pf-field-image .pf-media-preview img {
                    aspect-ratio: 1;
                    object-fit: cover;
                }

                .pf-field-image .pf-media-preview {
                    border-radius: 18px;
                    overflow: hidden;
                }

                .pf-field-image .pf-media-preview .dashicons {
                    font-size: 48px;
                    width: 48px;
                    height: 48px;
                    color: #cbd5e1;
                }

                body.dark-mode .pf-field-image .pf-media-preview .dashicons {
                    color: #334155;
                }
            </style>
            <?php
            $enqueued = true;
        }
    }
}