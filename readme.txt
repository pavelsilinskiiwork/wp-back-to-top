=== Pavel Silinskii Back to Top ===
Contributors:      pavelsilinskii
Tags:              back to top, scroll to top, button, scroll, accessibility
Requires at least: 6.0
Tested up to:      6.7
Stable tag:        1.0.0
Requires PHP:      8.0
License:           GPLv2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Lightweight, fully customizable Back to Top button with smooth scroll and accessibility support.

== Description ==

Pavel Silinskii Back to Top adds a highly configurable "Back to Top" button to your WordPress site.
No jQuery. Pure vanilla JavaScript.

**Features**

* Enable/disable with one click
* Position: right or left side of screen
* Shape: circle, rounded or square
* Custom size, colors (background, text, hover states), opacity
* Icon: arrow SVG, custom text or custom SVG
* Animations: fade, slide or none
* Smooth scroll (native CSS or JS fallback with easeInOutCubic)
* Scroll offset — button appears after N pixels
* Show on all devices, mobile only or desktop only
* Restrict to specific post types
* Shortcode [back_to_top] for inline placement
* Live preview in settings panel
* CSS custom properties — easy to override in theme CSS
* Accessibility: aria-label, focus-visible, prefers-reduced-motion
* No jQuery on the frontend, lightweight (~3KB JS + ~2KB CSS)

**Shortcode**

`[back_to_top text="↑ Back to Top" label="Back to top" class="my-btn"]`

* `text` — visible text (defaults to the configured icon)
* `label` — accessible label
* `class` — extra CSS classes

**CSS custom properties**

Override in your theme: `--psbtt-bg`, `--psbtt-text`, `--psbtt-bg-hover`, `--psbtt-text-hover`, `--psbtt-size`, `--psbtt-opacity`, `--psbtt-bottom`, `--psbtt-side`, `--psbtt-z`, `--psbtt-duration`.

== Installation ==

1. Upload the plugin folder to /wp-content/plugins/
2. Activate through the Plugins screen
3. Go to Settings → Back to Top to configure

== Frequently Asked Questions ==

= The mobile/desktop option doesn't work with my cache plugin =

Device detection runs on the server (wp_is_mobile()). If your page cache does not vary by device, use "All devices" or enable separate mobile caching.

== Screenshots ==

1. Settings page with live preview
2. Button examples: circle, rounded, square

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release.
