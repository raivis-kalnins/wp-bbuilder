(function (wp) {
  function wpbbCompileScopedScssPreview(selector, scss) {
    scss = (scss || '').trim();
    if (!scss) return '';
    if (scss.indexOf('{') === -1) return selector + ' {' + scss + '}';
    return scss.replace(/&/g, selector);
  }

  var el = wp.element.createElement;
  var registerBlockType = wp.blocks.registerBlockType;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var InspectorControls = wp.blockEditor.InspectorControls;
  var InnerBlocks = wp.blockEditor.InnerBlocks;
  var RichText = wp.blockEditor.RichText;
  var PanelBody = wp.components.PanelBody;
  var TextControl = wp.components.TextControl;
  var TextareaControl = wp.components.TextareaControl;
  var SelectControl = wp.components.SelectControl;
  var ToggleControl = wp.components.ToggleControl;
  var RangeControl = wp.components.RangeControl;
  var Button = wp.components.Button;

  function label(text) {
    return el('div', { className: 'wpbb-editor-label' }, text);
  }

  function colorInput(labelText, value, onChange, key) {
    return el('div', { key: key || labelText, style: { marginBottom: '12px' } }, [
      el('label', { key: 'l', style: { display: 'block', fontWeight: '600', marginBottom: '6px' } }, labelText),
      el('input', {
        key: 'c',
        type: 'color',
        value: value && /^#/.test(value) ? value : '#000000',
        onChange: function (e) { onChange(e.target.value); },
        style: { width: '100%', height: '36px' }
      }),
      el(TextControl, {
        key: 't',
        label: 'Transparent / rgba / custom',
        value: value || '',
        onChange: onChange
      })
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
    attributes: {
      containerClass: { type: 'string', default: '' },
      utilityClasses: { type: 'string', default: '' },
      maxWidth: { type: 'string', default: '' },
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
      align: { type: 'string', default: '' }, uniqueId:{type:'string',default:''}, customCss:{type:'string',default:''}, customScss:{type:'string',default:''}
    },
    edit: function (props) {
      var className = 'wpbb-row row ' + (props.attributes.gutterX || '') + ' ' + (props.attributes.gutterY || '') + ' ' +
        (props.attributes.align ? ('justify-content-' + props.attributes.align) : '') + ' ' +
        (props.attributes.utilityClasses || '') + ' ' + (props.attributes.visibilityClass || '') + ' ' + (props.attributes.animationClass || '');
      var blockProps = useBlockProps({
        className: className,
        style: {
          width: '100%',
          maxWidth: props.attributes.maxWidth || 'none',
          marginLeft: props.attributes.maxWidth ? 'auto' : undefined,
          marginRight: props.attributes.maxWidth ? 'auto' : undefined
        }
      });
      var controls = [
        el(SelectControl, { key:'containerClass', label:'Bootstrap container', value:props.attributes.containerClass, options:[{label:'None',value:''},{label:'container',value:'container'},{label:'container-fluid',value:'container-fluid'},{label:'container-xl',value:'container-xl'}], onChange:function(v){ props.setAttributes({containerClass:v}); } }),
        el(TextControl, { key:'utilityClasses', label:'Bootstrap class list', help:'Add Bootstrap classes like shadow, rounded, text-center, d-flex, align-items-center', value:props.attributes.utilityClasses, onChange:function(v){ props.setAttributes({utilityClasses:v}); } }), el(TextControl,{key:'spacingSm',label:'SM spacing classes',help:'Example: p-sm-2 m-sm-1',value:props.attributes.spacingSm||'',onChange:function(v){props.setAttributes({spacingSm:v});}}), el(TextControl,{key:'spacingMd',label:'MD spacing classes',help:'Example: p-md-3 m-md-2',value:props.attributes.spacingMd||'',onChange:function(v){props.setAttributes({spacingMd:v});}}), el(TextControl,{key:'spacingLg',label:'LG spacing classes',help:'Example: p-lg-4 m-lg-3',value:props.attributes.spacingLg||'',onChange:function(v){props.setAttributes({spacingLg:v});}}), el(TextControl,{key:'spacingXl',label:'XL spacing classes',help:'Example: p-xl-5 m-xl-4',value:props.attributes.spacingXl||'',onChange:function(v){props.setAttributes({spacingXl:v});}}), el(TextControl,{key:'spacingXxl',label:'XXL spacing classes',help:'Example: p-xxl-5 m-xxl-4',value:props.attributes.spacingXxl||'',onChange:function(v){props.setAttributes({spacingXxl:v});}}),
        el(TextControl, { key:'maxWidth', label:'Max width', value:props.attributes.maxWidth, onChange:function(v){ props.setAttributes({maxWidth:v}); } }), el(TextControl,{key:'uniqueId',label:'Unique ID',value:props.attributes.uniqueId||'',onChange:function(v){props.setAttributes({uniqueId:v});}}), el('div',{className:'wpbb-code-editor-preview'},[el(TextareaControl,{key:'customCss',label:'Custom CSS',className:'wpbb-code-editor',value:props.attributes.customCss||'',onChange:function(v){props.setAttributes({customCss:v});}}), el(TextareaControl,{key:'customScss',label:'Custom SCSS',className:'wpbb-code-editor',help:'Use & for this block scope',value:props.attributes.customScss||'',onChange:function(v){props.setAttributes({customScss:v});}}), el(TextareaControl,{key:'compiledPreview',label:'Compiled CSS preview',className:'wpbb-code-editor',value:wpbbCompileScopedScssPreview('#' + (props.attributes.uniqueId || 'preview-row'), props.attributes.customScss||''),readOnly:true}), el('div',{className:'wpbb-scss-build-note'},'SCSS preview updates automatically')]),
        el(SelectControl, { key:'visibilityClass', label:'Extra visibility class', value:props.attributes.visibilityClass, options:[{label:'None',value:''},{label:'d-none',value:'d-none'},{label:'d-none d-md-block',value:'d-none d-md-block'},{label:'d-md-none',value:'d-md-none'}], onChange:function(v){ props.setAttributes({visibilityClass:v}); } }),
        visibilitySwitches(props),
        el(SelectControl, { key:'animationClass', label:'Animation', value:props.attributes.animationClass, options:[{label:'None',value:''},{label:'anim-fade-in',value:'anim-fade-in'},{label:'anim-fade-up',value:'anim-fade-up'},{label:'anim-zoom-in',value:'anim-zoom-in'},{label:'Fade Left',value:'anim-fade-left'},{label:'Fade Right',value:'anim-fade-right'}], onChange:function(v){ props.setAttributes({animationClass:v}); } }),
        el(SelectControl, { key:'gutterX', label:'Horizontal gap', value:props.attributes.gutterX, options:[{label:'gx-0',value:'gx-0'},{label:'gx-1',value:'gx-1'},{label:'gx-2',value:'gx-2'},{label:'gx-3',value:'gx-3'},{label:'gx-4',value:'gx-4'}], onChange:function(v){ props.setAttributes({gutterX:v}); } }),
        el(SelectControl, { key:'gutterY', label:'Vertical gap', value:props.attributes.gutterY, options:[{label:'gy-0',value:'gy-0'},{label:'gy-1',value:'gy-1'},{label:'gy-2',value:'gy-2'},{label:'gy-3',value:'gy-3'},{label:'gy-4',value:'gy-4'}], onChange:function(v){ props.setAttributes({gutterY:v}); } }),
        el(SelectControl, { key:'align', label:'Alignment', value:props.attributes.align, options:[{label:'Default',value:''},{label:'Start',value:'start'},{label:'Center',value:'center'},{label:'End',value:'end'},{label:'Between',value:'between'}], onChange:function(v){ props.setAttributes({align:v}); } })
      ].concat(sideSpacingControls(props, 'padding')).concat(sideSpacingControls(props, 'margin'));
      return el(wp.element.Fragment, {},
        el(InspectorControls, {}, el(PanelBody, { title:'Row settings', initialOpen:true }, controls)),
        el('div', blockProps,
          label('ROW ' + (props.attributes.uniqueId || '')),
          props.attributes.containerClass
            ? el('div', { className: props.attributes.containerClass }, el(InnerBlocks, { allowedBlocks: ['wpbb/column'], orientation: 'horizontal' }))
            : el(InnerBlocks, { allowedBlocks: ['wpbb/column'], orientation: 'horizontal' })
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
    attributes: {
      xs: { type: 'number', default: 12 },
      sm: { type: 'number', default: 0 },
      md: { type: 'number', default: 6 },
      lg: { type: 'number', default: 0 }, xl: { type: 'number', default: 0 }, uniqueId:{type:'string',default:''}, customCss:{type:'string',default:''}, customScss:{type:'string',default:''},
      orderClass: { type: 'string', default: '' },
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
    },
    edit: function (props) {
      var cls = ['wpbb-column'];
      ['xs','sm','md','lg','xl'].forEach(function (bp) {
        var val = props.attributes[bp];
        if (val) cls.push(bp === 'xs' ? ('col-' + val) : ('col-' + bp + '-' + val));
      });
      if (props.attributes.orderClass) cls.push(props.attributes.orderClass);
      if (props.attributes.visibilityClass) cls.push(props.attributes.visibilityClass);
      if (props.attributes.animationClass) cls.push(props.attributes.animationClass);
      var basis = (props.attributes.xl || props.attributes.lg || props.attributes.md || props.attributes.sm || props.attributes.xs || 12);
      var pct = Math.max(1, Math.min(12, basis)) / 12 * 100;
      var blockProps = useBlockProps({ className: cls.join(' '), style: { flex: '0 0 ' + pct + '%', maxWidth: pct + '%', boxSizing: 'border-box' } });

      function sizeControl(bp, labelText) {
        return el(RangeControl, {
          label: labelText,
          value: props.attributes[bp] || 0,
          min: 0,
          max: 12,
          onChange: function (value) {
            var next = {}; next[bp] = value || 0; props.setAttributes(next);
          }
        });
      }

      var controls = [
        sizeControl('xs', 'XS'),
        sizeControl('sm', 'SM'),
        sizeControl('md', 'MD'),
        sizeControl('lg', 'LG'), sizeControl('xl', 'XL'), el(TextControl,{key:'uniqueId',label:'Unique ID',value:props.attributes.uniqueId||'',onChange:function(v){props.setAttributes({uniqueId:v});}}), el('div',{className:'wpbb-code-editor-preview'},[el(TextareaControl,{key:'customCss',label:'Custom CSS',className:'wpbb-code-editor',value:props.attributes.customCss||'',onChange:function(v){props.setAttributes({customCss:v});}}), el(TextareaControl,{key:'customScss',label:'Custom SCSS',className:'wpbb-code-editor',help:'Use & for this block scope',value:props.attributes.customScss||'',onChange:function(v){props.setAttributes({customScss:v});}}), el('div',{className:'wpbb-scss-build-note'},'SCSS compiles automatically on frontend scope')]),
        el(SelectControl, { key:'orderClass', label:'Order', value:props.attributes.orderClass, options:[{label:'Default',value:''},{label:'order-1',value:'order-1'},{label:'order-2',value:'order-2'},{label:'order-3',value:'order-3'},{label:'order-first',value:'order-first'},{label:'order-last',value:'order-last'}], onChange:function(v){ props.setAttributes({orderClass:v}); } }),
        el(SelectControl, { key:'visibilityClass', label:'Extra visibility class', value:props.attributes.visibilityClass, options:[{label:'None',value:''},{label:'d-none',value:'d-none'},{label:'d-none d-md-block',value:'d-none d-md-block'},{label:'d-md-none',value:'d-md-none'}], onChange:function(v){ props.setAttributes({visibilityClass:v}); } }),
        visibilitySwitches(props),
        el(SelectControl, { key:'animationClass', label:'Animation', value:props.attributes.animationClass, options:[{label:'None',value:''},{label:'anim-fade-in',value:'anim-fade-in'},{label:'anim-fade-up',value:'anim-fade-up'},{label:'anim-zoom-in',value:'anim-zoom-in'},{label:'Fade Left',value:'anim-fade-left'},{label:'Fade Right',value:'anim-fade-right'}], onChange:function(v){ props.setAttributes({animationClass:v}); } })
      ].concat(sideSpacingControls(props, 'padding')).concat(sideSpacingControls(props, 'margin'));

      return el(wp.element.Fragment, {},
        el(InspectorControls, {}, el(PanelBody, { title: 'Column settings', initialOpen: true }, controls)),
        el('div', blockProps, label('COLUMN ' + (props.attributes.uniqueId || '')), el(InnerBlocks))
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
      btnClass: { type: 'string', default: 'btn btn-primary' }
    },
    edit: function (props) {
      var computedClass = 'btn btn-' + (props.attributes.variant || 'primary') + (props.attributes.size ? (' btn-' + props.attributes.size) : '') + (props.attributes.fullWidth ? ' w-100' : '');
      var style = {
        background: props.attributes.backgroundColor || undefined,
        color: props.attributes.textColor || undefined,
        borderColor: props.attributes.backgroundColor || undefined,
        display: 'inline-block',
        padding: '10px 14px',
        borderRadius: '12px'
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
          colorInput('Background color', props.attributes.backgroundColor, function (v) { props.setAttributes({ backgroundColor: v }); }, 'button-bg'),
          colorInput('Text color', props.attributes.textColor, function (v) { props.setAttributes({ textColor: v }); }, 'button-text'),
          el(TextControl, { key: 'btnClass', label: 'Override classes', value: props.attributes.btnClass, onChange: function (v) { props.setAttributes({ btnClass: v }); } })
        ])),
        el('div', useBlockProps({ className: 'wpbb-button-editor' }),
          label('BUTTON'),
          el('div', { className: props.attributes.btnClass || computedClass, style: style },
            el(RichText, { tagName: 'span', value: props.attributes.text, onChange: function (v) { props.setAttributes({ text: v }); } })
          )
        )
      );
    },
    save: function () { return null; }
  });

  registerBlockType('wpbb/card', { title:'Card', icon:'id', category:'wpbb', edit:containerEdit('CARD'), save:function(){ return el(InnerBlocks.Content); } });
  registerBlockType('wpbb/cards', { title:'Cards', icon:'grid-view', category:'wpbb', edit:containerEdit('CARDS',['wpbb/cta-card']), save:function(){ return el(InnerBlocks.Content); } });
  registerBlockType('wpbb/accordion', { title:'Accordion', icon:'menu', category:'wpbb', edit:containerEdit('ACCORDION',['wpbb/accordion-item']), save:function(){ return el(InnerBlocks.Content); } });
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
      csvText:{type:'string',default:'Name,Role\nJohn,Designer\nAnna,Developer'},
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
            el(SelectControl, { key:'type', label:'Type', value:field.type || 'text', options:[{label:'Text',value:'text'},{label:'Email',value:'email'},{label:'Phone',value:'phone'},{label:'Select',value:'select'},{label:'Textarea',value:'textarea'}], onChange:function(v){ updateField(index,'type',v); } }),
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
    attributes:{ title:{type:'string',default:'CTA Card'}, text:{type:'string',default:'Call to action text'}, buttonText:{type:'string',default:'Learn more'}, buttonUrl:{type:'string',default:'#'}, bgColor:{type:'string',default:''}, textColor:{type:'string',default:''}, schemaEnable:{type:'boolean',default:false}, schemaType:{type:'string',default:'CreativeWork'}, schemaPrice:{type:'string',default:''} },
    edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'CTA Card settings',initialOpen:true},[
      el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}), el(TextControl,{key:'currency',label:'Currency',value:props.attributes.currency||'€',onChange:function(v){props.setAttributes({currency:v});}}),
      el(TextControl,{key:'menuSlug',label:'Menu slug or location',help:'Example: primary or main-menu',value:props.attributes.menuSlug||'',onChange:function(v){props.setAttributes({menuSlug:v});}}), el(ToggleControl,{key:'showMenuTitle',label:'Show title',checked:!!props.attributes.showMenuTitle,onChange:function(v){props.setAttributes({showMenuTitle:v});}}),
      el(TextControl,{key:'buttonText',label:'Button text',value:props.attributes.buttonText,onChange:function(v){props.setAttributes({buttonText:v});}}),
      el(TextControl,{key:'buttonUrl',label:'Button URL',value:props.attributes.buttonUrl,onChange:function(v){props.setAttributes({buttonUrl:v});}}),
      colorInput('Background color',props.attributes.bgColor,function(v){props.setAttributes({bgColor:v});},'cta-bg'),
      colorInput('Text color',props.attributes.textColor,function(v){props.setAttributes({textColor:v});},'cta-text'), el(ToggleControl,{key:'schemaEnable',label:'Enable schema',checked:!!props.attributes.schemaEnable,onChange:function(v){props.setAttributes({schemaEnable:v});}}), el(SelectControl,{key:'schemaType',label:'Schema type',value:props.attributes.schemaType||'CreativeWork',options:[{label:'CreativeWork',value:'CreativeWork'},{label:'Product',value:'Product'},{label:'Service',value:'Service'},{label:'Organization',value:'Organization'}],onChange:function(v){props.setAttributes({schemaType:v});}}), el(TextControl,{key:'schemaPrice',label:'Schema price',value:props.attributes.schemaPrice||'',onChange:function(v){props.setAttributes({schemaPrice:v});}})
    ])), el('div',useBlockProps({className:'wpbb-cta-card card h-100',style:{background:props.attributes.bgColor||undefined,color:props.attributes.textColor||undefined}}),label('CTA CARD'),el('div',{className:'card-body'},[el('strong',{key:'t'},props.attributes.title),el('p',{key:'p'},props.attributes.text),el('span',{key:'b',className:'btn btn-primary'},props.attributes.buttonText)]))); },
    save:function(){ return null; }
  });

  registerBlockType('wpbb/cta-section', {
    title:'CTA Section', icon:'cover-image', category:'wpbb',
    attributes:{ title:{type:'string',default:'CTA Section'}, text:{type:'string',default:'Call to action text'}, buttonText:{type:'string',default:'Get started'}, buttonUrl:{type:'string',default:'#'}, bgColor:{type:'string',default:''}, textColor:{type:'string',default:''}, backgroundImage:{type:'string',default:''}, parallax:{type:'boolean',default:false} },
    edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'CTA Section settings',initialOpen:true},[
      el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}),
      el(TextareaControl,{key:'text',label:'Text',value:props.attributes.text,onChange:function(v){props.setAttributes({text:v});}}),
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
    attributes:{ embedUrl:{type:'string',default:''}, height:{type:'string',default:'380px'}, mapFilter:{type:'string',default:''} },
    edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Google Map settings',initialOpen:true},[
      el(TextControl,{key:'embedUrl',label:'Embed URL',value:props.attributes.embedUrl,onChange:function(v){props.setAttributes({embedUrl:v});}}),
      el(TextControl,{key:'height',label:'Height',value:props.attributes.height,onChange:function(v){props.setAttributes({height:v});}}),
      el(TextControl,{key:'mapFilter',label:'CSS filter',value:props.attributes.mapFilter,onChange:function(v){props.setAttributes({mapFilter:v});}})
    ])), el('div',useBlockProps({className:'wpbb-google-map'}),label('GOOGLE MAP'),props.attributes.embedUrl || 'Add embed URL')); },
    save:function(){ return null; }
  });

  registerBlockType('wpbb/menu-option', {
    title:'Menu Option', icon:'menu', category:'wpbb',
    attributes:{ title:{type:'string',default:'Menu Item'}, text:{type:'string',default:''}, badge:{type:'string',default:''}, bgColor:{type:'string',default:''}, textColor:{type:'string',default:''} },
    edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Menu Option settings',initialOpen:true},[
      el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}),
      el(TextareaControl,{key:'text',label:'Text',value:props.attributes.text,onChange:function(v){props.setAttributes({text:v});}}),
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
    ])), el('div',useBlockProps({className:'wpbb-soc-follow'}),label('SOC FOLLOW'),'FB IG LI X YT TT PI WA @')); },
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
      props.attributes.iconStyle === 'icons' ? el('span',{className:'wpbb-social-preview-icons'},['f ','x ','in ','wa ','@']) : 'Facebook X LinkedIn WhatsApp Email'
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
          el(SelectControl,{key:'style',label:'Demo style',value:props.attributes.demoStyle||'cards',options:[{label:'Cards',value:'cards'},{label:'Text',value:'text'},{label:'Minimal',value:'minimal'}],onChange:function(v){props.setAttributes({demoStyle:v});}})
        ].concat(slides.map(function(slide,index){
          return el('div',{key:'slide'+index,className:'wpbb-mini-card'},[
            el(SelectControl,{key:'type',label:'Type',value:slide.type||'text',options:[{label:'Text',value:'text'},{label:'Card',value:'card'},{label:'Video',value:'video'}],onChange:function(v){updateSlide(index,'type',v);}}),
            el(TextControl,{key:'title',label:'Title',value:slide.title||'',onChange:function(v){updateSlide(index,'title',v);}}),
            el(TextareaControl,{key:'text',label:'Text',value:slide.text||'',onChange:function(v){updateSlide(index,'text',v);}}),
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


  registerBlockType('wpbb/weather', { title:'Weather', icon:'cloud', category:'wpbb', attributes:{ title:{type:'string',default:'Laikapstākļi'}, location:{type:'string',default:'Rīga'}, lang:{type:'string',default:'lv'}, apiKey:{type:'string',default:''}, showTemp:{type:'boolean',default:true} }, edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Weather settings',initialOpen:true},[ el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}), el(TextControl,{key:'location',label:'Location',value:props.attributes.location,onChange:function(v){props.setAttributes({location:v});}}), el(SelectControl,{key:'lang',label:'Language',value:props.attributes.lang,options:[{label:'Latviešu',value:'lv'},{label:'English',value:'en'}],onChange:function(v){props.setAttributes({lang:v});}}), el(TextControl,{key:'api',label:'OpenWeather API key',value:props.attributes.apiKey||'',onChange:function(v){props.setAttributes({apiKey:v});}}), el(ToggleControl,{key:'showTemp',label:'Show temperature',checked:!!props.attributes.showTemp,onChange:function(v){props.setAttributes({showTemp:v});}}) ])), el('div',useBlockProps({className:'wpbb-weather'}),label('WEATHER'),props.attributes.location)); }, save:function(){ return null; } });

  registerBlockType('wpbb/varda-dienas', { title:'Vārda dienas', icon:'calendar-alt', category:'wpbb', attributes:{ title:{type:'string',default:'Vārda dienas'}, dateText:{type:'string',default:'Šodien'}, names:{type:'string',default:'Alise, Madara'}, namesJson:{type:'string',default:''} }, edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Vārda dienas',initialOpen:true},[ el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}), el(TextControl,{key:'dateText',label:'Date text',value:props.attributes.dateText,onChange:function(v){props.setAttributes({dateText:v});}}), el(TextControl,{key:'names',label:'Names',value:props.attributes.names,onChange:function(v){props.setAttributes({names:v});}}), el(TextareaControl,{key:'json',label:'Full year JSON',help:'Example: {"01-01":["Solvja","Reinis"]}',value:props.attributes.namesJson||'',onChange:function(v){props.setAttributes({namesJson:v});}}) ])), el('div',useBlockProps({className:'wpbb-varda-dienas'}),label('VĀRDA DIENAS'),props.attributes.names || 'Ievadi vārdus')); }, save:function(){ return null; } });

  registerBlockType('wpbb/ajax-search', { title:'Ajax Search', icon:'search', category:'wpbb', attributes:{ title:{type:'string',default:'Meklēšana'}, placeholder:{type:'string',default:'Meklēt...'}, resultsLimit:{type:'number',default:10}, searchWooBy:{type:'string',default:'title'}, sortBy:{type:'string',default:'relevance'}, showExcerpt:{type:'boolean',default:true}, showPrice:{type:'boolean',default:true}, showButton:{type:'boolean',default:true} }, edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Ajax Search',initialOpen:true},[ el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}), el(TextControl,{key:'placeholder',label:'Placeholder',value:props.attributes.placeholder,onChange:function(v){props.setAttributes({placeholder:v});}}), el(RangeControl,{key:'limit',label:'Results',value:props.attributes.resultsLimit||10,min:1,max:10,onChange:function(v){props.setAttributes({resultsLimit:v||10});}}), el(SelectControl,{key:'mode',label:'Woo search by',value:props.attributes.searchWooBy||'title',options:[{label:'Title',value:'title'},{label:'ID',value:'id'},{label:'SKU',value:'sku'}],onChange:function(v){props.setAttributes({searchWooBy:v});}}), el(SelectControl,{key:'sort',label:'Sort',value:props.attributes.sortBy||'relevance',options:[{label:'Relevance',value:'relevance'},{label:'Date',value:'date'},{label:'Title',value:'title'}],onChange:function(v){props.setAttributes({sortBy:v});}}), el(ToggleControl,{key:'showExcerpt',label:'Show excerpt',checked:props.attributes.showExcerpt!==false,onChange:function(v){props.setAttributes({showExcerpt:v});}}), el(ToggleControl,{key:'showPrice',label:'Show price',checked:props.attributes.showPrice!==false,onChange:function(v){props.setAttributes({showPrice:v});}}), el(ToggleControl,{key:'showButton',label:'Show search page button',checked:!!props.attributes.showButton,onChange:function(v){props.setAttributes({showButton:v});}}) ])), el('div',useBlockProps({className:'wpbb-ajax-search'}),label('AJAX SEARCH'),props.attributes.placeholder)); }, save:function(){ return null; } });

  registerBlockType('wpbb/pricecards', { title:'Pricecards', icon:'index-card', category:'wpbb', attributes:{ title:{type:'string',default:'Cenas'}, cardsJson:{type:'string',default:''}, styleVariant:{type:'string',default:'default'}, currency:{type:'string',default:'€'} }, edit:function(props){ var cards=[]; try{cards=JSON.parse(props.attributes.cardsJson||'[]')}catch(e){cards=[]} if(!cards.length) cards=[{title:'Basic',price:'9',period:'/mo',text:'Apraksts',button:'Izvēlēties',featured:false},{title:'Pro',price:'29',period:'/mo',text:'Apraksts',button:'Izvēlēties',featured:true}]; function save(next){ props.setAttributes({cardsJson:JSON.stringify(next)}); } function upd(i,k,v){ var next=cards.slice(); next[i]=Object.assign({}, next[i], ((o={})=>{o[k]=v; return o;})()); save(next); } function add(){ var next=cards.slice(); next.push({title:'New',price:'€0',text:'Apraksts',button:'Poga'}); save(next); } return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Pricecards',initialOpen:true},[ el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}), el(TextControl,{key:'currency',label:'Currency',value:props.attributes.currency||'€',onChange:function(v){props.setAttributes({currency:v});}}), el(SelectControl,{key:'style',label:'Style',value:props.attributes.styleVariant||'default',options:[{label:'Default',value:'default'},{label:'Soft',value:'soft'},{label:'Outline',value:'outline'}],onChange:function(v){props.setAttributes({styleVariant:v});}}) ].concat(cards.map(function(card,i){ return el('div',{key:i,className:'wpbb-mini-card'},[ el(TextControl,{key:'t',label:'Title',value:card.title||'',onChange:function(v){upd(i,'title',v);}}), el(TextControl,{key:'p',label:'Price',value:card.price||'',onChange:function(v){upd(i,'price',v);}}), el(TextareaControl,{key:'x',label:'Text',value:card.text||'',onChange:function(v){upd(i,'text',v);}}), el(TextControl,{key:'per',label:'Period',value:card.period||'',onChange:function(v){upd(i,'period',v);}}), el(ToggleControl,{key:'f',label:'Featured',checked:!!card.featured,onChange:function(v){upd(i,'featured',v);}}), el(TextControl,{key:'b',label:'Button',value:card.button||'',onChange:function(v){upd(i,'button',v);}}) ]);})).concat([el(Button,{key:'a',variant:'secondary',onClick:add},'Add card')]))), el('div',useBlockProps({className:'wpbb-pricecards'}),label('PRICECARDS'),props.attributes.title)); }, save:function(){ return null; } });

  registerBlockType('wpbb/catalogue', { title:'Catalogue', icon:'screenoptions', category:'wpbb', attributes:{ title:{type:'string',default:'Katalogs'}, category:{type:'string',default:''}, postsToShow:{type:'number',default:6}, postType:{type:'string',default:'post'}, taxonomy:{type:'string',default:'category'}, sortBy:{type:'string',default:'date'}, sortOrder:{type:'string',default:'DESC'}, showImage:{type:'boolean',default:true}, showExcerpt:{type:'boolean',default:true} }, edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Catalogue',initialOpen:true},[ el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}), el(TextControl,{key:'cat',label:'Category/term slug',value:props.attributes.category||'',onChange:function(v){props.setAttributes({category:v});}}), el(SelectControl,{key:'pt',label:'Post type',value:props.attributes.postType||'post',options:[{label:'Post',value:'post'},{label:'Portfolio',value:'portfolio'}],onChange:function(v){props.setAttributes({postType:v});}}), el(SelectControl,{key:'tax',label:'Taxonomy',value:props.attributes.taxonomy||'category',options:[{label:'Category',value:'category'},{label:'Portfolio Category',value:'portfolio_category'}],onChange:function(v){props.setAttributes({taxonomy:v});}}), el(RangeControl,{key:'pts',label:'Posts to show',value:props.attributes.postsToShow||6,min:1,max:12,onChange:function(v){props.setAttributes({postsToShow:v||6});}}), el(SelectControl,{key:'sortBy',label:'Sort by',value:props.attributes.sortBy||'date',options:[{label:'Date',value:'date'},{label:'Title',value:'title'},{label:'Menu order',value:'menu_order'}],onChange:function(v){props.setAttributes({sortBy:v});}}), el(SelectControl,{key:'sortOrder',label:'Order',value:props.attributes.sortOrder||'DESC',options:[{label:'DESC',value:'DESC'},{label:'ASC',value:'ASC'}],onChange:function(v){props.setAttributes({sortOrder:v});}}), el(ToggleControl,{key:'showImage',label:'Show image',checked:props.attributes.showImage!==false,onChange:function(v){props.setAttributes({showImage:v});}}), el(ToggleControl,{key:'showExcerpt',label:'Show excerpt',checked:props.attributes.showExcerpt!==false,onChange:function(v){props.setAttributes({showExcerpt:v});}}) ])), el('div',useBlockProps({className:'wpbb-catalogue'}),label('CATALOGUE'),props.attributes.title)); }, save:function(){ return null; } });

  registerBlockType('wpbb/code-display', { title:'Code Display', icon:'editor-code', category:'wpbb', attributes:{ title:{type:'string',default:'Code'}, code:{type:'string',default:''}, language:{type:'string',default:'html'} }, edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Code Display',initialOpen:true},[ el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}), el(SelectControl,{key:'lang',label:'Language',value:props.attributes.language||'html',options:[{label:'HTML',value:'html'},{label:'CSS',value:'css'},{label:'JS',value:'javascript'},{label:'PHP',value:'php'}],onChange:function(v){props.setAttributes({language:v});}}), el(TextareaControl,{key:'code',label:'Code',value:props.attributes.code||'',onChange:function(v){props.setAttributes({code:v});}}) ])), el('div',useBlockProps({className:'wpbb-code-display'}),label('CODE'),el('pre',{},props.attributes.code || '<code>...</code>'))); }, save:function(){ return null; } });

  registerBlockType('wpbb/countdown-timer', { title:'Countdown Timer', icon:'clock', category:'wpbb', attributes:{ title:{type:'string',default:'Countdown'}, targetDate:{type:'string',default:'2030-01-01T00:00:00'} }, edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Countdown',initialOpen:true},[ el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}), el(TextControl,{key:'date',label:'Target date',value:props.attributes.targetDate,onChange:function(v){props.setAttributes({targetDate:v});}}) ])), el('div',useBlockProps({className:'wpbb-countdown-timer'}),label('COUNTDOWN TIMER'),props.attributes.targetDate)); }, save:function(){ return null; } });

  registerBlockType('wpbb/chart', { title:'Chart', icon:'chart-bar', category:'wpbb', attributes:{ title:{type:'string',default:'Chart'}, chartType:{type:'string',default:'bar'}, chartDataJson:{type:'string',default:'{"labels":["Jan","Feb","Mar"],"datasets":[{"label":"Sales","data":[12,19,7]}]}'}, chartOptionsJson:{type:'string',default:'{"responsive":true,"plugins":{"legend":{"display":true}}}'} }, edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Chart',initialOpen:true},[ el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}), el(SelectControl,{key:'type',label:'Type',value:props.attributes.chartType||'bar',options:[{label:'Bar',value:'bar'},{label:'Line',value:'line'},{label:'Pie',value:'pie'}],onChange:function(v){props.setAttributes({chartType:v});}}), el(TextareaControl,{key:'data',label:'Chart data JSON',value:props.attributes.chartDataJson||'',help:'Example: {"labels":["Jan","Feb"],"datasets":[{"label":"Sales","data":[12,19]}]}',onChange:function(v){props.setAttributes({chartDataJson:v});}}), el(TextareaControl,{key:'opts',label:'Chart options JSON',value:props.attributes.chartOptionsJson||'',help:'Example: {"responsive":true,"plugins":{"legend":{"display":true}}}',onChange:function(v){props.setAttributes({chartOptionsJson:v});}}) ])), el('div',useBlockProps({className:'wpbb-chart'}),label('CHART'),props.attributes.title)); }, save:function(){ return null; } });

  registerBlockType('wpbb/fun-fact', { title:'Fun Fact', icon:'star-filled', category:'wpbb', attributes:{ number:{type:'string',default:'100+'}, label:{type:'string',default:'Projects'}, icon:{type:'string',default:'⭐'}, styleVariant:{type:'string',default:'default'} }, edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'Fun Fact',initialOpen:true},[ el(TextControl,{key:'num',label:'Number',value:props.attributes.number,onChange:function(v){props.setAttributes({number:v});}}), el(TextControl,{key:'label',label:'Label',value:props.attributes.label,onChange:function(v){props.setAttributes({label:v});}}), el(TextControl,{key:'icon',label:'Icon',value:props.attributes.icon,onChange:function(v){props.setAttributes({icon:v});}}), el(SelectControl,{key:'variant',label:'Style',value:props.attributes.styleVariant||'default',options:[{label:'Default',value:'default'},{label:'Soft',value:'soft'}],onChange:function(v){props.setAttributes({styleVariant:v});}}) ])), el('div',useBlockProps({className:'wpbb-fun-fact'}),label('FUN FACT'),props.attributes.number + ' ' + props.attributes.label)); }, save:function(){ return null; } });

  registerBlockType('wpbb/mailchimp', { title:'MailChimp', icon:'email', category:'wpbb', attributes:{ title:{type:'string',default:'Subscribe'}, text:{type:'string',default:'Join our newsletter'}, actionUrl:{type:'string',default:''}, audienceFieldName:{type:'string',default:'EMAIL'}, showNameField:{type:'boolean',default:false}, buttonText:{type:'string',default:'Subscribe'} }, edit:function(props){ return el(wp.element.Fragment,{}, el(InspectorControls,{},el(PanelBody,{title:'MailChimp',initialOpen:true},[ el(TextControl,{key:'title',label:'Title',value:props.attributes.title,onChange:function(v){props.setAttributes({title:v});}}), el(TextareaControl,{key:'text',label:'Text',value:props.attributes.text,onChange:function(v){props.setAttributes({text:v});}}), el(TextControl,{key:'action',label:'Action URL',value:props.attributes.actionUrl||'',onChange:function(v){props.setAttributes({actionUrl:v});}}), el(TextControl,{key:'aud',label:'Audience field name',value:props.attributes.audienceFieldName||'EMAIL',onChange:function(v){props.setAttributes({audienceFieldName:v});}}), el(ToggleControl,{key:'showName',label:'Show name field',checked:!!props.attributes.showNameField,onChange:function(v){props.setAttributes({showNameField:v});}}), el(TextControl,{key:'btn',label:'Button text',value:props.attributes.buttonText||'',onChange:function(v){props.setAttributes({buttonText:v});}}) ])), el('div',useBlockProps({className:'wpbb-mailchimp'}),label('MAILCHIMP'),props.attributes.title)); }, save:function(){ return null; } });

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
          el(TextControl,{key:'uc',label:'Additional CSS class(es)',help:'Bootstrap classes like shadow, rounded, text-center, d-flex',value:props.attributes.utilityClasses||'',onChange:function(v){props.setAttributes({utilityClasses:v});}})
        ])),
        el('div',bp,label('BOOTSTRAP DIV'), el(InnerBlocks,{allowedBlocks:['core/paragraph','core/heading','wpbb/button','wpbb/cta-card','wpbb/code-display']}))
      );
    },
    save:function(){ return el(InnerBlocks.Content); }
  });

})(window.wp);
