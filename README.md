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

### Content
- **Accordion** — Bootstrap accordion with editor preview and frontend collapse behavior.
- **Tabs** — Tabbed content sections for structured layouts.
- **Card** — Flexible Bootstrap card wrapper.
- **Pricing Cards** — Pricing table cards with featured plan support.
- **Feature List** — Marketing feature items with icon and text.
- **Timeline** — Vertical or horizontal timeline for projects and case studies.
- **Countdown Timer** — Styled live countdown block.
- **Chart** — Chart.js-powered bar, line, pie, and doughnut charts.
- **Table** — CSV-based responsive table with optional DataTables.
- **Fun Fact** — Metric/stat highlight block.
- **Weather** — Live weather block with London/English defaults and free weather fallback.
- **Custom Embed** — URL or iframe embed wrapper for third-party integrations.
- **AI Content** — Starter AI content block with free endpoint connection guidance.
- **Login / Register** — WordPress login and registration UI block.

### Forms and integrations
- **Dynamic Form** — Flexible contact and lead form builder.
- **MailChimp** — Newsletter signup form with MailChimp action URL support.
- **Social Follow** — Brand links with SVG icons.
- **Social Share** — Share buttons with clean SVG icons.

### ACF Blocks
- **Hero** - Single & SWiper Slider
- **Gallery** - Drag & drop multiple images or videos
- **CTA Card** - Call to ation also Schema tag options

## Notes
- Frontend container width can be controlled from **WP BBuilder Settings**.
- Row and Column support custom SCSS with a native WordPress code editor.
- Bootstrap assets can be force-enqueued if the theme does not provide Bootstrap.

## License
GPLv2 or later
