<?php
/**
 * Persian Framework - Complete Demo Configuration
 * Demonstrates all available field types with their options
 *
 * @package PersianFramework
 */

if (!defined('ABSPATH')) {
    exit;
}

// ================================================================
// 1. Create plugin instance
// ================================================================
$demo_framework = PersianFramework::get_instance('persian-framework');

// ================================================================
// 2. Main configuration
// ================================================================
$demo_framework->set_args(array(
    // Database option name
    'opt_name' => 'persian_framework_options',

    // Display information
    'display_name' => 'Persian Framework',
    'display_version' => '1.0.0',
    'display_info' => 'Advanced Options Framework',

    // Menu settings
    'menu_title' => 'Persian Framework',
    'page_title' => 'Persian Framework Settings',
    'menu_slug' => 'persian-framework',
    'menu_icon' => 'dashicons-admin-generic',
    'menu_position' => 58,

    // Capability
    'capability' => 'manage_options',

    // Features
    'show_import_export' => true,
    'show_backup' => true,
    'dev_mode' => false,

    // Global variable
    'global_variable' => 'persian_framework_options',
));

// ================================================================
// 3. SECTION: Text Fields
// ================================================================
$demo_framework->add_section(array(
    'id' => 'text-fields',
    'title' => 'Text Fields',
    'icon' => 'dashicons-edit',
    'subtitle' => 'All text-based input fields',
    'fields' => array(

        // ----- Text Field -----
        array(
            'id' => 'text_field',
            'type' => 'text',
            'title' => 'Text Field',
            'subtitle' => 'Simple text input',
            'placeholder' => 'Enter some text...',
            'default' => 'Hello World',
            'desc' => 'Standard text input field.',
        ),

        // ----- Textarea Field -----
        array(
            'id' => 'textarea_field',
            'type' => 'textarea',
            'title' => 'Textarea Field',
            'subtitle' => 'Multi-line text input',
            'placeholder' => 'Enter long text...',
            'default' => "Line 1\nLine 2\nLine 3",
            'rows' => 5,
            'desc' => 'Multi-line text input field.',
        ),

        // ----- Number Field -----
        array(
            'id' => 'number_field',
            'type' => 'number',
            'title' => 'Number Field',
            'subtitle' => 'Numeric input with min/max',
            'default' => 42,
            'min' => 0,
            'max' => 100,
            'step' => 1,
            'desc' => 'Number input with minimum and maximum limits.',
        ),

        // ----- Email Field -----
        array(
            'id' => 'email_field',
            'type' => 'email',
            'title' => 'Email Field',
            'subtitle' => 'Email with validation',
            'placeholder' => 'example@domain.com',
            'default' => 'admin@example.com',
            'desc' => 'Email field with HTML5 validation.',
        ),

        // ----- URL Field -----
        array(
            'id' => 'url_field',
            'type' => 'url',
            'title' => 'URL Field',
            'subtitle' => 'Website URL input',
            'placeholder' => 'https://example.com',
            'default' => 'https://parswp.ir',
            'desc' => 'URL field with HTML5 validation.',
        ),

        // ----- Password Field -----
        array(
            'id' => 'password_field',
            'type' => 'password',
            'title' => 'Password Field',
            'subtitle' => 'Hidden password input',
            'placeholder' => 'Enter password...',
            'default' => '',
            'desc' => 'Password field with masked characters.',
        ),

        // ----- Hidden Field -----
        array(
            'id' => 'hidden_field',
            'type' => 'hidden',
            'default' => 'hidden_value',
            'desc' => 'Hidden field (not visible to user).',
        ),


        // ----- Multi-Text Field -----
        array(
            'id' => 'multi_text',
            'type' => 'multi-text',
            'title' => __('Multi Text Option', 'persian-framework'),
            'subtitle' => __('Add multiple text values with validation.', 'persian-framework'),
            'desc' => __('This field stores multiple text values in an array.', 'persian-framework'),
            'add_text' => __('Add More', 'persian-framework'),
            'show_empty' => true,
            'max' => 10,
            'min' => 1,
            'placeholder' => __('Enter text...', 'persian-framework'),
            'validate' => '', // color, email, url, numeric, alpha, alphanumeric
            'default' => array('Value 1', 'Value 2'),
        ),

    )
));

// ================================================================
// 4. SECTION: Selection Fields
// ================================================================
$demo_framework->add_section(array(
    'id' => 'selection-fields',
    'title' => 'Selection Fields',
    'icon' => 'dashicons-forms',
    'subtitle' => 'Switch, Checkbox, Radio, Select, and Button Set',
    'fields' => array(

        // ----- Switch Field -----
        array(
            'id' => 'switch_field',
            'type' => 'switch',
            'title' => 'Switch Field',
            'subtitle' => 'Toggle switch with custom labels',
            'default' => true,
            'on' => 'Active',
            'off' => 'Inactive',
            'desc' => 'Toggle switch with custom ON/OFF labels.',
        ),

        // ----- Switch Field (Colored) -----
        array(
            'id' => 'switch_colored',
            'type' => 'switch',
            'title' => 'Colored Switch',
            'subtitle' => 'Switch with custom colors',
            'default' => false,
            'on' => 'Enabled',
            'off' => 'Disabled',
            'desc' => 'Switch with customizable colors (add "color" attribute).',
        ),

        // ----- Single Checkbox -----
        array(
            'id' => 'checkbox_single',
            'type' => 'checkbox',
            'title' => 'Single Checkbox',
            'subtitle' => 'Boolean checkbox',
            'single' => true,
            'label' => 'Enable feature',
            'default' => true,
            'desc' => 'A single checkbox for boolean values.',
        ),

        // ----- Multiple Checkbox -----
        array(
            'id' => 'checkbox_multiple',
            'type' => 'checkbox',
            'title' => 'Multiple Checkboxes',
            'subtitle' => 'Select multiple options',
            'default' => array('option1'),
            'options' => array(
                'option1' => 'Option 1',
                'option2' => 'Option 2',
                'option3' => 'Option 3',
            ),
            'desc' => 'Multiple checkboxes for multiple selection.',
        ),

        // ----- Radio Field -----
        array(
            'id' => 'radio_field',
            'type' => 'radio',
            'title' => 'Radio Field',
            'subtitle' => 'Select one option',
            'default' => 'option1',
            'options' => array(
                'option1' => 'Option 1',
                'option2' => 'Option 2',
                'option3' => 'Option 3',
            ),
            'desc' => 'Radio buttons for single selection.',
        ),

        // ----- Select Field -----
        array(
            'id' => 'select_field',
            'type' => 'select',
            'title' => 'Select Field',
            'subtitle' => 'Dropdown selection',
            'default' => 'option2',
            'options' => array(
                '' => 'Select an option...',
                'option1' => 'Option 1',
                'option2' => 'Option 2',
                'option3' => 'Option 3',
            ),
            'desc' => 'Standard dropdown select field.',
        ),

        // ----- Multi Select -----
        array(
            'id' => 'multi_select_field',
            'type' => 'multi-select',
            'title' => 'Multi Select',
            'subtitle' => 'Select multiple options',
            'default' => array('option1', 'option3'),
            'options' => array(
                'option1' => 'Option 1',
                'option2' => 'Option 2',
                'option3' => 'Option 3',
                'option4' => 'Option 4',
            ),
            'searchable' => true,
            'max' => 3,
            'desc' => 'Multi-select with search, tags, and max limit.',
        ),

        array(
            'id' => 'site_layout',
            'type' => 'select-image',
            'title' => 'Site Layout',
            'subtitle' => 'Choose the layout style',
            'default' => 'full',
            'image_width' => 120,
            'image_height' => 80,
            'cols' => 4,
            'images_dir' => PERSIAN_FRAMEWORK_ASSETS . 'images/',
            'desc' => 'Select the layout style for your site',
            'options' => array(
                'none' => 'None',
                'left_side' => 'Left Sidebar',
                'right_side' => 'Right Sidebar',
                'both' => 'Both Sidebars',
                'both_left' => 'Both Left',
                'both_right' => 'Both Right',
                'full' => 'Full Width',
            ),
        ),

        // ----- Button Set -----
        array(
            'id' => 'button_set',
            'type' => 'button-set',
            'title' => 'Button Set',
            'subtitle' => 'Visual radio buttons',
            'default' => 'layout1',
            'options' => array(
                'layout1' => '📐 Layout 1',
                'layout2' => '📐 Layout 2',
                'layout3' => '📐 Layout 3',
            ),
            'icons' => array(
                'layout1' => 'dashicons-layout',
                'layout2' => 'dashicons-screenoptions',
                'layout3' => 'dashicons-list-view',
            ),
            'desc' => 'Button set as visual alternative to radio buttons.',
        ),

    )
));

// ================================================================
// 5. SECTION: Media Fields
// ================================================================
$demo_framework->add_section(array(
    'id' => 'media-fields',
    'title' => 'Media Fields',
    'icon' => 'dashicons-format-image',
    'subtitle' => 'Image, Gallery, and Media fields',
    'fields' => array(

        // ----- Image Field -----
        array(
            'id' => 'image_field',
            'type' => 'image',
            'title' => 'Image Field',
            'subtitle' => 'Single image selection',
            'desc' => 'Select a single image using WordPress Media Library.',
        ),

        // ----- Media Field -----
        array(
            'id' => 'media_field',
            'type' => 'media',
            'title' => 'Media Field',
            'subtitle' => 'Any media file',
            'desc' => 'Select any media file (image, PDF, video, etc.).',
        ),

        // ----- Gallery Field -----
        array(
            'id' => 'gallery_field',
            'type' => 'gallery',
            'title' => 'Gallery Field',
            'subtitle' => 'Multiple images gallery',
            'max' => 12,
            'desc' => 'Select and manage a gallery of images.',
        ),


        array(
            'id' => 'slider_slides',
            'type' => 'slides',
            'title' => __('Slides Options', 'persian-framework'),
            'subtitle' => __('Unlimited slides with drag and drop sorting.', 'persian-framework'),
            'desc' => __('This field will store all slides values into a multidimensional array.', 'persian-framework'),
            'max' => 10,
            'min' => 1,
            'button_text' => __('Add Slide', 'persian-framework'),
            'sortable' => true,
            'collapsible' => true,
            'placeholder' => array(
                'title' => __('This is a title', 'persian-framework'),
                'description' => __('Description Here', 'persian-framework'),
                'url' => __('https://example.com', 'persian-framework'),
                'image' => __('Select Image', 'persian-framework'),
            ),
        ),

    )
));

// ================================================================
// 6. SECTION: Date & Time Fields
// ================================================================
$demo_framework->add_section(array(
    'id' => 'date-fields',
    'title' => 'Date & Time Fields',
    'icon' => 'dashicons-calendar-alt',
    'subtitle' => 'Date, Time, and DateTime fields',
    'fields' => array(

        // ----- Persian Date Field -----
        array(
            'id' => 'persian_date',
            'type' => 'date',
            'title' => 'Persian Date',
            'subtitle' => 'Jalali calendar date picker',
            'calendar' => 'persian',
            'format' => 'YYYY/MM/DD',
            'placeholder' => 'Select date',
            'locale' => 'fa',
            'desc' => 'Persian (Jalali) date picker with PersianDatepicker.',
        ),

        // ----- Gregorian Date Field -----
        array(
            'id' => 'gregorian_date',
            'type' => 'date',
            'title' => 'Gregorian Date',
            'subtitle' => 'Miladi calendar date picker',
            'calendar' => 'gregorian',
            'format' => 'YYYY-MM-DD',
            'placeholder' => 'Select date',
            'locale' => 'en',
            'desc' => 'Gregorian date picker with PersianDatepicker.',
        ),

        // ----- Date with Time -----
        array(
            'id' => 'datetime_field',
            'type' => 'date',
            'title' => 'Date & Time',
            'subtitle' => 'Date with time picker',
            'calendar' => 'persian',
            'timepicker' => true,
            'format' => 'YYYY/MM/DD HH:mm',
            'desc' => 'Date picker with time selection.',
        ),

        // ----- Only Time -----
        array(
            'id' => 'time_only',
            'type' => 'date',
            'title' => 'Only Time',
            'subtitle' => 'Time only picker',
            'calendar' => 'persian',
            'only_time' => true,
            'format' => 'HH:mm',
            'desc' => 'Time-only picker without date.',
        ),

        // ----- Inline Date -----
        array(
            'id' => 'inline_date',
            'type' => 'date',
            'title' => 'Inline Date',
            'subtitle' => 'Always visible calendar',
            'calendar' => 'persian',
            'inline' => true,
            'desc' => 'Date picker always visible (inline mode).',
        ),

        // ----- HTML5 Time -----
        array(
            'id' => 'html5_time',
            'type' => 'time',
            'title' => 'Time Field',
            'subtitle' => 'HTML5 time input',
            'step' => 60,
            'desc' => 'Native HTML5 time input.',
        ),

        // ----- HTML5 DateTime -----
        array(
            'id' => 'html5_datetime',
            'type' => 'datetime',
            'title' => 'DateTime Field',
            'subtitle' => 'HTML5 datetime-local input',
            'step' => 60,
            'desc' => 'Native HTML5 datetime-local input.',
        ),

    )
));

// ================================================================
// 7. SECTION: Color & Style Fields
// ================================================================
$demo_framework->add_section(array(
    'id' => 'color-fields',
    'title' => 'Color & Style Fields',
    'icon' => 'dashicons-art',
    'subtitle' => 'Color, Gradient, Border, Spacing, and Typography',
    'fields' => array(

        // ----- Color Field -----
        array(
            'id' => 'color_field',
            'type' => 'color',
            'title' => 'Color Field',
            'subtitle' => 'Color picker',
            'default' => '#6366f1',
            'desc' => 'Color picker with hex input.',
        ),

        // ----- Gradient Field -----
        array(
            'id' => 'gradient_field',
            'type' => 'gradient',
            'title' => 'Gradient Field',
            'subtitle' => 'Linear/Radial gradient',
            'default' => array(
                'type' => 'linear',
                'from' => '#6366f1',
                'to' => '#8b5cf6',
                'angle' => 135,
                'position' => 'center-center',
            ),
            'desc' => 'Gradient builder with live preview.',
        ),

        // ----- Border Field -----
        array(
            'id' => 'border_field',
            'type' => 'border',
            'title' => 'Border Field',
            'subtitle' => 'Complete border control',
            'default' => array(
                'top' => 1,
                'right' => 1,
                'bottom' => 1,
                'left' => 1,
                'style' => 'solid',
                'color' => '#6366f1',
                'radius' => 8,
                'unit' => 'px',
            ),
            'desc' => 'Border width, style, color, and radius with preview.',
        ),

        // ----- Spacing Field -----
        array(
            'id' => 'spacing_field',
            'type' => 'spacing',
            'title' => 'Spacing Field',
            'subtitle' => 'Margin/Padding control',
            'default' => array(
                'top' => 10,
                'right' => 10,
                'bottom' => 10,
                'left' => 10,
                'unit' => 'px',
                'linked' => true,
            ),
            'desc' => 'Spacing control with linked values.',
        ),

        // ----- Dimensions Field -----
        array(
            'id' => 'dimensions_field',
            'type' => 'dimensions',
            'title' => 'Dimensions Field',
            'subtitle' => 'Width and height control',
            'advanced' => true,
            'default' => array(
                'width' => 300,
                'height' => 200,
                'min-width' => '',
                'max-width' => '',
                'min-height' => '',
                'max-height' => '',
                'unit' => 'px',
            ),
            'desc' => 'Dimensions control with advanced options.',
        ),

        // ----- Typography Field -----
        array(
            'id' => 'typography_field',
            'type' => 'typography',
            'title' => 'Typography Field',
            'subtitle' => 'Complete font control',
            'preview' => 'The quick brown fox jumps over the lazy dog',
            'fonts' => array(
                'inherit' => 'Inherit',
                'Arial, sans-serif' => 'Arial',
                'Helvetica, sans-serif' => 'Helvetica',
                'Tahoma, sans-serif' => 'Tahoma',
                'Verdana, sans-serif' => 'Verdana',
                'Georgia, serif' => 'Georgia',
                'Vazirmatn, sans-serif' => 'Vazirmatn',
            ),
            'default' => array(
                'font-family' => 'Vazirmatn, sans-serif',
                'font-size' => 16,
                'font-weight' => '400',
                'font-style' => 'normal',
                'line-height' => 1.6,
                'letter-spacing' => 0,
                'text-align' => 'start',
                'text-transform' => 'none',
                'color' => '#1a2332',
                'unit' => 'px',
            ),
            'desc' => 'Complete typography control with live preview.',
        ),


        array(
            'id' => 'color_palette',
            'type' => 'palette-color',
            'title' => __('Color Palette', 'persian-framework'),
            'subtitle' => __('Select a color palette for your theme', 'persian-framework'),
            'desc' => __('Choose from predefined color palettes', 'persian-framework'),
            'default' => 'default',
            'cols' => 4,
            'palettes' => array(
                'default' => array(
                    '#6366f1',
                    '#8b5cf6',
                    '#a78bfa',
                    '#c4b5fd',
                ),
                'green' => array(
                    '#059669',
                    '#10b981',
                    '#34d399',
                    '#6ee7b7',
                ),
                'red' => array(
                    '#dc2626',
                    '#ef4444',
                    '#f87171',
                    '#fca5a5',
                ),
                'orange' => array(
                    '#d97706',
                    '#f59e0b',
                    '#fbbf24',
                    '#fcd34d',
                ),
                'blue' => array(
                    '#2563eb',
                    '#3b82f6',
                    '#60a5fa',
                    '#93c5fd',
                ),
                'pink' => array(
                    '#db2777',
                    '#ec4899',
                    '#f472b6',
                    '#f9a8d4',
                ),
                'gray' => array(
                    '#4b5563',
                    '#6b7280',
                    '#9ca3af',
                    '#d1d5db',
                ),
                'dark' => array(
                    '#1e293b',
                    '#334155',
                    '#475569',
                    '#64748b',
                ),
            ),
        ),
        // With custom palettes
        array(
            'id' => 'brand_colors',
            'type' => 'palette-color',
            'title' => __('Brand Colors', 'persian-framework'),
            'subtitle' => __('Select brand color palette', 'persian-framework'),
            'desc' => __('Choose from brand color palettes', 'persian-framework'),
            'default' => 'primary',
            'cols' => 3,
            'palettes' => array(
                'primary' => array(
                    '#1a73e8',
                    '#4285f4',
                    '#8ab4f8',
                    '#d2e3fc',
                ),
                'secondary' => array(
                    '#ea4335',
                    '#fbbc04',
                    '#34a853',
                    '#f9ab00',
                ),
                'dark' => array(
                    '#202124',
                    '#3c4043',
                    '#5f6368',
                    '#9aa0a6',
                ),
            ),
        ),

    )
));

// ================================================================
// 8. SECTION: Code & Editor Fields
// ================================================================
$demo_framework->add_section(array(
    'id' => 'code-fields',
    'title' => 'Code & Editor Fields',
    'icon' => 'dashicons-editor-code',
    'subtitle' => 'Code Editor, WP Editor, and Ace Editor',
    'fields' => array(

        // ----- Code Editor -----
        array(
            'id' => 'code_editor',
            'type' => 'code-editor',
            'title' => 'Code Editor',
            'subtitle' => 'Simple code textarea',
            'default' => "/* Your CSS code here */\nbody {\n    margin: 0;\n    padding: 0;\n}",
            'rows' => 8,
            'desc' => 'Simple code editor with monospace font.',
        ),

        // ----- Ace Editor -----
        array(
            'id' => 'ace_editor',
            'type' => 'ace-editor',
            'title' => 'Ace Editor',
            'subtitle' => 'Advanced code editor',
            'mode' => 'php',
            'theme' => 'monokai',
            'height' => '300px',
            'font_size' => 14,
            'gutter' => true,
            'desc' => 'Advanced Ace code editor with syntax highlighting.',
        ),

        // ----- WP Editor -----
        array(
            'id' => 'wp_editor',
            'type' => 'wp-editor',
            'title' => 'WP Editor',
            'subtitle' => 'WordPress WYSIWYG editor',
            'rows' => 10,
            'media_buttons' => true,
            'quicktags' => true,
            'desc' => 'Full WordPress TinyMCE editor.',
        ),

    )
));

// ================================================================
// 9. SECTION: Advanced & Custom Fields
// ================================================================
$demo_framework->add_section(array(
    'id' => 'advanced-fields',
    'title' => 'Advanced Fields',
    'icon' => 'dashicons-admin-tools',
    'subtitle' => 'Sorter, Repeater, Icon Picker, and Map',
    'fields' => array(

        // ----- Sorter Field -----
        array(
            'id' => 'sorter_field',
            'type' => 'sorter',
            'title' => 'Sorter Field',
            'subtitle' => 'Drag & drop sorting',
            'desc' => 'Drag items between Enabled and Disabled columns.',
            'default' => array(
                'enabled' => array(
                    'slider' => 'Slider',
                    'categories' => 'Categories',
                    'products' => 'Products',
                ),
                'disabled' => array(
                    'features' => 'Features',
                    'newsletter' => 'Newsletter',
                ),
            ),
            'options' => array(
                'slider' => 'Slider',
                'categories' => 'Categories',
                'products' => 'Products',
                'features' => 'Features',
                'newsletter' => 'Newsletter',
                'blog' => 'Blog',
                'testimonials' => 'Testimonials',
            ),
        ),

        // ----- Sortable Field -----
        array(
            'id' => 'sortable_field',
            'type' => 'sortable',
            'title' => 'Sortable Field',
            'subtitle' => 'Sortable list items',
            'desc' => 'Drag items to reorder.',
            'default' => array(
                'item1' => 'First Item',
                'item2' => 'Second Item',
                'item3' => 'Third Item',
            ),
            'options' => array(
                'item1' => 'First Item',
                'item2' => 'Second Item',
                'item3' => 'Third Item',
                'item4' => 'Fourth Item',
            ),
            'allow_new' => true,
        ),

        // ----- Repeater Field -----
        array(
            'id' => 'repeater_field',
            'type' => 'repeater',
            'title' => 'Repeater Field',
            'subtitle' => 'Repeatable field groups',
            'max' => 10,
            'min' => 1,
            'button_text' => 'Add New Item',
            'sortable' => true,
            'collapsible' => true,
            'title_field' => 'title',
            'desc' => 'Create repeatable groups of fields.',
            'fields' => array(
                array(
                    'id' => 'title',
                    'type' => 'text',
                    'title' => 'Title',
                    'required' => true,
                ),
                array(
                    'id' => 'description',
                    'type' => 'textarea',
                    'title' => 'Description',
                    'rows' => 3,
                ),
                array(
                    'id' => 'image',
                    'type' => 'image',
                    'title' => 'Image',
                ),
                array(
                    'id' => 'color',
                    'type' => 'color',
                    'title' => 'Color',
                    'default' => '#6366f1',
                ),
                array(
                    'id' => 'status',
                    'type' => 'switch',
                    'title' => 'Active',
                    'default' => true,
                ),
            ),
        ),

        // ----- Icon Picker -----
        array(
            'id' => 'icon_picker',
            'type' => 'icon-picker',
            'title' => 'Icon Picker',
            'subtitle' => 'Dashicons selector',
            'placeholder' => 'dashicons-admin-generic',
            'desc' => 'Select a WordPress Dashicons icon.',
        ),

        // ----- Font Awesome Picker -----
        array(
            'id' => 'fa_picker',
            'type' => 'font-awesome-picker',
            'title' => 'Font Awesome Picker',
            'subtitle' => 'Font Awesome icons',
            'version' => '6',
            'placeholder' => 'fa-solid fa-star',
            'desc' => 'Select a Font Awesome 6 icon.',
        ),

        // ----- Map Field -----
        array(
            'id' => 'map_field',
            'type' => 'map',
            'title' => 'Map Field',
            'subtitle' => 'OpenStreetMap location',
            'default_lat' => 35.6892,
            'default_lng' => 51.3890,
            'height' => 300,
            'desc' => 'Select a location using OpenStreetMap.',
        ),

    )
));

// ================================================================
// 10. SECTION: Data Management Fields
// ================================================================
$demo_framework->add_section(array(
    'id' => 'data-fields',
    'title' => 'Data Management',
    'icon' => 'dashicons-database',
    'subtitle' => 'Backup, Import/Export, and Info',
    'fields' => array(

        // ----- Backup Field -----
        array(
            'id' => 'backup_field',
            'type' => 'backup',
            'title' => 'Backup Field',
            'subtitle' => 'Create and manage backups',
            'opt_name' => 'persian_framework_options',
            'instance_id' => 'persian-framework',
            'desc' => 'Create, restore, and delete backups of your settings.',
        ),

        // ----- Import/Export Field -----
        array(
            'id' => 'import_export',
            'type' => 'import-export',
            'title' => 'Import / Export',
            'subtitle' => 'JSON import and export',
            'rows' => 8,
            'desc' => 'Import or export settings in JSON format.',
        ),

        // ----- Info Field (System Information) -----
        array(
            'id' => 'system_info',
            'type' => 'info',
            'title' => 'System Information',
            'info_type' => 'info',
            'desc' => '
                <table>
                    <tr><td><strong>WordPress Version</strong></td><td>' . get_bloginfo('version') . '</td></tr>
                    <tr><td><strong>PHP Version</strong></td><td>' . phpversion() . ' <span class="pf-info-badge ' . (version_compare(phpversion(), '7.4', '>=') ? 'success' : 'error') . '">' . (version_compare(phpversion(), '7.4', '>=') ? '✓ Supported' : '⚠️ Upgrade recommended') . '</span></td></tr>
                    <tr><td><strong>MySQL Version</strong></td><td>' . $GLOBALS['wpdb']->db_version() . '</td></tr>
                    <tr><td><strong>Memory Limit</strong></td><td>' . ini_get('memory_limit') . ' <span class="pf-info-badge ' . (intval(ini_get('memory_limit')) >= 128 ? 'success' : 'warning') . '">' . (intval(ini_get('memory_limit')) >= 128 ? '✓ Good' : '⚠️ Low') . '</span></td></tr>
                    <tr><td><strong>Max Execution Time</strong></td><td>' . ini_get('max_execution_time') . 's</td></tr>
                    <tr><td><strong>Max Upload Size</strong></td><td>' . ini_get('upload_max_filesize') . '</td></tr>
                    <tr><td><strong>Active Theme</strong></td><td>' . wp_get_theme()->get('Name') . ' v' . wp_get_theme()->get('Version') . '</td></tr>
                    <tr><td><strong>Active Plugins</strong></td><td>' . count(get_option('active_plugins')) . ' plugins</td></tr>
                    <tr><td><strong>Plugin Version</strong></td><td>' . PERSIAN_FRAMEWORK_VERSION . ' <span class="pf-info-badge success">✓ Latest</span></td></tr>
                </table>
            ',
        ),

        // ----- Info Field (Warning) -----
        array(
            'id' => 'warning_info',
            'type' => 'info',
            'title' => '⚠️ Warning',
            'info_type' => 'warning',
            'icon' => 'dashicons-warning',
            'desc' => 'This is a warning message. Please review your settings before saving.',
        ),

        // ----- Info Field (Error) -----
        array(
            'id' => 'error_info',
            'type' => 'info',
            'title' => '❌ Error',
            'info_type' => 'error',
            'icon' => 'dashicons-dismiss',
            'desc' => 'This is an error message. Something went wrong!',
        ),

        // ----- Info Field (Success) -----
        array(
            'id' => 'success_info',
            'type' => 'info',
            'title' => '✅ Success',
            'info_type' => 'success',
            'icon' => 'dashicons-yes-alt',
            'desc' => 'All settings are configured correctly!',
        ),

    )
));

// ================================================================
// 11. SECTION: Spinner & Slider Fields
// ================================================================
$demo_framework->add_section(array(
    'id' => 'numeric-fields',
    'title' => 'Numeric Fields',
    'icon' => 'dashicons-slides',
    'subtitle' => 'Spinner and Slider controls',
    'fields' => array(

        // ----- Spinner Field -----
        array(
            'id' => 'spinner_field',
            'type' => 'spinner',
            'title' => 'Spinner Field',
            'subtitle' => 'Number with +/- buttons',
            'default' => 50,
            'min' => 0,
            'max' => 100,
            'step' => 1,
            'prefix' => '$',
            'suffix' => ' USD',
            'desc' => 'Number input with increment and decrement buttons.',
        ),

        // ----- Slider Field -----
        array(
            'id' => 'slider_field',
            'type' => 'slider',
            'title' => 'Slider Field',
            'subtitle' => 'Range slider',
            'default' => 50,
            'min' => 0,
            'max' => 100,
            'step' => 1,
            'unit' => '%',
            'desc' => 'Range slider with min/max values.',
        ),

    )
));