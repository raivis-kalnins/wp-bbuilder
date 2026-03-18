/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, useInnerBlocksProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl, TextControl, Button, ButtonGroup } from '@wordpress/components';
import { useEffect } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */
import './editor.css';

const ALLOWED_BLOCKS = ['dp-blocks/tab-item'];

const DEFAULT_TEMPLATE = [
    ['dp-blocks/tab-item', { title: __('Tab 1', 'dp-blocks') }],
    ['dp-blocks/tab-item', { title: __('Tab 2', 'dp-blocks') }],
    ['dp-blocks/tab-item', { title: __('Tab 3', 'dp-blocks') }],
];

const TYPE_OPTIONS = [
    { label: __('Tabs', 'dp-blocks'), value: 'tabs' },
    { label: __('Pills', 'dp-blocks'), value: 'pills' },
    { label: __('Underline', 'dp-blocks'), value: 'underline' },
];

const ALIGNMENT_OPTIONS = [
    { label: __('Default', 'dp-blocks'), value: '' },
    { label: __('Left', 'dp-blocks'), value: 'start' },
    { label: __('Center', 'dp-blocks'), value: 'center' },
    { label: __('Right', 'dp-blocks'), value: 'end' },
];

export default function Edit({ attributes, setAttributes, clientId }) {
    const { tabsId, type, alignment, vertical, fill, justify, fadeEffect, activeTab } = attributes;

    // Generate unique tabs ID if not set
    useEffect(() => {
        if (!tabsId) {
            setAttributes({ tabsId: `tabs-${clientId.slice(0, 8)}` });
        }
    }, [tabsId, clientId, setAttributes]);

    // Get inner blocks
    const innerBlocks = useSelect(
        (select) => select('core/block-editor').getBlocks(clientId),
        [clientId]
    );

    const { selectBlock } = useDispatch('core/block-editor');

    const blockProps = useBlockProps({
        className: `dp-tabs-editor dp-tabs-${type}`,
    });

    const innerBlocksProps = useInnerBlocksProps(
        { className: 'dp-tab-content-editor' },
        {
            allowedBlocks: ALLOWED_BLOCKS,
            template: DEFAULT_TEMPLATE,
            templateInsertUpdatesSelection: false,
            renderAppender: false,
        }
    );

    // Handle tab click in editor
    const handleTabClick = (index, tabClientId) => {
        setAttributes({ activeTab: index });
        selectBlock(tabClientId);
    };

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Tabs Settings', 'dp-blocks')} initialOpen={true}>
                    <SelectControl
                        label={__('Tab Type', 'dp-blocks')}
                        value={type}
                        options={TYPE_OPTIONS}
                        onChange={(value) => setAttributes({ type: value })}
                    />
                    <SelectControl
                        label={__('Alignment', 'dp-blocks')}
                        value={alignment}
                        options={ALIGNMENT_OPTIONS}
                        onChange={(value) => setAttributes({ alignment: value })}
                    />
                    <ToggleControl
                        label={__('Vertical Layout', 'dp-blocks')}
                        help={vertical ? __('Tabs displayed vertically', 'dp-blocks') : __('Tabs displayed horizontally', 'dp-blocks')}
                        checked={vertical}
                        onChange={(value) => setAttributes({ vertical: value })}
                    />
                    <ToggleControl
                        label={__('Fill Available Space', 'dp-blocks')}
                        help={__('Make tabs fill the entire width', 'dp-blocks')}
                        checked={fill}
                        onChange={(value) => setAttributes({ fill: value })}
                    />
                    <ToggleControl
                        label={__('Justify', 'dp-blocks')}
                        help={__('Distribute tabs evenly with equal space', 'dp-blocks')}
                        checked={justify}
                        onChange={(value) => setAttributes({ justify: value })}
                    />
                    <ToggleControl
                        label={__('Fade Effect', 'dp-blocks')}
                        help={__('Add fade animation to tab transitions', 'dp-blocks')}
                        checked={fadeEffect}
                        onChange={(value) => setAttributes({ fadeEffect: value })}
                    />
                    <TextControl
                        label={__('Tabs ID', 'dp-blocks')}
                        value={tabsId}
                        onChange={(value) => setAttributes({ tabsId: value })}
                        help={__('Unique identifier for this tabs component', 'dp-blocks')}
                    />
                </PanelBody>
            </InspectorControls>

            <div {...blockProps}>
                {/* Tab Navigation */}
                <div className={`dp-tab-nav-editor nav nav-${type} ${alignment ? `justify-content-${alignment}` : ''} ${vertical ? 'flex-column' : ''} ${fill ? 'nav-fill' : ''} ${justify ? 'nav-justified' : ''}`}>
                    {innerBlocks.map((block, index) => (
                        <button
                            key={block.clientId}
                            type="button"
                            className={`dp-tab-link-editor nav-link ${index === activeTab ? 'active' : ''}`}
                            onClick={() => handleTabClick(index, block.clientId)}
                        >
                            {block.attributes.icon && (
                                <span className="dp-tab-icon">{block.attributes.icon}</span>
                            )}
                            <span>{block.attributes.title || __('Tab', 'dp-blocks')}</span>
                        </button>
                    ))}
                    <Button
                        variant="secondary"
                        size="small"
                        className="dp-tab-add-button"
                        onClick={() => {
                            const newBlock = wp.blocks.createBlock('dp-blocks/tab-item', {
                                title: `${__('Tab', 'dp-blocks')} ${innerBlocks.length + 1}`,
                            });
                            wp.data.dispatch('core/block-editor').insertBlock(newBlock, innerBlocks.length, clientId);
                        }}
                    >
                        +
                    </Button>
                </div>

                {/* Tab Content */}
                <div {...innerBlocksProps} />
            </div>
        </>
    );
}