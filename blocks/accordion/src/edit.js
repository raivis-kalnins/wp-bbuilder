/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, useInnerBlocksProps } from '@wordpress/block-editor';
import { PanelBody, ToggleControl, SelectControl, TextControl } from '@wordpress/components';
import { useEffect } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import './editor.css';

const ALLOWED_BLOCKS = ['dp-blocks/accordion-item'];

const DEFAULT_TEMPLATE = [
    ['dp-blocks/accordion-item', { title: __('Accordion Item #1', 'dp-blocks') }],
    ['dp-blocks/accordion-item', { title: __('Accordion Item #2', 'dp-blocks') }],
];

const SPACING_OPTIONS = [
    { label: __('None', 'dp-blocks'), value: '' },
    { label: __('Extra Small (mb-1)', 'dp-blocks'), value: 'mb-1' },
    { label: __('Small (mb-2)', 'dp-blocks'), value: 'mb-2' },
    { label: __('Medium (mb-3)', 'dp-blocks'), value: 'mb-3' },
    { label: __('Large (mb-4)', 'dp-blocks'), value: 'mb-4' },
    { label: __('Extra Large (mb-5)', 'dp-blocks'), value: 'mb-5' },
];

export default function Edit({ attributes, setAttributes, clientId }) {
    const { accordionId, alwaysOpen, flush, firstItemOpen, itemSpacing } = attributes;

    // Generate unique accordion ID if not set
    useEffect(() => {
        if (!accordionId) {
            setAttributes({ accordionId: `accordion-${clientId.slice(0, 8)}` });
        }
    }, [accordionId, clientId, setAttributes]);

    // Get inner blocks count for visual indicator
    const innerBlocksCount = useSelect(
        (select) => select('core/block-editor').getBlockCount(clientId),
        [clientId]
    );

    const blockProps = useBlockProps({
        className: `dp-accordion-editor ${flush ? 'accordion-flush' : ''}`,
    });

    const innerBlocksProps = useInnerBlocksProps(blockProps, {
        allowedBlocks: ALLOWED_BLOCKS,
        template: DEFAULT_TEMPLATE,
        templateInsertUpdatesSelection: false,
        renderAppender: true,
    });

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Accordion Settings', 'dp-blocks')} initialOpen={true}>
                    <ToggleControl
                        label={__('Always Open', 'dp-blocks')}
                        help={
                            alwaysOpen
                                ? __('Multiple items can be open at once', 'dp-blocks')
                                : __('Only one item can be open at a time', 'dp-blocks')
                        }
                        checked={alwaysOpen}
                        onChange={(value) => setAttributes({ alwaysOpen: value })}
                    />
                    <ToggleControl
                        label={__('Flush Style', 'dp-blocks')}
                        help={
                            flush
                                ? __('Remove default background and borders', 'dp-blocks')
                                : __('Use default accordion styling', 'dp-blocks')
                        }
                        checked={flush}
                        onChange={(value) => setAttributes({ flush: value })}
                    />
                    <ToggleControl
                        label={__('First Item Open', 'dp-blocks')}
                        help={
                            firstItemOpen
                                ? __('First accordion item starts expanded', 'dp-blocks')
                                : __('All items start collapsed', 'dp-blocks')
                        }
                        checked={firstItemOpen}
                        onChange={(value) => setAttributes({ firstItemOpen: value })}
                    />
                    <SelectControl
                        label={__('Item Spacing', 'dp-blocks')}
                        value={itemSpacing}
                        options={SPACING_OPTIONS}
                        onChange={(value) => setAttributes({ itemSpacing: value })}
                        help={__('Space between accordion items', 'dp-blocks')}
                    />
                    <TextControl
                        label={__('Accordion ID', 'dp-blocks')}
                        value={accordionId}
                        onChange={(value) => setAttributes({ accordionId: value })}
                        help={__('Unique identifier for this accordion', 'dp-blocks')}
                    />
                </PanelBody>
            </InspectorControls>

            <div {...innerBlocksProps}>
                <div className="dp-accordion-header">
                    <span className="dp-accordion-icon">📋</span>
                    <span className="dp-accordion-title">
                        {__('Bootstrap Accordion', 'dp-blocks')}
                    </span>
                    <span className="dp-accordion-count">
                        {innerBlocksCount} {innerBlocksCount === 1 ? __('item', 'dp-blocks') : __('items', 'dp-blocks')}
                    </span>
                </div>
                {innerBlocksProps.children}
            </div>
        </>
    );
}