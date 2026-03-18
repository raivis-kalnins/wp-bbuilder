/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText, InspectorControls, useInnerBlocksProps } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl, SelectControl } from '@wordpress/components';
import { useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */
import './editor.css';

const HEADER_TAGS = [
    { label: 'H2', value: 'h2' },
    { label: 'H3', value: 'h3' },
    { label: 'H4', value: 'h4' },
    { label: 'H5', value: 'h5' },
    { label: 'H6', value: 'h6' },
    { label: 'P', value: 'p' },
];

export default function Edit({ attributes, setAttributes, clientId }) {
    const { title, itemId, initiallyOpen, headerTag, headerClass, buttonClass } = attributes;

    // Generate unique item ID if not set
    useEffect(() => {
        if (!itemId) {
            setAttributes({ itemId: `item-${clientId.slice(0, 8)}` });
        }
    }, [itemId, clientId, setAttributes]);

    const blockProps = useBlockProps({
        className: 'dp-accordion-item-editor',
    });

    const innerBlocksProps = useInnerBlocksProps(
        { className: 'dp-accordion-body-editor' },
        {
            template: [['core/paragraph', { placeholder: __('Add accordion content...', 'dp-blocks') }]],
            templateInsertUpdatesSelection: false,
            renderAppender: true,
        }
    );

    const HeaderTag = headerTag;

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Accordion Item Settings', 'dp-blocks')} initialOpen={true}>
                    <ToggleControl
                        label={__('Initially Open', 'dp-blocks')}
                        help={__('Show this item expanded by default', 'dp-blocks')}
                        checked={initiallyOpen}
                        onChange={(value) => setAttributes({ initiallyOpen: value })}
                    />
                    <SelectControl
                        label={__('Header Tag', 'dp-blocks')}
                        value={headerTag}
                        options={HEADER_TAGS}
                        onChange={(value) => setAttributes({ headerTag: value })}
                        help={__('HTML tag for the accordion header', 'dp-blocks')}
                    />
                    <TextControl
                        label={__('Header CSS Class', 'dp-blocks')}
                        value={headerClass}
                        onChange={(value) => setAttributes({ headerClass: value })}
                        placeholder="e.g., bg-primary text-white"
                    />
                    <TextControl
                        label={__('Button CSS Class', 'dp-blocks')}
                        value={buttonClass}
                        onChange={(value) => setAttributes({ buttonClass: value })}
                        placeholder="e.g., fw-bold"
                    />
                    <TextControl
                        label={__('Item ID', 'dp-blocks')}
                        value={itemId}
                        onChange={(value) => setAttributes({ itemId: value })}
                        help={__('Unique identifier for this item', 'dp-blocks')}
                    />
                </PanelBody>
            </InspectorControls>

            <div {...blockProps}>
                <HeaderTag className={`dp-accordion-header-editor ${headerClass}`}>
                    <button
                        type="button"
                        className={`dp-accordion-button-editor ${buttonClass} ${!initiallyOpen ? 'collapsed' : ''}`}
                    >
                        <RichText
                            tagName="span"
                            value={title}
                            onChange={(value) => setAttributes({ title: value })}
                            placeholder={__('Accordion Title...', 'dp-blocks')}
                            allowedFormats={['core/bold', 'core/italic']}
                        />
                        <span className="dp-accordion-chevron">▼</span>
                    </button>
                </HeaderTag>
                <div className={`dp-accordion-collapse-editor ${initiallyOpen ? 'show' : ''}`}>
                    <div {...innerBlocksProps} />
                </div>
            </div>
        </>
    );
}