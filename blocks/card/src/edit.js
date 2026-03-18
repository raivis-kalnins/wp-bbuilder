/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText, InspectorControls, useInnerBlocksProps, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl, Button, ButtonGroup, ToolbarGroup, ToolbarButton } from '@wordpress/components';
import { useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import './editor.css';

const TEXT_ALIGNMENTS = [
    { label: __('Left', 'dp-blocks'), value: 'left' },
    { label: __('Center', 'dp-blocks'), value: 'center' },
    { label: __('Right', 'dp-blocks'), value: 'right' },
];

const IMAGE_POSITIONS = [
    { label: __('Top', 'dp-blocks'), value: 'top' },
    { label: __('Bottom', 'dp-blocks'), value: 'bottom' },
    { label: __('Overlay', 'dp-blocks'), value: 'overlay' },
];

const BORDER_COLORS = [
    { label: __('Default', 'dp-blocks'), value: '' },
    { label: __('Primary', 'dp-blocks'), value: 'primary' },
    { label: __('Secondary', 'dp-blocks'), value: 'secondary' },
    { label: __('Success', 'dp-blocks'), value: 'success' },
    { label: __('Danger', 'dp-blocks'), value: 'danger' },
    { label: __('Warning', 'dp-blocks'), value: 'warning' },
    { label: __('Info', 'dp-blocks'), value: 'info' },
    { label: __('Light', 'dp-blocks'), value: 'light' },
    { label: __('Dark', 'dp-blocks'), value: 'dark' },
];

const BG_COLORS = [
    { label: __('Default (White)', 'dp-blocks'), value: '' },
    { label: __('Primary', 'dp-blocks'), value: 'primary' },
    { label: __('Secondary', 'dp-blocks'), value: 'secondary' },
    { label: __('Success', 'dp-blocks'), value: 'success' },
    { label: __('Danger', 'dp-blocks'), value: 'danger' },
    { label: __('Warning', 'dp-blocks'), value: 'warning' },
    { label: __('Info', 'dp-blocks'), value: 'info' },
    { label: __('Light', 'dp-blocks'), value: 'light' },
    { label: __('Dark', 'dp-blocks'), value: 'dark' },
];

export default function Edit({ attributes, setAttributes }) {
    const {
        borderColor,
        backgroundColor,
        textColor,
        textAlignment,
        showImage,
        showHeader,
        showFooter,
        imagePosition,
        imageUrl,
        imageAlt,
        overlay,
    } = attributes;

    const [isEditingImage, setIsEditingImage] = useState(false);

    const blockProps = useBlockProps({
        className: `dp-card-editor ${borderColor ? 'border-' + borderColor : ''} ${backgroundColor ? 'bg-' + backgroundColor : ''} ${textColor ? 'text-' + textColor : ''} text-${textAlignment}`,
    });

    const innerBlocksProps = useInnerBlocksProps(
        { className: 'dp-card-body-editor' },
        {
            template: [
                ['core/heading', { level: 5, placeholder: __('Card Title', 'dp-blocks') }],
                ['core/paragraph', { placeholder: __('Add card content...', 'dp-blocks') }],
            ],
            templateInsertUpdatesSelection: false,
            renderAppender: true,
        }
    );

    const cardImage = showImage && imageUrl && (
        <div className={`dp-card-image-editor ${imagePosition === 'overlay' ? 'dp-card-img-overlay-wrapper' : ''}`}>
            <img src={imageUrl} alt={imageAlt} className="card-img-top" />
            {imagePosition === 'overlay' && <div className="dp-card-image-overlay" />}
        </div>
    );

    const cardHeader = showHeader && (
        <div className="dp-card-header-editor">
            <RichText
                tagName="span"
                value={attributes.headerText}
                onChange={(value) => setAttributes({ headerText: value })}
                placeholder={__('Header text...', 'dp-blocks')}
            />
        </div>
    );

    const cardFooter = showFooter && (
        <div className="dp-card-footer-editor">
            <RichText
                tagName="span"
                value={attributes.footerText}
                onChange={(value) => setAttributes({ footerText: value })}
                placeholder={__('Footer text...', 'dp-blocks')}
            />
        </div>
    );

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Card Settings', 'dp-blocks')} initialOpen={true}>
                    {/* Image Settings */}
                    <ToggleControl
                        label={__('Show Image', 'dp-blocks')}
                        checked={showImage}
                        onChange={(value) => setAttributes({ showImage: value })}
                    />
                    {showImage && (
                        <>
                            <SelectControl
                                label={__('Image Position', 'dp-blocks')}
                                value={imagePosition}
                                options={IMAGE_POSITIONS}
                                onChange={(value) => setAttributes({ imagePosition: value })}
                            />
                            <div className="dp-card-image-upload">
                                {imageUrl ? (
                                    <div className="dp-card-image-preview">
                                        <img src={imageUrl} alt={imageAlt} />
                                        <Button
                                            isDestructive
                                            size="small"
                                            onClick={() => setAttributes({ imageUrl: '', imageAlt: '' })}
                                        >
                                            {__('Remove', 'dp-blocks')}
                                        </Button>
                                    </div>
                                ) : (
                                    <MediaUploadCheck>
                                        <MediaUpload
                                            onSelect={(media) => setAttributes({ imageUrl: media.url, imageAlt: media.alt })}
                                            allowedTypes={['image']}
                                            render={({ open }) => (
                                                <Button onClick={open} variant="secondary">
                                                    {__('Select Image', 'dp-blocks')}
                                                </Button>
                                            )}
                                        />
                                    </MediaUploadCheck>
                                )}
                            </div>
                        </>
                    )}

                    {/* Header/Footer */}
                    <ToggleControl
                        label={__('Show Header', 'dp-blocks')}
                        checked={showHeader}
                        onChange={(value) => setAttributes({ showHeader: value })}
                    />
                    <ToggleControl
                        label={__('Show Footer', 'dp-blocks')}
                        checked={showFooter}
                        onChange={(value) => setAttributes({ showFooter: value })}
                    />

                    {/* Styling */}
                    <SelectControl
                        label={__('Border Color', 'dp-blocks')}
                        value={borderColor}
                        options={BORDER_COLORS}
                        onChange={(value) => setAttributes({ borderColor: value })}
                    />
                    <SelectControl
                        label={__('Background Color', 'dp-blocks')}
                        value={backgroundColor}
                        options={BG_COLORS}
                        onChange={(value) => setAttributes({ backgroundColor: value })}
                    />
                    <SelectControl
                        label={__('Text Alignment', 'dp-blocks')}
                        value={textAlignment}
                        options={TEXT_ALIGNMENTS}
                        onChange={(value) => setAttributes({ textAlignment: value })}
                    />
                </PanelBody>
            </InspectorControls>

            <div {...blockProps}>
                {imagePosition !== 'bottom' && cardImage}
                {cardHeader}
                <div {...innerBlocksProps} />
                {cardFooter}
                {imagePosition === 'bottom' && cardImage}
            </div>
        </>
    );
}