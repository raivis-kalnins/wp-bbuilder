/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText, InspectorControls, useInnerBlocksProps } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import { useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */
import './editor.css';

export default function Edit({ attributes, setAttributes, clientId }) {
    const { title, tabId, icon, disabled } = attributes;

    // Generate unique tab ID if not set
    useEffect(() => {
        if (!tabId) {
            setAttributes({ tabId: `tab-${clientId.slice(0, 8)}` });
        }
    }, [tabId, clientId, setAttributes]);

    const blockProps = useBlockProps({
        className: `dp-tab-pane-editor ${disabled ? 'disabled' : ''}`,
    });

    const innerBlocksProps = useInnerBlocksProps(
        { className: 'dp-tab-pane-content' },
        {
            template: [['core/paragraph', { placeholder: __('Add tab content...', 'dp-blocks') }]],
            templateInsertUpdatesSelection: false,
            renderAppender: true,
        }
    );

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Tab Item Settings', 'dp-blocks')} initialOpen={true}>
                    <TextControl
                        label={__('Tab Title', 'dp-blocks')}
                        value={title}
                        onChange={(value) => setAttributes({ title: value })}
                        placeholder={__('Enter tab title', 'dp-blocks')}
                    />
                    <TextControl
                        label={__('Icon (emoji or HTML)', 'dp-blocks')}
                        value={icon}
                        onChange={(value) => setAttributes({ icon: value })}
                        placeholder={__('e.g., 🏠 or <svg>...</svg>', 'dp-blocks')}
                        help={__('Add an icon before the tab title', 'dp-blocks')}
                    />
                    <ToggleControl
                        label={__('Disabled', 'dp-blocks')}
                        help={__('Disable this tab from being clicked', 'dp-blocks')}
                        checked={disabled}
                        onChange={(value) => setAttributes({ disabled: value })}
                    />
                    <TextControl
                        label={__('Tab ID', 'dp-blocks')}
                        value={tabId}
                        onChange={(value) => setAttributes({ tabId: value })}
                        help={__('Unique identifier for this tab', 'dp-blocks')}
                    />
                </PanelBody>
            </InspectorControls>

            <div {...blockProps}>
                <div className="dp-tab-pane-header">
                    <RichText
                        tagName="span"
                        value={title}
                        onChange={(value) => setAttributes({ title: value })}
                        placeholder={__('Tab Title...', 'dp-blocks')}
                        allowedFormats={['core/bold', 'core/italic']}
                        className="dp-tab-pane-title"
                    />
                    {disabled && <span className="dp-tab-disabled-badge">{__('Disabled', 'dp-blocks')}</span>}
                </div>
                <div {...innerBlocksProps} />
            </div>
        </>
    );
}