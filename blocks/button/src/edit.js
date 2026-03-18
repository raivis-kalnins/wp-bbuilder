import { useState, useEffect } from '@wordpress/element';
import { useBlockProps, InspectorControls, RichText } from '@wordpress/block-editor';
import { 
  PanelBody, 
  PanelRow, 
  TextControl, 
  ToggleControl, 
  Button, 
  ButtonGroup,
  TokenItem,
  FormTokenField,
  SelectControl
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const VARIANTS = [
  { label: 'Primary', value: 'primary', color: '#0d6efd' },
  { label: 'Secondary', value: 'secondary', color: '#6c757d' },
  { label: 'Success', value: 'success', color: '#198754' },
  { label: 'Danger', value: 'danger', color: '#dc3545' },
  { label: 'Warning', value: 'warning', color: '#ffc107' },
  { label: 'Info', value: 'info', color: '#0dcaf0' },
  { label: 'Light', value: 'light', color: '#f8f9fa' },
  { label: 'Dark', value: 'dark', color: '#212529' },
  { label: 'Link', value: 'link', color: '#0d6efd' }
];

const SIZES = [
  { label: 'Default', value: '' },
  { label: 'Small', value: 'btn-sm' },
  { label: 'Large', value: 'btn-lg' }
];

const MODIFIERS = [
  { label: 'Outline', value: 'btn-outline-', type: 'variant' },
  { label: 'Block Level', value: 'd-block w-100', type: 'utility' },
  { label: 'Disabled', value: 'disabled', type: 'state' },
  { label: 'Active', value: 'active', type: 'state' },
  { label: 'Pill', value: 'rounded-pill', type: 'shape' },
  { label: 'Shadow', value: 'shadow', type: 'utility' },
  { label: 'Shadow LG', value: 'shadow-lg', type: 'utility' },
  { label: 'Bold', value: 'fw-bold', type: 'text' },
  { label: 'Uppercase', value: 'text-uppercase', type: 'text' }
];

export default function Edit({ attributes, setAttributes }) {
  const { text, url, classes, variant, size, openInNewTab } = attributes;
  const [customClassInput, setCustomClassInput] = useState('');
  
  const blockProps = useBlockProps({
    className: 'bootstrap-button-block-editor'
  });

  // Build class string for preview
  const classString = classes.join(' ');
  
  // Check if outline mode
  const isOutline = classes.some(c => c.startsWith('btn-outline-'));
  
  // Get available suggestions for FormTokenField
  const classSuggestions = [
    'rounded-pill', 'rounded-0', 'shadow', 'shadow-sm', 'shadow-lg',
    'fw-bold', 'fw-normal', 'fst-italic', 'text-uppercase', 'text-lowercase',
    'd-block', 'w-100', 'w-50', 'w-75', 'w-25',
    'disabled', 'active', 'collapsed'
  ];

  const updateClasses = (newClasses) => {
    setAttributes({ classes: [...new Set(newClasses)] });
  };

  const addClass = (className) => {
    if (className && !classes.includes(className)) {
      updateClasses([...classes, className]);
    }
  };

  const removeClass = (className) => {
    if (className === 'btn') return; // Protect base
    updateClasses(classes.filter(c => c !== className));
  };

  const setVariant = (newVariant) => {
    // Remove old variant classes
    const cleaned = classes.filter(c => 
      !c.match(/^btn-(primary|secondary|success|danger|warning|info|light|dark|link)$/) &&
      !c.match(/^btn-outline-(.*)$/)
    );
    
    // Add new variant
    const prefix = isOutline ? 'btn-outline-' : 'btn-';
    updateClasses([...cleaned, `${prefix}${newVariant}`]);
    setAttributes({ variant: newVariant });
  };

  const toggleOutline = () => {
    const currentVariantClass = classes.find(c => 
      c.match(/^btn-(primary|secondary|success|danger|warning|info|light|dark)$/)
    );
    const currentOutlineClass = classes.find(c => 
      c.match(/^btn-outline-(.*)$/)
    );
    
    if (currentOutlineClass) {
      // Switch to solid
      const v = currentOutlineClass.replace('btn-outline-', '');
      removeClass(currentOutlineClass);
      addClass(`btn-${v}`);
    } else if (currentVariantClass) {
      // Switch to outline
      const v = currentVariantClass.replace('btn-', '');
      removeClass(currentVariantClass);
      addClass(`btn-outline-${v}`);
    }
  };

  const setSize = (newSize) => {
    const cleaned = classes.filter(c => c !== 'btn-sm' && c !== 'btn-lg');
    if (newSize) {
      updateClasses([...cleaned, newSize]);
    } else {
      updateClasses(cleaned);
    }
    setAttributes({ size: newSize });
  };

  const toggleModifier = (modifier) => {
    if (modifier.includes('outline')) {
      toggleOutline();
      return;
    }
    
    if (classes.includes(modifier)) {
      removeClass(modifier);
    } else {
      addClass(modifier);
    }
  };

  const handleCustomClassAdd = () => {
    if (customClassInput.trim()) {
      // Split by space or comma for multiple classes
      const newClasses = customClassInput.split(/[\s,]+/).filter(c => c.length > 0);
      newClasses.forEach(c => addClass(c));
      setCustomClassInput('');
    }
  };

  const onTokensChange = (tokens) => {
    // Keep base btn class
    const baseClasses = classes.filter(c => c === 'btn' || c.startsWith('btn-') || c.startsWith('btn-outline-'));
    const customClasses = tokens.filter(t => !classes.includes(t));
    updateClasses([...baseClasses, ...customClasses]);
  };

  // Filter only custom utility classes for tokens (exclude bootstrap structural)
  const tokenClasses = classes.filter(c => 
    !c.startsWith('btn-') || c === 'btn'
  );

  return (
    <div {...blockProps}>
      <InspectorControls>
        <PanelBody title={__('Button Settings', 'bootstrap-blocks')} initialOpen={true}>
          <TextControl
            label={__('URL', 'bootstrap-blocks')}
            value={url}
            onChange={(val) => setAttributes({ url: val })}
            placeholder="https://..."
          />
          
          <ToggleControl
            label={__('Open in new tab', 'bootstrap-blocks')}
            checked={openInNewTab}
            onChange={(val) => setAttributes({ openInNewTab: val })}
          />
        </PanelBody>

        <PanelBody title={__('Styles', 'bootstrap-blocks')} initialOpen={true}>
          <div className="bootstrap-btn-variants">
            <label className="components-base-control__label">
              {__('Variant', 'bootstrap-blocks')}
            </label>
            <div className="variant-grid">
              {VARIANTS.map((v) => (
                <button
                  key={v.value}
                  className={`variant-btn ${
                    (classes.includes(`btn-${v.value}`) || classes.includes(`btn-outline-${v.value}`)) 
                    ? 'is-selected' 
                    : ''
                  } ${classes.includes(`btn-outline-${v.value}`) ? 'is-outline' : ''}`}
                  onClick={() => setVariant(v.value)}
                  style={{ '--btn-color': v.color }}
                  title={v.label}
                >
                  <span className="color-dot" style={{ backgroundColor: v.color }}></span>
                  {v.label}
                </button>
              ))}
            </div>
          </div>

          <SelectControl
            label={__('Size', 'bootstrap-blocks')}
            value={size}
            options={SIZES}
            onChange={setSize}
          />

          <div className="modifier-group">
            <label className="components-base-control__label">
              {__('Modifiers', 'bootstrap-blocks')}
            </label>
            <ButtonGroup>
              {MODIFIERS.map((mod) => (
                <Button
                  key={mod.value}
                  isPressed={classes.includes(mod.value) || (mod.type === 'variant' && isOutline)}
                  onClick={() => toggleModifier(mod.value)}
                  className="modifier-btn"
                  variant={classes.includes(mod.value) ? 'primary' : 'secondary'}
                  isSecondary={!classes.includes(mod.value) && !(mod.type === 'variant' && isOutline)}
                >
                  {mod.label}
                </Button>
              ))}
            </ButtonGroup>
          </div>
        </PanelBody>

        <PanelBody title={__('Advanced', 'bootstrap-blocks')} initialOpen={false}>
          <div className="custom-classes-section">
            <label className="components-base-control__label">
              {__('Additional CSS Classes', 'bootstrap-blocks')}
            </label>
            
            <div className="class-input-row">
              <TextControl
                value={customClassInput}
                onChange={setCustomClassInput}
                placeholder="e.g., rounded-pill shadow"
                onKeyDown={(e) => e.key === 'Enter' && handleCustomClassAdd()}
                className="class-input"
              />
              <Button isPrimary onClick={handleCustomClassAdd}>
                {__('Add', 'bootstrap-blocks')}
              </Button>
            </div>

            <FormTokenField
              label={__('Active Classes', 'bootstrap-blocks')}
              value={tokenClasses}
              suggestions={classSuggestions}
              onChange={onTokensChange}
              placeholder={__('Type to add classes...', 'bootstrap-blocks')}
            />
            
            <div className="class-list">
              {classes.map((cls) => (
                <span 
                  key={cls} 
                  className={`class-tag ${cls === 'btn' ? 'is-base' : ''}`}
                >
                  {cls}
                  {cls !== 'btn' && (
                    <button 
                      className="remove-class" 
                      onClick={() => removeClass(cls)}
                      aria-label={__('Remove class', 'bootstrap-blocks')}
                    >
                      ×
                    </button>
                  )}
                </span>
              ))}
            </div>
          </div>

          <PanelRow>
            <code className="generated-classes" style={{ 
              background: '#f0f0f1', 
              padding: '8px', 
              borderRadius: '4px',
              fontSize: '11px',
              wordBreak: 'break-all'
            }}>
              {classString}
            </code>
          </PanelRow>
        </PanelBody>
      </InspectorControls>

      <div className="bootstrap-button-preview">
        <div className="preview-label">{__('Preview', 'bootstrap-blocks')}</div>
        <RichText
          tagName="a"
          className={classString}
          href={url}
          value={text}
          onChange={(val) => setAttributes({ text: val })}
          placeholder={__('Button text...', 'bootstrap-blocks')}
          allowedFormats={['core/bold', 'core/italic']}
        />
      </div>

      <div className="bootstrap-code-preview">
        <code>{`<a class="${classString}" href="${url}">${text}</a>`}</code>
      </div>
    </div>
  );
}