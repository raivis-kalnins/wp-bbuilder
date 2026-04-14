# Admin Helpers

WP BBuilder includes lightweight helper features intended to improve the editing experience without adding unnecessary frontend code.

## Admin spellcheck

The current helper system includes an optional admin-only spellcheck enhancement.

### What it does
- Enables browser/editor spellcheck where supported
- Applies the selected content language via the `lang` attribute
- Targets Gutenberg text editing surfaces
- Targets ACF text and textarea inputs
- Supports classic editor / TinyMCE environments
- Observes dynamically added fields so repeaters and flexible layouts continue to work

### What it does not do
- It does not load on the frontend
- It does not affect SEO output
- It does not add a heavy third-party grammar service by default
- It does not rewrite content automatically

### When to use it
Use it when your editors work in one or more supported languages and want browser-level spelling feedback directly in the WordPress admin.

### Supported languages
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

## General guidance

Admin helpers in WP BBuilder should follow the same design rule:
- improve editor experience
- keep frontend clean
- remain optional
- stay compatible with Gutenberg and ACF workflows
