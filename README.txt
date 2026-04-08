=== BBuilder – Lightweight Bootstrap Blocks ===
Contributors: raivis-kalnins
Tags: gutenberg, bootstrap, blocks, layout, performance
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 4.7.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Lightweight Bootstrap 5.3 Gutenberg blocks focused on layout, performance, and flexible frontend output.

== Description ==

BBuilder is a lightweight Gutenberg block builder powered by Bootstrap 5.3.

It provides responsive layout blocks, content blocks, social blocks, table/chart blocks, form blocks, and utility blocks with a performance-first approach.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/wp-bbuilder/` directory, or install the ZIP from the WordPress plugin uploader.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Open **WP BBuilder Settings** and configure Bootstrap loading and container width.
4. Start using blocks from the **BBuilder** category in the block editor.

== Block List ==

= Layout =
* Row — Bootstrap row wrapper with spacing, background, SCSS, and container controls.
* Column — Responsive Bootstrap column with spacing, shadow, background, and SCSS.

= Content =
* Accordion — Bootstrap accordion with frontend collapse behavior.
* Tabs — Tabbed content areas.
* Card — Flexible content card.
* Pricing Cards — Pricing plans with featured style support.
* Feature List — Icon plus text feature sections for marketing pages.
* Timeline — Vertical or horizontal timeline for case studies.
* Countdown Timer — Styled live countdown.
* Chart — Bar, line, pie, and doughnut chart block.
* Table — CSV-driven responsive table.
* Fun Fact — Metric or stat callout.
* Weather — Live weather with free fallback source.
* Custom Embed — Embed wrapper for CRM, SaaS, or iframe content.
* AI Content — Starter AI content block with endpoint guidance.
* Login / Register — WordPress auth UI block.

= Forms and integrations =
* Dynamic Form
* MailChimp
* Social Follow
* Social Share

== Changelog ==

= 4.7.6 =
* fixed row/column spacing control sizing in the inspector
* improved row/column custom SCSS editor styling
* improved frontend table HTML rendering
* fixed frontend rendering callback registration for new blocks
* improved Login/Register frontend output
* refreshed X and WhatsApp icons
* updated README files

== Upgrade Notice ==

= 4.7.6 =
Improves spacing controls, table rendering, new block rendering, and frontend auth output.
