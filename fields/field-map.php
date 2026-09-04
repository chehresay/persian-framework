<?php
/**
 * Persian Framework - Map Field
 * Location picker with OpenStreetMap
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

class PersianFramework_Field_Map {

    private $field;
    private $value;

    public function __construct($field, $value = null) {
        $this->field = $field;
        $this->value = $value;
    }

    public function render() {
        $id = isset($this->field['id']) ? $this->field['id'] : '';
        $name = isset($this->field['name']) ? $this->field['name'] : $id;

        $defaults = array(
            'lat' => isset($this->field['default_lat']) ? $this->field['default_lat'] : 35.6892,
            'lng' => isset($this->field['default_lng']) ? $this->field['default_lng'] : 51.3890,
            'address' => '',
            'zoom' => 12
        );

        $value = wp_parse_args(
            is_array($this->value) ? $this->value : array(),
            $defaults
        );

        $height = isset($this->field['height']) ? $this->field['height'] : 300;

        ?>
        <div class="pf-field-wrapper pf-field-map">
            <?php if (isset($this->field['title'])): ?>
                <label class="pf-field-label">
                    <?php echo esc_html($this->field['title']); ?>
                    <?php if (isset($this->field['subtitle'])): ?>
                        <span class="pf-subtitle"><?php echo esc_html($this->field['subtitle']); ?></span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <div class="pf-map-container" id="<?php echo esc_attr($id); ?>_map_container">
                <div class="pf-map-fields">
                    <div class="pf-map-row">
                        <label class="pf-map-label">
                            <span class="pf-map-label-text"><?php _e('Latitude', 'persian-framework'); ?></span>
                            <input type="number"
                                   step="any"
                                   min="-90"
                                   max="90"
                                   name="<?php echo esc_attr($name); ?>[lat]"
                                   value="<?php echo esc_attr($value['lat']); ?>"
                                   class="pf-map-input pf-map-lat" />
                        </label>

                        <label class="pf-map-label">
                            <span class="pf-map-label-text"><?php _e('Longitude', 'persian-framework'); ?></span>
                            <input type="number"
                                   step="any"
                                   min="-180"
                                   max="180"
                                   name="<?php echo esc_attr($name); ?>[lng]"
                                   value="<?php echo esc_attr($value['lng']); ?>"
                                   class="pf-map-input pf-map-lng" />
                        </label>

                        <label class="pf-map-label">
                            <span class="pf-map-label-text"><?php _e('Zoom', 'persian-framework'); ?></span>
                            <input type="number"
                                   min="1"
                                   max="22"
                                   name="<?php echo esc_attr($name); ?>[zoom]"
                                   value="<?php echo esc_attr($value['zoom']); ?>"
                                   class="pf-map-input pf-map-zoom" />
                        </label>
                    </div>

                    <div class="pf-map-row">
                        <label class="pf-map-label pf-map-address-label">
                            <span class="pf-map-label-text"><?php _e('Address', 'persian-framework'); ?></span>
                            <input type="text"
                                   name="<?php echo esc_attr($name); ?>[address]"
                                   value="<?php echo esc_attr($value['address']); ?>"
                                   class="pf-map-input pf-map-address"
                                   placeholder="<?php esc_attr_e('Enter address...', 'persian-framework'); ?>" />
                            <button type="button" class="pf-btn pf-btn-secondary pf-map-geocode">
                                <span class="dashicons dashicons-search"></span>
                            </button>
                        </label>
                    </div>
                </div>

                <div class="pf-map-display" style="height: <?php echo esc_attr($height); ?>px;">
                    <div id="<?php echo esc_attr($id); ?>_map" class="pf-map-iframe"></div>
                    <div class="pf-map-loading">
                        <span class="dashicons dashicons-update spin"></span>
                        <?php _e('Loading map...', 'persian-framework'); ?>
                    </div>
                </div>

                <div class="pf-map-actions">
                    <a href="https://www.openstreetmap.org/?mlat=<?php echo esc_attr($value['lat']); ?>&mlon=<?php echo esc_attr($value['lng']); ?>#map=<?php echo esc_attr($value['zoom']); ?>/<?php echo esc_attr($value['lat']); ?>/<?php echo esc_attr($value['lng']); ?>"
                       target="_blank"
                       rel="noopener"
                       class="pf-btn pf-btn-secondary pf-map-open">
                        <span class="dashicons dashicons-location"></span>
                        <?php _e('Open in OSM', 'persian-framework'); ?>
                    </a>
                    <a href="https://www.google.com/maps?q=<?php echo esc_attr($value['lat']); ?>,<?php echo esc_attr($value['lng']); ?>"
                       target="_blank"
                       rel="noopener"
                       class="pf-btn pf-btn-secondary pf-map-open">
                        <span class="dashicons dashicons-location-alt"></span>
                        <?php _e('Open in Google Maps', 'persian-framework'); ?>
                    </a>
                    <button type="button" class="pf-btn pf-btn-secondary pf-map-current">
                        <span class="dashicons dashicons-location"></span>
                        <?php _e('My Location', 'persian-framework'); ?>
                    </button>
                </div>
            </div>

            <?php if (isset($this->field['desc'])): ?>
                <p class="pf-field-desc"><?php echo esc_html($this->field['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php
        $this->enqueue_scripts($id, $value);
    }

    private function enqueue_scripts($id, $value) {
        static $enqueued = false;

        if (!$enqueued) {
            // Leaflet CSS
            wp_enqueue_style(
                'leaflet-css',
                    PERSIAN_FRAMEWORK_URL . 'vendor/leaflet/leaflet.min.css',
                array(),
                '1.9.4'
            );

            // Leaflet JS
            wp_enqueue_script(
                'leaflet-js',
                    PERSIAN_FRAMEWORK_URL . 'vendor/leaflet/leaflet.min.js',
                array('jquery'),
                '1.9.4',
                true
            );

            $enqueued = true;
        }

        ?>
        <style>
            .pf-map-container {
                margin-top: 8px;
            }

            .pf-map-fields {
                display: flex;
                flex-direction: column;
                gap: 10px;
                margin-bottom: 12px;
                padding: 12px;
                background: #fafbfc;
                border: 2px solid #e8edf4;
                border-radius: 12px 12px 0 0;
            }
            body.dark-mode .pf-map-fields {
                background: #0f172a;
                border-color: #334155;
            }

            .pf-map-row {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }

            .pf-map-label {
                display: flex;
                flex-direction: column;
                gap: 4px;
                flex: 1;
                min-width: 100px;
            }

            .pf-map-label-text {
                font-size: 12px;
                font-weight: 600;
                color: #6b7a8f;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            body.dark-mode .pf-map-label-text {
                color: #94a3b8;
            }

            .pf-map-input {
                padding: 8px 12px;
                border: 2px solid #e8edf4;
                border-radius: 8px;
                font-size: 14px;
                font-family: inherit;
                background: white;
                width: 100%;
            }
            body.dark-mode .pf-map-input {
                background: #1e293b;
                border-color: #334155;
                color: #e2e8f0;
            }

            .pf-map-input:focus {
                border-color: #6366f1;
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
                outline: none;
            }

            .pf-map-address-label {
                flex: 3;
                position: relative;
            }

            .pf-map-address {
                padding-right: 50px !important;
            }

            .pf-map-geocode {
                position: absolute;
                right: 4px;
                top: 50%;
                transform: translateY(50%);
                padding: 4px 10px;
                min-height: 0;
                border-radius: 6px;
            }
            .pf-map-geocode .dashicons {
                font-size: 14px;
                width: 14px;
                height: 14px;
            }

            .pf-map-display {
                position: relative;
                width: 100%;
                border: 2px solid #e8edf4;
                border-top: none;
                border-radius: 0 0 12px 12px;
                overflow: hidden;
            }
            body.dark-mode .pf-map-display {
                border-color: #334155;
            }

            .pf-map-iframe {
                width: 100%;
                height: 100%;
                min-height: 200px;
            }

            .pf-map-loading {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                display: flex;
                align-items: center;
                gap: 10px;
                color: #94a3b8;
                font-size: 14px;
            }
            .pf-map-loading .dashicons {
                font-size: 20px;
                width: 20px;
                height: 20px;
                animation: pfSpin 1s linear infinite;
            }

            .pf-map-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 12px;
            }

            .pf-map-actions .pf-btn {
                padding: 6px 14px;
                font-size: 13px;
            }
            .pf-map-actions .pf-btn .dashicons {
                font-size: 14px;
                width: 14px;
                height: 14px;
            }

            @media (max-width: 768px) {
                .pf-map-row {
                    flex-direction: column;
                }
                .pf-map-label {
                    min-width: auto;
                }
                .pf-map-actions {
                    flex-direction: column;
                }
                .pf-map-actions .pf-btn {
                    justify-content: center;
                }
            }
        </style>

        <script>
            (function($) {
                'use strict';

                var containerId = '<?php echo esc_js($id); ?>_map_container';
                var mapId = '<?php echo esc_js($id); ?>_map';
                var lat = <?php echo floatval($value['lat']); ?>;
                var lng = <?php echo floatval($value['lng']); ?>;
                var zoom = <?php echo intval($value['zoom']); ?>;

                var mapInitialized = false;
                var map;

                function initMap() {
                    if (mapInitialized) return;
                    if (typeof L === 'undefined') {
                        setTimeout(initMap, 500);
                        return;
                    }

                    var container = document.getElementById(mapId);
                    if (!container) return;

                    map = L.map(mapId).setView([lat, lng], zoom);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    var marker = L.marker([lat, lng], {
                        draggable: true
                    }).addTo(map);

                    marker.on('dragend', function(e) {
                        var pos = marker.getLatLng();
                        updateFields(pos.lat, pos.lng);
                    });

                    map.on('click', function(e) {
                        var pos = e.latlng;
                        marker.setLatLng(pos);
                        updateFields(pos.lat, pos.lng);
                    });

                    mapInitialized = true;
                    $('.pf-map-loading').hide();

                    // Update marker position from fields
                    $(document).on('change input', '.pf-map-lat, .pf-map-lng', function() {
                        var newLat = parseFloat($('.pf-map-lat').val()) || 0;
                        var newLng = parseFloat($('.pf-map-lng').val()) || 0;
                        if (mapInitialized) {
                            marker.setLatLng([newLat, newLng]);
                            map.setView([newLat, newLng], map.getZoom());
                        }
                    });
                }

                function updateFields(lat, lng) {
                    $('.pf-map-lat').val(lat.toFixed(6));
                    $('.pf-map-lng').val(lng.toFixed(6));
                    $('.pf-map-lat, .pf-map-lng').trigger('change');
                }

                // Geocode address
                $(document).on('click', '.pf-map-geocode', function() {
                    var address = $('.pf-map-address').val();
                    if (!address) {
                        alert('<?php esc_js(__('Please enter an address.', 'persian-framework')); ?>');
                        return;
                    }

                    // Use Nominatim API
                    var url = 'https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(address);

                    $.ajax({
                        url: url,
                        dataType: 'json',
                        success: function(data) {
                            if (data && data.length > 0) {
                                var result = data[0];
                                var lat = parseFloat(result.lat);
                                var lng = parseFloat(result.lon);
                                updateFields(lat, lng);
                                if (mapInitialized) {
                                    map.setView([lat, lng], 16);
                                }
                                $('.pf-map-address').val(result.display_name);
                            } else {
                                alert('<?php esc_js(__('Address not found.', 'persian-framework')); ?>');
                            }
                        },
                        error: function() {
                            alert('<?php esc_js(__('Geocoding service unavailable.', 'persian-framework')); ?>');
                        }
                    });
                });

                // Get current location
                $(document).on('click', '.pf-map-current', function() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(pos) {
                            var lat = pos.coords.latitude;
                            var lng = pos.coords.longitude;
                            updateFields(lat, lng);
                            if (mapInitialized) {
                                map.setView([lat, lng], 16);
                            }
                        }, function() {
                            alert('<?php esc_js(__('Unable to get your location.', 'persian-framework')); ?>');
                        });
                    } else {
                        alert('<?php esc_js(__('Geolocation is not supported by your browser.', 'persian-framework')); ?>');
                    }
                });

                // Initialize map
                $(document).ready(function() {
                    setTimeout(initMap, 300);
                });

                // Re-initialize on tab switch
                $(document).on('pf-tab-switch', function() {
                    setTimeout(function() {
                        if (mapInitialized) {
                            map.invalidateSize();
                        }
                    }, 500);
                });

            })(jQuery);
        </script>
        <?php
    }
}