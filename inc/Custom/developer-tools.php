<?php
if (!defined('ABSPATH')) { exit; }

if (!function_exists('wp_theme_dev_tools_state')) {
    function wp_theme_dev_tools_state() {
        if (!is_user_logged_in() || !current_user_can('edit_theme_options')) {
            return [];
        }
        $mockup = wp_theme_acf_get('theme_dev_mockup_image', 'option', '');
        $mockup_url = '';
        if (is_array($mockup)) {
            $mockup_url = esc_url_raw($mockup['url'] ?? '');
        } elseif (is_numeric($mockup)) {
            $mockup_url = esc_url_raw(wp_get_attachment_image_url((int) $mockup, 'full'));
        } else {
            $mockup_url = esc_url_raw((string) $mockup);
        }
        return [
            'borders' => (bool) wp_theme_acf_get('theme_dev_show_borders', 'option', 0),
            'spacing' => (bool) wp_theme_acf_get('theme_dev_show_spacing', 'option', 0),
            'typography' => (bool) wp_theme_acf_get('theme_dev_show_typography', 'option', 0),
            'colors' => (bool) wp_theme_acf_get('theme_dev_show_colors', 'option', 0),
            'pixel' => (bool) wp_theme_acf_get('theme_dev_pixel_perfect', 'option', 0),
            'mockup' => $mockup_url,
            'opacity' => max(5, min(100, absint(wp_theme_acf_get('theme_dev_mockup_opacity', 'option', 35)))),
        ];
    }
}

add_filter('body_class', function ($classes) {
    if (is_admin()) { return $classes; }
    $state = wp_theme_dev_tools_state();
    if (!$state) { return $classes; }
    foreach (['borders','spacing','typography','colors','pixel'] as $flag) {
        if (!empty($state[$flag])) { $classes[] = 'wp-theme-dev-' . $flag; }
    }
    return $classes;
});

add_action('wp_enqueue_scripts', function () {
    if (is_admin()) { return; }
    $state = wp_theme_dev_tools_state();
    if (!$state || !array_filter($state)) { return; }

    $css = <<<'CSS'
.wp-theme-dev-borders *{outline:1px dashed rgba(210,22,41,.28);outline-offset:-1px}
.wp-theme-dev-spacing section,.wp-theme-dev-spacing .container,.wp-theme-dev-spacing .row,.wp-theme-dev-spacing [class*="col-"]{box-shadow:inset 0 0 0 1px rgba(37,99,235,.22)}
#wp-theme-dev-panel{position:fixed;right:16px;bottom:16px;z-index:999999;background:#0f172a;color:#fff;border:1px solid rgba(148,163,184,.25);padding:0;border-radius:16px;width:min(380px,calc(100vw - 32px));font:12px/1.5 system-ui,sans-serif;box-shadow:0 18px 50px rgba(2,6,23,.35);overflow:hidden;backdrop-filter:blur(10px)}
#wp-theme-dev-panel.is-collapsed .wp-theme-dev-panel-body,#wp-theme-dev-panel.is-collapsed .wp-theme-dev-panel-footer{display:none}
.wp-theme-dev-panel-head{display:flex;align-items:center;justify-content:space-between;padding:12px 14px;background:rgba(255,255,255,.04);cursor:move}
.wp-theme-dev-panel-head strong{font-size:13px;letter-spacing:.02em}
.wp-theme-dev-panel-actions{display:flex;gap:6px}
.wp-theme-dev-panel-actions button{appearance:none;border:1px solid rgba(148,163,184,.35);background:rgba(255,255,255,.06);color:#fff;border-radius:10px;padding:4px 8px;font-size:11px;cursor:pointer}
.wp-theme-dev-panel-body{padding:12px 14px;display:grid;gap:10px}
.wp-theme-dev-panel-body code{color:#93c5fd;word-break:break-word}
.wp-theme-dev-panel-footer{padding:10px 14px;border-top:1px solid rgba(148,163,184,.15);color:#cbd5e1;background:rgba(255,255,255,.03)}
.wp-theme-dev-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}
.wp-theme-dev-card{padding:8px 10px;border-radius:12px;background:rgba(255,255,255,.06)}
.wp-theme-dev-card label{display:block;font-size:10px;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin-bottom:2px}
.wp-theme-dev-card span{display:block;color:#fff;word-break:break-word}
.wp-theme-dev-chip-row{display:flex;flex-wrap:wrap;gap:6px}
.wp-theme-dev-chip{display:inline-flex;align-items:center;padding:3px 8px;border-radius:999px;background:rgba(255,255,255,.08);color:#e2e8f0}
#wp-theme-dev-overlay{position:fixed;inset:0;z-index:999997;pointer-events:none;background-position:top center;background-repeat:no-repeat;background-size:contain}
#wp-theme-dev-crosshair-x,#wp-theme-dev-crosshair-y{position:fixed;z-index:999996;pointer-events:none;background:rgba(34,197,94,.45)}
#wp-theme-dev-crosshair-x{height:1px;left:0;right:0;display:none}
#wp-theme-dev-crosshair-y{width:1px;top:0;bottom:0;display:none}
.wp-theme-dev-target{outline:2px solid #22c55e !important;outline-offset:2px}
CSS;

    wp_register_style('wp-theme-dev-tools', false, [], null);
    wp_enqueue_style('wp-theme-dev-tools');
    wp_add_inline_style('wp-theme-dev-tools', $css);
    wp_register_script('wp-theme-dev-tools', false, [], null, true);
    wp_enqueue_script('wp-theme-dev-tools');

    $script = <<<'JS'
window.wpThemeDevTools = __STATE__;
(function () {
  const s = window.wpThemeDevTools || {};
  const enabled = ['borders', 'spacing', 'typography', 'colors', 'pixel'].some((key) => !!s[key]);
  if (!document.body || !enabled) return;

  const panel = document.createElement('div');
  panel.id = 'wp-theme-dev-panel';
  panel.innerHTML = [
    '<div class="wp-theme-dev-panel-head">',
      '<strong>Developer Tools</strong>',
      '<div class="wp-theme-dev-panel-actions">',
        '<button type="button" data-dev-action="copy">Copy</button>',
        '<button type="button" data-dev-action="collapse">Hide</button>',
      '</div>',
    '</div>',
    '<div class="wp-theme-dev-panel-body">',
      '<div class="wp-theme-dev-card"><label>How to inspect</label><span>Use Alt/Option + click on any element. Press Esc to clear.</span></div>',
      '<div class="wp-theme-dev-chip-row" id="wp-theme-dev-flags"></div>',
      '<div class="wp-theme-dev-grid" id="wp-theme-dev-grid"></div>',
    '</div>',
    '<div class="wp-theme-dev-panel-footer">Tip: drag this panel by its header.</div>'
  ].join('');
  document.body.appendChild(panel);

  const crossX = document.createElement('div');
  crossX.id = 'wp-theme-dev-crosshair-x';
  document.body.appendChild(crossX);
  const crossY = document.createElement('div');
  crossY.id = 'wp-theme-dev-crosshair-y';
  document.body.appendChild(crossY);

  if (s.pixel && s.mockup) {
    const overlay = document.createElement('div');
    overlay.id = 'wp-theme-dev-overlay';
    overlay.style.backgroundImage = 'url(' + s.mockup + ')';
    overlay.style.opacity = (s.opacity || 35) / 100;
    document.body.appendChild(overlay);
  }

  const flags = panel.querySelector('#wp-theme-dev-flags');
  ['borders','spacing','typography','colors','pixel'].forEach((key) => {
    if (!s[key]) return;
    const chip = document.createElement('span');
    chip.className = 'wp-theme-dev-chip';
    chip.textContent = key;
    flags.appendChild(chip);
  });

  let selectedSummary = 'No element selected yet.';

  function setGrid(items) {
    const grid = panel.querySelector('#wp-theme-dev-grid');
    grid.innerHTML = items.map((item) => {
      return '<div class="wp-theme-dev-card"><label>' + item.label + '</label><span>' + item.value + '</span></div>';
    }).join('');
  }

  function inspect(el, evt) {
    document.querySelectorAll('.wp-theme-dev-target').forEach((item) => item.classList.remove('wp-theme-dev-target'));
    el.classList.add('wp-theme-dev-target');

    const cs = window.getComputedStyle(el);
    const rect = el.getBoundingClientRect();
    const selector = [String(el.tagName || '').toLowerCase(), el.id ? ('#' + el.id) : '', el.className && typeof el.className === 'string' ? ('.' + el.className.trim().replace(/\s+/g, '.')) : ''].join('');
    const items = [
      { label: 'Selector', value: selector || 'unknown' },
      { label: 'Classes', value: (typeof el.className === 'string' && el.className.trim()) ? el.className.trim() : 'none' },
      { label: 'Size', value: Math.round(rect.width) + ' × ' + Math.round(rect.height) + ' px' },
      { label: 'Position', value: Math.round(rect.left) + ', ' + Math.round(rect.top) },
    ];

    if (s.typography) {
      items.push({ label: 'Font family', value: cs.fontFamily });
      items.push({ label: 'Typography', value: cs.fontSize + ' / ' + cs.lineHeight + ' / ' + cs.fontWeight });
    }
    if (s.colors) {
      items.push({ label: 'Text color', value: cs.color });
      items.push({ label: 'Background', value: cs.backgroundColor });
      items.push({ label: 'Border', value: cs.borderColor });
    }
    if (s.spacing) {
      items.push({ label: 'Margin', value: [cs.marginTop, cs.marginRight, cs.marginBottom, cs.marginLeft].join(' ') });
      items.push({ label: 'Padding', value: [cs.paddingTop, cs.paddingRight, cs.paddingBottom, cs.paddingLeft].join(' ') });
    }

    selectedSummary = items.map((item) => item.label + ': ' + item.value).join('\n');
    setGrid(items);

    const x = evt ? evt.clientX : rect.left + rect.width / 2;
    const y = evt ? evt.clientY : rect.top + rect.height / 2;
    crossX.style.top = y + 'px';
    crossY.style.left = x + 'px';
    crossX.style.display = 'block';
    crossY.style.display = 'block';
  }

  document.addEventListener('click', function (e) {
    if (!(e.altKey || e.getModifierState('Alt'))) return;
    const action = e.target && e.target.closest ? e.target.closest('[data-dev-action]') : null;
    if (action) return;
    e.preventDefault();
    e.stopPropagation();
    const el = e.target;
    if (!el || typeof el.getBoundingClientRect !== 'function') return;
    inspect(el, e);
  }, true);

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.wp-theme-dev-target').forEach((item) => item.classList.remove('wp-theme-dev-target'));
      crossX.style.display = 'none';
      crossY.style.display = 'none';
      selectedSummary = 'No element selected yet.';
      setGrid([{ label: 'How to inspect', value: 'Use Alt/Option + click on any element. Press Esc to clear.' }]);
    }
  });

  panel.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-dev-action]');
    if (!btn) return;
    const action = btn.getAttribute('data-dev-action');
    if (action === 'collapse') {
      panel.classList.toggle('is-collapsed');
      btn.textContent = panel.classList.contains('is-collapsed') ? 'Show' : 'Hide';
    }
    if (action === 'copy' && navigator.clipboard) {
      navigator.clipboard.writeText(selectedSummary).then(function () {
        btn.textContent = 'Copied';
        setTimeout(function () { btn.textContent = 'Copy'; }, 1200);
      });
    }
  });

  const head = panel.querySelector('.wp-theme-dev-panel-head');
  let dragging = false, startX = 0, startY = 0, baseRight = 16, baseBottom = 16;
  head.addEventListener('mousedown', function (e) {
    if (e.target.closest('[data-dev-action]')) return;
    dragging = true;
    startX = e.clientX;
    startY = e.clientY;
    baseRight = parseInt(window.getComputedStyle(panel).right, 10) || 16;
    baseBottom = parseInt(window.getComputedStyle(panel).bottom, 10) || 16;
    document.body.style.userSelect = 'none';
  });
  document.addEventListener('mousemove', function (e) {
    if (!dragging) return;
    panel.style.right = Math.max(8, baseRight - (e.clientX - startX)) + 'px';
    panel.style.bottom = Math.max(8, baseBottom - (e.clientY - startY)) + 'px';
  });
  document.addEventListener('mouseup', function () {
    dragging = false;
    document.body.style.userSelect = '';
  });

  setGrid([{ label: 'How to inspect', value: 'Use Alt/Option + click on any element. Press Esc to clear.' }]);
})();
JS;

    $script = str_replace('__STATE__', wp_json_encode($state), $script);
    wp_add_inline_script('wp-theme-dev-tools', $script);
});
