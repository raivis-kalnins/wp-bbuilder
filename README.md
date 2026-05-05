# WP BBuilder

![Version](https://img.shields.io/badge/version-5.0.7-blue)
![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-green)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)
![License](https://img.shields.io/badge/license-GPLv2-blue)

WP BBuilder is a modular Gutenberg block builder for WordPress with Bootstrap-friendly layouts, server-side rendering, optional ACF integration, frontend-safe output, and admin-side editing helpers.

It is built to give editors more useful blocks without turning the frontend into a heavy page builder.

## What is included

- Custom Gutenberg blocks for layout, content, data, forms, and integrations
- Optional ACF-compatible workflows
- Bootstrap-oriented structure and utility support
- Dynamic frontend rendering where needed
- Admin-side helpers that do **not** load on the frontend
- Modular settings for enabling and disabling functionality

## Highlights

### Social Feeds block
The plugin now includes a **Social Feeds** block for combining social sources in one content section.

Supported platforms:
- Instagram
- Facebook
- TikTok
- X / Twitter
- YouTube

Per-platform source methods:
- Shortcode
- Embed URL
- Raw HTML
- Direct link
- Optional ACF fallback field name

This makes it suitable for direct embeds, feed plugin shortcodes, or flexible ACF-driven content builds.

### Admin spellcheck helper
The plugin also includes an optional **admin-only spellcheck system**.

It is designed for content teams who want spelling assistance in the editor while keeping the frontend clean for performance and SEO.

Supported admin editors:
- Gutenberg text and rich text fields
- ACF text and textarea fields
- Classic editor / TinyMCE / visual editor

Supported spellcheck languages:
- English
- Latvian
- Estonian
- Lithuanian
- Polish
- German
- French
- Spanish
- Italian
- Swedish
- Finnish
- Norwegian
- Danish
- Icelandic
- Russian

## Installation

1. Upload the plugin to `/wp-content/plugins/wp-bbuilder/` or install the ZIP via **Plugins -> Add New -> Upload Plugin**.
2. Activate the plugin.
3. Open **WP BBuilder Settings** in the WordPress admin.
4. Review Bootstrap loading, builder defaults, and optional admin helpers.
5. Start building pages from the **BBuilder** block category in Gutenberg.

## Builder settings overview

WP BBuilder settings are intended to keep the plugin flexible for different projects.

Typical settings areas include:
- Bootstrap CSS / JS loading controls
- Shared CSS loading
- Frontend rendering and utility options
- Form defaults and behavior
- Cookie consent and analytics options
- Admin spellcheck enable / disable toggle
- Admin spellcheck default language

### Admin spellcheck behavior
When enabled:
- loads only in `wp-admin`
- adds `spellcheck="true"` where supported
- applies the selected language via `lang`
- watches dynamically added editor fields so Gutenberg and ACF repeaters continue to work

Nothing from this helper is loaded on the frontend.

## Block categories

### Layout and structure
- Row
- Column
- Section
- Hero
- Bootstrap Div
- Swiper
- Tabs
- Tab Item
- Accordion
- Accordion Item

### Content and UI
- Button
- Card
- Cards
- CTA Card
- CTA Section
- Alert
- Badge
- Breadcrumb
- List Group
- Navbar
- Progress
- Spinner
- Feature List
- Timeline
- Code Display
- Countdown Timer
- Fun Fact
- Table
- Video
- Gallery
- Custom Embed
- Inline SVG
- File

### Social and sharing
- Social Follow
- Social Share
- Social Feeds

### Forms, search, and marketing
- Dynamic Form
- Mailchimp
- Login / Register
- Ajax Search
- Load More
- Blog Filter
- Catalogue
- Events
- Testimonials
- Pricing Cards
- Contact Links
- Booking Calendar

### Data and utility
- Chart
- Google Map
- Weather
- Varda Dienas / Name Days
- Sitemap
- Menu Option
- AI Content

### ACF-related blocks
- ACF Hero
- ACF Gallery
- ACF Card

## Quick usage notes

### Social Feeds block
Use this block when you want one section to hold multiple feed sources.

Examples:
- Instagram shortcode from a feed plugin
- Facebook shortcode from a social feed plugin
- TikTok embed URL
- YouTube playlist or video URL
- Raw HTML widget code where needed

### Dynamic forms
Use the Dynamic Form block for structured forms with project-specific settings. Keep styles centralized in plugin or theme CSS where possible.

### Layout blocks
Use Row and Column for predictable Bootstrap-oriented structure instead of deeply nested ad hoc groups.

### Charts and data blocks
These blocks are useful for dashboards, campaign pages, case studies, and internal marketing content.

## Documentation included in this package

- `docs/AVAILABLE-BLOCKS.md` — quick block catalogue with short explanations
- `docs/ADMIN-HELPERS.md` — summary of admin-side helpers and how they work
- `blocks/*/README.txt` — block-specific notes where available

## Performance and SEO approach

WP BBuilder is built with a practical separation between admin conveniences and frontend output.

Frontend goals:
- avoid loading unnecessary assets
- keep rendering predictable
- support server-side output where useful
- keep helper systems out of the public site unless they are actually needed

Admin goals:
- make editing easier
- allow optional enhancements
- support Gutenberg and ACF workflows without forcing extra frontend code

## Developer notes

Typical extension flow:
1. Create a block folder if needed.
2. Register the block in the plugin block registry.
3. Add editor controls and render logic.
4. Keep frontend output minimal and escaped.
5. Use admin-only scripts for editorial assistance.

Recommended approach:
- prefer server-side render callbacks for dynamic content
- use `do_shortcode()` only for trusted shortcode integrations
- use `wp_oembed_get()` for supported embed URLs
- keep project styling in shared CSS or theme CSS rather than inline block-by-block duplication where possible

## Changelog

### 5.0.7
- Added Social Feeds block
- Added optional admin-only spellcheck helper
- Added spellcheck language selection
- Added support targeting Gutenberg, ACF fields, and TinyMCE/classic editor on admin side
- Updated documentation and package help files

## License

GPLv2 or later
