# 🚀 Persian Framework

<div align="center">

![Persian Framework](https://img.shields.io/badge/version-1.0.0-blue)
![WordPress](https://img.shields.io/badge/WordPress-5.0+-green)
![PHP](https://img.shields.io/badge/PHP-7.4+-purple)
![License](https://img.shields.io/badge/license-GPLv2-yellow)
![Platform](https://img.shields.io/badge/platform-WordPress-lightgrey)

**Advanced WordPress Options Framework with 40+ professional fields for building stunning admin panels and theme options**

[![GitHub stars](https://img.shields.io/github/stars/chehresay/persian-framework)](https://github.com/chehresay/persian-framework/stargazers)
[![GitHub issues](https://img.shields.io/github/issues/chehresay/persian-framework)](https://github.com/chehresay/persian-framework/issues)
[![GitHub forks](https://img.shields.io/github/forks/chehresay/persian-framework)](https://github.com/chehresay/persian-framework/network)

</div>

---

## 📖 Table of Contents

- [About](#-about)
- [Features](#-features)
- [Field Types](#-field-types)
- [Screenshots](#-screenshots)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Quick Start](#-quick-start)
- [Usage Guide](#-usage-guide)
- [API Reference](#-api-reference)
- [Project Structure](#-project-structure)
- [Contributing](#-contributing)
- [Donations](#-donations)
- [License](#-license)
- [Author](#-author)

---

## 📱 About

**Persian Framework** is a powerful and flexible WordPress options framework designed for developers and theme authors. With over 40 professional field types, it allows you to create beautiful and functional admin panels, theme options, and custom settings pages with minimal effort.

### Why Persian Framework?

- 🎯 **No boilerplate code** - Create complex admin panels with minimal code
- 🚀 **Boost productivity** - 40+ ready-to-use field types
- 🎨 **Modern UI** - Clean design with dark/light mode support
- 🔌 **Extensible** - Add custom fields and modify behavior with hooks
- 📱 **Responsive** - Works on all devices from desktop to mobile
- 🏗️ **Multi-instance** - Create multiple independent framework instances

---

## ✨ Features

### Core Features
- ✅ **40+ Professional Fields** - Text, Image, Gallery, Color, Gradient, Typography, Border, Spacing, Repeater, Map, and more!
- ✅ **Multi-Instance Architecture** - Create multiple independent framework instances for different sections
- ✅ **Drag & Drop Interface** - Easily reorder sections and fields
- ✅ **AJAX Saving** - Save settings without page reload
- ✅ **Dark Mode** - Built-in dark mode support for comfortable work
- ✅ **Import/Export** - JSON import and export of settings
- ✅ **Backup System** - Create, restore, and manage backups
- ✅ **Live Preview** - See changes in real-time
- ✅ **Search Settings** - Quickly find any setting
- ✅ **Responsive Design** - Works perfectly on all devices

### Developer Features
- 🔧 **Hooks & Filters** - Extensive WordPress-style hooks for customization
- 📝 **Well Documented** - Clean, documented code
- 🎨 **Customizable** - Override templates and styles
- 🔌 **Extensible** - Add custom field types
- 🧪 **Tested** - Tested up to WordPress 6.7

---

## 📦 Field Types

### Text Fields
| Field | Description |
|-------|-------------|
| **Text** | Simple text input |
| **Textarea** | Multi-line text input |
| **Number** | Numeric input with min/max limits |
| **Email** | Email input with validation |
| **URL** | URL input with validation |
| **Password** | Password input |
| **Hidden** | Hidden field |

### Selection Fields
| Field | Description |
|-------|-------------|
| **Switch** | Toggle switch with custom labels |
| **Checkbox** | Single and multiple checkboxes |
| **Radio** | Radio buttons |
| **Select** | Dropdown selection |
| **Multi-Select** | Multi-select with search and tags |
| **Button Set** | Visual radio buttons |

### Media Fields
| Field | Description |
|-------|-------------|
| **Image** | Single image selection with WordPress Media Library |
| **Media** | Any media file (PDF, video, audio, etc.) |
| **Gallery** | Multiple images gallery |

### Date & Time Fields
| Field | Description |
|-------|-------------|
| **Date** | Jalali (Persian) and Gregorian date pickers |
| **Time** | HTML5 time input |
| **DateTime** | HTML5 datetime-local input |

### Color & Style Fields
| Field | Description |
|-------|-------------|
| **Color** | Color picker with hex input |
| **Gradient** | Linear/Radial gradient builder with live preview |
| **Border** | Complete border control with width, style, color, and radius |
| **Spacing** | Margin/Padding control with linked values |
| **Dimensions** | Width/height control with advanced options |
| **Typography** | Complete font control with live preview |

### Code & Editor Fields
| Field | Description |
|-------|-------------|
| **Code Editor** | Simple code textarea |
| **Ace Editor** | Advanced code editor with syntax highlighting |
| **WP Editor** | Full WordPress WYSIWYG editor |

### Advanced Fields
| Field | Description |
|-------|-------------|
| **Sorter** | Drag & drop sorting between enabled/disabled columns |
| **Sortable** | Sortable list items |
| **Repeater** | Repeatable field groups |
| **Icon Picker** | Dashicons selector |
| **Font Awesome Picker** | Font Awesome 6 icons |
| **Map** | OpenStreetMap location picker |

### Data Management
| Field | Description |
|-------|-------------|
| **Backup** | Create and manage backups |
| **Import/Export** | JSON import and export |
| **Info** | Display system information and notices |

---

## 🖥️ Screenshots

> *Screenshots will be added soon...*

---

## 📋 Requirements

### Minimum Requirements
- **WordPress** 5.0 or higher
- **PHP** 7.4 or higher
- **MySQL** 5.6 or higher

### Recommended
- **WordPress** 6.0 or higher
- **PHP** 8.0 or higher
- **SSL** for secure admin access

---

## 🚀 Quick Start

### Installation

#### From WordPress Admin
1. Go to **Plugins > Add New**
2. Search for **"Persian Framework"**
3. Click **Install Now** and then **Activate**

#### Manual Installation
1. Upload `persian-framework` to `/wp-content/plugins/`
2. Activate the plugin through the **Plugins** menu in WordPress

### Basic Usage

```php
// Create framework instance
$framework = PersianFramework::get_instance('my-framework');

// Set configuration
$framework->set_args([
    'opt_name' => 'my_options',
    'display_name' => 'My Theme Options',
    'menu_title' => 'Theme Options',
    'menu_slug' => 'my-theme-options',
]);

// Add a section
$framework->add_section([
    'id' => 'general',
    'title' => 'General Settings',
    'icon' => 'dashicons-admin-home',
    'fields' => [
        [
            'id' => 'site_logo',
            'type' => 'image',
            'title' => 'Site Logo',
            'desc' => 'Upload your site logo',
        ],
        [
            'id' => 'primary_color',
            'type' => 'color',
            'title' => 'Primary Color',
            'default' => '#0073aa',
        ],
    ],
]);

// Add multiple sections
$framework->add_section([
    'id' => 'styling',
    'title' => 'Styling Options',
    'icon' => 'dashicons-art',
    'fields' => [
        [
            'id' => 'typography',
            'type' => 'typography',
            'title' => 'Main Typography',
            'default' => [
                'font-family' => 'Roboto',
                'font-size' => '16px',
                'line-height' => '1.6',
                'font-weight' => '400',
            ],
        ],
        [
            'id' => 'spacing',
            'type' => 'spacing',
            'title' => 'Content Spacing',
            'mode' => 'padding',
            'units' => ['px', 'em', '%'],
            'default' => [
                'top' => '20px',
                'right' => '20px',
                'bottom' => '20px',
                'left' => '20px',
            ],
        ],
    ],
]);
```


## 💝 Donations

If you find Cordova Pro GUI useful, please consider supporting its development:

### Cryptocurrency

| Currency | Address |
|----------|---------|
| **Bitcoin (BTC)** | `bc1q0r3gzt5xtlglerst36vh6567023thpv5huthrl` |
| **Ethereum (ETH)** | `0xd77935cb0f1b03054720de9cb94c3d7df12b9d0e` |

### Other Ways to Support
- ⭐ **Star** the project on GitHub
- 🐛 **Report** bugs and suggest features
- 📝 **Write** documentation or tutorials
- 🔀 **Contribute** code via pull requests


## 📄 License

Copyright (c) 2026 Morad Chehresay (Parswp.ir)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

---

## 👤 Author

**Morad Chehresay**

- 📧 Email: [chehresay@gmail.com](mailto:chehresay@gmail.com)
- 🐙 GitHub: [@chehresay](https://github.com/chehresay)
- 🔗 LinkedIn: [chehresay](https://linkedin.com/in/chehresay)

---

## 🙏 Acknowledgments

- [WordPress](https://wordpress.org/) - The amazing CMS
- [Font Awesome](https://fontawesome.com/) - Icons
- [Ace Editor](https://ace.c9.io/) - Code editor
- [OpenStreetMap](https://www.openstreetmap.org/) - Map field
- [Leaflet.js](https://leafletjs.com/) - Interactive maps
- All contributors and users of this project

---

<div align="center">

**⭐ Made with ❤️ by [Morad Chehresay](https://github.com/chehresay) ⭐**

</div>