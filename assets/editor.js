(function (wp) {
  function wpbbClassArray(value) {
    return String(value || '').split(/\s+/).map(function (item) { return item.trim(); }).filter(Boolean);
  }

  function wpbbUniqueClassList(list) {
    var seen = {};
    var out = [];
    (list || []).forEach(function (item) {
      if (!item) return;
      if (!seen[item]) {
        seen[item] = true;
        out.push(item);
      }
    });
    return out;
  }

  function wpbbJoinClasses(parts) {
    return wpbbUniqueClassList(parts.join(' ').split(/\s+/).filter(Boolean)).join(' ');
  }

  function wpbbResponsiveFieldName(prefix, bp, side) {
    var bpKey = bp === 'default' ? 'Default' : bp.charAt(0).toUpperCase() + bp.slice(1);
    return prefix + bpKey + side;
  }

  function wpbbLegacySpacingValue(props, prefix, side) {
    var key = prefix + side;
    var unitKey = key + 'Unit';
    var raw = props.attributes[key];
    if (raw === undefined || raw === null || raw === '' || Number(raw) === 0) return '';
    return String(raw) + (props.attributes[unitKey] || 'px');
  }

  function wpbbParseSpacingValue(value) {
    var raw = String(value || '').trim();
    if (!raw) return { number: '', unit: 'px' };
    if (raw === 'auto') return { number: '', unit: 'auto' };
    var match = raw.match(/^(-?\d*\.?\d+)(px|%|em|rem|vw|vh)$/i);
    if (match) return { number: match[1], unit: match[2].toLowerCase() };
    return { number: raw.replace(/[^-\d.]/g, ''), unit: raw.replace(/^-?\d*\.?\d+/, '') || 'px' };
  }

  function wpbbBuildSpacingValue(numberValue, unitValue) {
    var numberText = String(numberValue || '').trim();
    var unit = unitValue || 'px';
    if (unit === 'auto') return 'auto';
    if (!numberText) return '';
    return numberText + unit;
  }

  function wpbbEnsureUniqueId(props, prefix) {
    if (props.attributes.uniqueId) return props.attributes.uniqueId;
    var generated = prefix + '-' + String(props.clientId || Math.random()).replace(/[^a-zA-Z0-9_-]/g, '').slice(0, 8);
    props.setAttributes({ uniqueId: generated });
    return generated;
  }

  function wpbbValueWithUnitField(props, valueKey, unitKey, labelText) {
    var parsed = wpbbParseSpacingValue(props.attributes[valueKey] || '');
    return el('div', { className: 'wpbb-responsive-group wpbb-maxwidth-inline', key: valueKey + '-group' }, [
      el('div', { className: 'wpbb-responsive-group__label', key: 'label' }, labelText),
      el('div', { className: 'wpbb-responsive-side-field wpbb-responsive-side-field--single', key: 'field' }, [
        el('div', { className: 'wpbb-responsive-side-field__controls', key: 'controls' }, [
          el('input', {
            key: 'value',
            className: 'wpbb-spacing-native-input',
            type: 'text',
            inputMode: parsed.unit === 'auto' ? 'text' : 'decimal',
            value: parsed.number,
            placeholder: parsed.unit === 'auto' ? '' : '0',
            disabled: parsed.unit === 'auto',
            onInput: function (event) {
              var v = event && event.target ? event.target.value : '';
              var next = {};
              next[valueKey] = wpbbBuildSpacingValue(v, parsed.unit);
              next[unitKey] = parsed.unit;
              props.setAttributes(next);
            },
            onChange: function (event) {
              var v = event && event.target ? event.target.value : '';
              var next = {};
              next[valueKey] = wpbbBuildSpacingValue(v, parsed.unit);
              next[unitKey] = parsed.unit;
              props.setAttributes(next);
            }
          }),
          el('select', {
            key: 'unit',
            className: 'wpbb-spacing-native-select',
            value: parsed.unit,
            onChange: function (event) {
              var unit = event && event.target ? event.target.value : 'px';
              var next = {};
              next[valueKey] = wpbbBuildSpacingValue(parsed.number, unit);
              next[unitKey] = unit;
              props.setAttributes(next);
            }
          }, [
            el('option', { key: 'px', value: 'px' }, 'px'),
            el('option', { key: '%', value: '%' }, '%'),
            el('option', { key: 'em', value: 'em' }, 'em'),
            el('option', { key: 'rem', value: 'rem' }, 'rem'),
            el('option', { key: 'vw', value: 'vw' }, 'vw'),
            el('option', { key: 'vh', value: 'vh' }, 'vh'),
            el('option', { key: 'auto', value: 'auto' }, 'auto')
          ])
        ])
      ])
    ]);
  }

  function wpbbReadScssFromPanelButton(buttonEl, fallbackValue) {
    try {
      var panel = buttonEl && buttonEl.closest ? buttonEl.closest('.wpbb-code-editor-preview') : null;
      var textarea = panel ? panel.querySelector('textarea') : null;
      if (!textarea) return fallbackValue || '';
      if (textarea.nextSibling && textarea.nextSibling.CodeMirror && typeof textarea.nextSibling.CodeMirror.getValue === 'function') {
        return textarea.nextSibling.CodeMirror.getValue();
      }
      if (textarea.wpbbCodeMirror && typeof textarea.wpbbCodeMirror.getValue === 'function') {
        return textarea.wpbbCodeMirror.getValue();
      }
      return textarea.value || fallbackValue || '';
    } catch (err) {}
    return fallbackValue || '';
  }

  function wpbbEditorBgStyle(attrs) {
    var style = {};
    if (attrs.backgroundImageUrl) {
      style.backgroundImage = 'url(' + attrs.backgroundImageUrl + ')';
      style.backgroundSize = attrs.backgroundSize || 'cover';
      style.backgroundPosition = attrs.backgroundPosition || 'center center';
      style.backgroundRepeat = 'no-repeat';
      style.backgroundAttachment = attrs.backgroundAttachment || 'scroll';
    }
    if (attrs.backgroundColor) {
      style.backgroundColor = attrs.backgroundColor;
    }
    return style;
  }

  function wpbbOverlayNode(attrs) {
    if (!attrs.overlayColor || Number(attrs.overlayOpacity || 0) <= 0) return null;
    return el('div', {
      className: 'wpbb-editor-overlay',
      style: {
        position: 'absolute',
        inset: '0',
        pointerEvents: 'none',
        background: attrs.overlayColor,
        opacity: Number(attrs.overlayOpacity || 0)
      }
    });
  }


  function wpbbPreviewSocialIcon(name) {
    var map = {
      facebook: 'M13.5 22v-8h2.7l.4-3h-3.1V9.1c0-.9.3-1.6 1.7-1.6h1.6V4.8c-.3 0-1.2-.1-2.3-.1-2.3 0-3.8 1.4-3.8 4v2.3H8v3h2.7v8h2.8z',
      instagram: 'M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm0 2.2A2.8 2.8 0 0 0 4.2 7v10A2.8 2.8 0 0 0 7 19.8h10a2.8 2.8 0 0 0 2.8-2.8V7A2.8 2.8 0 0 0 17 4.2H7zm10.5 1.6a1.1 1.1 0 1 1 0 2.2 1.1 1.1 0 0 1 0-2.2zM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10zm0 2.2A2.8 2.8 0 1 0 12 14.8 2.8 2.8 0 0 0 12 9.2z',
      linkedin: 'M6.94 8.5H4V20h2.94V8.5zM5.47 4A1.72 1.72 0 1 0 5.5 7.44 1.72 1.72 0 0 0 5.47 4zM20 12.9c0-3.1-1.66-4.54-3.88-4.54-1.8 0-2.6.99-3.05 1.68V8.5H10.1c.04 1 .04 11.5 0 11.5h2.97v-6.42c0-.34.02-.68.12-.92.27-.68.88-1.38 1.91-1.38 1.35 0 1.89 1.03 1.89 2.54V20H20v-7.1z',
      x: 'M18.9 3H21l-4.6 5.2L21.8 21h-5.7l-4.5-5.8L6.5 21H4.4l5-5.7L2.2 3H8l4.1 5.4L18.9 3zm-2 16h1.6L7 4.9H5.3L16.9 19z',
      youtube: 'M23 12s0-3.5-.45-5.2a2.7 2.7 0 0 0-1.9-1.9C18.9 4.4 12 4.4 12 4.4s-6.9 0-8.65.5a2.7 2.7 0 0 0-1.9 1.9C1 8.5 1 12 1 12s0 3.5.45 5.2a2.7 2.7 0 0 0 1.9 1.9c1.75.5 8.65.5 8.65.5s6.9 0 8.65-.5a2.7 2.7 0 0 0 1.9-1.9C23 15.5 23 12 23 12zM10 15.5v-7l6 3.5-6 3.5z',
      whatsapp: 'M20.5 3.5A11.8 11.8 0 0 0 1.8 17.7L.5 23.5l5.9-1.3A11.8 11.8 0 1 0 20.5 3.5zm-8.7 18a9.7 9.7 0 0 1-4.9-1.3l-.4-.2-3.5.8.8-3.4-.2-.4a9.7 9.7 0 1 1 8.2 4.5zm5.3-7.2c-.3-.1-1.8-.9-2.1-1s-.5-.1-.7.1-.8 1-1 1.1-.4.2-.7 0a7.9 7.9 0 0 1-2.3-1.4 8.8 8.8 0 0 1-1.6-2c-.2-.3 0-.5.1-.7l.5-.6.2-.4a.8.8 0 0 0 0-.5c-.1-.1-.7-1.7-1-2.3-.2-.6-.5-.5-.7-.5h-.6a1.2 1.2 0 0 0-.8.4c-.3.3-1 1-1 2.4s1 2.7 1.2 2.9c.1.2 2 3 4.8 4.2.7.3 1.2.5 1.6.6.7.2 1.4.2 1.9.1.6-.1 1.8-.8 2.1-1.5.3-.7.3-1.4.2-1.5-.1-.1-.3-.2-.6-.3z',
      email: 'M3 5h18a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2zm0 2v.5l9 5.6 9-5.6V7H3zm18 10V9.8l-8.5 5.3a1 1 0 0 1-1 0L3 9.8V17h18z'
    };
    return el('span', { className: 'wpbb-social-preview-badge wpbb-social-preview-badge--icon' }, el('svg', { viewBox: '0 0 24 24', width: 18, height: 18, 'aria-hidden': 'true' }, el('path', { fill: 'currentColor', d: map[name] || map.email })));
  }

  function wpbbShadowOptions() {
    return [
      { label: 'None', value: '' },
      { label: 'Small', value: 'shadow-sm' },
      { label: 'Regular', value: 'shadow' },
      { label: 'Large', value: 'shadow-lg' }
    ];
  }

  function wpbbBackgroundControls(props, kind) {
    return [
      colorInput('Background color', props.attributes.backgroundColor || '', function (v) { props.setAttributes({ backgroundColor: v }); }, kind + '-bg-color'),
      el(TextControl, { key: kind + '-bg-image', label: 'Background image URL', value: props.attributes.backgroundImageUrl || '', onChange: function (v) { props.setAttributes({ backgroundImageUrl: v }); } }),
      el(MediaUploadCheck, { key: kind + '-bg-media-check' },
        el(MediaUpload, {
          onSelect: function (media) { props.setAttributes({ backgroundImageUrl: (media && media.url) ? media.url : '' }); },
          allowedTypes: ['image'],
          render: function (obj) {
            return el('div', { className: 'wpbb-media-buttons' }, [
              el(Button, { key: 'open', variant: 'secondary', onClick: obj.open }, props.attributes.backgroundImageUrl ? 'Replace background image' : 'Select background image'),
              props.attributes.backgroundImageUrl ? el(Button, { key: 'clear', variant: 'tertiary', onClick: function(){ props.setAttributes({ backgroundImageUrl: '' }); } }, 'Remove image') : null
            ]);
          }
        })
      ),
      el(SelectControl, {
        key: kind + '-bg-size',
        label: 'Background size',
        value: props.attributes.backgroundSize || 'cover',
        options: [
          { label: 'Cover', value: 'cover' },
          { label: 'Contain', value: 'contain' },
          { label: 'Auto', value: 'auto' }
        ],
        onChange: function (v) { props.setAttributes({ backgroundSize: v }); }
      }),
      el(SelectControl, {
        key: kind + '-bg-position',
        label: 'Background position',
        value: props.attributes.backgroundPosition || 'center center',
        options: [
          { label: 'Center center', value: 'center center' },
          { label: 'Top center', value: 'top center' },
          { label: 'Bottom center', value: 'bottom center' },
          { label: 'Center left', value: 'center left' },
          { label: 'Center right', value: 'center right' }
        ],
        onChange: function (v) { props.setAttributes({ backgroundPosition: v }); }
      }),
      el(SelectControl, {
        key: kind + '-bg-attachment',
        label: 'Background attachment',
        value: props.attributes.backgroundAttachment || 'scroll',
        options: [
          { label: 'Scroll', value: 'scroll' },
          { label: 'Fixed / parallax', value: 'fixed' }
        ],
        onChange: function (v) { props.setAttributes({ backgroundAttachment: v }); }
      }),
      colorInput('Overlay color', props.attributes.overlayColor || '', function (v) { props.setAttributes({ overlayColor: v }); }, kind + '-overlay-color'),
      el(RangeControl, {
        key: kind + '-overlay-opacity',
        label: 'Overlay opacity',
        value: Number(props.attributes.overlayOpacity || 0),
        min: 0,
        max: 1,
        step: 0.05,
        onChange: function (v) { props.setAttributes({ overlayOpacity: Number(v || 0) }); }
      })
    ];
  }

  function wpbbResponsiveSpacingField(props, prefix, bp, side) {
    var key = wpbbResponsiveFieldName(prefix, bp, side);
    var fallback = bp === 'default' ? wpbbLegacySpacingValue(props, prefix, side) : '';
    var parsed = wpbbParseSpacingValue(props.attributes[key] || fallback);
    return el('div', { key: key, className: 'wpbb-responsive-side-field' }, [
      el('label', { key: 'label', className: 'wpbb-responsive-side-field__label' }, side.toLowerCase()),
      el('div', { key: 'controls', className: 'wpbb-responsive-side-field__controls' }, [
        el('input', {
          key: 'value',
          className: 'wpbb-spacing-native-input',
          type: 'text',
          inputMode: parsed.unit === 'auto' ? 'text' : 'decimal',
          value: parsed.number,
          placeholder: parsed.unit === 'auto' ? '' : '0',
          disabled: parsed.unit === 'auto',
          onInput: function (event) {
            var v = event && event.target ? event.target.value : '';
            var next = {};
            next[key] = wpbbBuildSpacingValue(v, parsed.unit);
            props.setAttributes(next);
          },
          onChange: function (event) {
            var v = event && event.target ? event.target.value : '';
            var next = {};
            next[key] = wpbbBuildSpacingValue(v, parsed.unit);
            props.setAttributes(next);
          }
        }),
        el('select', {
          key: 'unit',
          className: 'wpbb-spacing-native-select',
          value: parsed.unit,
          onChange: function (event) {
            var unit = event && event.target ? event.target.value : 'px';
            var next = {};
            next[key] = wpbbBuildSpacingValue(parsed.number, unit);
            props.setAttributes(next);
          }
        }, [
          el('option', { key: 'px', value: 'px' }, 'px'),
          el('option', { key: '%', value: '%' }, '%'),
          el('option', { key: 'em', value: 'em' }, 'em'),
          el('option', { key: 'rem', value: 'rem' }, 'rem'),
          el('option', { key: 'vw', value: 'vw' }, 'vw'),
          el('option', { key: 'vh', value: 'vh' }, 'vh'),
          el('option', { key: 'auto', value: 'auto' }, 'auto')
        ])
      ])
    ]);
  }

  function wpbbResponsiveSpacingGroup(props, prefix, title) {
    var tabs = [
      { name: 'default', title: 'def' },
      { name: 'sm', title: 'sm' },
      { name: 'md', title: 'md' },
      { name: 'lg', title: 'lg' },
      { name: 'xl', title: 'xl' },
      { name: 'xxl', title: 'xxl' }
    ];
    return el('div', { className: 'wpbb-responsive-group', key: prefix + '-group' }, [
      el('div', { className: 'wpbb-responsive-group__label', key: 'label' }, title),
      el(TabPanel, {
        key: 'tabs',
        className: 'wpbb-responsive-tabs wpbb-responsive-tabs--compact',
        tabs: tabs
      }, function (tab) {
        return el('div', { className: 'wpbb-responsive-tab-panel', key: tab.name }, [
          wpbbResponsiveSpacingField(props, prefix, tab.name, 'Top'),
          wpbbResponsiveSpacingField(props, prefix, tab.name, 'Right'),
          wpbbResponsiveSpacingField(props, prefix, tab.name, 'Bottom'),
          wpbbResponsiveSpacingField(props, prefix, tab.name, 'Left')
        ]);
      })
    ]);
  }

  function wpbbBootstrapOptions(kind) {
    var rowOptions = [
      'row', 'row-cols-1', 'row-cols-2', 'row-cols-3', 'row-cols-4', 'row-cols-5', 'row-cols-6',
      'row-cols-sm-1', 'row-cols-sm-2', 'row-cols-sm-3', 'row-cols-sm-4', 'row-cols-sm-6',
      'row-cols-md-1', 'row-cols-md-2', 'row-cols-md-3', 'row-cols-md-4', 'row-cols-md-6',
      'row-cols-lg-1', 'row-cols-lg-2', 'row-cols-lg-3', 'row-cols-lg-4', 'row-cols-lg-6',
      'row-cols-xl-1', 'row-cols-xl-2', 'row-cols-xl-3', 'row-cols-xl-4', 'row-cols-xl-6',
      'row-cols-xxl-1', 'row-cols-xxl-2', 'row-cols-xxl-3', 'row-cols-xxl-4', 'row-cols-xxl-6',
      'g-0', 'g-1', 'g-2', 'g-3', 'g-4', 'g-5', 'gx-0', 'gx-1', 'gx-2', 'gx-3', 'gx-4', 'gx-5', 'gy-0', 'gy-1', 'gy-2', 'gy-3', 'gy-4', 'gy-5',
      'justify-content-start', 'justify-content-center', 'justify-content-end', 'justify-content-between', 'justify-content-around', 'justify-content-evenly',
      'align-items-start', 'align-items-center', 'align-items-end', 'align-items-stretch',
      'align-content-start', 'align-content-center', 'align-content-end',
      'd-flex', 'd-block', 'd-none', 'flex-row', 'flex-column', 'flex-wrap', 'flex-nowrap',
      'shadow', 'shadow-sm', 'shadow-lg', 'rounded', 'rounded-0', 'rounded-3', 'text-center', 'text-start', 'text-end'
    ];
    var colOptions = [
      'col', 'col-auto', 'col-1', 'col-2', 'col-3', 'col-4', 'col-5', 'col-6', 'col-7', 'col-8', 'col-9', 'col-10', 'col-11', 'col-12',
      'col-sm-auto', 'col-sm-1', 'col-sm-2', 'col-sm-3', 'col-sm-4', 'col-sm-5', 'col-sm-6', 'col-sm-7', 'col-sm-8', 'col-sm-9', 'col-sm-10', 'col-sm-11', 'col-sm-12',
      'col-md-auto', 'col-md-1', 'col-md-2', 'col-md-3', 'col-md-4', 'col-md-5', 'col-md-6', 'col-md-7', 'col-md-8', 'col-md-9', 'col-md-10', 'col-md-11', 'col-md-12',
      'col-lg-auto', 'col-lg-1', 'col-lg-2', 'col-lg-3', 'col-lg-4', 'col-lg-5', 'col-lg-6', 'col-lg-7', 'col-lg-8', 'col-lg-9', 'col-lg-10', 'col-lg-11', 'col-lg-12',
      'col-xl-auto', 'col-xl-1', 'col-xl-2', 'col-xl-3', 'col-xl-4', 'col-xl-5', 'col-xl-6', 'col-xl-7', 'col-xl-8', 'col-xl-9', 'col-xl-10', 'col-xl-11', 'col-xl-12',
      'col-xxl-auto', 'col-xxl-1', 'col-xxl-2', 'col-xxl-3', 'col-xxl-4', 'col-xxl-5', 'col-xxl-6', 'col-xxl-7', 'col-xxl-8', 'col-xxl-9', 'col-xxl-10', 'col-xxl-11', 'col-xxl-12',
      'offset-1', 'offset-2', 'offset-3', 'offset-4', 'offset-5', 'offset-6',
      'offset-sm-1', 'offset-sm-2', 'offset-sm-3', 'offset-sm-4', 'offset-sm-5', 'offset-sm-6',
      'offset-md-1', 'offset-md-2', 'offset-md-3', 'offset-md-4', 'offset-md-5', 'offset-md-6',
      'offset-lg-1', 'offset-lg-2', 'offset-lg-3', 'offset-lg-4', 'offset-lg-5', 'offset-lg-6',
      'offset-xl-1', 'offset-xl-2', 'offset-xl-3', 'offset-xl-4', 'offset-xl-5', 'offset-xl-6',
      'offset-xxl-1', 'offset-xxl-2', 'offset-xxl-3', 'offset-xxl-4', 'offset-xxl-5', 'offset-xxl-6',
      'order-0', 'order-1', 'order-2', 'order-3', 'order-4', 'order-5', 'order-first', 'order-last',
      'order-sm-0', 'order-sm-1', 'order-sm-2', 'order-sm-3', 'order-sm-4', 'order-sm-5',
      'order-md-0', 'order-md-1', 'order-md-2', 'order-md-3', 'order-md-4', 'order-md-5',
      'order-lg-0', 'order-lg-1', 'order-lg-2', 'order-lg-3', 'order-lg-4', 'order-lg-5',
      'order-xl-0', 'order-xl-1', 'order-xl-2', 'order-xl-3', 'order-xl-4', 'order-xl-5',
      'order-xxl-0', 'order-xxl-1', 'order-xxl-2', 'order-xxl-3', 'order-xxl-4', 'order-xxl-5',
      'align-self-start', 'align-self-center', 'align-self-end', 'align-self-stretch',
      'd-flex', 'd-block', 'd-none', 'shadow', 'shadow-sm', 'shadow-lg', 'rounded', 'rounded-0', 'rounded-3', 'text-center', 'text-start', 'text-end'
    ];
    var source = kind === 'row' ? rowOptions : colOptions;
    return source.map(function (item) { return { label: item, value: item }; });
  }

  function wpbbBootstrapClassSelector(props, kind) {
    var options = wpbbBootstrapOptions(kind);
    var selected = wpbbClassArray(props.attributes.bootstrapClasses);
    var selectedMap = {};
    selected.forEach(function (item) { selectedMap[item] = true; });
    var filterKey = kind === 'row' ? 'bootstrapSearchRow' : 'bootstrapSearchColumn';
    var query = String(props.attributes[filterKey] || '').toLowerCase();
    var filteredOptions = options.filter(function (option) {
      return !query || option.label.toLowerCase().indexOf(query) !== -1;
    });
    function removeClass(item) {
      props.setAttributes({ bootstrapClasses: selected.filter(function (value) { return value !== item; }).join(' ') });
    }
    function addSelected(event) {
      var values = selected.slice();
      var optionsList = event.target.options || [];
      for (var i = 0; i < optionsList.length; i++) {
        if (optionsList[i].selected) values.push(optionsList[i].value);
      }
      props.setAttributes({ bootstrapClasses: wpbbUniqueClassList(values).join(' ') });
    }
    return el('div', { className: 'wpbb-bootstrap-select', key: kind + '-bootstrap-classes' }, [
      el('label', { key: 'label', className: 'wpbb-editor-field-label' }, 'Bootstrap class(es)'),
      el(TextControl, {
        key: 'search',
        className: 'wpbb-bootstrap-search',
        placeholder: 'Search Bootstrap classes',
        value: props.attributes[filterKey] || '',
        onChange: function (v) {
          var next = {};
          next[filterKey] = v;
          props.setAttributes(next);
        }
      }),
      el('select', {
        key: 'select',
        className: 'wpbb-bootstrap-select__input',
        multiple: true,
        size: 10,
        value: selected,
        onChange: addSelected
      }, filteredOptions.map(function (option) {
        return el('option', { key: option.value, value: option.value, selected: !!selectedMap[option.value] }, option.label);
      })),
      el('div', { key: 'help', className: 'wpbb-bootstrap-class-tip' }, 'Multi-select list. Selected items stay added. Click × on a chip to remove.'),
      selected.length ? el('div', { key: 'selected', className: 'wpbb-selected-class-list' }, selected.map(function (item) {
        return el('span', { key: item, className: 'wpbb-selected-class-chip' }, [
          el('span', { key: 'text', className: 'wpbb-selected-class-chip__text' }, item),
          el('button', {
            key: 'remove',
            type: 'button',
            className: 'wpbb-selected-class-chip__remove',
            onClick: function () { removeClass(item); },
            'aria-label': 'Remove ' + item
          }, '×')
        ]);
      })) : null
    ]);
  }

  function wpbbCustomClassField(props, keyName) {
    var current = props.attributes.customClasses || props.attributes.utilityClasses || '';
    return el(TextControl, {
      key: keyName || 'customClasses',
      label: 'Additional CSS class(es)',
      help: 'Custom classes are appended after selected Bootstrap classes.',
      value: current,
      onChange: function (v) { props.setAttributes({ customClasses: v }); }
    });
  }

  function wpbbApplyResponsiveSpacingAttributes(attributes) {
    ['padding', 'margin'].forEach(function (prefix) {
      ['Default', 'Sm', 'Md', 'Lg', 'Xl', 'Xxl'].forEach(function (bp) {
        ['Top', 'Right', 'Bottom', 'Left'].forEach(function (side) {
          attributes[prefix + bp + side] = { type: 'string', default: '' };
        });
      });
    });
    return attributes;
  }

  function wpbbCompileScopedScssPreview(selector, scss) {
    scss = (scss || '').trim();
    if (!scss) return '';
    if (scss.indexOf('{') === -1) return selector + ' {' + scss + '}';
    return scss.replace(/&/g, selector);
  }

  function wpbbDirectCompileScss(selector, scss) {
    scss = String(scss || '').trim();
    if (!scss) return '';
    if (scss.indexOf('{') === -1) return selector + '{' + scss + '}';

    scss = scss.replace(/\/\*[\s\S]*?\*\//g, '');

    var vars = {};
    scss = scss.replace(/\$([a-zA-Z0-9_-]+)\s*:\s*([^;]+);/g, function (_, name, value) {
      vars[name] = String(value || '').trim();
      return '';
    });

    Object.keys(vars).forEach(function (name) {
      var pattern = new RegExp('\\$' + name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\b', 'g');
      scss = scss.replace(pattern, vars[name]);
    });

    function normalizeSelector(parent, selectorText) {
      return selectorText.split(',').map(function (part) {
        part = part.trim();
        if (!part) return '';
        if (!parent) return part;
        if (part.indexOf('&') !== -1) return part.replace(/&/g, parent);
        return (parent + ' ' + part).trim();
      }).filter(Boolean).join(',');
    }

    function extractNestedRanges(source) {
      var ranges = [];
      var i = 0;
      while (i < source.length) {
        while (i < source.length && /\s/.test(source.charAt(i))) i++;
        var start = i;
        while (i < source.length && source.charAt(i) !== '{' && source.charAt(i) !== '}') i++;
        if (i < source.length && source.charAt(i) === '{') {
          var depth = 1;
          i++;
          while (i < source.length && depth > 0) {
            if (source.charAt(i) === '{') depth++;
            if (source.charAt(i) === '}') depth--;
            i++;
          }
          ranges.push([start, i]);
        } else {
          i++;
        }
      }
      return ranges;
    }

    function flatten(source, parent) {
      var css = '';
      var len = source.length;
      var i = 0;

      while (i < len) {
        while (i < len && /\s/.test(source.charAt(i))) i++;
        if (i >= len) break;

        var selStart = i;
        while (i < len && source.charAt(i) !== '{' && source.charAt(i) !== '}') i++;
        if (i >= len || source.charAt(i) === '}') break;

        var selectorText = source.slice(selStart, i).trim();
        i++;

        var depth = 1;
        var bodyStart = i;
        while (i < len && depth > 0) {
          if (source.charAt(i) === '{') depth++;
          if (source.charAt(i) === '}') depth--;
          i++;
        }

        var body = source.slice(bodyStart, Math.max(bodyStart, i - 1)).trim();
        if (!selectorText) continue;

        var fullSelector = normalizeSelector(parent, selectorText);

        var ranges = extractNestedRanges(body);
        var plain = '';
        if (ranges.length) {
          var cursor = 0;
          ranges.forEach(function (range) {
            plain += body.slice(cursor, range[0]) + ' ';
            cursor = range[1];
          });
          plain += body.slice(cursor);
        } else {
          plain = body;
        }

        plain = plain
          .replace(/\s*;\s*/g, ';')
          .replace(/\s*:\s*/g, ':')
          .replace(/^;+|;+$/g, '')
          .trim();

        if (plain) css += fullSelector + '{' + plain + '}';
        if (body.indexOf('{') !== -1) css += flatten(body, fullSelector);
      }
      return css;
    }

    var wrapped = scss;
    if (scss.charAt(0) === '&' || /^[a-zA-Z0-9.#:[\]&_-]/.test(scss) === false) {
      wrapped = selector + '{' + scss + '}';
    }

    var result = flatten(wrapped, '');
    if (!result) result = wrapped.replace(/&/g, selector);

    return result
      .replace(/\s+/g, ' ')
      .replace(/\s*{\s*/g, '{')
      .replace(/\s*}\s*/g, '}')
      .replace(/\s*;\s*/g, ';')
      .replace(/\s*:\s*/g, ':')
      .replace(/\s*,\s*/g, ',')
      .trim();
  }

  var el = wp.element.createElement;
  var registerBlockType = wp.blocks.registerBlockType;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var InspectorControls = wp.blockEditor.InspectorControls;
  var Button = wp.components.Button;
  var ButtonGroup = wp.components.ButtonGroup;
  var ColorPicker = wp.components.ColorPicker;
  var MediaUpload = wp.blockEditor.MediaUpload;
  var MediaUploadCheck = wp.blockEditor.MediaUploadCheck;
  var InnerBlocks = wp.blockEditor.InnerBlocks;
  var RichText = wp.blockEditor.RichText;
  var PanelBody = wp.components.PanelBody;
  var TextControl = wp.components.TextControl;
  var TextareaControl = wp.components.TextareaControl;
  var TabPanel = wp.components.TabPanel;
  var SelectControl = wp.components.SelectControl;
  var ToggleControl = wp.components.ToggleControl;
  var RangeControl = wp.components.RangeControl;
  var useState = wp.element.useState;
  var Button = wp.components.Button;

  function label(text) {
    return el('div', { className: 'wpbb-editor-label' }, text);
  }

  function colorInput(labelText, value, onChange, key) {
    return el('div', { key: key || labelText, style: { marginBottom: '12px' }, className: 'wpbb-color-control' }, [
      el('label', { key: 'l', style: { display: 'block', fontWeight: '600', marginBottom: '6px' } }, labelText),
      el('div', { key: 'picker', className: 'wpbb-color-control__picker' }, [
        el(ColorPicker, {
          key: 'cp',
          color: value || '#000000',
          enableAlpha: true,
          onChangeComplete: function (next) {
            var out = next && next.rgb ? ('rgba(' + next.rgb.r + ',' + next.rgb.g + ',' + next.rgb.b + ',' + next.rgb.a + ')') : (next.hex || value || '');
            onChange(out);
          }
        })
      ])
    ]);
  }


  function unitOptions() {
    return [{ label: 'px', value: 'px' }, { label: '%', value: '%' }, { label: 'em', value: 'em' }];
  }

  function sideSpacingControls(props, prefix) {
    function keyName(side) { return prefix + side; }
    function unitKey(side) { return prefix + side + 'Unit'; }
    function row(side, shortLabel) {
      return el('div', { key: prefix + side, className: 'wpbb-inline-grid' }, [
        el(RangeControl, {
          key: 'r',
          label: shortLabel,
          value: props.attributes[keyName(side)] || 0,
          min: 0,
          max: 200,
          onChange: function(v){ var o={}; o[keyName(side)] = v || 0; props.setAttributes(o); }
        }),
        el(SelectControl, {
          key: 'u',
          label: 'Unit',
          value: props.attributes[unitKey(side)] || 'px',
          options: unitOptions(),
          onChange: function(v){ var o={}; o[unitKey(side)] = v; props.setAttributes(o); }
        })
      ]);
    }
    return [
      row('Top', prefix + ' top'),
      row('Right', prefix + ' right'),
      row('Bottom', prefix + ' bottom'),
      row('Left', prefix + ' left')
    ];
  }

  function visibilitySwitches(props) {
    return el('div', { className: 'wpbb-mini-card', key: 'visibility-switches' }, [
      el(ToggleControl, { key:'xs', label:'Show XS', checked: props.attributes.visibilityXs !== false, onChange:function(v){ props.setAttributes({ visibilityXs:v }); } }),
      el(ToggleControl, { key:'sm', label:'Show SM', checked: props.attributes.visibilitySm !== false, onChange:function(v){ props.setAttributes({ visibilitySm:v }); } }),
      el(ToggleControl, { key:'md', label:'Show MD', checked: props.attributes.visibilityMd !== false, onChange:function(v){ props.setAttributes({ visibilityMd:v }); } }),
      el(ToggleControl, { key:'lg', label:'Show LG', checked: props.attributes.visibilityLg !== false, onChange:function(v){ props.setAttributes({ visibilityLg:v }); } }),
      el(ToggleControl, { key:'xl', label:'Show XL', checked: props.attributes.visibilityXl !== false, onChange:function(v){ props.setAttributes({ visibilityXl:v }); } })
    ]);
  }

  function containerEdit(blockLabel, allowedBlocks, extraControls) {
    return function (props) {
      var blockProps = useBlockProps({ className: 'wpbb-' + props.name.split('/')[1], style: { width: '100%', maxWidth: 'none' } });
      return el(wp.element.Fragment, {},
        extraControls ? el(InspectorControls, {}, el(PanelBody, { title: 'Settings', initialOpen: true }, extraControls(props))) : null,
        el('div', blockProps, label(blockLabel), el(InnerBlocks, { allowedBlocks: allowedBlocks || undefined }))
      );
    };
  }


  registerBlockType('wpbb/row', {
    title: 'Row',
    icon: 'grid-view',
    category: 'wpbb',
    attributes: wpbbApplyResponsiveSpacingAttributes({
      containerClass: { type: 'string', default: '' },
      utilityClasses: { type: 'string', default: '' },
      bootstrapClasses: { type: 'string', default: '' },
      customClasses: { type: 'string', default: '' },
      bootstrapSearchRow: { type: 'string', default: '' },
      scssBuildStamp: { type: 'string', default: '' },
      compiledCss: { type: 'string', default: '' },
      backgroundColor: { type: 'string', default: '' },
      backgroundImageUrl: { type: 'string', default: '' },
      backgroundSize: { type: 'string', default: 'cover' },
      backgroundPosition: { type: 'string', default: 'center center' },
      overlayColor: { type: 'string', default: '' },
      overlayOpacity: { type: 'number', default: 0 },
      backgroundAttachment: { type: 'string', default: 'scroll' },
      maxWidth: { type: 'string', default: '' },
      maxWidthUnit: { type: 'string', default: 'px' },
      visibilityClass: { type: 'string', default: '' },
      visibilityXs: { type: 'boolean', default: true },
      visibilitySm: { type: 'boolean', default: true },
      visibilityMd: { type: 'boolean', default: true },
      visibilityLg: { type: 'boolean', default: true },
      visibilityXl: { type: 'boolean', default: true },
      animationClass: { type: 'string', default: '' },
      paddingTop: { type: 'number', default: 0 }, paddingTopUnit: { type: 'string', default: 'px' },
      paddingRight: { type: 'number', default: 0 }, paddingRightUnit: { type: 'string', default: 'px' },
      paddingBottom: { type: 'number', default: 0 }, paddingBottomUnit: { type: 'string', default: 'px' },
      paddingLeft: { type: 'number', default: 0 }, paddingLeftUnit: { type: 'string', default: 'px' },
      marginTop: { type: 'number', default: 0 }, marginTopUnit: { type: 'string', default: 'px' },
      marginRight: { type: 'number', default: 0 }, marginRightUnit: { type: 'string', default: 'px' },
      marginBottom: { type: 'number', default: 0 }, marginBottomUnit: { type: 'string', default: 'px' },
      marginLeft: { type: 'number', default: 0 }, marginLeftUnit: { type: 'string', default: 'px' },
      gutterX: { type: 'string', default: 'gx-2' },
      gutterY: { type: 'string', default: 'gy-2' },
      align: { type: 'string', default: '' },
      spacingSm: { type: 'string', default: '' }, spacingMd: { type: 'string', default: '' }, spacingLg: { type: 'string', default: '' }, spacingXl: { type: 'string', default: '' }, spacingXxl: { type: 'string', default: '' },
      paddingSm: { type: 'string', default: '' }, paddingMd: { type: 'string', default: '' }, paddingLg: { type: 'string', default: '' }, paddingXl: { type: 'string', default: '' }, paddingXxl: { type: 'string', default: '' },
      marginSm: { type: 'string', default: '' }, marginMd: { type: 'string', default: '' }, marginLg: { type: 'string', default: '' }, marginXl: { type: 'string', default: '' }, marginXxl: { type: 'string', default: '' },
      uniqueId: { type: 'string', default: '' }, customCss: { type: 'string', default: '' }, customScss: { type: 'string', default: '' }
    }),
    edit: function (props) {
      wpbbEnsureUniqueId(props, 'wpbb-row');
      var className = wpbbJoinClasses([
        'wpbb-row', 'row',
        props.attributes.gutterX || '',
        props.attributes.gutterY || '',
        props.attributes.align ? ('justify-content-' + props.attributes.align) : '',
        props.attributes.bootstrapClasses || '',
        props.attributes.customClasses || '',
        props.attributes.utilityClasses || '',
        props.attributes.visibilityClass || '',
        props.attributes.animationClass || ''
      ]);
      var blockProps = useBlockProps({
        className: className,
        style: Object.assign({
          width: '100%',
          maxWidth: (props.attributes.maxWidth ? String(props.attributes.maxWidth) + (props.attributes.maxWidthUnit || 'px') : 'none'),
          marginLeft: props.attributes.maxWidth ? 'auto' : undefined,
          marginRight: props.attributes.maxWidth ? 'auto' : undefined,
          position: 'relative',
          overflow: 'hidden'
        }, wpbbEditorBgStyle(props.attributes))
      });
      var controls = [
        el(SelectControl, { key: 'containerClass', label: 'Bootstrap container', value: props.attributes.containerClass, options: [{ label: 'None', value: '' }, { label: 'container', value: 'container' }, { label: 'container-fluid', value: 'container-fluid' }], onChange: function (v) { props.setAttributes({ containerClass: v }); } }),
        el(PanelBody, { title: 'Spacing', initialOpen: false }, [wpbbResponsiveSpacingGroup(props, 'padding', 'Padding'), wpbbResponsiveSpacingGroup(props, 'margin', 'Margin')]),
        el(PanelBody, { title: 'Classes', initialOpen: false }, [wpbbBootstrapClassSelector(props, 'row'), wpbbCustomClassField(props, 'customClasses')]),
        el(PanelBody, { title: 'Layout', initialOpen: false }, [wpbbValueWithUnitField(props, 'maxWidth', 'maxWidthUnit', 'Max width')]),
        el(TextControl, { key: 'uniqueId', label: 'Unique ID', value: props.attributes.uniqueId || '', help: 'Auto-generated, but you can change it.', onChange: function (v) { props.setAttributes({ uniqueId: v }); } })
      ].concat([el(PanelBody, { title: 'Background', initialOpen: false }, wpbbBackgroundControls(props, 'row'))]).concat([
        el(PanelBody, { title: 'Custom SCSS', initialOpen: false }, [el('div', { key: 'customStyles', className: 'wpbb-code-editor-preview' }, [
          el(TextareaControl, { key: 'customScss', label: 'Custom SCSS', className: 'wpbb-code-editor wpbb-code-editor--scss', help: 'Use & for this block scope', value: props.attributes.customScss || '', onChange: function (v) { props.setAttributes({ customScss: v, compiledCss: '' }); } }),
          el('div', { key: 'buildBar', className: 'wpbb-scss-build-bar' }, [
            el(Button, { key: 'buildBtn', variant: 'secondary', onClick: function (event) {
              var scss = wpbbReadScssFromPanelButton(event && event.target ? event.target : null, props.attributes.customScss || '');
              var selector = '#' + (props.attributes.uniqueId || wpbbEnsureUniqueId(props, 'wpbb-row'));
              var css = wpbbDirectCompileScss(selector, scss);
              props.setAttributes({ customScss: scss, compiledCss: css || '', scssBuildStamp: String(Date.now()) });
            } }, 'Build SCSS'),
            el('span', { key: 'note', className: 'wpbb-scss-build-note' }, (props.attributes.compiledCss ? 'Built successfully below' : 'Click Build SCSS after typing'))
          ]),
          el('div', { key: 'compiledPreviewWrap', className: 'wpbb-code-preview-wrap' }, [
            el('label', { key: 'compiledPreviewLabel', style: { display: 'block', fontWeight: '600', marginBottom: '6px' } }, 'Compiled CSS preview'),
            el('textarea', {
              key: 'compiledPreview',
              className: 'wpbb-code-editor wpbb-code-editor--compiled-preview',
              value: props.attributes.compiledCss || '',
              readOnly: true,
              rows: 8,
              style: { width: '100%', fontFamily: 'monospace' }
            })
          ])])]),
        el(PanelBody, { title: 'Visibility & Motion', initialOpen: false }, [el(SelectControl, { key: 'visibilityClass', label: 'Extra visibility class', value: props.attributes.visibilityClass, options: [{ label: 'None', value: '' }, { label: 'd-none', value: 'd-none' }, { label: 'd-none d-md-block', value: 'd-none d-md-block' }, { label: 'd-md-none', value: 'd-md-none' }], onChange: function (v) { props.setAttributes({ visibilityClass: v }); } }),
        visibilitySwitches(props),
        el(SelectControl, { key: 'animationClass', label: 'Animation', value: props.attributes.animationClass, options: [{ label: 'None', value: '' }, { label: 'anim-fade-in', value: 'anim-fade-in' }, { label: 'anim-fade-up', value: 'anim-fade-up' }, { label: 'anim-zoom-in', value: 'anim-zoom-in' }, { label: 'Fade Left', value: 'anim-fade-left' }, { label: 'Fade Right', value: 'anim-fade-right' }], onChange: function (v) { props.setAttributes({ animationClass: v }); } }),
        el(SelectControl, { key: 'gutterX', label: 'Horizontal gap', value: props.attributes.gutterX, options: [{ label: 'gx-0', value: 'gx-0' }, { label: 'gx-1', value: 'gx-1' }, { label: 'gx-2', value: 'gx-2' }, { label: 'gx-3', value: 'gx-3' }, { label: 'gx-4', value: 'gx-4' }, { label: 'gx-5', value: 'gx-5' }], onChange: function (v) { props.setAttributes({ gutterX: v }); } }),
        el(SelectControl, { key: 'gutterY', label: 'Vertical gap', value: props.attributes.gutterY, options: [{ label: 'gy-0', value: 'gy-0' }, { label: 'gy-1', value: 'gy-1' }, { label: 'gy-2', value: 'gy-2' }, { label: 'gy-3', value: 'gy-3' }, { label: 'gy-4', value: 'gy-4' }, { label: 'gy-5', value: 'gy-5' }], onChange: function (v) { props.setAttributes({ gutterY: v }); } }),
        el(SelectControl, { key: 'align', label: 'Alignment', value: props.attributes.align, options: [{ label: 'Default', value: '' }, { label: 'Start', value: 'start' }, { label: 'Center', value: 'center' }, { label: 'End', value: 'end' }, { label: 'Between', value: 'between' }], onChange: function (v) { props.setAttributes({ align: v }); } })])
      ]);
      return el(wp.element.Fragment, {},
        el(InspectorControls, {}, el(PanelBody, { title: 'Row settings', initialOpen: true }, controls)),
        el('div', blockProps,
          props.attributes.customScss ? el('style', {}, wpbbCompileScopedScssPreview('#' + (props.attributes.uniqueId || 'preview-row'), props.attributes.customScss || '')) : null,
          wpbbOverlayNode(props.attributes),
          el('div', { style: { position: 'relative', zIndex: 1, width: '100%' } }, [
            label('ROW ' + (props.attributes.uniqueId || '')),
            props.attributes.containerClass
              ? el('div', { className: props.attributes.containerClass, style: { width: '100%', maxWidth: props.attributes.containerClass === 'container-fluid' ? 'none' : undefined } },
                  el(InnerBlocks, { allowedBlocks: ['wpbb/column'], orientation: 'horizontal' }))
              : el(InnerBlocks, { allowedBlocks: ['wpbb/column'], orientation: 'horizontal' })
          ])
        )
      );
    },
    save: function () { return el(InnerBlocks.Content); }
  });

  registerBlockType('wpbb/column', {
    title: 'Column',
    icon: 'columns',
    category: 'wpbb',
    parent: ['wpbb/row'],
    attributes: wpbbApplyResponsiveSpacingAttributes({
      xs: { type: 'number', default: 12 },
      sm: { type: 'number', default: 0 },
      md: { type: 'number', default: 6 },
      lg: { type: 'number', default: 0 },
      xl: { type: 'number', default: 0 },
      xxl: { type: 'number', default: 0 },
      uniqueId: { type: 'string', default: '' },
      maxWidth: { type: 'string', default: '' },
      maxWidthUnit: { type: 'string', default: 'px' },
      customScss: { type: 'string', default: '' },
      bootstrapClasses: { type: 'string', default: '' },
      customClasses: { type: 'string', default: '' },
      bootstrapSearchColumn: { type: 'string', default: '' },
      scssBuildStamp: { type: 'string', default: '' },
      compiledCss: { type: 'string', default: '' },
      backgroundColor: { type: 'string', default: '' },
      backgroundImageUrl: { type: 'string', default: '' },
      backgroundSize: { type: 'string', default: 'cover' },
      backgroundPosition: { type: 'string', default: 'center center' },
      overlayColor: { type: 'string', default: '' },
      overlayOpacity: { type: 'number', default: 0 },
      backgroundAttachment: { type: 'string', default: 'scroll' },
      boxShadowClass: { type: 'string', default: '' },
      boxShadowColor: { type: 'string', default: '' },
      utilityClasses: { type: 'string', default: '' },
      orderClass: { type: 'string', default: '' },
      verticalAlign: { type: 'string', default: '' },
      horizontalAlign: { type: 'string', default: '' },
      visibilityClass: { type: 'string', default: '' },
      visibilityXs: { type: 'boolean', default: true },
      visibilitySm: { type: 'boolean', default: true },
      visibilityMd: { type: 'boolean', default: true },
      visibilityLg: { type: 'boolean', default: true },
      visibilityXl: { type: 'boolean', default: true },
      animationClass: { type: 'string', default: '' },
      paddingTop: { type: 'number', default: 0 }, paddingTopUnit: { type: 'string', default: 'px' },
      paddingRight: { type: 'number', default: 0 }, paddingRightUnit: { type: 'string', default: 'px' },
      paddingBottom: { type: 'number', default: 0 }, paddingBottomUnit: { type: 'string', default: 'px' },
      paddingLeft: { type: 'number', default: 0 }, paddingLeftUnit: { type: 'string', default: 'px' },
      marginTop: { type: 'number', default: 0 }, marginTopUnit: { type: 'string', default: 'px' },
      marginRight: { type: 'number', default: 0 }, marginRightUnit: { type: 'string', default: 'px' },
      marginBottom: { type: 'number', default: 0 }, marginBottomUnit: { type: 'string', default: 'px' },
      marginLeft: { type: 'number', default: 0 }, marginLeftUnit: { type: 'string', default: 'px' }
    }),
    edit: function (props) {
      wpbbEnsureUniqueId(props, 'wpbb-col');
      var previewState = useState('lg');
      var previewBp = previewState[0];
      var setPreviewBp = previewState[1];
      var cls = ['wpbb-column'];
      ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'].forEach(function (bp) {
        var val = props.attributes[bp];
        if (val) cls.push(bp === 'xs' ? ('col-' + val) : ('col-' + bp + '-' + val));
      });
      cls = cls.concat(wpbbClassArray(props.attributes.orderClass || ''));
      cls = cls.concat(wpbbClassArray(props.attributes.verticalAlign || ''));
      cls = cls.concat(wpbbClassArray(props.attributes.horizontalAlign || ''));
      cls = cls.concat(wpbbClassArray(props.attributes.visibilityClass || ''));
      cls = cls.concat(wpbbClassArray(props.attributes.animationClass || ''));
      cls = cls.concat(wpbbClassArray(props.attributes.bootstrapClasses || ''));
      cls = cls.concat(wpbbClassArray(props.attributes.customClasses || ''));
      cls = cls.concat(wpbbClassArray(props.attributes.utilityClasses || ''));
      cls = cls.concat(wpbbClassArray(props.attributes.boxShadowClass || ''));
      function responsiveValueForBreakpoint(bp) {
        var orderMap = { xs: ['xs'], sm: ['sm', 'xs'], md: ['md', 'sm', 'xs'], lg: ['lg', 'md', 'sm', 'xs'], xl: ['xl', 'lg', 'md', 'sm', 'xs'], xxl: ['xxl', 'xl', 'lg', 'md', 'sm', 'xs'] };
        var order = orderMap[bp] || ['lg', 'md', 'sm', 'xs'];
        for (var i = 0; i < order.length; i++) {
          var val = parseInt(props.attributes[order[i]] || 0, 10);
          if (val > 0) return val;
        }
        return 12;
      }

      var previewBasis = responsiveValueForBreakpoint(previewBp);
      var pct = Math.max(1, Math.min(12, previewBasis)) / 12 * 100;
      var blockProps = useBlockProps({ className: wpbbUniqueClassList(cls).join(' '), style: Object.assign({ flex: '0 0 ' + pct + '%', maxWidth: (props.attributes.maxWidth ? String(props.attributes.maxWidth) + (props.attributes.maxWidthUnit || 'px') : (pct + '%')), boxSizing: 'border-box', position: 'relative', overflow: 'hidden', boxShadow: props.attributes.boxShadowColor && props.attributes.boxShadowClass ? ('0 10px 28px ' + props.attributes.boxShadowColor) : undefined }, wpbbEditorBgStyle(props.attributes)) });

      function sizeControl(bp, labelText, helpText) {
        var presets = [
          { label: 'Full', value: 12 },
          { label: '1/2', value: 6 },
          { label: '1/3', value: 4 },
          { label: '1/4', value: 3 },
          { label: 'Auto', value: 0 }
        ];
        return el('div', { className: 'wpbb-width-control', key: bp + '-width' }, [
          el(RangeControl, {
            key: 'range',
            label: labelText,
            help: helpText,
            value: props.attributes[bp] || 0,
            min: 0,
            max: 12,
            onChange: function (value) {
              var next = {};
              next[bp] = value || 0;
              props.setAttributes(next);
            }
          }),
          el('div', { key: 'presets', className: 'wpbb-width-presets' }, [
            el('span', { key: 'preset-label', className: 'wpbb-width-presets__label' }, 'Quick presets'),
            el(ButtonGroup, { key: 'preset-buttons', className: 'wpbb-width-presets__group' }, presets.map(function (preset) {
              var active = (props.attributes[bp] || 0) === preset.value;
              return el(Button, {
                key: bp + '-' + preset.label,
                isSmall: true,
                variant: active ? 'primary' : 'secondary',
                className: active ? 'is-active' : '',
                onClick: function () {
                  var next = {};
                  next[bp] = preset.value;
                  props.setAttributes(next);
                }
              }, preset.label);
            }))
          ])
        ]);
      }

      function responsiveClassSummary() {
        var out = [];
        ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'].forEach(function (bp) {
          var val = props.attributes[bp] || 0;
          if (!val) return;
          out.push(bp === 'xs' ? ('col-' + val) : ('col-' + bp + '-' + val));
        });
        return out.join(' ');
      }

      var controls = [
        el(PanelBody, { title: 'Responsive widths', initialOpen: true }, [
          el('div', { key: 'device-preview', className: 'wpbb-device-preview' }, [
            el('div', { key: 'device-label', className: 'wpbb-device-preview__label' }, 'Editor preview device'),
            el(ButtonGroup, { key: 'device-buttons', className: 'wpbb-device-preview__buttons' }, [
              { bp: 'xs', label: 'Mobile' },
              { bp: 'md', label: 'Tablet' },
              { bp: 'lg', label: 'Desktop' },
              { bp: 'xl', label: 'Wide' }
            ].map(function (item) {
              var active = previewBp === item.bp;
              return el(Button, {
                key: item.bp,
                isSmall: true,
                variant: active ? 'primary' : 'secondary',
                className: active ? 'is-active' : '',
                onClick: function () { setPreviewBp(item.bp); }
              }, item.label);
            }))
          ]),
          el('div', { key: 'device-note', className: 'wpbb-device-preview__note' }, 'Preview width now follows ' + previewBp.toUpperCase() + ' breakpoint.'),
          sizeControl('xs', 'Mobile', 'Phones: col-' + (props.attributes.xs || 12)),
          sizeControl('sm', 'Small tablet', 'Small screens: ' + (props.attributes.sm ? ('col-sm-' + props.attributes.sm) : 'inherit')),
          sizeControl('md', 'Tablet', 'Medium screens: ' + (props.attributes.md ? ('col-md-' + props.attributes.md) : 'inherit')),
          sizeControl('lg', 'Desktop', 'Large screens: ' + (props.attributes.lg ? ('col-lg-' + props.attributes.lg) : 'inherit')),
          sizeControl('xl', 'Large desktop', 'Extra large: ' + (props.attributes.xl ? ('col-xl-' + props.attributes.xl) : 'inherit')),
          sizeControl('xxl', 'Wide desktop', 'XXL: ' + (props.attributes.xxl ? ('col-xxl-' + props.attributes.xxl) : 'inherit')),
          el('div', { className: 'wpbb-responsive-summary' }, [
            el('strong', { key: 't' }, 'Generated classes'),
            el('code', { key: 'c' }, responsiveClassSummary() || 'col-12')
          ])
        ]),
        el(SelectControl, { key: 'verticalAlign', label: 'Vertical align', value: props.attributes.verticalAlign || '', options: [
          { label: 'Default', value: '' },
          { label: 'Start', value: 'align-self-start' },
          { label: 'Center', value: 'align-self-center' },
          { label: 'End', value: 'align-self-end' },
          { label: 'Stretch', value: 'align-self-stretch' }
        ], onChange: function (v) { props.setAttributes({ verticalAlign: v }); } }),
        el(SelectControl, { key: 'horizontalAlign', label: 'Horizontal align', value: props.attributes.horizontalAlign || '', options: [
          { label: 'Default', value: '' },
          { label: 'Auto left', value: 'me-auto' },
          { label: 'Center', value: 'mx-auto' },
          { label: 'Auto right', value: 'ms-auto' }
        ], onChange: function (v) { props.setAttributes({ horizontalAlign: v }); } }),
        el(PanelBody, { title: 'Spacing', initialOpen: false }, [wpbbResponsiveSpacingGroup(props, 'padding', 'Padding'), wpbbResponsiveSpacingGroup(props, 'margin', 'Margin')]),
        el(PanelBody, { title: 'Classes', initialOpen: false }, [wpbbBootstrapClassSelector(props, 'column'), wpbbCustomClassField(props, 'columnCustomClasses')]),
        el(PanelBody, { title: 'Layout', initialOpen: false }, [el(TextControl, { key: 'uniqueId', label: 'Unique ID', value: props.attributes.uniqueId || '', onChange: function (v) { props.setAttributes({ uniqueId: v }); } }), wpbbValueWithUnitField(props, 'maxWidth', 'maxWidthUnit', 'Max width'), el(SelectControl, { key: 'boxShadowClass', label: 'Box shadow', value: props.attributes.boxShadowClass || '', options: wpbbShadowOptions(), onChange: function (v) { props.setAttributes({ boxShadowClass: v }); } }), colorInput('Box shadow color', props.attributes.boxShadowColor || '', function (v) { props.setAttributes({ boxShadowColor: v }); }, 'column-shadow-color')])
      ].concat([el(PanelBody, { title: 'Background', initialOpen: false }, wpbbBackgroundControls(props, 'column'))]).concat([
        el(PanelBody, { title: 'Custom SCSS', initialOpen: false }, [el('div', { key: 'customStyles', className: 'wpbb-code-editor-preview' }, [
          el(TextareaControl, { key: 'customScss', label: 'Custom SCSS', className: 'wpbb-code-editor wpbb-code-editor--scss', help: 'Use & for this block scope', value: props.attributes.customScss || '', onChange: function (v) { props.setAttributes({ customScss: v, compiledCss: '' }); } }),
          el('div', { key: 'buildBar', className: 'wpbb-scss-build-bar' }, [
            el(Button, { key: 'buildBtn', variant: 'secondary', onClick: function (event) {
              var scss = wpbbReadScssFromPanelButton(event && event.target ? event.target : null, props.attributes.customScss || '');
              var selector = '#' + (props.attributes.uniqueId || wpbbEnsureUniqueId(props, 'wpbb-col'));
              var css = wpbbDirectCompileScss(selector, scss);
              props.setAttributes({ customScss: scss, compiledCss: css || '', scssBuildStamp: String(Date.now()) });
            } }, 'Build SCSS'),
            el('span', { key: 'note', className: 'wpbb-scss-build-note' }, (props.attributes.compiledCss ? 'Built successfully below' : 'Click Build SCSS after typing'))
          ]),
          el('div', { key: 'compiledPreviewWrap', className: 'wpbb-code-preview-wrap' }, [
            el('label', { key: 'compiledPreviewLabel', style: { display: 'block', fontWeight: '600', marginBottom: '6px' } }, 'Compiled CSS preview'),
            el('textarea', {
              key: 'compiledPreview',
              className: 'wpbb-code-editor wpbb-code-editor--compiled-preview',
              value: props.attributes.compiledCss || '',
              readOnly: true,
              rows: 8,
              style: { width: '100%', fontFamily: 'monospace' }
            })
          ])])]),
        el(PanelBody, { title: 'Order, Visibility & Motion', initialOpen: false }, [el(SelectControl, { key: 'orderClass', label: 'Order', value: props.attributes.orderClass, options: [{ label: 'Default', value: '' }, { label: 'order-1', value: 'order-1' }, { label: 'order-2', value: 'order-2' }, { label: 'order-3', value: 'order-3' }, { label: 'order-first', value: 'order-first' }, { label: 'order-last', value: 'order-last' }], onChange: function (v) { props.setAttributes({ orderClass: v }); } }),
        el(SelectControl, { key: 'visibilityClass', label: 'Extra visibility class', value: props.attributes.visibilityClass, options: [{ label: 'None', value: '' }, { label: 'd-none', value: 'd-none' }, { label: 'd-none d-md-block', value: 'd-none d-md-block' }, { label: 'd-md-none', value: 'd-md-none' }], onChange: function (v) { props.setAttributes({ visibilityClass: v }); } }),
        visibilitySwitches(props),
        el(SelectControl, { key: 'animationClass', label: 'Animation', value: props.attributes.animationClass, options: [{ label: 'None', value: '' }, { label: 'anim-fade-in', value: 'anim-fade-in' }, { label: 'anim-fade-up', value: 'anim-fade-up' }, { label: 'anim-zoom-in', value: 'anim-zoom-in' }, { label: 'Fade Left', value: 'anim-fade-left' }, { label: 'Fade Right', value: 'anim-fade-right' }], onChange: function (v) { props.setAttributes({ animationClass: v }); } })])
      ]);

      return el(wp.element.Fragment, {},
        el(InspectorControls, {}, controls),
        el('div', blockProps,
          props.attributes.customScss ? el('style', {}, wpbbCompileScopedScssPreview('#' + (props.attributes.uniqueId || 'preview-column'), props.attributes.customScss || '')) : null,
          wpbbOverlayNode(props.attributes),
          el('div', { style: { position: 'relative', zIndex: 1 } }, [
            label('COLUMN ' + (props.attributes.uniqueId || '')),
            el(InnerBlocks)
          ])
        )
      );
    },
    save: function () { return el(InnerBlocks.Content); }
  });


  registerBlockType('wpbb/button', {
    title: 'Button',
    icon: 'button',
    category: 'wpbb',
    attributes: {
      text: { type: 'string', default: 'Button' },
      url: { type: 'string', default: '#' },
      variant: { type: 'string', default: 'primary' },
      size: { type: 'string', default: '' },
      fullWidth: { type: 'boolean', default: false },
      backgroundColor: { type: 'string', default: '' },
      textColor: { type: 'string', default: '' },
      btnClass: { type: 'string', default: 'btn btn-primary' },
      align: { type: 'string', default: '' },
      borderRadius: { type: 'string', default: '12px' }
    },
    edit: function (props) {
      var computedClass = 'btn btn-' + (props.attributes.variant || 'primary') + (props.attributes.size ? (' btn-' + props.attributes.size) : '') + (props.attributes.fullWidth ? ' w-100' : '');
      var style = {
        background: props.attributes.backgroundColor || undefined,
        color: props.attributes.textColor || undefined,
        borderColor: props.attributes.backgroundColor || undefined,
        display: 'inline-block',
        padding: '10px 14px',
        borderRadius: props.attributes.borderRadius || '12px'
      };
      return el(wp.element.Fragment, {},
        el(InspectorControls, {}, el(PanelBody, { title: 'Button settings', initialOpen: true }, [
          el(TextControl, { key: 'url', label: 'URL', value: props.attributes.url, onChange: function (v) { props.setAttributes({ url: v }); } }),
          el(SelectControl, {
            key: 'variant',
            label: 'Variant',
            value: props.attributes.variant,
            options: [
              { label: 'Primary', value: 'primary' },
              { label: 'Secondary', value: 'secondary' },
              { label: 'Success', value: 'success' },
              { label: 'Danger', value: 'danger' },
              { label: 'Warning', value: 'warning' },
              { label: 'Info', value: 'info' },
              { label: 'Light', value: 'light' },
              { label: 'Dark', value: 'dark' }
            ],
            onChange: function (v) { props.setAttributes({ variant: v }); }
          }),
          el(SelectControl, {
            key: 'size',
            label: 'Size',
            value: props.attributes.size,
            options: [
              { label: 'Default', value: '' },
              { label: 'Small', value: 'sm' },
              { label: 'Large', value: 'lg' }
            ],
            onChange: function (v) { props.setAttributes({ size: v }); }
          }),
          el(ToggleControl, { key: 'fullWidth', label: 'Full width', checked: !!props.attributes.fullWidth, onChange: function (v) { props.setAttributes({ fullWidth: v }); } }),
          el(SelectControl, { key: 'align', label: 'Align', value: props.attributes.align || '', options: [
            { label: 'Default', value: '' },
            { label: 'Left', value: 'start' },
            { label: 'Center', value: 'center' },
            { label: 'Right', value: 'end' }
          ], onChange: function (v) { props.setAttributes({ align: v }); } }),
          el(TextControl, { key: 'borderRadius', label: 'Border radius', value: props.attributes.borderRadius || '12px', onChange: function (v) { props.setAttributes({ borderRadius: v }); } }),
          colorInput('Background color', props.attributes.backgroundColor, function (v) { props.setAttributes({ backgroundColor: v }); }, 'button-bg'),
          colorInput('Text color', props.attributes.textColor, function (v) { props.setAttributes({ textColor: v }); }, 'button-text'),
          el(TextControl, { key: 'btnClass', label: 'Override classes', value: props.attributes.btnClass, onChange: function (v) { props.setAttributes({ btnClass: v }); } })
        ])),
        el('div', useBlockProps({ className: 'wpbb-button-editor', style: { textAlign: props.attributes.align || undefined } }),
          label('BUTTON'),
          el('div', { className: props.attributes.btnClass || computedClass, style: style },
            el(RichText, { tagName: 'span', value: props.attributes.text, onChange: function (v) { props.setAttributes({ text: v }); } })
          )
        )
      );
    },
    save: function () { return null; }
  });

  registerBlockType('wpbb/card', {
    title:'Card', icon:'id', category:'wpbb',
    attributes:{ boxShadowClass:{type:'string',default:'shadow-sm'}, backgroundColor:{type:'string',default:''}, borderColor:{type:'string',default:''}, borderRadius:{type:'string',default:'12px'} },
    edit:function(props){
      var blockProps = useBlockProps({ className:'card ' + (props.attributes.boxShadowClass || 'shadow-sm'), style:{ background:props.attributes.backgroundColor||undefined, borderColor:props.attributes.borderColor||undefined, borderRadius:props.attributes.borderRadius||'12px' }});
      return el(wp.element.Fragment,{},
        el(InspectorControls,{},el(PanelBody,{title:'Card settings',initialOpen:true},[
          el(SelectControl,{key:'boxShadowClass',label:'Box shadow',value:props.attributes.boxShadowClass||'shadow-sm',options:wpbbShadowOptions(),onChange:function(v){props.setAttributes({boxShadowClass:v});}}),
          colorInput('Background color', props.attributes.backgroundColor || '', function(v){ props.setAttributes({backgroundColor:v}); }, 'card-bg'),
          colorInput('Border color', props.attributes.borderColor || '', function(v){ props.setAttributes({borderColor:v}); }, 'card-border'),
          el(TextControl,{key:'borderRadius',label:'Border radius',value:props.attributes.borderRadius||'12px',onChange:function(v){props.setAttributes({borderRadius:v});}})
        ])),
        el('div', blockProps, [label('CARD'), el('div',{className:'card-body'}, el(InnerBlocks))])
      );
    },
    save:function(){ return el(InnerBlocks.Content); }
  });
  registerBlockType('wpbb/cards', { title:'Cards', icon:'grid-view', category:'wpbb', edit:containerEdit('CARDS',['wpbb/cta-card']), save:function(){ return el(InnerBlocks.Content); } });
  registerBlockType('wpbb/accordion', {
    title:'Accordion', icon:'menu', category:'wpbb',
    attributes:{ flush:{type:'boolean',default:false}, alwaysOpen:{type:'boolean',default:false}, boxShadowClass:{type:'string',default:''}, backgroundColor:{type:'string',default:''}, borderColor:{type:'string',default:''} },
    edit:function(props){
      return el(wp.element.Fragment,{},
        el(InspectorControls,{},el(PanelBody,{title:'Accordion settings',initialOpen:true},[
          el(ToggleControl,{key:'flush',label:'Flush style',checked:!!props.attributes.flush,onChange:function(v){props.setAttributes({flush:v});}}),
          el(ToggleControl,{key:'alwaysOpen',label:'Always open items',checked:!!props.attributes.alwaysOpen,onChange:function(v){props.setAttributes({alwaysOpen:v});}}),
          el(SelectControl,{key:'boxShadowClass',label:'Box shadow',value:props.attributes.boxShadowClass||'',options:wpbbShadowOptions(),onChange:function(v){props.setAttributes({boxShadowClass:v});}}),
          colorInput('Background color', props.attributes.backgroundColor || '', function(v){ props.setAttributes({backgroundColor:v}); }, 'accordion-bg'),
          colorInput('Border color', props.attributes.borderColor || '', function(v){ props.setAttributes({borderColor:v}); }, 'accordion-border')
        ])),
        el('div', useBlockProps({className:'wpbb-accordion-editor-preview ' + (props.attributes.flush ? 'accordion-flush ' : '') + (props.attributes.boxShadowClass || ''), style:{background:props.attributes.backgroundColor||undefined,borderColor:props.attributes.borderColor||undefined}}), [
          label('ACCORDION'),
          el('div',{className:'accordion ' + (props.attributes.flush ? 'accordion-flush' : '')}, el(InnerBlocks,{allowedBlocks:['wpbb/accordion-item']}))
        ])
      );
    },
    save:function(){ return el(InnerBlocks.Content); }
  });
  registerBlockType('wpbb/accordion-item', {
    title:'Accordion Item',
    icon:'excerpt-view',
    category:'wpbb',
    parent:['wpbb/accordion'],
    attributes:{ title:{type:'string',default:'Accordion Item'} },
    edit:function(props){
      return el(wp.element.Fragment,{},
        el(InspectorControls,{},el(PanelBody,{title:'Accordion Item settings',initialOpen:true},[
          el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}})
        ])),
        el('div',useBlockProps({className:'wpbb-accordion-item'}),label('ACCORDION ITEM'), el('div',{className:'wpbb-tab-title'}, props.attributes.title || 'Accordion Item'), el(InnerBlocks))
      );
    },
    save:function(){ return el(InnerBlocks.Content); }
  });
  registerBlockType('wpbb/tabs', { title:'Tabs', icon:'index-card', category:'wpbb', edit:containerEdit('TABS',['wpbb/tab-item']), save:function(){ return el(InnerBlocks.Content); } });
  registerBlockType('wpbb/tab-item', {
    title:'Tab Item',
    icon:'editor-table',
    category:'wpbb',
    parent:['wpbb/tabs'],
    attributes:{ title:{type:'string',default:'Tab Item'} },
    edit:function(props){
      return el(wp.element.Fragment,{},
        el(InspectorControls,{},el(PanelBody,{title:'Tab Item settings',initialOpen:true},[
          el(TextControl,{key:'title',label:'Tab title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}})
        ])),
        el('div',useBlockProps({className:'wpbb-tab-item'}),label('TAB ITEM'), el('div',{className:'wpbb-tab-title'}, props.attributes.title || 'Tab Item'), el(InnerBlocks))
      );
    },
    save:function(){ return el(InnerBlocks.Content); }
  });

  
  registerBlockType('wpbb/table', {
    title:'Bootstrap Table',
    icon:'table-col-after',
    category:'wpbb',
    attributes:{
      csvText:{type:'string',default:'ID,Name,Role\n1,John,Designer\n2,Anna,Developer'},
      csvFileName:{type:'string',default:''},
      delimiter:{type:'string',default:','},
      useFirstRowHeader:{type:'boolean',default:true},
      tableClass:{type:'string',default:'table table-striped table-hover'},
      responsive:{type:'boolean',default:true},
      small:{type:'boolean',default:false},
      bordered:{type:'boolean',default:false},
      datatable:{type:'boolean',default:true},
      datatableSearch:{type:'boolean',default:true},
      datatablePaging:{type:'boolean',default:true},
      datatableOrdering:{type:'boolean',default:true},
      datatableInfo:{type:'boolean',default:true},
      datatableLengthChange:{type:'boolean',default:true}
    },
    edit:function(props){
      var rows=(props.attributes.csvText||'').split(/\r?\n/).filter(Boolean).map(function(line){ return line.split(props.attributes.delimiter || ','); });
      var headers=props.attributes.useFirstRowHeader && rows.length ? rows[0] : [];
      var body=props.attributes.useFirstRowHeader ? rows.slice(1) : rows;
      function handleFile(event){
        var file = event.target.files && event.target.files[0];
        if (!file) return;
        var reader = new FileReader();
        reader.onload = function(e){ props.setAttributes({ csvText: String(e.target.result || ''), csvFileName: file.name || '' }); };
        reader.readAsText(file);
      }
      return el(wp.element.Fragment,{},
        el(InspectorControls,{},el(PanelBody,{title:'Table settings',initialOpen:true},[
          el(TextareaControl,{key:'csvText',label:'CSV content',value:props.attributes.csvText,onChange:function(v){props.setAttributes({csvText:v});}}),
          el('div',{key:'upload'},[
            el('label',{style:{display:'block',fontWeight:'600',marginBottom:'6px'}},'Upload CSV'),
            el('input',{type:'file',accept:'.csv,text/csv',onChange:handleFile}),
            props.attributes.csvFileName ? el('div',{style:{marginTop:'6px',fontSize:'12px'}},props.attributes.csvFileName) : null
          ]),
          el(TextControl,{key:'delimiter',label:'Delimiter',value:props.attributes.delimiter,onChange:function(v){props.setAttributes({delimiter:v||','});}}),
          el(ToggleControl,{key:'useFirstRowHeader',label:'First row is header',checked:!!props.attributes.useFirstRowHeader,onChange:function(v){props.setAttributes({useFirstRowHeader:v});}}),
          el(TextControl,{key:'tableClass',label:'Bootstrap table classes',value:props.attributes.tableClass,onChange:function(v){props.setAttributes({tableClass:v});}}),
          el(ToggleControl,{key:'responsive',label:'Responsive wrapper',checked:!!props.attributes.responsive,onChange:function(v){props.setAttributes({responsive:v});}}),
          el(ToggleControl,{key:'small',label:'Small table',checked:!!props.attributes.small,onChange:function(v){props.setAttributes({small:v});}}),
          el(ToggleControl,{key:'bordered',label:'Bordered table',checked:!!props.attributes.bordered,onChange:function(v){props.setAttributes({bordered:v});}}),
          el(ToggleControl,{key:'datatable',label:'Enable DataTables',checked:!!props.attributes.datatable,onChange:function(v){props.setAttributes({datatable:v});}}),
          el(ToggleControl,{key:'datatableSearch',label:'Search',checked:!!props.attributes.datatableSearch,onChange:function(v){props.setAttributes({datatableSearch:v});}}),
          el(ToggleControl,{key:'datatablePaging',label:'Paging',checked:!!props.attributes.datatablePaging,onChange:function(v){props.setAttributes({datatablePaging:v});}}),
          el(ToggleControl,{key:'datatableOrdering',label:'Ordering',checked:!!props.attributes.datatableOrdering,onChange:function(v){props.setAttributes({datatableOrdering:v});}}),
          el(ToggleControl,{key:'datatableInfo',label:'Info',checked:!!props.attributes.datatableInfo,onChange:function(v){props.setAttributes({datatableInfo:v});}}),
          el(ToggleControl,{key:'datatableLengthChange',label:'Length change',checked:!!props.attributes.datatableLengthChange,onChange:function(v){props.setAttributes({datatableLengthChange:v});}})
        ])),
        el('div',useBlockProps({className:'wpbb-table-editor'}),
          label('TABLE'),
          props.attributes.csvFileName ? el('div',{className:'wpbb-form-preview-meta'},'CSV: ' + props.attributes.csvFileName) : null,
          el('div',{className:props.attributes.responsive?'table-responsive':''},
            el('table',{className:props.attributes.tableClass + (props.attributes.small ? ' table-sm' : '') + (props.attributes.bordered ? ' table-bordered' : '')},
              headers.length ? el('thead',{},el('tr',{},headers.map(function(h,i){ return el('th',{key:i},h); }))) : null,
              el('tbody',{},body.map(function(row,ri){ return el('tr',{key:ri},row.map(function(cell,ci){ return el('td',{key:ci},cell); })); }))
            )
          )
        )
      );
    },
    save:function(){ return null; }
  });

  registerBlockType('wpbb/dynamic-form', {
    title:'Dynamic Form',
    icon:'feedback',
    category:'wpbb',
    attributes:{
      formTitle:{type:'string',default:'Contact form'},
      recipient:{type:'string',default:''},
      emailSubject:{type:'string',default:'New form submission'},
      successMessage:{type:'string',default:'Thank you for your submission!'},
      submitText:{type:'string',default:'Submit'},
      showTitle:{type:'boolean',default:true},
      formClass:{type:'string',default:'wpbb-form'},
      buttonClass:{type:'string',default:'btn btn-primary'},
      stylePreset:{type:'string',default:'default'},
      labelPosition:{type:'string',default:'top'},
      gap:{type:'number',default:2},
      fieldsJson:{type:'string',default:''}
    },
    edit:function(props){
      var fields = [];
      try { fields = JSON.parse(props.attributes.fieldsJson || '[]'); } catch(e) { fields = []; }
      if (!fields.length) fields = [
        { type:'text', name:'name', label:'Name', required:true, width:6, placeholder:'', options:'' },
        { type:'email', name:'email', label:'Email', required:true, width:6, placeholder:'', options:'' },
        { type:'phone', name:'phone', label:'Phone', required:false, width:6, placeholder:'', options:'' },
        { type:'select', name:'language', label:'Language', required:false, width:6, placeholder:'Select language', options:'English\nLatvian\nRussian' },
        { type:'textarea', name:'message', label:'Message', required:true, width:12, placeholder:'', options:'' }
      ];
      function saveFields(next){ props.setAttributes({ fieldsJson: JSON.stringify(next) }); }
      function updateField(index, key, value){
        var next = fields.slice();
        next[index] = Object.assign({}, next[index], (function(){ var o={}; o[key]=value; return o; })());
        saveFields(next);
      }
      function addField(){
        var next = fields.slice();
        next.push({ type:'text', name:'field_' + (fields.length + 1), label:'New field', required:false, width:6, placeholder:'', options:'' });
        saveFields(next);
      }
      function removeField(index){
        var next = fields.slice();
        next.splice(index, 1);
        saveFields(next);
      }
      return el(wp.element.Fragment, {},
        el(InspectorControls, {}, el(PanelBody, { title:'Form settings', initialOpen:true }, [
          el(TextControl, { key:'formTitle', label:'Form title', value:props.attributes.formTitle, onChange:function(v){ props.setAttributes({formTitle:v}); } }),
          el(TextControl, { key:'recipient', label:'Recipient email', value:props.attributes.recipient, onChange:function(v){ props.setAttributes({recipient:v}); } }),
          el(TextControl, { key:'emailSubject', label:'Email subject', value:props.attributes.emailSubject, onChange:function(v){ props.setAttributes({emailSubject:v}); } }),
          el(TextControl, { key:'successMessage', label:'Success message', value:props.attributes.successMessage, onChange:function(v){ props.setAttributes({successMessage:v}); } }),
          el(TextControl, { key:'submitText', label:'Submit text', value:props.attributes.submitText, onChange:function(v){ props.setAttributes({submitText:v}); } }),
          el(TextControl, { key:'formClass', label:'Form class', value:props.attributes.formClass, onChange:function(v){ props.setAttributes({formClass:v}); } }),
          el(TextControl, { key:'buttonClass', label:'Button class', value:props.attributes.buttonClass, onChange:function(v){ props.setAttributes({buttonClass:v}); } }),
          el(SelectControl, { key:'stylePreset', label:'Style preset', value:props.attributes.stylePreset, options:[{label:'Default',value:'default'},{label:'Soft',value:'soft'},{label:'Outline',value:'outline'}], onChange:function(v){ props.setAttributes({stylePreset:v}); } }),
          el(SelectControl, { key:'labelPosition', label:'Label position', value:props.attributes.labelPosition, options:[{label:'Top',value:'top'},{label:'Left',value:'left'},{label:'Hidden',value:'hidden'}], onChange:function(v){ props.setAttributes({labelPosition:v}); } }),
          el(RangeControl, { key:'gap', label:'Gap', value:props.attributes.gap || 2, min:0, max:5, onChange:function(v){ props.setAttributes({gap:v||0}); } }),
          el(ToggleControl, { key:'showTitle', label:'Show title', checked: !!props.attributes.showTitle, onChange:function(v){ props.setAttributes({showTitle:v}); } })
        ].concat(fields.map(function(field, index){
          return el('div', { key:'field_' + index, className:'wpbb-mini-card' }, [
            el(TextControl, { key:'label', label:'Label', value:field.label || '', onChange:function(v){ updateField(index,'label',v); } }),
            el(TextControl, { key:'name', label:'Name', value:field.name || '', onChange:function(v){ updateField(index,'name',v); } }),
            el(SelectControl, { key:'type', label:'Type', value:field.type || 'text', options:[{label:'Description',value:'text'},{label:'Email',value:'email'},{label:'Phone',value:'phone'},{label:'Select',value:'select'},{label:'Textarea',value:'textarea'}], onChange:function(v){ updateField(index,'type',v); } }),
            el(TextControl, { key:'placeholder', label:'Placeholder', value:field.placeholder || '', onChange:function(v){ updateField(index,'placeholder',v); } }),
            field.type === 'select' ? el(TextareaControl, { key:'options', label:'Options (one per line)', value:field.options || '', onChange:function(v){ updateField(index,'options',v); } }) : null,
            el(RangeControl, { key:'width', label:'Width /12', value:field.width || 6, min:1, max:12, onChange:function(v){ updateField(index,'width',v || 6); } }),
            el(ToggleControl, { key:'required', label:'Required', checked:!!field.required, onChange:function(v){ updateField(index,'required',v); } }),
            el(Button, { key:'remove', variant:'secondary', onClick:function(){ removeField(index); } }, 'Remove field')
          ]);
        })).concat([el(Button, { key:'add', variant:'secondary', onClick:addField }, 'Add field')]))),
        el('div', useBlockProps({ className:'wpbb-dynamic-form-editor' }),
          label('DYNAMIC FORM'),
          props.attributes.showTitle !== false ? el('strong', {}, props.attributes.formTitle) : null,
          el('div', { className:'wpbb-form-preview-grid' },
            fields.map(function(field, index){
              return el('div', { key:index, className:'wpbb-form-preview-field', style:{ gridColumn:'span ' + (field.width || 6) } }, [
                el('div', { key:'l', className:'wpbb-form-preview-field__label' }, field.label || field.name),
                el('div', { key:'i', className:'wpbb-form-preview-field__input' + (field.type === 'textarea' ? ' is-textarea' : '') }),
                el('div', { key:'m', className:'wpbb-form-preview-meta' }, (field.type || 'text') + ' | col ' + (field.width || 6) + (field.required ? ' | required' : ''))
              ]);
            })
          )
        )
      );
    },
    save:function(){ return null; }
  });

  registerBlockType('wpbb/cta-card', {
    title:'CTA Card', icon:'megaphone', category:'wpbb',
    attributes:{
      title:{type:'string',default:'CTA Card'},
      text:{type:'string',default:'Call to action text'},
      buttonText:{type:'string',default:'Learn more'},
      buttonUrl:{type:'string',default:'#'},
      bgColor:{type:'string',default:''},
      textColor:{type:'string',default:''},
      currency:{type:'string',default:'€'},
      schemaEnable:{type:'boolean',default:false},
      schemaType:{type:'string',default:'CreativeWork'},
      schemaPrice:{type:'string',default:''}
    },
    edit:function(props){
      return el(wp.element.Fragment,{},
        el(InspectorControls,{},el(PanelBody,{title:'CTA Card settings',initialOpen:true},[
          el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}),
          el(TextareaControl,{key:'text',label:'Description',value:props.attributes.text,onChange:function(v){props.setAttributes({text:v});}}),
          el(TextControl,{key:'buttonText',label:'Button text',value:props.attributes.buttonText,onChange:function(v){props.setAttributes({buttonText:v});}}),
          el(TextControl,{key:'buttonUrl',label:'Button URL',value:props.attributes.buttonUrl,onChange:function(v){props.setAttributes({buttonUrl:v});}}),
          colorInput('Background color',props.attributes.bgColor,function(v){props.setAttributes({bgColor:v});},'cta-bg'),
          colorInput('Text color',props.attributes.textColor,function(v){props.setAttributes({textColor:v});},'cta-text'),
          el(ToggleControl,{key:'schemaEnable',label:'Enable schema',checked:!!props.attributes.schemaEnable,onChange:function(v){props.setAttributes({schemaEnable:v});}}),
          el(SelectControl,{key:'schemaType',label:'Schema type',value:props.attributes.schemaType||'CreativeWork',options:[
            {label:'CreativeWork',value:'CreativeWork'},
            {label:'Product',value:'Product'},
            {label:'Service',value:'Service'},
            {label:'Organization',value:'Organization'}
          ],onChange:function(v){props.setAttributes({schemaType:v});}}),
          props.attributes.schemaEnable && props.attributes.schemaType === 'Product'
            ? el(TextControl,{key:'currency',label:'Currency',value:props.attributes.currency||'€',onChange:function(v){props.setAttributes({currency:v});}})
            : null,
          props.attributes.schemaEnable && props.attributes.schemaType === 'Product'
            ? el(TextControl,{key:'schemaPrice',label:'Schema price',value:props.attributes.schemaPrice||'',onChange:function(v){props.setAttributes({schemaPrice:v});}})
            : null
        ])),
        el('div',useBlockProps({className:'wpbb-cta-card card h-100',style:{background:props.attributes.bgColor||undefined,color:props.attributes.textColor||undefined}}),
          label('CTA CARD'),
          el('div',{className:'card-body'},[
            el('strong',{key:'t'},props.attributes.title),
            el('p',{key:'p'},props.attributes.text),
            props.attributes.schemaEnable && props.attributes.schemaType === 'Product' && props.attributes.schemaPrice
              ? el('div',{key:'price',className:'wpbb-cta-card-price'}, (props.attributes.currency||'€') + props.attributes.schemaPrice)
              : null,
            el('span',{key:'b',className:'btn btn-primary'},props.attributes.buttonText)
          ])
        )
      );
    },
    save:function(){ return null; }
  });

  registerBlockType('wpbb/cta-section', {
    title:'CTA Section', icon:'cover-image', category:'wpbb',
    attributes:{ title:{type:'string',default:'CTA Section'}, text:{type:'string',default:'Call to action text'}, buttonText:{type:'string',default:'Get started'}, buttonUrl:{type:'string',default:'#'}, bgColor:{type:'string',default:''}, textColor:{type:'string',default:''}, backgroundImage:{type:'string',default:''}, parallax:{type:'boolean',default:false} },
    edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'CTA Section settings',initialOpen:true},[
      el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}),
      el(TextareaControl,{key:'text',label:'Description',value:props.attributes.text,onChange:function(v){props.setAttributes({text:v});}}),
      el(TextControl,{key:'buttonText',label:'Button text',value:props.attributes.buttonText,onChange:function(v){props.setAttributes({buttonText:v});}}),
      el(TextControl,{key:'buttonUrl',label:'Button URL',value:props.attributes.buttonUrl,onChange:function(v){props.setAttributes({buttonUrl:v});}}),
      el(TextControl,{key:'backgroundImage',label:'Background image URL',value:props.attributes.backgroundImage,onChange:function(v){props.setAttributes({backgroundImage:v});}}),
      el(ToggleControl,{key:'parallax',label:'Parallax background',checked:!!props.attributes.parallax,onChange:function(v){props.setAttributes({parallax:v});}}),
      colorInput('Background color',props.attributes.bgColor,function(v){props.setAttributes({bgColor:v});},'ctas-bg'),
      colorInput('Text color',props.attributes.textColor,function(v){props.setAttributes({textColor:v});},'ctas-text')
    ])), el('section',useBlockProps({className:'wpbb-cta-section text-center py-5' + (props.attributes.parallax ? ' parallax-bg' : ''),style:{background:props.attributes.bgColor||undefined,color:props.attributes.textColor||undefined,backgroundImage:props.attributes.backgroundImage ? 'url(' + props.attributes.backgroundImage + ')' : undefined,backgroundSize:'cover',backgroundPosition:'center'}}),label('CTA SECTION'),el('h3',{},props.attributes.title),el('p',{},props.attributes.text), props.attributes.backgroundImage ? el('img',{src:props.attributes.backgroundImage,alt:'',style:{maxWidth:'160px',height:'auto',display:'block',margin:'8px auto'}}) : null, el('span',{className:'btn btn-primary'},props.attributes.buttonText))); },
    save:function(){ return null; }
  });

  registerBlockType('wpbb/google-map', {
    title:'Google Map', icon:'location-alt', category:'wpbb',
    attributes:{ address:{type:'string',default:''}, zoom:{type:'number',default:14}, height:{type:'string',default:'380px'}, overlayColor:{type:'string',default:''}, overlayOpacity:{type:'number',default:0.2}, embedUrl:{type:'string',default:''}, mapFilter:{type:'string',default:''} },
    edit:function(props){
      var address = props.attributes.address || '';
      var zoom = Number(props.attributes.zoom || 14);
      var overlayColor = props.attributes.overlayColor || '';
      var overlayOpacity = Number(props.attributes.overlayOpacity || 0);
      var src = address ? ('https://maps.google.com/maps?q=' + encodeURIComponent(address) + '&t=&z=' + zoom + '&ie=UTF8&iwloc=&output=embed') : (props.attributes.embedUrl || '');
      return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Google Map settings',initialOpen:true},[
        el(TextControl,{key:'address',label:'Address',value:address,onChange:function(v){props.setAttributes({address:v});}}),
        el(RangeControl,{key:'zoom',label:'Zoom',value:zoom,min:1,max:21,onChange:function(v){props.setAttributes({zoom:Number(v||14)});}}),
        el(TextControl,{key:'height',label:'Height',value:props.attributes.height,onChange:function(v){props.setAttributes({height:v});}}),
        colorInput('Overlay color', overlayColor, function(v){ props.setAttributes({overlayColor:v}); }, 'google-map-overlay-color'),
        el(RangeControl,{key:'overlayOpacity',label:'Overlay opacity',value:overlayOpacity,min:0,max:1,step:0.05,onChange:function(v){props.setAttributes({overlayOpacity:Number(v||0)});}}),
        el(TextControl,{key:'embedUrl',label:'Legacy embed URL (optional)',value:props.attributes.embedUrl || '',onChange:function(v){props.setAttributes({embedUrl:v});}})
      ])), el('div',useBlockProps({className:'wpbb-google-map'}),[
        label('GOOGLE MAP'),
        src ? el('div',{className:'wpbb-google-map__frame',style:{position:'relative',width:'100%',height:props.attributes.height||'380px',overflow:'hidden'}},[
          el('iframe',{key:'map',src:src,style:{width:'100%',height:'100%',border:0,display:'block'},loading:'lazy',allowFullScreen:true}),
          overlayColor && overlayOpacity > 0 ? el('span',{key:'overlay',className:'wpbb-google-map__overlay',style:{position:'absolute',inset:'0',pointerEvents:'none',background:overlayColor,opacity:overlayOpacity}}) : null
        ]) : 'Add address'
      ]));
    },
    save:function(){ return null; }
  });



  registerBlockType('wpbb/file', {
    title:'File', icon:'media-document', category:'wpbb',
    attributes:{ title:{type:'string',default:'File'}, fileUrl:{type:'string',default:''}, fileName:{type:'string',default:''}, buttonText:{type:'string',default:'Download file'}, targetBlank:{type:'boolean',default:true} },
    edit:function(props){
      return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'File settings',initialOpen:true},[
        el(TextControl,{label:'Title',value:props.attributes.title||'',onChange:function(v){props.setAttributes({title:v});}}),
        el(TextControl,{label:'File URL',value:props.attributes.fileUrl||'',onChange:function(v){props.setAttributes({fileUrl:v});}}),
        el(MediaUploadCheck,{key:'file-check'},el(MediaUpload,{allowedTypes:['application','text','image','video','audio'],onSelect:function(media){props.setAttributes({fileUrl:(media&&media.url)?media.url:'', fileName:(media&&media.filename)?media.filename:(props.attributes.fileName||'')});},render:function(obj){return el(Button,{variant:'secondary',onClick:obj.open},props.attributes.fileUrl ? 'Replace file' : 'Select file');}})),
        el(TextControl,{label:'File name',value:props.attributes.fileName||'',onChange:function(v){props.setAttributes({fileName:v});}}),
        el(TextControl,{label:'Button text',value:props.attributes.buttonText||'',onChange:function(v){props.setAttributes({buttonText:v});}}),
        el(ToggleControl,{label:'Open in new tab',checked:props.attributes.targetBlank!==false,onChange:function(v){props.setAttributes({targetBlank:v});}})
      ])), el('div',useBlockProps({className:'wpbb-file-block'}),[
        label('FILE'),
        el('div',{className:'wpbb-file-block__name'},props.attributes.fileName || props.attributes.title || 'File'),
        props.attributes.fileUrl ? el('a',{className:'btn btn-primary',href:props.attributes.fileUrl,target:props.attributes.targetBlank!==false ? '_blank' : undefined,rel:props.attributes.targetBlank!==false ? 'noopener' : undefined},props.attributes.buttonText || 'Download file') : 'Add file'
      ]));
    },
    save:function(){ return null; }
  });

  registerBlockType('wpbb/inline-svg', {
    title:'Inline SVG', icon:'format-image', category:'wpbb',
    attributes:{ title:{type:'string',default:'Inline SVG'}, svgCode:{type:'string',default:''} },
    edit:function(props){
      return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Inline SVG settings',initialOpen:true},[
        el(TextControl,{label:'Title',value:props.attributes.title||'',onChange:function(v){props.setAttributes({title:v});}}),
        el(TextareaControl,{label:'SVG source',help:'Paste full <svg>...</svg> code here.',value:props.attributes.svgCode||'',onChange:function(v){props.setAttributes({svgCode:v});}})
      ])), el('div',useBlockProps({className:'wpbb-inline-svg'}),[
        label('INLINE SVG'),
        props.attributes.title ? el('div',{style:{marginBottom:'8px',fontWeight:'600'}},props.attributes.title) : null,
        props.attributes.svgCode ? el('div',{dangerouslySetInnerHTML:{__html:props.attributes.svgCode}}) : 'Paste SVG source'
      ]));
    },
    save:function(){ return null; }
  });

  registerBlockType('wpbb/menu-option', {
    title:'Menu Option', icon:'menu', category:'wpbb',
    attributes:{ title:{type:'string',default:'Menu Item'}, text:{type:'string',default:''}, badge:{type:'string',default:''}, bgColor:{type:'string',default:''}, textColor:{type:'string',default:''} },
    edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Menu Option settings',initialOpen:true},[
      el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}),
      el(TextareaControl,{key:'text',label:'Description',value:props.attributes.text,onChange:function(v){props.setAttributes({text:v});}}),
      el(TextControl,{key:'badge',label:'Badge',value:props.attributes.badge,onChange:function(v){props.setAttributes({badge:v});}}), el(TextControl,{key:'menuSlug',label:'Menu slug / name',value:props.attributes.menuSlug,onChange:function(v){props.setAttributes({menuSlug:v});}}), el(ToggleControl,{key:'schemaEnable',label:'Enable schema.org MenuItem',checked:!!props.attributes.schemaEnable,onChange:function(v){props.setAttributes({schemaEnable:v});}}), el(TextControl,{key:'price',label:'Optional price',value:props.attributes.price,onChange:function(v){props.setAttributes({price:v});}}),
      colorInput('Background color',props.attributes.bgColor,function(v){props.setAttributes({bgColor:v});},'menu-bg'),
      colorInput('Text color',props.attributes.textColor,function(v){props.setAttributes({textColor:v});},'menu-text')
    ])), el('div',useBlockProps({className:'wpbb-menu-option',style:{background:props.attributes.bgColor||undefined,color:props.attributes.textColor||undefined}}),label('MENU OPTION'),props.attributes.title)); },
    save:function(){ return null; }
  });

  registerBlockType('wpbb/sitemap', {
    title:'Sitemap', icon:'networking', category:'wpbb',
    attributes:{ title:{type:'string',default:'Sitemap'}, showPages:{type:'boolean',default:true}, showPosts:{type:'boolean',default:false} },
    edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Sitemap settings',initialOpen:true},[
      el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}),
      el(ToggleControl,{key:'showPages',label:'Show pages',checked:!!props.attributes.showPages,onChange:function(v){props.setAttributes({showPages:v});}}),
      el(ToggleControl,{key:'showPosts',label:'Show posts',checked:!!props.attributes.showPosts,onChange:function(v){props.setAttributes({showPosts:v});}})
    ])), el('div',useBlockProps({className:'wpbb-sitemap'}),label('SITEMAP'),props.attributes.title + ' | pages:' + (props.attributes.showPages ? 'yes' : 'no') + ' | posts:' + (props.attributes.showPosts ? 'yes' : 'no'))); },
    save:function(){ return null; }
  });

  registerBlockType('wpbb/soc-follow-block', {
    title:'Social Follow', icon:'share', category:'wpbb',
    attributes:{ title:{type:'string',default:'Follow Us'}, facebook:{type:'string',default:''}, instagram:{type:'string',default:''}, linkedin:{type:'string',default:''}, x:{type:'string',default:''}, youtube:{type:'string',default:''}, whatsapp:{type:'string',default:''}, socialStyle:{type:'string',default:'icons'}, iconBgColor:{type:'string',default:''}, iconTextColor:{type:'string',default:''} },
    edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Social Follow settings',initialOpen:true},[
      el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}),
      el(TextControl,{key:'facebook',label:'Facebook URL',value:props.attributes.facebook,onChange:function(v){props.setAttributes({facebook:v});}}),
      el(TextControl,{key:'instagram',label:'Instagram URL',value:props.attributes.instagram,onChange:function(v){props.setAttributes({instagram:v});}}),
      el(TextControl,{key:'linkedin',label:'LinkedIn URL',value:props.attributes.linkedin,onChange:function(v){props.setAttributes({linkedin:v});}}),
      el(TextControl,{key:'x',label:'X URL',value:props.attributes.x,onChange:function(v){props.setAttributes({x:v});}}), el(TextControl,{key:'youtube',label:'YouTube URL',value:props.attributes.youtube||'',onChange:function(v){props.setAttributes({youtube:v});}}), el(TextControl,{key:'whatsapp',label:'WhatsApp URL',value:props.attributes.whatsapp||'',onChange:function(v){props.setAttributes({whatsapp:v});}}), colorInput('Icon background',props.attributes.iconBgColor,function(v){props.setAttributes({iconBgColor:v});},'followbg'), colorInput('Icon color',props.attributes.iconTextColor,function(v){props.setAttributes({iconTextColor:v});},'followtxt'), el(SelectControl,{key:'socialStyle',label:'Style',value:props.attributes.socialStyle||'icons',options:[{label:'Icons',value:'icons'},{label:'Normal',value:'normal'}],onChange:function(v){props.setAttributes({socialStyle:v});}})
    ])), el('div',useBlockProps({className:'wpbb-soc-follow wpbb-social-preview-card'}),label('SOC FOLLOW'),el('div',{className:'wpbb-social-preview-icons'},[wpbbPreviewSocialIcon('facebook'),wpbbPreviewSocialIcon('instagram'),wpbbPreviewSocialIcon('linkedin'),wpbbPreviewSocialIcon('x'),wpbbPreviewSocialIcon('youtube'),wpbbPreviewSocialIcon('whatsapp'),wpbbPreviewSocialIcon('email')]))); },
    save:function(){ return null; }
  });

  registerBlockType('wpbb/soc-share', {
    title:'Social Share', icon:'share-alt2', category:'wpbb',
    attributes:{ title:{type:'string',default:'Share'}, iconStyle:{type:'string',default:'icons'}, shareFacebook:{type:'boolean',default:true}, shareX:{type:'boolean',default:true}, shareLinkedIn:{type:'boolean',default:true}, shareWhatsApp:{type:'boolean',default:true}, shareEmail:{type:'boolean',default:true}, iconBgColor:{type:'string',default:''}, iconTextColor:{type:'string',default:''} },
    edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Social Share settings',initialOpen:true},[
      el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}),
      el(SelectControl,{key:'iconStyle',label:'Icon style',value:props.attributes.iconStyle,options:[{label:'Icons',value:'icons'},{label:'Normal',value:'buttons'}],onChange:function(v){props.setAttributes({iconStyle:v});}}), colorInput('Icon background',props.attributes.iconBgColor,function(v){props.setAttributes({iconBgColor:v});},'sharebg'), colorInput('Icon color',props.attributes.iconTextColor,function(v){props.setAttributes({iconTextColor:v});},'sharetxt'), el(ToggleControl,{key:'shareFacebook',label:'Facebook',checked:props.attributes.shareFacebook!==false,onChange:function(v){props.setAttributes({shareFacebook:v});}}), el(ToggleControl,{key:'shareX',label:'X',checked:props.attributes.shareX!==false,onChange:function(v){props.setAttributes({shareX:v});}}), el(ToggleControl,{key:'shareLinkedIn',label:'LinkedIn',checked:props.attributes.shareLinkedIn!==false,onChange:function(v){props.setAttributes({shareLinkedIn:v});}}), el(ToggleControl,{key:'shareWhatsApp',label:'WhatsApp',checked:props.attributes.shareWhatsApp!==false,onChange:function(v){props.setAttributes({shareWhatsApp:v});}}), el(ToggleControl,{key:'shareEmail',label:'Email',checked:props.attributes.shareEmail!==false,onChange:function(v){props.setAttributes({shareEmail:v});}})
    ])), el('div',useBlockProps({className:'wpbb-soc-share'}),label('SOC SHARE'),
      props.attributes.title + ' ',
      props.attributes.iconStyle === 'icons' ? el('span',{className:'wpbb-social-preview-icons'},[wpbbPreviewSocialIcon('facebook'),wpbbPreviewSocialIcon('x'),wpbbPreviewSocialIcon('linkedin'),wpbbPreviewSocialIcon('whatsapp'),wpbbPreviewSocialIcon('email')]) : 'Facebook X LinkedIn WhatsApp Email'
    )); },
    save:function(){ return null; }
  });

  registerBlockType('wpbb/video', {
    title:'Video', icon:'format-video', category:'wpbb',
    attributes:{ videoUrl:{type:'string',default:''}, ratioClass:{type:'string',default:'ratio ratio-16x9'}, poster:{type:'string',default:''} },
    edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Video settings',initialOpen:true},[
      el(TextControl,{key:'videoUrl',label:'Video URL',value:props.attributes.videoUrl,onChange:function(v){props.setAttributes({videoUrl:v});}}),
      el(SelectControl,{key:'ratioClass',label:'Ratio',value:props.attributes.ratioClass,options:[{label:'16:9',value:'ratio ratio-16x9'},{label:'4:3',value:'ratio ratio-4x3'},{label:'1:1',value:'ratio ratio-1x1'}],onChange:function(v){props.setAttributes({ratioClass:v});}}),
      el(TextControl,{key:'poster',label:'Poster URL',value:props.attributes.poster,onChange:function(v){props.setAttributes({poster:v});}})
    ])), el('div',useBlockProps({className:'wpbb-video'}),label('VIDEO'),props.attributes.videoUrl || 'Add video URL')); },
    save:function(){ return null; }
  });


  registerBlockType('wpbb/swiper', {
    title:'Swiper', icon:'images-alt2', category:'wpbb',
    attributes:{ slidesJson:{type:'string',default:''}, slidesPerView:{type:'number',default:1}, spaceBetween:{type:'number',default:20}, speed:{type:'number',default:600}, loop:{type:'boolean',default:true}, autoplay:{type:'boolean',default:false}, demoStyle:{type:'string',default:'cards'}, showPagination:{type:'boolean',default:true}, showNavigation:{type:'boolean',default:true} },
    edit:function(props){
      var slides=[]; try{slides=JSON.parse(props.attributes.slidesJson||'[]')}catch(e){slides=[]}
      if(!slides.length) slides=[{type:'text',title:'Slide 1',text:'Demo text'},{type:'card',title:'Slide 2',text:'Card content'},{type:'video',title:'Slide 3',video:''}];
      function saveSlides(next){ props.setAttributes({slidesJson:JSON.stringify(next)}); }
      function updateSlide(i,k,v){ var next=slides.slice(); next[i]=Object.assign({}, next[i], (function(){var o={}; o[k]=v; return o;})()); saveSlides(next); }
      function addSlide(){ var next=slides.slice(); next.push({type:'text',title:'New slide',text:'',video:''}); saveSlides(next); }
      function removeSlide(i){ var next=slides.slice(); next.splice(i,1); saveSlides(next); }
      return el(wp.element.Fragment,{},
        el(InspectorControls,{},el(PanelBody,{title:'Swiper settings',initialOpen:true},[
          el(RangeControl,{key:'spv',label:'Slides per view',value:props.attributes.slidesPerView||1,min:1,max:4,onChange:function(v){props.setAttributes({slidesPerView:v||1});}}),
          el(RangeControl,{key:'space',label:'Space between',value:props.attributes.spaceBetween||20,min:0,max:60,onChange:function(v){props.setAttributes({spaceBetween:v||0});}}),
          el(RangeControl,{key:'speed',label:'Speed',value:props.attributes.speed||600,min:100,max:3000,step:100,onChange:function(v){props.setAttributes({speed:v||600});}}),
          el(ToggleControl,{key:'loop',label:'Loop',checked:!!props.attributes.loop,onChange:function(v){props.setAttributes({loop:v});}}),
          el(ToggleControl,{key:'autoplay',label:'Autoplay',checked:!!props.attributes.autoplay,onChange:function(v){props.setAttributes({autoplay:v});}}),
          el(ToggleControl,{key:'pag',label:'Pagination',checked:!!props.attributes.showPagination,onChange:function(v){props.setAttributes({showPagination:v});}}),
          el(ToggleControl,{key:'nav',label:'Navigation',checked:!!props.attributes.showNavigation,onChange:function(v){props.setAttributes({showNavigation:v});}}),
          el(SelectControl,{key:'style',label:'Demo style',value:props.attributes.demoStyle||'cards',options:[{label:'Cards',value:'cards'},{label:'Description',value:'text'},{label:'Minimal',value:'minimal'}],onChange:function(v){props.setAttributes({demoStyle:v});}})
        ].concat(slides.map(function(slide,index){
          return el('div',{key:'slide'+index,className:'wpbb-mini-card'},[
            el(SelectControl,{key:'type',label:'Type',value:slide.type||'text',options:[{label:'Description',value:'text'},{label:'Card',value:'card'},{label:'Video',value:'video'}],onChange:function(v){updateSlide(index,'type',v);}}),
            el(TextControl,{key:'title',label:'Title',value:slide.title||'',onChange:function(v){updateSlide(index,'title',v);}}),
            el(TextareaControl,{key:'text',label:'Description',value:slide.text||'',onChange:function(v){updateSlide(index,'text',v);}}),
            el(TextControl,{key:'video',label:'Video URL',value:slide.video||'',onChange:function(v){updateSlide(index,'video',v);}}),
            el(Button,{key:'rm',variant:'secondary',onClick:function(){removeSlide(index);}},'Remove slide')
          ]);
        })).concat([el(Button,{key:'add',variant:'secondary',onClick:addSlide},'Add slide')]))),
        el('div',useBlockProps({className:'wpbb-swiper-editor'}),label('SWIPER'),
          el('div',{className:'wpbb-swiper-editor-track'},slides.map(function(slide,idx){
            return el('div',{key:idx,className:'wpbb-swiper-editor-slide'},[
              el('strong',{key:'t'},slide.title||('Slide '+(idx+1))),
              el('div',{key:'b'},slide.type||'text')
            ]);
          }))
        )
      );
    },
    save:function(){ return null; }
  });


  registerBlockType('wpbb/weather', { title:'Weather', icon:'cloud', category:'wpbb', attributes:{ title:{type:'string',default:'Weather'}, location:{type:'string',default:'London'}, lang:{type:'string',default:'en'}, apiKey:{type:'string',default:''}, showTemp:{type:'boolean',default:true} }, edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Weather settings',initialOpen:true},[ el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}), el(TextControl,{key:'location',label:'Location',value:props.attributes.location,onChange:function(v){props.setAttributes({location:v});}}), el(SelectControl,{key:'lang',label:'Language',value:props.attributes.lang,options:[{label:'English',value:'en'},{label:'Latvian',value:'lv'}],onChange:function(v){props.setAttributes({lang:v});}}), el(TextControl,{key:'api',label:'OpenWeather API key',value:props.attributes.apiKey||'',onChange:function(v){props.setAttributes({apiKey:v});}}), el(ToggleControl,{key:'showTemp',label:'Show temperature',checked:!!props.attributes.showTemp,onChange:function(v){props.setAttributes({showTemp:v});}}) ])), el('div',useBlockProps({className:'wpbb-weather card wpbb-weather-editor-preview'}),[label('WEATHER'), el('div',{className:'card-body'},[el('h3',{className:'card-title'},props.attributes.title||'Weather'), el('div',{className:'wpbb-weather-location'},props.attributes.location||'London'), props.attributes.showTemp!==false ? el('div',{className:'wpbb-weather-temp'},'12°C') : null, el('div',{className:'wpbb-weather-note'},(props.attributes.lang||'en') === 'en' ? 'Live weather preview' : 'Tiešsaistes laikapstākļu priekšskatījums')]) ])); }, save:function(){ return null; } });

  registerBlockType('wpbb/varda-dienas', { title:'Name Days', icon:'calendar-alt', category:'wpbb', attributes:{ title:{type:'string',default:'Name Days'}, dateText:{type:'string',default:''}, names:{type:'string',default:''}, namesJson:{type:'string',default:''} }, edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Name Days',initialOpen:true},[ el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}), el(TextControl,{key:'dateText',label:'Date text override',help:'Leave empty to show today automatically.',value:props.attributes.dateText||'',onChange:function(v){props.setAttributes({dateText:v});}}), el(TextControl,{key:'names',label:'Manual names fallback',help:'Optional fallback if live source is unavailable.',value:props.attributes.names||'',onChange:function(v){props.setAttributes({names:v});}}), el(TextareaControl,{key:'json',label:'Optional local JSON fallback',help:'Example: {"01-01":["Solvija","Reinis"]}',value:props.attributes.namesJson||'',onChange:function(v){props.setAttributes({namesJson:v});}}) ])), el('div',useBlockProps({className:'wpbb-varda-dienas card wpbb-name-days-editor-preview'}),[label('NAME DAYS'), el('div',{className:'card-body'},[el('h3',{className:'card-title'},props.attributes.title||'Name Days'), el('div',{className:'small text-muted'},props.attributes.dateText || 'Today'), el('div',{className:'wpbb-varda-dienas-names'},props.attributes.names || 'Latvian names will load automatically by date')]) ])); }, save:function(){ return null; } });

  registerBlockType('wpbb/ajax-search', { title:'Ajax Search', icon:'search', category:'wpbb', attributes:{ title:{type:'string',default:'Meklēšana'}, placeholder:{type:'string',default:'Meklēt...'}, resultsLimit:{type:'number',default:10}, searchWooBy:{type:'string',default:'title'}, sortBy:{type:'string',default:'relevance'}, showExcerpt:{type:'boolean',default:true}, showPrice:{type:'boolean',default:true}, showButton:{type:'boolean',default:true} }, edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Ajax Search',initialOpen:true},[ el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}), el(TextControl,{key:'placeholder',label:'Placeholder',value:props.attributes.placeholder,onChange:function(v){props.setAttributes({placeholder:v});}}), el(RangeControl,{key:'limit',label:'Results',value:props.attributes.resultsLimit||10,min:1,max:10,onChange:function(v){props.setAttributes({resultsLimit:v||10});}}), el(SelectControl,{key:'mode',label:'Woo search by',value:props.attributes.searchWooBy||'title',options:[{label:'Title',value:'title'},{label:'ID',value:'id'},{label:'SKU',value:'sku'}],onChange:function(v){props.setAttributes({searchWooBy:v});}}), el(SelectControl,{key:'sort',label:'Sort',value:props.attributes.sortBy||'relevance',options:[{label:'Relevance',value:'relevance'},{label:'Date',value:'date'},{label:'Title',value:'title'}],onChange:function(v){props.setAttributes({sortBy:v});}}), el(ToggleControl,{key:'showExcerpt',label:'Show excerpt',checked:props.attributes.showExcerpt!==false,onChange:function(v){props.setAttributes({showExcerpt:v});}}), el(ToggleControl,{key:'showPrice',label:'Show price',checked:props.attributes.showPrice!==false,onChange:function(v){props.setAttributes({showPrice:v});}}), el(ToggleControl,{key:'showButton',label:'Show search page button',checked:!!props.attributes.showButton,onChange:function(v){props.setAttributes({showButton:v});}}) ])), el('div',useBlockProps({className:'wpbb-ajax-search'}),label('AJAX SEARCH'),props.attributes.placeholder)); }, save:function(){ return null; } });

  registerBlockType('wpbb/pricecards', { title:'Pricing cards', icon:'index-card', category:'wpbb', attributes:{ title:{type:'string',default:'Pricing'}, subtitle:{type:'string',default:''}, cardsJson:{type:'string',default:''}, styleVariant:{type:'string',default:'default'}, currency:{type:'string',default:'€'}, boxShadowClass:{type:'string',default:'shadow-sm'} }, edit:function(props){ var cards=[]; try{cards=JSON.parse(props.attributes.cardsJson||'[]')}catch(e){cards=[]} if(!cards.length) cards=[{title:'Basic',price:'9',period:'/mo',text:'Short plan description',button:'Choose plan',featured:false},{title:'Pro',price:'29',period:'/mo',text:'Short plan description',button:'Choose plan',featured:true}]; function save(next){ props.setAttributes({cardsJson:JSON.stringify(next)}); } function upd(i,k,v){ var next=cards.slice(); next[i]=Object.assign({}, next[i], ((o={})=>{o[k]=v; return o;})()); save(next); } function add(){ var next=cards.slice(); next.push({title:'New',price:'0',text:'Short plan description',button:'Choose plan'}); save(next); } return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Pricing cards',initialOpen:true},[ el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}), el(TextControl,{key:'currency',label:'Currency',value:props.attributes.currency||'€',onChange:function(v){props.setAttributes({currency:v});}}), el(TextControl,{key:'subtitle',label:'Subtitle',value:props.attributes.subtitle||'',onChange:function(v){props.setAttributes({subtitle:v});}}), el(SelectControl,{key:'style',label:'Style',value:props.attributes.styleVariant||'default',options:[{label:'Default',value:'default'},{label:'Soft',value:'soft'},{label:'Outline',value:'outline'}],onChange:function(v){props.setAttributes({styleVariant:v});}}), el(SelectControl,{key:'boxShadowClass',label:'Card shadow',value:props.attributes.boxShadowClass||'shadow-sm',options:wpbbShadowOptions(),onChange:function(v){props.setAttributes({boxShadowClass:v});}}) ].concat(cards.map(function(card,i){ return el('div',{key:i,className:'wpbb-mini-card'},[ el(TextControl,{key:'t',label:'Title',value:card.title||'',onChange:function(v){upd(i,'title',v);}}), el(TextControl,{key:'p',label:'Price',value:card.price||'',onChange:function(v){upd(i,'price',v);}}), el(TextareaControl,{key:'x',label:'Description',value:card.text||'',onChange:function(v){upd(i,'text',v);}}), el(TextControl,{key:'per',label:'Period',value:card.period||'',onChange:function(v){upd(i,'period',v);}}), el(ToggleControl,{key:'f',label:'Featured',checked:!!card.featured,onChange:function(v){upd(i,'featured',v);}}), el(TextControl,{key:'b',label:'Button text',value:card.button||'',onChange:function(v){upd(i,'button',v);}}) ]);})).concat([el(Button,{key:'a',variant:'secondary',onClick:add},'Add pricing card')]))), el('div',useBlockProps({className:'wpbb-pricecards wpbb-pricecards-editor'}),[label('PRICECARDS'),el('h3',{className:'wpbb-pricecards-editor__title'},props.attributes.title),props.attributes.subtitle?el('div',{className:'wpbb-pricecards-editor__subtitle'},props.attributes.subtitle):null,el('div',{className:'wpbb-pricecards-editor__grid'},cards.map(function(card,i){return el('div',{key:i,className:'wpbb-pricecards-editor__card ' + (props.attributes.boxShadowClass||'shadow-sm') + (card.featured?' is-featured':'')},[el('div',{className:'wpbb-pricecards-editor__card-title'},card.title||''),el('div',{className:'wpbb-pricecards-editor__card-price'},(props.attributes.currency||'€') + (card.price||'')),card.period?el('div',{className:'wpbb-pricecards-editor__card-period'},card.period):null,el('div',{className:'wpbb-pricecards-editor__card-text'},card.text||''),el('div',{className:'wpbb-pricecards-editor__card-button'},card.button||'Button')]);}))])); }, save:function(){ return null; } });

  registerBlockType('wpbb/catalogue', { title:'Catalogue', icon:'screenoptions', category:'wpbb', attributes:{ title:{type:'string',default:'Katalogs'}, category:{type:'string',default:''}, postsToShow:{type:'number',default:6}, postType:{type:'string',default:'post'}, taxonomy:{type:'string',default:'category'}, sortBy:{type:'string',default:'date'}, sortOrder:{type:'string',default:'DESC'}, showImage:{type:'boolean',default:true}, showExcerpt:{type:'boolean',default:true} }, edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Catalogue',initialOpen:true},[ el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}), el(TextControl,{key:'cat',label:'Category/term slug',value:props.attributes.category||'',onChange:function(v){props.setAttributes({category:v});}}), el(SelectControl,{key:'pt',label:'Post type',value:props.attributes.postType||'post',options:[{label:'Post',value:'post'},{label:'Portfolio',value:'portfolio'}],onChange:function(v){props.setAttributes({postType:v});}}), el(SelectControl,{key:'tax',label:'Taxonomy',value:props.attributes.taxonomy||'category',options:[{label:'Category',value:'category'},{label:'Portfolio Category',value:'portfolio_category'}],onChange:function(v){props.setAttributes({taxonomy:v});}}), el(RangeControl,{key:'pts',label:'Posts to show',value:props.attributes.postsToShow||6,min:1,max:12,onChange:function(v){props.setAttributes({postsToShow:v||6});}}), el(SelectControl,{key:'sortBy',label:'Sort by',value:props.attributes.sortBy||'date',options:[{label:'Date',value:'date'},{label:'Title',value:'title'},{label:'Menu order',value:'menu_order'}],onChange:function(v){props.setAttributes({sortBy:v});}}), el(SelectControl,{key:'sortOrder',label:'Order',value:props.attributes.sortOrder||'DESC',options:[{label:'DESC',value:'DESC'},{label:'ASC',value:'ASC'}],onChange:function(v){props.setAttributes({sortOrder:v});}}), el(ToggleControl,{key:'showImage',label:'Show image',checked:props.attributes.showImage!==false,onChange:function(v){props.setAttributes({showImage:v});}}), el(ToggleControl,{key:'showExcerpt',label:'Show excerpt',checked:props.attributes.showExcerpt!==false,onChange:function(v){props.setAttributes({showExcerpt:v});}}) ])), el('div',useBlockProps({className:'wpbb-catalogue'}),label('CATALOGUE'),props.attributes.title)); }, save:function(){ return null; } });

  registerBlockType('wpbb/code-display', { title:'Code Display', icon:'editor-code', category:'wpbb', attributes:{ title:{type:'string',default:'Code'}, code:{type:'string',default:''}, language:{type:'string',default:'html'} }, edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Code Display',initialOpen:true},[ el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}), el(SelectControl,{key:'lang',label:'Language',value:props.attributes.language||'html',options:[{label:'HTML',value:'html'},{label:'CSS',value:'css'},{label:'JS',value:'javascript'},{label:'PHP',value:'php'}],onChange:function(v){props.setAttributes({language:v});}}), el(TextareaControl,{key:'code',label:'Code',value:props.attributes.code||'',onChange:function(v){props.setAttributes({code:v});}}) ])), el('div',useBlockProps({className:'wpbb-code-display'}),label('CODE'),el('pre',{},props.attributes.code || '<code>...</code>'))); }, save:function(){ return null; } });

  registerBlockType('wpbb/countdown-timer', {
    title:'Countdown Timer', icon:'clock', category:'wpbb',
    attributes:{ title:{type:'string',default:'Countdown'}, targetDate:{type:'string',default:'2030-01-01T00:00:00'}, styleVariant:{type:'string',default:'default'}, accentColor:{type:'string',default:'#2563eb'}, backgroundColor:{type:'string',default:''}, textColor:{type:'string',default:''}, boxShadowClass:{type:'string',default:'shadow-sm'}, labelDays:{type:'string',default:'Days'}, labelHours:{type:'string',default:'Hours'}, labelMinutes:{type:'string',default:'Minutes'}, labelSeconds:{type:'string',default:'Seconds'} },
    edit:function(props){
      return el(wp.element.Fragment,{},
        el(InspectorControls,{},el(PanelBody,{title:'Countdown Timer',initialOpen:true},[
          el(TextControl,{key:'title',label:'Title',value:props.attributes.title||'',onChange:function(v){props.setAttributes({title:v});}}),
          el(TextControl,{key:'target',label:'Target date/time',value:props.attributes.targetDate||'',onChange:function(v){props.setAttributes({targetDate:v});}}),
          el(SelectControl,{key:'variant',label:'Style',value:props.attributes.styleVariant||'default',options:[{label:'Default',value:'default'},{label:'Soft',value:'soft'},{label:'Dark',value:'dark'}],onChange:function(v){props.setAttributes({styleVariant:v});}}),
          el(SelectControl,{key:'shadow',label:'Box shadow',value:props.attributes.boxShadowClass||'shadow-sm',options:wpbbShadowOptions(),onChange:function(v){props.setAttributes({boxShadowClass:v});}}),
          colorInput('Accent color', props.attributes.accentColor || '#2563eb', function(v){ props.setAttributes({accentColor:v}); }, 'cd-accent'),
          colorInput('Background color', props.attributes.backgroundColor || '', function(v){ props.setAttributes({backgroundColor:v}); }, 'cd-bg'),
          colorInput('Text color', props.attributes.textColor || '', function(v){ props.setAttributes({textColor:v}); }, 'cd-text')
        ])),
        el('div',useBlockProps({className:'wpbb-countdown-editor card ' + (props.attributes.boxShadowClass||'shadow-sm') + ' wpbb-countdown-editor--' + (props.attributes.styleVariant||'default'), style:{background:props.attributes.backgroundColor||undefined,color:props.attributes.textColor||undefined}}),[
          label('COUNTDOWN'),
          el('h3',{className:'card-title'},props.attributes.title || 'Countdown'),
          el('div',{className:'wpbb-countdown-editor__grid'},[
            el('div',{className:'wpbb-countdown-editor__box',style:{borderColor:props.attributes.accentColor||'#2563eb'}},[el('strong',{},'12'),el('span',{},props.attributes.labelDays||'Days')]),
            el('div',{className:'wpbb-countdown-editor__box',style:{borderColor:props.attributes.accentColor||'#2563eb'}},[el('strong',{},'08'),el('span',{},props.attributes.labelHours||'Hours')]),
            el('div',{className:'wpbb-countdown-editor__box',style:{borderColor:props.attributes.accentColor||'#2563eb'}},[el('strong',{},'42'),el('span',{},props.attributes.labelMinutes||'Minutes')]),
            el('div',{className:'wpbb-countdown-editor__box',style:{borderColor:props.attributes.accentColor||'#2563eb'}},[el('strong',{},'09'),el('span',{},props.attributes.labelSeconds||'Seconds')])
          ])
        ])
      );
    },
    save:function(){ return null; }
  });

  registerBlockType('wpbb/chart', {
    title:'Chart', icon:'chart-bar', category:'wpbb',
    attributes:{ title:{type:'string',default:'Chart'}, chartType:{type:'string',default:'bar'}, chartDataJson:{type:'string',default:'{"labels":["Jan","Feb","Mar"],"datasets":[{"label":"Sales","data":[12,19,7],"backgroundColor":["#2563eb","#60a5fa","#93c5fd"]}]}'}, chartOptionsJson:{type:'string',default:'{"responsive":true,"plugins":{"legend":{"display":true}}}'}, height:{type:'string',default:'320px'} },
    edit:function(props){
      function previewNodes(){
        try {
          var d = JSON.parse(props.attributes.chartDataJson || '{}');
          var arr = (d.datasets && d.datasets[0] && d.datasets[0].data) ? d.datasets[0].data : [12,19,7];
          var max = Math.max.apply(null, arr.concat([1]));
          if ((props.attributes.chartType || 'bar') === 'line') {
            return el('div',{className:'wpbb-chart-editor__line'}, arr.slice(0,8).map(function(v,i){
              return el('span',{key:i,style:{bottom:(Math.max(8, (Number(v||0)/max)*100))+'%'}});
            }));
          }
          if ((props.attributes.chartType || 'bar') === 'pie' || (props.attributes.chartType || 'bar') === 'doughnut') {
            return el('div',{className:'wpbb-chart-editor__pie' + ((props.attributes.chartType || 'bar') === 'doughnut' ? ' is-doughnut' : '')});
          }
          return el('div',{className:'wpbb-chart-editor__bars'},
            arr.slice(0,8).map(function(v,i){ return el('span',{key:i,style:{height:(20 + (Number(v||0)/max)*140)+'px'}}); })
          );
        } catch(err) {
          return el('div',{className:'wpbb-chart-editor__bars'},[
            el('span',{key:0,style:{height:'80px'}}),
            el('span',{key:1,style:{height:'120px'}}),
            el('span',{key:2,style:{height:'60px'}})
          ]);
        }
      }
      return el(wp.element.Fragment,{},
        el(InspectorControls,{},el(PanelBody,{title:'Chart',initialOpen:true},[
          el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}),
          el(SelectControl,{key:'type',label:'Type',value:props.attributes.chartType||'bar',options:[{label:'Bar',value:'bar'},{label:'Line',value:'line'},{label:'Pie',value:'pie'},{label:'Doughnut',value:'doughnut'}],onChange:function(v){props.setAttributes({chartType:v});}}),
          el(TextControl,{key:'height',label:'Height',value:props.attributes.height||'320px',onChange:function(v){props.setAttributes({height:v});}}),
          el(TextareaControl,{key:'data',label:'Chart data JSON',value:props.attributes.chartDataJson||'',help:'Labels + datasets JSON',onChange:function(v){props.setAttributes({chartDataJson:v});}}),
          el(TextareaControl,{key:'opts',label:'Chart options JSON',value:props.attributes.chartOptionsJson||'',help:'Chart.js options JSON',onChange:function(v){props.setAttributes({chartOptionsJson:v});}})
        ])),
        el('div',useBlockProps({className:'wpbb-chart wpbb-chart-editor card'}),[
          label('CHART'),
          el('h3',{className:'card-title'},props.attributes.title || 'Chart'),
          el('div',{className:'wpbb-chart-editor__mock wpbb-chart-editor__mock--' + (props.attributes.chartType||'bar'),style:{height:props.attributes.height||'320px'}},[
            previewNodes()
          ])
        ])
      );
    },
    save:function(){ return null; }
  });

  registerBlockType('wpbb/fun-fact', { title:'Fun Fact', icon:'star-filled', category:'wpbb', attributes:{ number:{type:'string',default:'100+'}, label:{type:'string',default:'Projects'}, icon:{type:'string',default:'⭐'}, styleVariant:{type:'string',default:'default'} }, edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Fun Fact',initialOpen:true},[ el(TextControl,{key:'num',label:'Number',value:props.attributes.number,onChange:function(v){props.setAttributes({number:v});}}), el(TextControl,{key:'label',label:'Label',value:props.attributes.label,onChange:function(v){props.setAttributes({label:v});}}), el(TextControl,{key:'icon',label:'Icon',value:props.attributes.icon,onChange:function(v){props.setAttributes({icon:v});}}), el(SelectControl,{key:'variant',label:'Style',value:props.attributes.styleVariant||'default',options:[{label:'Default',value:'default'},{label:'Soft',value:'soft'}],onChange:function(v){props.setAttributes({styleVariant:v});}}) ])), el('div',useBlockProps({className:'wpbb-fun-fact'}),label('FUN FACT'),props.attributes.number + ' ' + props.attributes.label)); }, save:function(){ return null; } });

  registerBlockType('wpbb/mailchimp', {
    title:'MailChimp', icon:'email', category:'wpbb',
    attributes:{ title:{type:'string',default:'Subscribe'}, text:{type:'string',default:'Join our newsletter'}, actionUrl:{type:'string',default:''}, audienceFieldName:{type:'string',default:'EMAIL'}, showNameField:{type:'boolean',default:false}, buttonText:{type:'string',default:'Subscribe'}, styleVariant:{type:'string',default:'soft'}, useHcaptcha:{type:'boolean',default:false}, submitBg:{type:'string',default:'#2563eb'}, submitColor:{type:'string',default:'#ffffff'} },
    edit:function(props){
      return el(wp.element.Fragment,{},
        el(InspectorControls,{},el(PanelBody,{title:'MailChimp',initialOpen:true},[
          el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}),
          el(TextareaControl,{key:'text',label:'Description',value:props.attributes.text,onChange:function(v){props.setAttributes({text:v});}}),
          el(TextControl,{key:'action',label:'MailChimp form action URL',value:props.attributes.actionUrl||'',onChange:function(v){props.setAttributes({actionUrl:v});}}),
          el(TextControl,{key:'aud',label:'Audience field name',value:props.attributes.audienceFieldName||'EMAIL',onChange:function(v){props.setAttributes({audienceFieldName:v});}}),
          el(ToggleControl,{key:'showName',label:'Show name field',checked:!!props.attributes.showNameField,onChange:function(v){props.setAttributes({showNameField:v});}}),
          el(ToggleControl,{key:'hcap',label:'Show hCaptcha note',checked:!!props.attributes.useHcaptcha,onChange:function(v){props.setAttributes({useHcaptcha:v});}}),
          el(SelectControl,{key:'style',label:'Style',value:props.attributes.styleVariant||'soft',options:[{label:'Default',value:'default'},{label:'Soft',value:'soft'},{label:'Outline',value:'outline'}],onChange:function(v){props.setAttributes({styleVariant:v});}}),
          colorInput('Button background', props.attributes.submitBg || '#2563eb', function(v){ props.setAttributes({submitBg:v}); }, 'mc-bg'),
          colorInput('Button text', props.attributes.submitColor || '#ffffff', function(v){ props.setAttributes({submitColor:v}); }, 'mc-color'),
          el(TextControl,{key:'btn',label:'Button text',value:props.attributes.buttonText||'',onChange:function(v){props.setAttributes({buttonText:v});}})
        ])),
        el('div',useBlockProps({className:'wpbb-mailchimp-editor wpbb-dynamic-form-wrap style-' + (props.attributes.styleVariant||'soft')}),[
          label('MAILCHIMP'),
          el('h3',{className:'wpbb-form-title'},props.attributes.title || 'Subscribe'),
          el('p',{},props.attributes.text || 'Join our newsletter'),
          el('div',{className:'wpbb-mailchimp-editor__form'},[
            props.attributes.showNameField ? el('input',{className:'form-control',placeholder:'Name'}) : null,
            el('div',{className:'wpbb-mailchimp-editor__row'},[
              el('input',{className:'form-control',placeholder:'Email'}),
              el('button',{className:'btn btn-primary',style:{background:props.attributes.submitBg||'#2563eb',color:props.attributes.submitColor||'#fff',borderColor:props.attributes.submitBg||'#2563eb'}},props.attributes.buttonText || 'Subscribe')
            ]),
            props.attributes.useHcaptcha ? el('div',{className:'wpbb-captcha-note'},'hCaptcha enabled') : null
          ])
        ])
      );
    },
    save:function(){ return null; }
  });

  registerBlockType('wpbb/bootstrap-div', {
    title:'Bootstrap Div', icon:'screenoptions', category:'wpbb',
    attributes:{ tagName:{type:'string',default:'div'}, maxWidth:{type:'string',default:''}, maxHeight:{type:'string',default:''}, minHeight:{type:'string',default:''}, backgroundColor:{type:'string',default:''}, textColor:{type:'string',default:''}, borderRadius:{type:'string',default:''}, padding:{type:'string',default:''}, margin:{type:'string',default:''}, utilityClasses:{type:'string',default:''} },
    edit:function(props){
      var bp = useBlockProps({className:'wpbb-bootstrap-div ' + (props.attributes.utilityClasses||''), style:{maxWidth:props.attributes.maxWidth||undefined,maxHeight:props.attributes.maxHeight||undefined,minHeight:props.attributes.minHeight||undefined,background:props.attributes.backgroundColor||undefined,color:props.attributes.textColor||undefined,borderRadius:props.attributes.borderRadius||undefined,padding:props.attributes.padding||undefined,margin:props.attributes.margin||undefined}});
      return el(wp.element.Fragment,{}, 
        el(InspectorControls,{},el(PanelBody,{title:'Bootstrap Div',initialOpen:true},[
          el(SelectControl,{key:'tag',label:'Tag',value:props.attributes.tagName||'div',options:[{label:'div',value:'div'},{label:'section',value:'section'},{label:'article',value:'article'},{label:'aside',value:'aside'}],onChange:function(v){props.setAttributes({tagName:v});}}),
          el(TextControl,{key:'mw',label:'Max width',value:props.attributes.maxWidth||'',onChange:function(v){props.setAttributes({maxWidth:v});}}),
          el(TextControl,{key:'mh',label:'Max height',value:props.attributes.maxHeight||'',onChange:function(v){props.setAttributes({maxHeight:v});}}),
          el(TextControl,{key:'minh',label:'Min height',value:props.attributes.minHeight||'',onChange:function(v){props.setAttributes({minHeight:v});}}),
          colorInput('Background',props.attributes.backgroundColor,function(v){props.setAttributes({backgroundColor:v});},'bd-bg'),
          colorInput('Text color',props.attributes.textColor,function(v){props.setAttributes({textColor:v});},'bd-tx'),
          el(TextControl,{key:'br',label:'Border radius',value:props.attributes.borderRadius||'',onChange:function(v){props.setAttributes({borderRadius:v});}}),
          el(TextControl,{key:'pad',label:'Padding',value:props.attributes.padding||'',onChange:function(v){props.setAttributes({padding:v});}}),
          el(TextControl,{key:'mar',label:'Margin',value:props.attributes.margin||'',onChange:function(v){props.setAttributes({margin:v});}}),
          el(TextControl,{key:'uc',label:'Additional CSS class(es) - Add Bootstrap class',help:'Add Bootstrap classes like shadow, rounded, text-center, d-flex, align-items-center, p-3, m-2',value:props.attributes.utilityClasses||'',onChange:function(v){props.setAttributes({utilityClasses:v});}})
        ])),
        el('div',bp,label('BOOTSTRAP DIV'), el(InnerBlocks,{allowedBlocks:['core/paragraph','core/heading','wpbb/button','wpbb/cta-card','wpbb/code-display']}))
      );
    },
    save:function(){ return el(InnerBlocks.Content); }
  });



registerBlockType('wpbb/alert', {
  title:'Alert',
  icon:'warning',
  category:'wpbb',
  attributes:{
    text:{type:'string',default:'Heads up! This is a fast, accessible alert block.'},
    variant:{type:'string',default:'primary'},
    dismissible:{type:'boolean',default:false}
  },
  edit:function(props){
    return el(wp.element.Fragment,{},
      el(InspectorControls,{},
        el(PanelBody,{title:'Alert settings',initialOpen:true},[
          el(SelectControl,{key:'variant',label:'Variant',value:props.attributes.variant||'primary',options:[
            {label:'Primary',value:'primary'},{label:'Success',value:'success'},{label:'Warning',value:'warning'},{label:'Danger',value:'danger'},{label:'Info',value:'info'}
          ],onChange:function(v){props.setAttributes({variant:v});}}),
          el(ToggleControl,{key:'dismiss',label:'Dismissible',checked:!!props.attributes.dismissible,onChange:function(v){props.setAttributes({dismissible:v});}})
        ])
      ),
      el('div',useBlockProps({className:'alert alert-' + (props.attributes.variant||'primary')}),
        el(RichText,{tagName:'div',value:props.attributes.text,onChange:function(v){props.setAttributes({text:v});}})
      )
    );
  },
  save:function(){ return null; }
});

registerBlockType('wpbb/badge', {
  title:'Badge',
  icon:'tag',
  category:'wpbb',
  attributes:{
    text:{type:'string',default:'New'},
    variant:{type:'string',default:'primary'},
    pill:{type:'boolean',default:true}
  },
  edit:function(props){
    return el(wp.element.Fragment,{},
      el(InspectorControls,{},
        el(PanelBody,{title:'Badge settings',initialOpen:true},[
          el(TextControl,{key:'text',label:'Description',value:props.attributes.text,onChange:function(v){props.setAttributes({text:v});}}),
          el(SelectControl,{key:'variant',label:'Variant',value:props.attributes.variant||'primary',options:[
            {label:'Primary',value:'primary'},{label:'Secondary',value:'secondary'},{label:'Success',value:'success'},{label:'Warning',value:'warning'},{label:'Danger',value:'danger'}
          ],onChange:function(v){props.setAttributes({variant:v});}}),
          el(ToggleControl,{key:'pill',label:'Rounded pill',checked:props.attributes.pill!==false,onChange:function(v){props.setAttributes({pill:v});}})
        ])
      ),
      el('div',useBlockProps({className:'wpbb-badge-preview'}),
        el('span',{className:'badge text-bg-' + (props.attributes.variant||'primary') + (props.attributes.pill!==false ? ' rounded-pill' : '')}, props.attributes.text || 'New')
      )
    );
  },
  save:function(){ return null; }
});

registerBlockType('wpbb/breadcrumb', {
  title:'Breadcrumb',
  icon:'editor-ol',
  category:'wpbb',
  attributes:{
    itemsJson:{type:'string',default:'[{"label":"Home","url":"/"},{"label":"Library","url":"#"},{"label":"Current page","url":""}]'}
  },
  edit:function(props){
    var items;
    try { items = JSON.parse(props.attributes.itemsJson || '[]'); } catch(e) { items = []; }
    var previewItems = items.length ? items : [{label:'Home'},{label:'Current page'}];
    return el(wp.element.Fragment,{},
      el(InspectorControls,{},
        el(PanelBody,{title:'Breadcrumb items',initialOpen:true},[
          el(TextareaControl,{key:'items',label:'Items JSON',value:props.attributes.itemsJson||'',onChange:function(v){props.setAttributes({itemsJson:v});}})
        ])
      ),
      el('div',useBlockProps({className:'wpbb-breadcrumb-preview'}),
        el('nav',{'aria-label':'Breadcrumb'},
          el('ol',{className:'breadcrumb mb-0'},
            previewItems.map(function(item,index){
              var isLast = index === previewItems.length - 1;
              return el('li',{key:index,className:'breadcrumb-item' + (isLast ? ' active' : '')}, item.label || 'Item');
            })
          )
        )
      )
    );
  },
  save:function(){ return null; }
});

registerBlockType('wpbb/list-group', {
  title:'List Group',
  icon:'list-view',
  category:'wpbb',
  attributes:{
    itemsJson:{type:'string',default:'[{"text":"Fast loading","active":true},{"text":"Bootstrap components"},{"text":"Server-side rendering"}]'},
    flush:{type:'boolean',default:false},
    numbered:{type:'boolean',default:false}
  },
  edit:function(props){
    var items;
    try { items = JSON.parse(props.attributes.itemsJson || '[]'); } catch(e) { items = []; }
    var previewItems = items.length ? items : [{text:'Item'}];
    return el(wp.element.Fragment,{},
      el(InspectorControls,{},
        el(PanelBody,{title:'List group',initialOpen:true},[
          el(TextareaControl,{key:'items',label:'Items JSON',value:props.attributes.itemsJson||'',onChange:function(v){props.setAttributes({itemsJson:v});}}),
          el(ToggleControl,{key:'flush',label:'Flush',checked:!!props.attributes.flush,onChange:function(v){props.setAttributes({flush:v});}}),
          el(ToggleControl,{key:'numbered',label:'Numbered',checked:!!props.attributes.numbered,onChange:function(v){props.setAttributes({numbered:v});}})
        ])
      ),
      el('div',useBlockProps({className:'list-group' + (props.attributes.flush ? ' list-group-flush' : '') + (props.attributes.numbered ? ' list-group-numbered' : '')}),
        previewItems.map(function(item,index){
          return el('div',{key:index,className:'list-group-item' + (item.active ? ' active' : '')}, item.text || 'Item');
        })
      )
    );
  },
  save:function(){ return null; }
});

registerBlockType('wpbb/navbar', {
  title:'Navbar',
  icon:'menu',
  category:'wpbb',
  attributes:{
    brand:{type:'string',default:'BBuilder'},
    brandUrl:{type:'string',default:'/'},
    expand:{type:'string',default:'lg'},
    scheme:{type:'string',default:'light'},
    bgClass:{type:'string',default:'bg-light'},
    itemsJson:{type:'string',default:'[{"label":"Home","url":"/","active":true},{"label":"Docs","url":"#"},{"label":"Pricing","url":"#"}]'}
  },
  edit:function(props){
    var items;
    try { items = JSON.parse(props.attributes.itemsJson || '[]'); } catch(e) { items = []; }
    var previewItems = items.length ? items : [{label:'Home'}];
    return el(wp.element.Fragment,{},
      el(InspectorControls,{},
        el(PanelBody,{title:'Navbar settings',initialOpen:true},[
          el(TextControl,{key:'brand',label:'Brand',value:props.attributes.brand||'',onChange:function(v){props.setAttributes({brand:v});}}),
          el(TextControl,{key:'brandUrl',label:'Brand URL',value:props.attributes.brandUrl||'/',onChange:function(v){props.setAttributes({brandUrl:v});}}),
          el(SelectControl,{key:'expand',label:'Expand breakpoint',value:props.attributes.expand||'lg',options:[{label:'sm',value:'sm'},{label:'md',value:'md'},{label:'lg',value:'lg'},{label:'xl',value:'xl'}],onChange:function(v){props.setAttributes({expand:v});}}),
          el(SelectControl,{key:'scheme',label:'Color scheme',value:props.attributes.scheme||'light',options:[{label:'Light',value:'light'},{label:'Dark',value:'dark'}],onChange:function(v){props.setAttributes({scheme:v});}}),
          el(TextControl,{key:'bgClass',label:'Background class',value:props.attributes.bgClass||'bg-light',onChange:function(v){props.setAttributes({bgClass:v});}}),
          el(TextareaControl,{key:'items',label:'Items JSON',value:props.attributes.itemsJson||'',onChange:function(v){props.setAttributes({itemsJson:v});}})
        ])
      ),
      el('div',useBlockProps({className:'navbar navbar-expand-' + (props.attributes.expand||'lg') + ' ' + (props.attributes.bgClass||'bg-light') + ' rounded-4 px-3 py-2'}),
        el('div',{className:'container-fluid p-0'}, [
          el('strong',{key:'brand',className:'navbar-brand m-0'}, props.attributes.brand||'BBuilder'),
          el('ul',{key:'links',className:'navbar-nav ms-auto flex-row gap-3'},
            previewItems.map(function(item,index){
              return el('li',{key:index,className:'nav-item'}, el('span',{className:'nav-link' + (item.active ? ' active' : '')}, item.label || 'Link'));
            })
          )
        ])
      )
    );
  },
  save:function(){ return null; }
});

registerBlockType('wpbb/progress', {
  title:'Progress',
  icon:'performance',
  category:'wpbb',
  attributes:{
    value:{type:'number',default:72},
    label:{type:'string',default:'Performance'},
    variant:{type:'string',default:'success'},
    striped:{type:'boolean',default:false},
    animated:{type:'boolean',default:false}
  },
  edit:function(props){
    var barClasses = 'progress-bar bg-' + (props.attributes.variant||'success') + (props.attributes.striped ? ' progress-bar-striped' : '') + (props.attributes.animated ? ' progress-bar-animated' : '');
    return el(wp.element.Fragment,{},
      el(InspectorControls,{},
        el(PanelBody,{title:'Progress settings',initialOpen:true},[
          el(TextControl,{key:'label',label:'Label',value:props.attributes.label||'',onChange:function(v){props.setAttributes({label:v});}}),
          el(RangeControl,{key:'value',label:'Value',value:props.attributes.value||0,min:0,max:100,onChange:function(v){props.setAttributes({value:v||0});}}),
          el(SelectControl,{key:'variant',label:'Variant',value:props.attributes.variant||'success',options:[{label:'Success',value:'success'},{label:'Primary',value:'primary'},{label:'Info',value:'info'},{label:'Warning',value:'warning'},{label:'Danger',value:'danger'}],onChange:function(v){props.setAttributes({variant:v});}}),
          el(ToggleControl,{key:'striped',label:'Striped',checked:!!props.attributes.striped,onChange:function(v){props.setAttributes({striped:v});}}),
          el(ToggleControl,{key:'animated',label:'Animated',checked:!!props.attributes.animated,onChange:function(v){props.setAttributes({animated:v});}})
        ])
      ),
      el('div',useBlockProps({className:'wpbb-progress-preview'}), [
        el('div',{key:'meta',className:'d-flex justify-content-between small mb-2'}, [
          el('span',{key:'l'}, props.attributes.label||'Progress'),
          el('strong',{key:'v'}, String(props.attributes.value||0) + '%')
        ]),
        el('div',{key:'bar',className:'progress'},
          el('div',{className:barClasses,style:{width:String(props.attributes.value||0)+'%'}}, String(props.attributes.value||0) + '%')
        )
      ])
    );
  },
  save:function(){ return null; }
});

registerBlockType('wpbb/section', {
  title:'Section',
  icon:'cover-image',
  category:'wpbb',
  attributes:{
    title:{type:'string',default:'Section'},
    lead:{type:'string',default:'Use this semantic section wrapper for hero areas, feature strips, and content bands.'},
    containerClass:{type:'string',default:'container'},
    backgroundClass:{type:'string',default:'py-5'}
  },
  edit:function(props){
    return el(wp.element.Fragment,{},
      el(InspectorControls,{},
        el(PanelBody,{title:'Section settings',initialOpen:true},[
          el(TextControl,{key:'title',label:'Title',value:props.attributes.title||'',onChange:function(v){props.setAttributes({title:v});}}),
          el(TextareaControl,{key:'lead',label:'Lead',value:props.attributes.lead||'',onChange:function(v){props.setAttributes({lead:v});}}),
          el(SelectControl,{key:'container',label:'Container',value:props.attributes.containerClass||'container',options:[{label:'container',value:'container'},{label:'container-fluid',value:'container-fluid'},{label:'container-lg',value:'container-lg'}],onChange:function(v){props.setAttributes({containerClass:v});}}),
          el(TextControl,{key:'bg',label:'Wrapper classes',value:props.attributes.backgroundClass||'py-5',onChange:function(v){props.setAttributes({backgroundClass:v});}})
        ])
      ),
      el('section',useBlockProps({className:'wpbb-section-preview ' + (props.attributes.backgroundClass||'py-5')}),
        el('div',{className:props.attributes.containerClass||'container'}, [
          el(RichText,{key:'title',tagName:'h2',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}),
          el(RichText,{key:'lead',tagName:'p',value:props.attributes.lead,onChange:function(v){props.setAttributes({lead:v});}}),
          el(InnerBlocks,{key:'content',allowedBlocks:['core/paragraph','core/heading','core/list','wpbb/button','wpbb/row','wpbb/cards','wpbb/list-group','wpbb/alert']})
        ])
      )
    );
  },
  save:function(){ return el(InnerBlocks.Content); }
});

registerBlockType('wpbb/spinner', {
  title:'Spinner',
  icon:'update',
  category:'wpbb',
  attributes:{
    type:{type:'string',default:'border'},
    variant:{type:'string',default:'primary'},
    label:{type:'string',default:'Loading'}
  },
  edit:function(props){
    var klass = (props.attributes.type||'border') === 'grow' ? 'spinner-grow' : 'spinner-border';
    return el(wp.element.Fragment,{},
      el(InspectorControls,{},
        el(PanelBody,{title:'Spinner settings',initialOpen:true},[
          el(SelectControl,{key:'type',label:'Type',value:props.attributes.type||'border',options:[{label:'Border',value:'border'},{label:'Grow',value:'grow'}],onChange:function(v){props.setAttributes({type:v});}}),
          el(SelectControl,{key:'variant',label:'Variant',value:props.attributes.variant||'primary',options:[{label:'Primary',value:'primary'},{label:'Secondary',value:'secondary'},{label:'Success',value:'success'},{label:'Warning',value:'warning'},{label:'Danger',value:'danger'}],onChange:function(v){props.setAttributes({variant:v});}}),
          el(TextControl,{key:'label',label:'Accessible label',value:props.attributes.label||'Loading',onChange:function(v){props.setAttributes({label:v});}})
        ])
      ),
      el('div',useBlockProps({className:'text-' + (props.attributes.variant||'primary')}),
        el('div',{className:klass,role:'status'}, el('span',{className:'visually-hidden'}, props.attributes.label || 'Loading'))
      )
    );
  },
  save:function(){ return null; }
});


registerBlockType('wpbb/feature-list', {
  title:'Feature List', icon:'yes', category:'wpbb',
  attributes:{ title:{type:'string',default:'Features'}, itemsJson:{type:'string',default:''}, iconColor:{type:'string',default:'#2563eb'} },
  edit:function(props){
    var items=[]; try{ items=JSON.parse(props.attributes.itemsJson||'[]'); }catch(e){ items=[]; }
    if(!items.length) items=[{title:'Fast setup',text:'Launch quickly with reusable UI.'},{title:'Better UX',text:'Clear value points for marketing pages.'},{title:'Flexible styling',text:'Works with Bootstrap blocks.'}];
    function save(next){ props.setAttributes({itemsJson:JSON.stringify(next)}); }
    function upd(i,k,v){ var next=items.slice(); next[i]=Object.assign({}, next[i], ((o={})=>{o[k]=v; return o;})()); save(next); }
    function add(){ var next=items.slice(); next.push({title:'New feature',text:'Describe this feature.'}); save(next); }
    return el(wp.element.Fragment,{},
      el(InspectorControls,{},el(PanelBody,{title:'Feature List settings',initialOpen:true},[
        el(TextControl,{key:'title',label:'Title',value:props.attributes.title||'',onChange:function(v){props.setAttributes({title:v});}}),
        colorInput('Icon color', props.attributes.iconColor || '#2563eb', function(v){ props.setAttributes({iconColor:v}); }, 'feature-icon-color')
      ].concat(items.map(function(item,i){ return el('div',{key:i,className:'wpbb-mini-card'},[
        el(TextControl,{key:'t',label:'Title',value:item.title||'',onChange:function(v){upd(i,'title',v);}}),
        el(TextareaControl,{key:'x',label:'Description',value:item.text||'',onChange:function(v){upd(i,'text',v);}})
      ]);})).concat([el(Button,{key:'a',variant:'secondary',onClick:add},'Add feature')]))),
      el('div',useBlockProps({className:'wpbb-feature-list-editor'}),[
        label('FEATURE LIST'),
        el('h3',{},props.attributes.title||'Features'),
        el('div',{className:'wpbb-feature-list-editor__items'},items.map(function(item,i){
          return el('div',{key:i,className:'wpbb-feature-list-editor__item'},[
            el('span',{className:'wpbb-feature-list-editor__icon',style:{color:props.attributes.iconColor||'#2563eb'}},'✓'),
            el('div',{},[
              el('div',{className:'wpbb-feature-list-editor__title'},item.title||''),
              el('div',{className:'wpbb-feature-list-editor__text'},item.text||'')
            ])
          ]);
        }))
      ])
    );
  },
  save:function(){ return null; }
});

registerBlockType('wpbb/timeline', {
  title:'Timeline', icon:'clock', category:'wpbb',
  attributes:{ title:{type:'string',default:'Timeline'}, layout:{type:'string',default:'vertical'}, itemsJson:{type:'string',default:''} },
  edit:function(props){
    var items=[]; try{ items=JSON.parse(props.attributes.itemsJson||'[]'); }catch(e){ items=[]; }
    if(!items.length) items=[{date:'2024',title:'Discovery',text:'Research and planning.'},{date:'2025',title:'Build',text:'Implementation and launch.'}];
    function save(next){ props.setAttributes({itemsJson:JSON.stringify(next)}); }
    function upd(i,k,v){ var next=items.slice(); next[i]=Object.assign({}, next[i], ((o={})=>{o[k]=v; return o;})()); save(next); }
    function add(){ var next=items.slice(); next.push({date:'2026',title:'Next step',text:'Describe milestone.'}); save(next); }
    return el(wp.element.Fragment,{},
      el(InspectorControls,{},el(PanelBody,{title:'Timeline settings',initialOpen:true},[
        el(TextControl,{key:'title',label:'Title',value:props.attributes.title||'',onChange:function(v){props.setAttributes({title:v});}}),
        el(SelectControl,{key:'layout',label:'Layout',value:props.attributes.layout||'vertical',options:[{label:'Vertical',value:'vertical'},{label:'Horizontal',value:'horizontal'}],onChange:function(v){props.setAttributes({layout:v});}})
      ].concat(items.map(function(item,i){ return el('div',{key:i,className:'wpbb-mini-card'},[
        el(TextControl,{key:'d',label:'Date',value:item.date||'',onChange:function(v){upd(i,'date',v);}}),
        el(TextControl,{key:'t',label:'Title',value:item.title||'',onChange:function(v){upd(i,'title',v);}}),
        el(TextareaControl,{key:'x',label:'Description',value:item.text||'',onChange:function(v){upd(i,'text',v);}})
      ]);})).concat([el(Button,{key:'a',variant:'secondary',onClick:add},'Add item')]))),
      el('div',useBlockProps({className:'wpbb-timeline-editor wpbb-timeline-editor--' + (props.attributes.layout||'vertical')}),[
        label('TIMELINE'),
        el('h3',{},props.attributes.title||'Timeline'),
        el('div',{className:'wpbb-timeline-editor__items'},items.map(function(item,i){
          return el('div',{key:i,className:'wpbb-timeline-editor__item'},[
            el('div',{className:'wpbb-timeline-editor__dot'}),
            el('div',{className:'wpbb-timeline-editor__content'},[
              el('div',{className:'wpbb-timeline-editor__date'},item.date||''),
              el('div',{className:'wpbb-timeline-editor__title'},item.title||''),
              el('div',{className:'wpbb-timeline-editor__text'},item.text||'')
            ])
          ]);
        }))
      ])
    );
  },
  save:function(){ return null; }
});

registerBlockType('wpbb/custom-embed', {
  title:'Custom Embed', icon:'embed-generic', category:'wpbb',
  attributes:{ title:{type:'string',default:'Embed'}, embedUrl:{type:'string',default:''}, embedHtml:{type:'string',default:''}, height:{type:'string',default:'420px'} },
  edit:function(props){
    return el(wp.element.Fragment,{},
      el(InspectorControls,{},el(PanelBody,{title:'Custom Embed settings',initialOpen:true},[
        el(TextControl,{key:'title',label:'Title',value:props.attributes.title||'',onChange:function(v){props.setAttributes({title:v});}}),
        el(TextControl,{key:'url',label:'Embed URL',value:props.attributes.embedUrl||'',onChange:function(v){props.setAttributes({embedUrl:v});}}),
        el(TextareaControl,{key:'html',label:'Embed HTML / iframe',value:props.attributes.embedHtml||'',onChange:function(v){props.setAttributes({embedHtml:v});}}),
        el(TextControl,{key:'h',label:'Height',value:props.attributes.height||'420px',onChange:function(v){props.setAttributes({height:v});}})
      ])),
      el('div',useBlockProps({className:'wpbb-custom-embed-editor'}),[
        label('CUSTOM EMBED'),
        el('div',{className:'wpbb-custom-embed-editor__box',style:{minHeight:props.attributes.height||'420px'}}, props.attributes.embedUrl || props.attributes.embedHtml ? 'Embed preview placeholder' : 'Add embed URL or HTML')
      ])
    );
  },
  save:function(){ return null; }
});

registerBlockType('wpbb/ai-content', {
  title:'AI Content', icon:'admin-comments', category:'wpbb',
  attributes:{ title:{type:'string',default:'AI Content'}, shortDescription:{type:'string',default:''}, prompt:{type:'string',default:''}, generatedText:{type:'string',default:''}, provider:{type:'string',default:'simple-ai'}, tone:{type:'string',default:'professional'}, contentType:{type:'string',default:'paragraph'}, length:{type:'string',default:'medium'} },
  edit:function(props){
    function generateSimpleAiText() {
      var keywords = (props.attributes.shortDescription || '').trim();
      var description = (props.attributes.prompt || '').trim();
      var tone = props.attributes.tone || 'professional';
      var contentType = props.attributes.contentType || 'paragraph';
      var length = props.attributes.length || 'medium';
      var source = description || keywords;
      if (!source) return;
      var opener = 'Create ' + (length === 'short' ? 'a concise ' : (length === 'long' ? 'a detailed ' : 'a clear ')) + tone + ' ' + contentType + ' about ' + source + '.';
      var body = contentType === 'bullet-points'
        ? '• Main benefit: highlight the strongest value for the reader.\n• Why it matters: explain the practical result clearly.\n• Next step: end with a simple action or takeaway.'
        : 'This content explains ' + source + ' in a ' + tone + ' way, focusing on clear value, practical benefits, and a strong next step for the reader.';
      props.setAttributes({ generatedText: opener + '\n\n' + body });
    }
    return el(wp.element.Fragment,{},
      el(InspectorControls,{},el(PanelBody,{title:'AI Content settings',initialOpen:true},[
        el(TextControl,{key:'title',label:'Title',value:props.attributes.title||'',onChange:function(v){props.setAttributes({title:v});}}),
        el(TextControl,{key:'keywords',label:'Keywords',help:'Example: solar panels, energy savings, installation',value:props.attributes.shortDescription||'',onChange:function(v){props.setAttributes({shortDescription:v});}}),
        el(TextareaControl,{key:'desc',label:'Description',help:'Short explanation of what text to generate.',value:props.attributes.prompt||'',onChange:function(v){props.setAttributes({prompt:v});}}),
        el(SelectControl,{key:'tone',label:'Tone',value:props.attributes.tone||'professional',options:[{label:'Professional',value:'professional'},{label:'Friendly',value:'friendly'},{label:'Bold',value:'bold'}],onChange:function(v){props.setAttributes({tone:v});}}),
        el(SelectControl,{key:'type',label:'Output type',value:props.attributes.contentType||'paragraph',options:[{label:'Paragraph',value:'paragraph'},{label:'Bullet points',value:'bullet-points'},{label:'Intro text',value:'intro'}],onChange:function(v){props.setAttributes({contentType:v});}}),
        el(SelectControl,{key:'length',label:'Length',value:props.attributes.length||'medium',options:[{label:'Short',value:'short'},{label:'Medium',value:'medium'},{label:'Long',value:'long'}],onChange:function(v){props.setAttributes({length:v});}}),
        el(Button,{key:'gen',variant:'secondary',onClick:generateSimpleAiText},'Generate text now'),
        el(TextareaControl,{key:'generated',label:'Generated content',value:props.attributes.generatedText||'',onChange:function(v){props.setAttributes({generatedText:v});}})
      ])),
      el('div',useBlockProps({className:'wpbb-ai-content wpbb-ai-content-editor'}),[
        label('AI CONTENT'),
        el('div',{className:'wpbb-ai-content-editor__meta'},'Simple generator'),
        el('div',{className:'wpbb-ai-content-editor__body'}, props.attributes.generatedText || 'Add keywords or a short description, then click Generate text now.')
      ])
    );
  },
  save:function(){ return null; }
});

registerBlockType('wpbb/login-register', {
  title:'Login / Register', icon:'lock', category:'wpbb',
  attributes:{ title:{type:'string',default:'Account Access'}, showRegister:{type:'boolean',default:true}, styleVariant:{type:'string',default:'split'} },
  edit:function(props){
    return el(wp.element.Fragment,{},
      el(InspectorControls,{},el(PanelBody,{title:'Login / Register settings',initialOpen:true},[
        el(TextControl,{key:'title',label:'Title',value:props.attributes.title||'',onChange:function(v){props.setAttributes({title:v});}}),
        el(SelectControl,{key:'style',label:'Layout',value:props.attributes.styleVariant||'split',options:[{label:'Split',value:'split'},{label:'Stacked',value:'stacked'}],onChange:function(v){props.setAttributes({styleVariant:v});}}),
        el(ToggleControl,{key:'register',label:'Show register form',checked:props.attributes.showRegister!==false,onChange:function(v){props.setAttributes({showRegister:v});}})
      ])),
      el('div',useBlockProps({className:'wpbb-auth-editor wpbb-auth-editor--' + (props.attributes.styleVariant||'split')}),[
        label('LOGIN / REGISTER'),
        el('h3',{},props.attributes.title||'Account Access'),
        el('div',{className:'wpbb-auth-editor__grid'},[
          el('div',{className:'wpbb-auth-editor__panel'},'Login form preview'),
          props.attributes.showRegister!==false ? el('div',{className:'wpbb-auth-editor__panel'},'Register form preview') : null
        ])
      ])
    );
  },
  save:function(){ return null; }
});


registerBlockType('wpbb/load-more', {
  title:'Load More', icon:'plus-alt2', category:'wpbb',
  attributes:{ buttonText:{type:'string',default:'Load more'}, buttonClass:{type:'string',default:'btn btn-primary'}, buttonColor:{type:'string',default:'#2563eb'}, visibleItems:{type:'number',default:6}, loadItems:{type:'number',default:3}, parentClass:{type:'string',default:'row'}, itemClass:{type:'string',default:'col-md-4'}, queryPostType:{type:'string',default:'post'}, queryCategory:{type:'string',default:''} },
  edit:function(props){
    var previewButtonStyle = {
      background: props.attributes.buttonColor || '#2563eb',
      borderColor: props.attributes.buttonColor || '#2563eb',
      color: '#ffffff'
    };
    return el(wp.element.Fragment,{},
      el(InspectorControls,{},el(PanelBody,{title:'Load more settings',initialOpen:true},[
        el(TextControl,{label:'Button text',value:props.attributes.buttonText||'',onChange:function(v){props.setAttributes({buttonText:v});}}),
        el(TextControl,{label:'Button class',value:props.attributes.buttonClass||'',onChange:function(v){props.setAttributes({buttonClass:v});}}),
        colorInput('Button color', props.attributes.buttonColor || '#2563eb', function(v){ props.setAttributes({buttonColor:v}); }, 'load-more-button-color'),
        el(RangeControl,{label:'Visible items',value:props.attributes.visibleItems||6,min:1,max:24,onChange:function(v){props.setAttributes({visibleItems:v||6});}}),
        el(RangeControl,{label:'Load items',value:props.attributes.loadItems||3,min:1,max:12,onChange:function(v){props.setAttributes({loadItems:v||3});}}),
        el(TextControl,{label:'Parent query class',value:props.attributes.parentClass||'',onChange:function(v){props.setAttributes({parentClass:v});}}),
        el(TextControl,{label:'Child item class',value:props.attributes.itemClass||'',onChange:function(v){props.setAttributes({itemClass:v});}}),
        el(TextControl,{label:'Post type',value:props.attributes.queryPostType||'post',onChange:function(v){props.setAttributes({queryPostType:v});}}),
        el(TextControl,{label:'Category slug',value:props.attributes.queryCategory||'',onChange:function(v){props.setAttributes({queryCategory:v});}})
      ])),
      el('div',useBlockProps({className:'wpbb-editor-card wpbb-load-more-editor-preview'}),[
        label('LOAD MORE'),
        el('div',{className:'wpbb-load-more-editor-preview__stats'},[
          el('p',{key:'a'},'Visible: ' + (props.attributes.visibleItems||6)),
          el('p',{key:'b'},'Load per click: ' + (props.attributes.loadItems||3)),
          el('p',{key:'c'},'Parent class: ' + (props.attributes.parentClass||'row')),
          el('p',{key:'d'},'Item class: ' + (props.attributes.itemClass||'col-md-4')),
          el('p',{key:'e'},'Category: ' + (props.attributes.queryCategory||'all'))
        ]),
        el('div',{className:'wpbb-load-more-editor-preview__button-wrap'},
          el('button',{type:'button',className:props.attributes.buttonClass||'btn btn-primary',style:previewButtonStyle}, props.attributes.buttonText||'Load more')
        )
      ])
    );
  },
  save:function(){ return null; }
});

registerBlockType('wpbb/contact-links', {
  title:'Email & Phone', icon:'phone', category:'wpbb',
  attributes:{ email:{type:'string',default:'hello@example.com'}, phone:{type:'string',default:'+44 20 1234 5678'}, emailIcon:{type:'string',default:'email'}, phoneIcon:{type:'string',default:'whatsapp'}, iconColor:{type:'string',default:'#2563eb'}, linkColor:{type:'string',default:'#0f172a'}, layoutClass:{type:'string',default:'d-flex flex-column gap-2'} },
  edit:function(props){
    var iconStyle = { color: props.attributes.iconColor || '#2563eb' };
    var linkStyle = { color: props.attributes.linkColor || '#0f172a' };
    return el(wp.element.Fragment,{},
      el(InspectorControls,{},el(PanelBody,{title:'Email & phone settings',initialOpen:true},[
        el(TextControl,{label:'Email',value:props.attributes.email||'',onChange:function(v){props.setAttributes({email:v});}}),
        el(TextControl,{label:'Phone',value:props.attributes.phone||'',onChange:function(v){props.setAttributes({phone:v});}}),
        el(SelectControl,{label:'Email icon',value:props.attributes.emailIcon||'email',options:[{label:'Email',value:'email'},{label:'WhatsApp',value:'whatsapp'},{label:'Facebook',value:'facebook'}],onChange:function(v){props.setAttributes({emailIcon:v});}}),
        el(SelectControl,{label:'Phone icon',value:props.attributes.phoneIcon||'whatsapp',options:[{label:'WhatsApp',value:'whatsapp'},{label:'Email',value:'email'},{label:'Facebook',value:'facebook'}],onChange:function(v){props.setAttributes({phoneIcon:v});}}),
        colorInput('Icon color', props.attributes.iconColor || '#2563eb', function(v){props.setAttributes({iconColor:v});}, 'contact-icon-color'),
        colorInput('Link color', props.attributes.linkColor || '#0f172a', function(v){props.setAttributes({linkColor:v});}, 'contact-link-color'),
        el(TextControl,{label:'Wrapper classes',value:props.attributes.layoutClass||'',onChange:function(v){props.setAttributes({layoutClass:v});}})
      ])),
      el('div',useBlockProps({className:'wpbb-contact-links wpbb-editor-card wpbb-contact-links-editor-preview'}),[
        label('EMAIL & PHONE'),
        el('a',{className:'wpbb-contact-links__item',href:'#',style:linkStyle,onClick:function(e){e.preventDefault();}},[
          el('span',{className:'wpbb-contact-links__icon',style:iconStyle},'✆'),
          el('span',{},props.attributes.phone || '+44 20 1234 5678')
        ]),
        el('a',{className:'wpbb-contact-links__item',href:'#',style:linkStyle,onClick:function(e){e.preventDefault();}},[
          el('span',{className:'wpbb-contact-links__icon',style:iconStyle},'✉'),
          el('span',{},props.attributes.email || 'hello@example.com')
        ])
      ])
    );
  },
  save:function(){ return null; }
});

registerBlockType('wpbb/events', {
  title:'Events', icon:'calendar-alt', category:'wpbb',
  attributes:{ postType:{type:'string',default:'event'}, postsToShow:{type:'number',default:6}, taxonomy:{type:'string',default:'event_category'}, showCalendar:{type:'boolean',default:true}, title:{type:'string',default:'Events'} },
  edit:function(props){
    return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Events settings',initialOpen:true},[
      el(TextControl,{label:'Title',value:props.attributes.title||'',onChange:function(v){props.setAttributes({title:v});}}),
      el(TextControl,{label:'Events CPT',value:props.attributes.postType||'event',onChange:function(v){props.setAttributes({postType:v});}}),
      el(TextControl,{label:'Event category taxonomy',value:props.attributes.taxonomy||'event_category',onChange:function(v){props.setAttributes({taxonomy:v});}}),
      el(RangeControl,{label:'Posts to show',value:props.attributes.postsToShow||6,min:1,max:24,onChange:function(v){props.setAttributes({postsToShow:v||6});}}),
      el(ToggleControl,{label:'Show calendar',checked:props.attributes.showCalendar!==false,onChange:function(v){props.setAttributes({showCalendar:v});}})
    ])), el('div',useBlockProps({className:'wpbb-editor-card'}),[label('EVENTS'), el('h4',{},props.attributes.title||'Events'), el('p',{},'CPT: ' + (props.attributes.postType||'event')), props.attributes.showCalendar!==false ? el('div',{},'Calendar preview') : null ]));
  },
  save:function(){ return null; }
});

registerBlockType('wpbb/testimonials', {
  title:'Testimonials', icon:'format-quote', category:'wpbb',
  attributes:{ postType:{type:'string',default:'testimonial'}, postsToShow:{type:'number',default:9}, slidesDesktop:{type:'number',default:3}, slidesTablet:{type:'number',default:2}, slidesMobile:{type:'number',default:1}, showNavigation:{type:'boolean',default:true}, showPagination:{type:'boolean',default:true}, title:{type:'string',default:'Testimonials'} },
  edit:function(props){
    return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Testimonials settings',initialOpen:true},[
      el(TextControl,{label:'Title',value:props.attributes.title||'',onChange:function(v){props.setAttributes({title:v});}}),
      el(TextControl,{label:'Testimonials CPT',value:props.attributes.postType||'testimonial',onChange:function(v){props.setAttributes({postType:v});}}),
      el(RangeControl,{label:'Posts to show',value:props.attributes.postsToShow||9,min:1,max:24,onChange:function(v){props.setAttributes({postsToShow:v||9});}}),
      el(RangeControl,{label:'Desktop slides',value:props.attributes.slidesDesktop||3,min:1,max:6,onChange:function(v){props.setAttributes({slidesDesktop:v||3});}}),
      el(RangeControl,{label:'Tablet slides',value:props.attributes.slidesTablet||2,min:1,max:4,onChange:function(v){props.setAttributes({slidesTablet:v||2});}}),
      el(RangeControl,{label:'Mobile slides',value:props.attributes.slidesMobile||1,min:1,max:2,onChange:function(v){props.setAttributes({slidesMobile:v||1});}}),
      el(ToggleControl,{label:'Show navigation',checked:props.attributes.showNavigation!==false,onChange:function(v){props.setAttributes({showNavigation:v});}}),
      el(ToggleControl,{label:'Show pagination',checked:props.attributes.showPagination!==false,onChange:function(v){props.setAttributes({showPagination:v});}})
    ])), el('div',useBlockProps({className:'wpbb-editor-card'}),[label('TESTIMONIALS'), el('h4',{},props.attributes.title||'Testimonials'), el('p',{},'Slides: ' + [props.attributes.slidesDesktop||3, props.attributes.slidesTablet||2, props.attributes.slidesMobile||1].join(' / '))]));
  },
  save:function(){ return null; }
});

registerBlockType('wpbb/blog-filter', {
  title:'Blog Filter', icon:'filter', category:'wpbb',
  attributes:{ postType:{type:'string',default:'post'}, postsToShow:{type:'number',default:6}, taxonomy:{type:'string',default:'category'}, title:{type:'string',default:'Blog'}, buttonText:{type:'string',default:'Filter'}, buttonColor:{type:'string',default:'#2563eb'} },
  edit:function(props){
    return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Blog filter settings',initialOpen:true},[
      el(TextControl,{label:'Title',value:props.attributes.title||'',onChange:function(v){props.setAttributes({title:v});}}),
      el(TextControl,{label:'Post type / CPT',value:props.attributes.postType||'post',onChange:function(v){props.setAttributes({postType:v});}}),
      el(TextControl,{label:'Taxonomy',value:props.attributes.taxonomy||'category',onChange:function(v){props.setAttributes({taxonomy:v});}}),
      el(RangeControl,{label:'Posts to show',value:props.attributes.postsToShow||6,min:1,max:24,onChange:function(v){props.setAttributes({postsToShow:v||6});}}),
      el(TextControl,{label:'Button text',value:props.attributes.buttonText||'',onChange:function(v){props.setAttributes({buttonText:v});}}),
      colorInput('Button color', props.attributes.buttonColor || '#2563eb', function(v){ props.setAttributes({buttonColor:v}); }, 'blog-filter-button-color')
    ])), el('div',useBlockProps({className:'wpbb-editor-card'}),[label('BLOG FILTER'), el('h4',{},props.attributes.title||'Blog'), el('p',{},'Category, year, date, alphabetical and Ajax search filters are enabled on the frontend.'), el('div',{style:{display:'inline-block',marginTop:'8px',padding:'10px 14px',borderRadius:'999px',background:props.attributes.buttonColor||'#2563eb',color:'#fff',fontWeight:'700'}},props.attributes.buttonText||'Filter') ]));
  },
  save:function(){ return null; }
});

})(window.wp);
