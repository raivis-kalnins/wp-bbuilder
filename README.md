# BBuilder – Lightweight Bootstrap Blocks for Gutenberg

![Version](https://img.shields.io/badge/version-4.7.6-blue)
![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-green)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)
![License](https://img.shields.io/badge/license-GPLv2-blue)

BBuilder is a lightweight Gutenberg block builder powered by Bootstrap 5.3 and focused on fast frontend output, flexible layout control, and clean admin tooling.

## Installation

1. Upload the plugin folder to `/wp-content/plugins/wp-bbuilder/` or install the ZIP from **Plugins → Add New → Upload Plugin**.
2. Activate the plugin.
3. Open **WP BBuilder Settings** and choose your Bootstrap loading mode.
4. Start building with the **BBuilder** block category in Gutenberg.

## Builder structure

```text
wp-bbuilder/
├── wp-bbuilder.php
├── assets/
│   ├── editor.js
│   ├── editor.css
│   ├── editor-enhancer.js
│   ├── admin-builder.js
│   ├── admin.css
│   ├── shared.css
│   └── chart-view.js
├── includes/
│   ├── class-admin.php
│   ├── class-blocks.php
│   └── helpers.php
└── README.md
```

## Block list

### Layout
- **Row** — Bootstrap row wrapper with spacing, background, SCSS, and container controls.
- **Column** — Responsive Bootstrap column with spacing, shadow, background, and SCSS.
- **Section** — General content section wrapper for grouped layouts and background styling.
- **Hero** — Large visual intro section for banners, page headers, and promotional content.
- **Bootstrap Div** — Flexible Bootstrap-based wrapper for custom classes and nested content.

### Content
- **Button** — Standalone call-to-action button with Bootstrap styling options.
- **Card** — Flexible Bootstrap card wrapper for text, media, and actions.
- **Cards** — Repeating card layout for grids, services, or feature sets.
- **Accordion** — Bootstrap accordion with editor preview and frontend collapse behavior.
- **Tabs** — Tabbed content sections for structured layouts.
- **Bootstrap Table** — Responsive table block with Bootstrap table styling.
- **Alert** — Highlighted alert message box for notices, warnings, or success states.
- **Badge** — Small label element for tags, statuses, and counters.
- **Breadcrumb** — Breadcrumb navigation trail for page hierarchy.
- **List Group** — Bootstrap list group for linked or static item lists.
- **Navbar** — Navigation bar block for menu-style layouts.
- **Progress** — Progress bar for percentages, steps, or completion states.
- **Spinner** — Loading spinner for async states and placeholders.
- **Feature List** — Marketing feature items with icon and text.
- **Timeline** — Vertical or horizontal timeline for projects and case studies.
- **Code Display** — Styled code block with frontend copy support.
- **Countdown Timer** — Styled live countdown block with real-time frontend updates.
- **Chart** — Chart.js-powered bar, line, pie, and doughnut charts.
- **Fun Fact** — Metric or stat highlight block for counters and key numbers.
- **Custom Embed** — URL or iframe embed wrapper for third-party integrations.
- **AI Content** — Starter AI content block with endpoint connection guidance.
- **Video** — Video embed/display block for media content.
- **Swiper** — Slider wrapper block for carousel-style content areas.
- **Gallery** — ACF gallery block for drag-and-drop images or videos.
- **CTA Card** — Call-to-action card with strong visual emphasis.
- **CTA Section** — Promotional section block for campaigns, offers, or lead capture.
- **Google Map** — Map embed block for locations and contact pages.
- **Menu Option** — Structured menu item block for service or navigation listings.
- **Sitemap** — Visual sitemap/navigation helper block.

### Forms and integrations
- **Dynamic Form** — Flexible contact and lead form builder with validation support.
- **MailChimp** — Newsletter signup form with MailChimp action URL support.
- **Login / Register** — WordPress login and registration UI block.
- **Ajax Search** — AJAX-powered live search block with configurable search modes.
- **Catalogue** — Query-based catalogue/grid block for posts or custom post types.
- **Load More** — AJAX post loader with configurable button text, color, and item classes.
- **Blog Filter** — AJAX filter block for posts or CPTs with category, year, sort, and search controls.
- **Events** — Events query block with event category filtering and calendar-focused output.
- **Testimonials** — Swiper-powered testimonials slider with responsive item counts.
- **Pricing Cards** — Pricing table cards with featured plan support.
- **Email & Phone** — Contact links block with mailto/tel links and icon support.
- **Social Follow** — Brand links with SVG icons for social profiles.
- **Social Share** — Share buttons with clean SVG icons.

### Utility and data blocks
- **Weather** — Live weather block with location support and free weather fallback.
- **Name Days** — Automatically loads today’s Latvian name days from a free live JSON API, with local fallback support.
- **Vārda dienas / Name Days** — Same block slug, now shown in English while still displaying Latvian names automatically by date.

### ACF Blocks
- **Hero** — Single hero and Swiper slider hero layouts.
- **Gallery** — Drag-and-drop image or video gallery.
- **CTA Card** — Call-to-action card with schema tag options.

## Notes
- Frontend container width can be controlled from **WP BBuilder Settings**.
- Row and Column support custom SCSS with a native WordPress code editor.
- Bootstrap assets can be force-enqueued if the theme does not provide Bootstrap.

## License
GPLv2 or later


## REST API: Vārda dienas

This plugin now exposes public REST endpoints for vārda dienas data from `assets/json/varda-dienas.json`.

### Endpoints

- `GET /wp-json/wpbb/v1/varda-dienas`
- `GET /wp-json/wpbb/v1/varda-dienas?date=05-29`
- `GET /wp-json/wpbb/v1/varda-dienas?date=2026-05-29`
- `GET /wp-json/wpbb/v1/varda-dienas/today`

### Example response

```json
{
  "success": true,
  "date": "2026-05-29",
  "key": "05-29",
  "today": true,
  "names": ["Maksis", "Maksims", "Raivis", "Raivo"],
  "count": 4
}
```


## Container Width Theme Sync

Builder no longer stores its own `Frontend container max width` setting.

Container width now:
- links admins to Theme Settings
- reads width from the active theme
- supports these sources in order:
  - `wpbb_theme_container_width` filter
  - `get_theme_mod('container_width')`
  - `get_theme_mod('container_max_width')`
  - `get_theme_mod('site_container_width')`
  - `get_option('wpbb_theme_container_width')`
  - `get_option('bbtheme_container_width')`

You can also override the Theme Settings URL with:

```php
add_filter('wpbb_theme_settings_url', function () {
    return admin_url('admin.php?page=wpbb-theme-settings');
});
```


## Inspector Panel Style Fixes

The editor keeps a more WordPress-native inspector look by removing extra borders from:
- `.block-editor-block-inspector .components-panel__body`
- `.block-editor-block-inspector .components-panel__body .wpbb-responsive-group`
- `.block-editor-block-inspector .wpbb-responsive-side-field`


## Current block list

Custom WP BBuilder blocks currently included:

- Accordion
- Accordion Item
- Alert
- Badge
- Breadcrumb
- Button
- Card
- Cards
- Column
- CTA Card
- CTA Section
- Dynamic Form
- Google Map
- List Group
- Menu Option
- Navbar
- Progress
- Row
- Section
- Sitemap
- Social Follow
- Social Share
- Spinner
- Tab Item
- Table
- Tabs
- Video
- File
- Inline SVG
- Swiper
- Weather
- Varda Dienas
- Ajax Search
- Pricecards
- Catalogue
- Code Display
- Countdown Timer
- Chart
- Fun Fact
- Mailchimp
- Bootstrap Div
- Feature List
- Timeline
- Custom Embed
- AI Content
- Login Register
- Load More
- Contact Links
- Events
- Testimonials
- Blog Filter

Core Gutenberg blocks intentionally supported alongside builder blocks include Paragraph, Heading, List, Spacer, HTML, Shortcode, Code, Image, Gallery, Cover, Media & Text, Buttons, Button, Audio, File, and Query-related core blocks depending on settings.

### Not included as custom WP BBuilder blocks

- Custom WP BBuilder Audio block removed. Use Core Audio block instead.
- If a block is disabled in plugin settings, it will not appear in the inserter.


## Responsive columns

Column block supports Bootstrap responsive widths directly in the editor:
- Mobile: `col-*`
- Small tablet: `col-sm-*`
- Tablet: `col-md-*`
- Desktop: `col-lg-*`
- Large desktop: `col-xl-*`
- Wide desktop: `col-xxl-*`

Example output: `col-12 col-md-6 col-lg-4`.
