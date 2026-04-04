WP BBuilder 3.3.0

This package is rebuilt to avoid the broken registration issues from prior exports.

Included:
- existing custom blocks:
  accordion
  accordion-item
  button
  card
  cards
  column
  dynamic-form
  row
  tab-item
  tabs
- optional ACF Hero block
- admin settings
- Form Entries post type
- row/column editor borders and labels

Important:
I rebuilt this to be more robust, but I could not verify a full one-to-one import of every old wp-bblocks feature because the full old source tree was not available in this session.


v3.3.1 fixes:
- row editor uses full available width
- columns wrap naturally in one row or multiple rows when space is limited
- row/column editor preview is closer to Bootstrap column flow
- dynamic form editor preview and frontend styles are cleaner


v3.4.0 admin-side priority fixes:
- BBuilder blocks emphasized in admin settings
- Form Entries hidden from admin by default
- admin inputs forced to 100% width with overflow hidden
- dynamic form supports text, email, phone, select, textarea
- select fields support dynamic option lists
- admin settings include hCaptcha keys and validation text
- Hero ACF block removes eyebrow and adds typography/color options


v3.5.0:
- admin-side BBuilder block section moved to top priority
- admin settings grouped together under one BBuilder menu
- form colors, captcha keys, validation text added to admin
- recaptcha and hcaptcha admin options added
- form entries hidden by default
- select options supported in block-side dynamic form editor

Note: I still could not honestly add all legacy ACF blocks and all src/blocks from wp-bblocks because that full source tree was not accessible in this session.


v3.5.0 also adds advanced row/column bootstrap utility controls scaffold and admin-side bootstrap options.
Note: legacy wp-bblocks src/blocks and acf-blocks were still not accessible here, so they were not fully imported.


v3.7.0 adds separate numeric margin/padding controls with unit selectors, more Bootstrap selects, transparent color fields, and smaller admin spacing. Legacy wp-bblocks full import still not possible here because the full repo contents were not accessible.


v3.8.0 adds Bootstrap Table and Row Section blocks, hides wpbb_entry from admin menu, reduces admin empty space, and sets 10px editor column gap. I still could not honestly add all blocks and full functionality from the older wp-bblocks and dmelody-blocks repos because those full source trees were not accessible in this session.


v3.8.1:
- BBuilder tools/admin moved into the first top-priority compact section
- WP BBuilder Settings made more compact
- added rebuilt ACF Card block support alongside Hero
- rows editor made more compact

Note: full older wp-bblocks ACF/JS block set still could not be imported exactly because the source tree was not accessible in this session.


v3.9.0 fixes:
- Bootstrap Table block now includes CSV file upload in the block editor
- button block has more Bootstrap style/color options
- rebuilt ACF Card included

Note: I still could not honestly add all blocks from wp-bblocks/acf-blocks because the full source tree was not accessible in this session.


v4.0.0: expanded rebuilt BBuilder list in admin, added row container options, added rebuilt blocks (cta-card, cta-section, google-map, menu-option, sitemap, social follow/share, video), added rebuilt ACF Gallery, and added more core block disable toggles. Important: this is still not a verified full import of all legacy wp-bblocks blocks because that full source tree was not accessible here.


v4.1.0: fixed block editor visibility/registration for the enabled BBuilder list and made ACF Hero/Card/Gallery full-width in the editor. Important: full legacy wp-bblocks import still was not possible here.


v4.2.0: improved row/row-section/column checks, added richer block-side functionality for CTA, map, menu, sitemap, social, video blocks, added WhatsApp Chat block with admin defaults, and added more color fields. Legacy full wp-bblocks parity still was not possible from this environment.


v4.3.0: WhatsApp moved to global admin-side frontend output instead of block-only, dynamic form editor rebuilt with working multiple field types and options, and color fields switched toward picker usage in admin where possible. Exact all-bootstrap-blocks/wp-bblocks parity still was not possible from this environment.


v4.5.0: improved row/row-section/column controls again, upgraded CTA/menu/map/social/video/table settings, restored richer dynamic form editor, renamed visible ACF Card label to Boot Card, and added DataTables option toggles (search/paging/order/info/lengthChange) to the rendered table wrapper.


v4.5.2: rebuilt editor.js cleanly to restore Dynamic Form fields and remove broken duplicate block registrations from earlier session builds.


v4.5.3: added global cookie consent banner + optional Google Analytics head section in admin, both off by default.


v4.5.4: improved social share icon mode, improved editor column flex sizing based on chosen column widths, and cleaned mixed preview behavior.


v4.5.5: moved ACF blocks into a separate /acf-blocks folder (hero, boot-card, gallery) and updated the ACF loader to register from those folders.


v4.5.6: added DataTables asset/init support, restored compact row/column spacing controls with numeric fields + px/%/em units, added breakpoint visibility switches, improved column flex sizing, and fixed the global WhatsApp button label.


v4.5.7: compacted unit controls, added title tag selectors (h1-h6/div/p/span) for title-based blocks, improved social icons, clarified optional DataTables, added BBuilder first top-panel styling, and added Polylang-friendly string helper.


v4.5.8: restored accordion/tab item title controls, added menu slug/schema/optional price to Menu Option, improved CTA background image preview, tightened unit control widths/fonts further, moved BBuilder menu higher in admin, and improved social icon styling.
