/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, useInnerBlocksProps } from '@wordpress/block-editor';
import { PanelBody, RangeControl, SelectControl, ToggleControl, Button } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */
import './editor.css';

const ALLOWED_BLOCKS = ['dp-blocks/card'];

const DEFAULT_TEMPLATE = [
    ['dp-blocks/card'],
    ['dp-blocks/card'],
    ['dp-blocks/card'],
];

const GUTTER_OPTIONS = [
    { label: __('None (g-0)', 'dp-blocks'), value: 'g-0' },
    { label: __('Extra Small (g-1)', 'dp-blocks'), value: 'g-1' },
    { label: __('Small (g-2)', 'dp-blocks'), value: 'g-2' },
    { label: __('Medium (g-3)', 'dp-blocks'), value: 'g-3' },
    { label: __('Large (g-4)', 'dp-blocks'), value: 'g-4' },
    { label: __('Extra Large (g-5)', 'dp-blocks'), value: 'g-5' },
];

export default function Edit({ attributes, setAttributes, clientId }) {
    const { columns, columnsTablet, columnsMobile, gutter, equalHeight, cardGroup, cardDeck } = attributes;

    // Get inner blocks
    const innerBlocks = useSelect(
        (select) => select('core/block-editor').getBlocks(clientId),
        [clientId]
    );

    const { insertBlock } = useDispatch('core/block-editor');

    const blockProps = useBlockProps({
        className: `dp-cards-editor row ${gutter}`,
    });

    const innerBlocksProps = useInnerBlocksProps(blockProps, {
        allowedBlocks: ALLOWED_BLOCKS,
        template: DEFAULT_TEMPLATE,
        templateInsertUpdatesSelection: false,
        renderAppender: false,
    });

    // Calculate column classes
    const getColumnClass = () => {
        const classes = [];
        if (columnsMobile) classes.push(`col-${Math.floor(12 / columnsMobile)}`);
        if (columnsTablet) classes.push(`col-sm-${Math.floor(12 / columnsTablet)}`);
        if (columns) classes.push(`col-md-${Math.floor(12 / columns)}`);
        return classes.join(' ');
    };

    const addCard = () => {
        const newBlock = wp.blocks.createBlock('dp-blocks/card');
        insertBlock(newBlock, innerBlocks.length, clientId);
    };

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Cards Grid Settings', 'dp-blocks')} initialOpen={true}>
                    <RangeControl
                        label={__('Columns (Desktop)', 'dp-blocks')}
                        value={columns}
                        onChange={(value) => setAttributes({ columns: value })}
                        min={1}
                        max={6}
                        help={__('Number of cards per row on desktop', 'dp-blocks')}
                    />
                    <RangeControl
                        label={__('Columns (Tablet)', 'dp-blocks')}
                        value={columnsTablet}
                        onChange={(value) => setAttributes({ columnsTablet: value })}
                        min={1}
                        max={4}
                        help={__('Number of cards per row on tablet', 'dp-blocks')}
                    />
                    <RangeControl
                        label={__('Columns (Mobile)', 'dp-blocks')}
                        value={columnsMobile}
                        onChange={(value) => setAttributes({ columnsMobile: value })}
                        min={1}
                        max={2}
                        help={__('Number of cards per row on mobile', 'dp-blocks')}
                    />
                    <SelectControl
                        label={__('Gutter Size', 'dp-blocks')}
                        value={gutter}
                        options={GUTTER_OPTIONS}
                        onChange={(value) => setAttributes({ gutter: value })}
                    />
                    <ToggleControl
                        label={__('Equal Height Cards', 'dp-blocks')}
                        help={__('Make all cards the same height', 'dp-blocks')}
                        checked={equalHeight}
                        onChange={(value) => setAttributes({ equalHeight: value })}
                    />
                    <ToggleControl
                        label={__('Card Group', 'dp-blocks')}
                        help={__('Remove gutters and rounded corners between cards', 'dp-blocks')}
                        checked={cardGroup}
                        onChange={(value) => setAttributes({ cardGroup: value })}
                    />
                    <ToggleControl
                        label={__('Card Deck (Legacy)', 'dp-blocks')}
                        help={__('Use card deck layout (Bootstrap 4 style)', 'dp-blocks')}
                        checked={cardDeck}
                        onChange={(value) => setAttributes({ cardDeck: value })}
                    />
                </PanelBody>
            </InspectorControls>

            <div className="dp-cards-wrapper">
                <div className="dp-cards-header">
                    <span className="dp-cards-icon">🃏</span>
                    <span className="dp-cards-title">
                        {__('Bootstrap Cards Grid', 'dp-blocks')}
                    </span>
                    <span className="dp-cards-count">
                        {innerBlocks.length} {innerBlocks.length === 1 ? __('card', 'dp-blocks') : __('cards', 'dp-blocks')}
                    </span>
                </div>
                <div {...innerBlocksProps} />
                <Button
                    variant="secondary"
                    className="dp-cards-add-button"
                    onClick={addCard}
                    icon="plus"
                >
                    {__('Add Card', 'dp-blocks')}
                </Button>
            </div>
        </>
    );
}