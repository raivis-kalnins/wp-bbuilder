import { __ } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps, BlockControls, InspectorControls } from '@wordpress/block-editor';
import { ToolbarButton, ToolbarGroup, PanelBody, ToggleControl, SelectControl, Button } from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';
import { layout, grid } from '@wordpress/icons';
import { useDispatch, useSelect } from '@wordpress/data';
import { ROW_TEMPLATES, GUTTER_OPTIONS, ALIGN_OPTIONS, VERTICAL_ALIGN_OPTIONS } from './templates';

export default function Edit({ attributes, setAttributes, clientId }) {
    const { template, noGutters, horizontalGutters, verticalGutters, alignment, verticalAlignment, isCssGrid, gridColumns, editorStackColumns } = attributes;
    const [showPicker, setShowPicker] = useState(false);
    
    const { replaceInnerBlocks } = useDispatch('core/block-editor');
    const innerBlocks = useSelect(select => select('core/block-editor').getBlock(clientId)?.innerBlocks || [], [clientId]);
    
    useEffect(() => {
        if (template !== 'custom' && innerBlocks.length === 0) {
            const tpl = ROW_TEMPLATES.find(t => t.name === template);
            if (tpl) {
                replaceInnerBlocks(clientId, tpl.columns.map(([n, a]) => wp.blocks.createBlock(n, a)));
            }
        }
    }, [template, clientId]);
    
    const classes = ['wpbb-row'];
    if (isCssGrid) {
        classes.push('grid');
        if (gridColumns) classes.push(`grid-cols-${gridColumns}`);
    } else {
        classes.push('row');
        if (noGutters) classes.push('g-0');
        else {
            if (horizontalGutters) classes.push(horizontalGutters);
            if (verticalGutters) classes.push(verticalGutters);
        }
        if (alignment) classes.push(`justify-content-${alignment}`);
        if (verticalAlignment) classes.push(`align-items-${verticalAlignment}`);
    }
    if (editorStackColumns) classes.push('is-stacked');
    
    const blockProps = useBlockProps({ className: classes.join(' ') });
    const innerProps = useInnerBlocksProps({ className: isCssGrid ? 'grid-inner' : 'row-inner' }, {
        allowedBlocks: ['wpbb/column'],
        renderAppender: innerBlocks.length ? undefined : InnerBlocks.ButtonBlockAppender
    });
    
    return (
        <>
            <BlockControls>
                <ToolbarGroup>
                    <ToolbarButton icon={layout} label={__('Layout', 'wp-bblocks')} onClick={() => setShowPicker(true)} />
                </ToolbarGroup>
                <ToolbarGroup>
                    <ToolbarButton icon={grid} label={__('CSS Grid', 'wp-bblocks')} isPressed={isCssGrid} onClick={() => setAttributes({ isCssGrid: !isCssGrid })} />
                </ToolbarGroup>
            </BlockControls>
            
            <InspectorControls>
                <PanelBody title={__('Settings', 'wp-bblocks')}>
                    <SelectControl label={__('Template', 'wp-bblocks')} value={template} options={[{label:__('Custom','wp-bblocks'),value:'custom'},...ROW_TEMPLATES.map(t=>({label:t.label,value:t.name}))]} onChange={v=>setAttributes({template:v})} />
                    <ToggleControl label={__('CSS Grid', 'wp-bblocks')} checked={isCssGrid} onChange={v=>setAttributes({isCssGrid:v})} />
                    {isCssGrid && <SelectControl label={__('Grid Columns', 'wp-bblocks')} value={gridColumns} options={[12,6,4,3,2,1].map(n=>({label:n,value:n}))} onChange={v=>setAttributes({gridColumns:parseInt(v)})} />}
                </PanelBody>
                {!isCssGrid && (
                    <PanelBody title={__('Gutters', 'wp-bblocks')} initialOpen={false}>
                        <ToggleControl label={__('No Gutters', 'wp-bblocks')} checked={noGutters} onChange={v=>setAttributes({noGutters:v})} />
                        {!noGutters && (
                            <>
                                <SelectControl label={__('Horizontal', 'wp-bblocks')} value={horizontalGutters} options={GUTTER_OPTIONS} onChange={v=>setAttributes({horizontalGutters:v})} />
                                <SelectControl label={__('Vertical', 'wp-bblocks')} value={verticalGutters} options={[{label:__('Default','wp-bblocks'),value:''},...GUTTER_OPTIONS]} onChange={v=>setAttributes({verticalGutters:v})} />
                            </>
                        )}
                    </PanelBody>
                )}
                {!isCssGrid && (
                    <PanelBody title={__('Alignment', 'wp-bblocks')} initialOpen={false}>
                        <SelectControl label={__('Horizontal', 'wp-bblocks')} value={alignment} options={[{label:__('Default','wp-bblocks'),value:''},...ALIGN_OPTIONS]} onChange={v=>setAttributes({alignment:v})} />
                        <SelectControl label={__('Vertical', 'wp-bblocks')} value={verticalAlignment} options={[{label:__('Default','wp-bblocks'),value:''},...VERTICAL_ALIGN_OPTIONS]} onChange={v=>setAttributes({verticalAlignment:v})} />
                    </PanelBody>
                )}
                <PanelBody title={__('Editor', 'wp-bblocks')} initialOpen={false}>
                    <ToggleControl label={__('Stack Columns', 'wp-bblocks')} checked={editorStackColumns} onChange={v=>setAttributes({editorStackColumns:v})} />
                </PanelBody>
            </InspectorControls>
            
            {showPicker && (
                <div className="wpbb-modal" onClick={()=>setShowPicker(false)}>
                    <div className="wpbb-modal__content" onClick={e=>e.stopPropagation()}>
                        <h3>{__('Choose Layout', 'wp-bblocks')}</h3>
                        <div className="wpbb-templates">
                            {ROW_TEMPLATES.map(tpl => (
                                <button key={tpl.name} className="wpbb-template-btn" onClick={()=>{setAttributes({template:tpl.name});setShowPicker(false);}}>
                                    <span className="wpbb-template-btn__icon">{tpl.columns.length} cols</span>
                                    <span className="wpbb-template-btn__label">{tpl.label}</span>
                                </button>
                            ))}
                        </div>
                        <Button isSecondary onClick={()=>setShowPicker(false)}>{__('Cancel', 'wp-bblocks')}</Button>
                    </div>
                </div>
            )}
            
            <div {...blockProps}>
                <div className="wpbb-row__label">Row {innerBlocks.length ? `(${innerBlocks.length})` : ''}</div>
                <div {...innerProps} />
            </div>
        </>
    );
}