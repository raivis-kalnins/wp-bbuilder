import { __ } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl, ToggleControl, TabPanel, SelectControl } from '@wordpress/components';

const BREAKPOINTS = [
    { name: 'xs', label: __('Mobile (<576px)', 'wp-bblocks') },
    { name: 'sm', label: __('Small (≥576px)', 'wp-bblocks') },
    { name: 'md', label: __('Medium (≥768px)', 'wp-bblocks') },
    { name: 'lg', label: __('Large (≥992px)', 'wp-bblocks') },
    { name: 'xl', label: __('Extra Large (≥1200px)', 'wp-bblocks') },
    { name: 'xxl', label: __('XXL (≥1400px)', 'wp-bblocks') }
];

export default function Edit({ attributes, setAttributes, context }) {
    const { xs, sm, md, lg, xl, xxl, equalWidth, offset, order } = attributes;
    const isCssGrid = context['wpbb/rowIsCssGrid'] || false;
    
    const getClasses = () => {
        const classes = ['wpbb-column'];
        
        if (isCssGrid) {
            if (xs) classes.push(`g-col-${xs}`);
            if (sm) classes.push(`g-col-sm-${sm}`);
            if (md) classes.push(`g-col-md-${md}`);
            if (lg) classes.push(`g-col-lg-${lg}`);
            if (xl) classes.push(`g-col-xl-${xl}`);
            if (xxl) classes.push(`g-col-xxl-${xxl}`);
        } else {
            BREAKPOINTS.forEach(({ name }) => {
                if (equalWidth[name]) {
                    classes.push(name === 'xs' ? 'col' : `col-${name}`);
                } else if (attributes[name]) {
                    classes.push(name === 'xs' ? `col-${attributes[name]}` : `col-${name}-${attributes[name]}`);
                }
            });
            
            Object.entries(offset).forEach(([bp, val]) => {
                if (val > 0) classes.push(bp === 'xs' ? `offset-${val}` : `offset-${bp}-${val}`);
            });
            
            Object.entries(order).forEach(([bp, val]) => {
                if (val !== '') classes.push(bp === 'xs' ? `order-${val}` : `order-${bp}-${val}`);
            });
        }
        
        return classes.join(' ') || 'col-12';
    };
    
    const blockProps = useBlockProps({ className: getClasses() });
    const innerProps = useInnerBlocksProps({}, { renderAppender: InnerBlocks.ButtonBlockAppender });
    
    const SizeControl = ({ bp }) => (
        <div style={{ marginBottom: '20px' }}>
            <ToggleControl
                label={__('Equal width', 'wp-bblocks')}
                checked={equalWidth[bp]}
                onChange={v => setAttributes({ equalWidth: { ...equalWidth, [bp]: v } })}
            />
            {!equalWidth[bp] && (
                <RangeControl
                    label={__('Columns (1-12)', 'wp-bblocks')}
                    value={attributes[bp] || 0}
                    onChange={v => setAttributes({ [bp]: v || 0 })}
                    min={1}
                    max={12}
                />
            )}
        </div>
    );
    
    return (
        <>
            <InspectorControls>
                <TabPanel
                    tabs={[
                        { name: 'sizes', title: __('Sizes', 'wp-bblocks') },
                        ...(!isCssGrid ? [{ name: 'offset', title: __('Offset', 'wp-bblocks') }, { name: 'order', title: __('Order', 'wp-bblocks') }] : [])
                    ]}
                >
                    {tab => {
                        if (tab.name === 'sizes') {
                            return (
                                <PanelBody title={__('Column Sizes', 'wp-bblocks')} initialOpen={true}>
                                    {BREAKPOINTS.map(({ name, label }) => (
                                        <div key={name}>
                                            <strong>{label}</strong>
                                            <SizeControl bp={name} />
                                        </div>
                                    ))}
                                </PanelBody>
                            );
                        }
                        if (tab.name === 'offset') {
                            return (
                                <PanelBody title={__('Offset', 'wp-bblocks')} initialOpen={true}>
                                    {BREAKPOINTS.map(({ name, label }) => (
                                        <RangeControl
                                            key={name}
                                            label={label}
                                            value={offset[name]}
                                            onChange={v => setAttributes({ offset: { ...offset, [name]: v } })}
                                            min={0}
                                            max={11}
                                        />
                                    ))}
                                </PanelBody>
                            );
                        }
                        if (tab.name === 'order') {
                            return (
                                <PanelBody title={__('Order', 'wp-bblocks')} initialOpen={true}>
                                    {BREAKPOINTS.map(({ name, label }) => (
                                        <SelectControl
                                            key={name}
                                            label={label}
                                            value={order[name]}
                                            options={[
                                                { label: __('Default', 'wp-bblocks'), value: '' },
                                                { label: __('First', 'wp-bblocks'), value: 'first' },
                                                { label: __('Last', 'wp-bblocks'), value: 'last' },
                                                ...Array.from({length:12},(_,i)=>({label:i+1,value:i+1}))
                                            ]}
                                            onChange={v => setAttributes({ order: { ...order, [name]: v } })}
                                        />
                                    ))}
                                </PanelBody>
                            );
                        }
                        return null;
                    }}
                </TabPanel>
            </InspectorControls>
            
            <div {...blockProps}>
                <div className="wpbb-column__label">{getColumnLabel()}</div>
                <div {...innerProps} />
            </div>
        </>
    );
    
    function getColumnLabel() {
        if (isCssGrid) return `g-col-${xs}`;
        const parts = [];
        if (equalWidth.xs || !xs) parts.push('col');
        else parts.push(`col-${xs}`);
        if (md) parts.push(`md-${md}`);
        return parts.join(' ');
    }
}