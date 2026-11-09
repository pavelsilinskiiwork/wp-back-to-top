# Pavel Silinskii Back to Top

![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759B?logo=wordpress&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-GPL%20v2-green)
![Version](https://img.shields.io/badge/Version-1.0.0-blue)
![No jQuery](https://img.shields.io/badge/Frontend-Vanilla%20JS-F7DF1E?logo=javascript&logoColor=black)

Lightweight, fully customizable **Back to Top** button for WordPress. It appears after the visitor scrolls past a set offset and smoothly scrolls the page back up on click. You can change its shape, size, colors, icon and animation, and limit it to devices or post types. The settings screen shows a live preview. The frontend is pure vanilla JavaScript with no jQuery.

---

## Table of Contents

- [Features](#features)
- [How It Works](#how-it-works)
- [Installation](#installation)
- [Configuration](#configuration)
- [Shortcode](#shortcode)
- [Theming with CSS Custom Properties](#theming-with-css-custom-properties)
- [Settings Reference](#settings-reference)
- [Accessibility](#accessibility)
- [Security](#security)
- [Project Structure](#project-structure)
- [Data & Privacy](#data--privacy)
- [Requirements](#requirements)
- [Testing Scenarios](#testing-scenarios)
- [Changelog](#changelog)

---

## Features

- **One-click enable/disable**
- **Position**: right or left corner, with custom bottom and side offsets
- **Shape**: circle, rounded or square
- **Size** from 32 to 80 px, plus **opacity**
- **Colors**: background and icon, each with a separate hover state
- **Icon**: built-in arrow SVG, custom text (e.g. `↑`, `Top`) or your own SVG
- **Show animation**: fade, slide up or none
- **Smooth scroll**: native `scroll-behavior`, with an `easeInOutCubic` JS fallback
- **Scroll offset**: the button appears after N pixels
- **Device targeting**: all devices, mobile only or desktop only
- **Post type targeting**: show the button only on selected post types
- **`[back_to_top]` shortcode** for an inline button inside content
- **Live preview** on the settings page
- **CSS custom properties**: override any style from your theme
- **Custom CSS** field
- **Accessible**: `aria-label`, `:focus-visible`, keyboard support, `prefers-reduced-motion`
- **Lightweight**: about 3 KB JS and 2 KB CSS, no frontend dependencies
- **Clean uninstall**: the single option row is removed on delete

---

## How It Works

1. On `wp_enqueue_scripts`, `Frontend::should_display()` decides whether the button is needed. It checks that the plugin is enabled, the device rule (`wp_is_mobile()`) and the post type rule (`is_singular()`).
2. If it is needed, the stylesheet is enqueued together with an inline `:root { --psbtt-*: … }` block built from the settings. The script is enqueued with a `psbttConfig` object.
3. The button markup is printed in `wp_footer`, hidden at first.
4. A passive, `requestAnimationFrame`-throttled scroll listener adds the `.psbtt-visible` class once `window.scrollY` passes the offset.
5. On click the page scrolls to the top: smoothly, or instantly if smooth scroll is off or the visitor prefers reduced motion.

---

## Installation

### Manual

1. Download or clone this repository.
2. Upload the `pavel-silinskii-back-to-top` folder to `/wp-content/plugins/`.
3. Activate the plugin in **WordPress Admin → Plugins**.
4. Go to **Settings → Back to Top** to configure it.

### Via Git

```bash
cd wp-content/plugins
git clone https://github.com/pavelsilinskiiwork/wp-back-to-top.git pavel-silinskii-back-to-top
```

Then activate it in **WordPress Admin → Plugins**.

---

## Configuration

All options are on one screen at **Settings → Back to Top**, split into four sections. A live preview sits to the right of the form.

| Section | Options |
|---|---|
| **General** | Enable button, Show on (all / mobile / desktop), Limit to post types |
| **Button Style** | Position, Shape, Size, Bottom offset, Side offset, Background, Icon color, Background (hover), Icon color (hover), Opacity, Icon type, Icon text, Custom SVG |
| **Behavior** | Show after scrolling (px), Show animation, Smooth scroll, Scroll duration, z-index |
| **Advanced** | Custom CSS |

Some fields only apply to one choice: **Icon text**, **Custom SVG** and **Scroll duration**. They show or hide automatically. The preview updates as you type, and you can hover over it to check the hover colors.

---

## Shortcode

Place an inline (non-fixed) button anywhere in post content:

```
[back_to_top]
[back_to_top text="↑ Back to Top" class="my-btn"]
[back_to_top label="Scroll to the top of the page"]
```

| Attribute | Default | Description |
|---|---|---|
| `text` | *(configured icon)* | Visible text. Replaces the icon when set |
| `label` | `Back to top` | Accessible name (`aria-label`) |
| `class` | — | Extra CSS classes, separated by spaces |

The inline button uses the same colors, size and shape as the floating one. Its stylesheet loads even when the floating button is turned off.

---

## Theming with CSS Custom Properties

All visual values are exposed as CSS variables on `:root`, so a theme can override them without touching the plugin:

```css
:root {
  --psbtt-bg:         #0f172a;
  --psbtt-text:       #ffffff;
  --psbtt-bg-hover:   #22c55e;
  --psbtt-text-hover: #0f172a;
  --psbtt-size:       52px;
  --psbtt-opacity:    0.9;
  --psbtt-bottom:     24px;
  --psbtt-side:       24px;
  --psbtt-z:          9999;
  --psbtt-duration:   0.25s;
}
```

| Selector | Element |
|---|---|
| `#psbtt-btn` | Floating button |
| `#psbtt-btn.psbtt-visible` | Floating button when shown |
| `.psbtt-shape-circle` / `-rounded` / `-square` | Shape modifiers |
| `.psbtt-pos-left` | Left-side position |
| `.psbtt-anim-fade` / `-slide` / `-none` | Animation modifiers |
| `.psbtt-inline` | Shortcode button |

---

## Settings Reference

All settings are stored as a single option with the key `psbtt_settings`.

| Key | Type | Default | Description |
|---|---|---|---|
| `enabled` | bool | `true` | Master on/off switch |
| `show_on` | string | `all` | `all`, `mobile_only` or `desktop_only` |
| `post_types` | array | `[]` | Post type slugs. Empty means every page |
| `position` | string | `right` | `right` or `left` |
| `shape` | string | `circle` | `circle`, `rounded` or `square` |
| `size` | int | `48` | Button size in px (32–80) |
| `bottom_offset` | int | `30` | Distance from the bottom in px |
| `side_offset` | int | `30` | Distance from the side in px |
| `bg_color` | string | `#1a1a2e` | Background color |
| `text_color` | string | `#ffffff` | Icon / text color |
| `bg_color_hover` | string | `#6c63ff` | Background color on hover |
| `text_color_hover` | string | `#ffffff` | Icon / text color on hover |
| `opacity` | float | `0.85` | Idle opacity (0.1–1.0) |
| `icon_type` | string | `arrow` | `arrow`, `text` or `custom_svg` |
| `icon_text` | string | `↑` | Text icon (max 20 chars) |
| `custom_svg` | string | `''` | Inline SVG markup, filtered by an allowlist |
| `scroll_offset` | int | `300` | Px scrolled before the button appears |
| `animation_show` | string | `fade` | `fade`, `slide` or `none` |
| `smooth_scroll` | bool | `true` | Animate the scroll to the top |
| `scroll_duration` | int | `400` | JS fallback duration in ms (100–3000) |
| `z_index` | int | `9999` | Stacking order |
| `custom_css` | string | `''` | Extra CSS printed after the plugin styles |

---

## Accessibility

- A native `<button type="button">` with an `aria-label` and a `title`.
- While hidden, the button is removed from the Tab order (`tabindex="-1"`, `aria-hidden="true"`).
- A visible `:focus-visible` outline.
- Pressing Enter or Space scrolls to the top and moves keyboard focus to the top of the page (the theme's skip link or `<body>`).
- `prefers-reduced-motion: reduce` turns off transitions and makes the scroll jump instantly.

---

## Security

| Measure | Where |
|---|---|
| Settings API with `register_setting()` + `sanitize_callback` | `Admin::register_settings()` |
| `current_user_can( 'manage_options' )` | Settings page |
| Allowlisted values for every choice field | `Admin::sanitize_settings()` |
| `absint()` with min/max clamping | All numeric settings |
| `sanitize_hex_color()` | All color settings, on save and on output |
| `wp_kses()` with an SVG tag/attribute allowlist | `custom_svg` (no `script`, `on*`, `href`, `style`) |
| `wp_strip_all_tags()` | `custom_css` |
| `esc_html()` / `esc_attr()` / `esc_textarea()` / `sanitize_html_class()` | All template and shortcode output |
| Client-side SVG sanitizing in the live preview | `psbtt-admin.js` |

---

## Project Structure

```
pavel-silinskii-back-to-top/
├── pavel-silinskii-back-to-top.php   # Main plugin file, constants, bootstrap, activation
├── readme.txt                        # WordPress.org readme
├── README.md                         # This file
├── uninstall.php                     # Removes the psbtt_settings option
├── includes/
│   ├── class-psbtt-settings.php      # Option name, defaults, Settings::get()
│   ├── class-psbtt-icons.php         # Arrow / text / custom SVG icon + SVG allowlist
│   ├── class-psbtt-frontend.php      # Display rules, assets, inline CSS vars, button markup
│   └── class-psbtt-shortcode.php     # [back_to_top] inline button
├── admin/
│   ├── class-psbtt-admin.php         # Settings API: sections, fields, sanitizing
│   └── partials/
│       └── psbtt-settings-page.php   # Settings screen + live preview markup
├── assets/
│   ├── css/
│   │   ├── psbtt-frontend.css        # Button, shapes, animations, inline variant
│   │   └── psbtt-admin.css           # Two-column layout, preview, range/radio UI
│   └── js/
│       ├── psbtt-frontend.js         # Show/hide on scroll, scroll to top
│       └── psbtt-admin.js            # Color pickers, conditional fields, live preview
└── languages/                        # Translation files
```

All classes live in the `PavelSilinskii\BackToTop\` namespace. The option, CSS and JS identifiers use the `psbtt` prefix.

---

## Data & Privacy

The plugin stores **one option row** (`psbtt_settings`) in the database, which is removed on uninstall.

It sets no cookies, makes no external requests and does not collect or store any visitor data.

---

## Requirements

- WordPress 6.0+
- PHP 8.0+

> **Page caching note:** *Mobile only* / *Desktop only* use the server-side `wp_is_mobile()` check. If your page cache serves the same HTML to all devices, choose *All devices* or turn on separate mobile caching.

---

## Testing Scenarios

| # | Scenario | Expected |
|---|---|---|
| 1 | Page load at the top | Button hidden |
| 2 | Scroll down past the offset | Button appears (fade / slide) |
| 3 | Scroll back up above the offset | Button hides |
| 4 | Click the button | Smooth scroll to the top |
| 5 | Smooth scroll disabled | Instant jump to the top |
| 6 | Tab to the button, press Enter/Space | Scrolls up, focus moves to the top |
| 7 | OS "reduce motion" enabled | No transitions, instant scroll |
| 8 | Position = Left | Button in the bottom-left corner |
| 9 | Icon = Text / Custom SVG | The chosen icon is rendered |
| 10 | Show on = Mobile only, desktop browser | Button not rendered |
| 11 | Post types = Post, viewing a Page | Button not rendered |
| 12 | Plugin disabled | Button never rendered |
| 13 | `[back_to_top]` in post content | Inline button shown and working |
| 14 | Change settings in admin | Live preview updates instantly |
| 15 | DevTools → `:root` | `--psbtt-*` variables match the settings |

---

## Changelog

### 1.0.0

- Initial release.
- Floating button with configurable position, shape, size, colors and opacity.
- Arrow, text and custom SVG icons.
- Fade, slide and no-animation modes. Native smooth scroll with a JS fallback.
- Device and post type targeting.
- `[back_to_top]` shortcode.
- Settings page with live preview.

---

## About the Developer

Built by **Pavel Silinskii**, Full-Stack PHP Developer.

- GitHub: [github.com/pavelsilinskiiwork](https://github.com/pavelsilinskiiwork)
- LinkedIn: [linkedin.com/in/pavel-silinskii](https://linkedin.com/in/pavel-silinskii)

---

## License

Licensed under the [GPL v2 or later](https://www.gnu.org/licenses/gpl-2.0.html).
