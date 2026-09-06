=== Persian Framework ===
Contributors: parswp
Donate link: https://parswp.ir
Tags: options, framework, admin, fields, developer
Requires at least: 5.0
Tested up to: 7.1
Stable tag: 1.0.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Advanced WordPress Options Framework with 40+ professional fields for building stunning admin panels and theme options.

== Description ==

Persian Framework is a powerful and flexible WordPress options framework designed for developers and theme authors. With over 40 professional field types, it allows you to create beautiful and functional admin panels, theme options, and custom settings pages with minimal effort.

= Key Features =

* **40+ Professional Fields** - Text, Textarea, Number, Email, URL, Password, Hidden, Switch, Checkbox, Radio, Select, Multi-Select, Image, Gallery, Media, Color, Gradient, Typography, Border, Spacing, Dimensions, Date, Time, DateTime, Slider, Spinner, Code Editor, Ace Editor, Repeater, Sortable, Sorter, Button Set, Icon Picker, Font Awesome Picker, WP Editor, Map, Backup, Import/Export, Info and more!

* **Multi-Instance Architecture** - Create multiple independent instances of the framework for different sections of your project

* **Drag & Drop Interface** - Easily reorder sections and fields with intuitive drag & drop functionality

* **AJAX Saving** - Save settings without page reload for a smooth user experience

* **Dark Mode** - Built-in dark mode support for comfortable work in low-light environments

* **Import/Export** - Easily import and export settings as JSON files

* **Backup System** - Create, restore, and manage backups of your settings

* **Live Preview** - See changes in real-time with live preview functionality

* **Search Settings** - Quickly find any setting with the built-in search feature

* **Responsive Design** - Works perfectly on all devices, from desktop to mobile

* **Developer Friendly** - Clean, well-documented code with hooks and filters for customization

= Field Types =

=== Text Fields ===
* Text - Simple text input
* Textarea - Multi-line text input
* Number - Numeric input with min/max limits
* Email - Email input with validation
* URL - URL input with validation
* Password - Password input
* Hidden - Hidden field

=== Selection Fields ===
* Switch - Toggle switch with custom labels
* Checkbox - Single and multiple checkboxes
* Radio - Radio buttons
* Select - Dropdown selection
* Multi-Select - Multi-select with search and tags
* Button Set - Visual radio buttons

=== Media Fields ===
* Image - Single image selection with WordPress Media Library
* Media - Any media file (PDF, video, audio, etc.)
* Gallery - Multiple images gallery

=== Date & Time Fields ===
* Date - Jalali (Persian) and Gregorian date pickers
* Time - HTML5 time input
* DateTime - HTML5 datetime-local input

=== Color & Style Fields ===
* Color - Color picker with hex input
* Gradient - Linear/Radial gradient builder with live preview
* Border - Complete border control with width, style, color, and radius
* Spacing - Margin/Padding control with linked values
* Dimensions - Width/height control with advanced options
* Typography - Complete font control with live preview

=== Code & Editor Fields ===
* Code Editor - Simple code textarea
* Ace Editor - Advanced code editor with syntax highlighting
* WP Editor - Full WordPress WYSIWYG editor

=== Advanced Fields ===
* Sorter - Drag & drop sorting between enabled/disabled columns
* Sortable - Sortable list items
* Repeater - Repeatable field groups
* Icon Picker - Dashicons selector
* Font Awesome Picker - Font Awesome 6 icons
* Map - OpenStreetMap location picker

=== Data Management ===
* Backup - Create and manage backups
* Import/Export - JSON import and export
* Info - Display system information and notices

= Usage Examples =

=== Basic Configuration ===

```php
// Create framework instance
$framework = PersianFramework::get_instance('my-framework');

// Set configuration
$framework->set_args(array(
    'opt_name' => 'my_options',
    'display_name' => 'My Theme Options',
    'menu_title' => 'Theme Options',
    'menu_slug' => 'my-theme-options',
));

// Add a section
$framework->add_section(array(
    'id' => 'general',
    'title' => 'General Settings',
    'icon' => 'dashicons-admin-home',
    'fields' => array(
        array(
            'id' => 'site_logo',
            'type' => 'image',
            'title' => 'Site Logo',
            'desc' => 'Upload your site logo',
        ),
    ),
));