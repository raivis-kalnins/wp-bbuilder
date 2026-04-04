 import classnames from 'classnames';
 import { get } from 'lodash';
 import './editor.scss';
 import { __ } from '@wordpress/i18n';
 import { PanelBody, SelectControl,	ColorPalette, Button, RangeControl,	ToggleControl } from '@wordpress/components';
 import { __experimentalInputControl as InputControl } from '@wordpress/components';
 import { useEffect, useState } from '@wordpress/element';
 import { InspectorControls, MediaUpload, MediaUploadCheck, useInnerBlocksProps as __stableUseInnerBlocksProps, __experimentalUseInnerBlocksProps, __experimentalBlockVariationPicker, InnerBlocks, useBlockProps, store as blockEditorStore } from '@wordpress/block-editor';
 import { withDispatch, useDispatch, useSelect } from '@wordpress/data';
 import { createBlocksFromInnerBlocksTemplate, store as blocksStore } from '@wordpress/blocks';
 
 /**
  * Allowed blocks constant is passed to InnerBlocks precisely as specified here.
  * The contents of the array should never change.
  * The array should contain the name of each block that is allowed.
  * In columns block, the only block we allow is 'core/column'.
  *
  * @constant
  * @type {string[]}
  */
 const ALLOWED_BLOCKS = [ 'dp/column' ];
 
 function ColumnsEditContainer( {
	 attributes,
	 setAttributes,
	 clientId
 } ) {
	 const { blockId, bgAttach, mediaUrl, overColor, contBoxed, rowWidth, rowWidthXL, rowWidthLG, rowWidthMD, rowWidthSM, rowWidthVal, rowWidthValXL, rowWidthValLG, rowWidthValMD, rowWidthValSM, bgStyle, topPadding, topPaddingXL, topPaddingLG, topPaddingMD, topPaddingSM, botPadding, botPaddingXL, botPaddingLG, botPaddingMD, botPaddingSM, topMargin, rightMargin, bottomMargin, leftMargin, topMarginXD, rightMarginXD, bottomMarginXD, leftMarginXD, topMarginLG, rightMarginLG, bottomMarginLG, leftMarginLG, topMarginMD, rightMarginMD, bottomMarginMD, leftMarginMD, topMarginSM, rightMarginSM, bottomMarginSM, leftMarginSM, isAnimated, isAnimation, isSlider, isSliderAutoplay, isInline, allHidden, xlHidden, lgHidden, mdHidden, smHidden } = attributes;

	if ( ! blockId ) {
        setAttributes( { blockId: clientId } );
    }

	 const useInnerBlocksProps = __stableUseInnerBlocksProps
		 ? __stableUseInnerBlocksProps
		 : __experimentalUseInnerBlocksProps;

	 const blockProps = useBlockProps( {
		 attributes
	 } );

	 const innerBlocksProps = useInnerBlocksProps( blockProps, {
		 allowedBlocks: ALLOWED_BLOCKS,
		 orientation: 'horizontal',
		 renderAppender: false,
	 } );

	 const removeMedia = () => {
		setAttributes({
			mediaId: 0,
			mediaUrl: ''
		});
	}

	const onSelectMedia = (media) => {
		setAttributes({
			mediaId: media.id,
			mediaUrl: media.url
		});
	}
	
	const colors = [
		{ name: 'white', color: '#FFFFFF' },
		{ name: 'black', color: '#000000' },
		{ name: 'grey', color: '#999999' },
		{ name: 'blue', color: '#29478F' },		
		{ name: 'red', color: '#CC1818' },
		{ name: 'yellow', color: '#FFF300' },
	];
 
	 return (
		 <>
			<InspectorControls>
				<PanelBody title={'Background'} initialOpen={ false }>
					<ToggleControl
						label="Fixed Background"
						help={
							bgAttach
								? 'fixed'
								: 'Not fixed background'
						}
						checked={ bgAttach }
						onChange={ () => setAttributes( {  bgAttach: !bgAttach} ) }
					/>
				 	<h3>Background Color</h3>
					<ColorPalette
						label="Section Background Color"
						colors={ colors }
						value={ bgStyle }
						enableAlpha
						onChange={ ( color ) => setAttributes( { bgStyle: color } ) }
					/>
					<h3>Overlay Colour</h3>
					<ColorPalette
						label="Section Overlay Colour"
						colors={ colors }
						value={ overColor }
						enableAlpha
						onChange={ ( color ) => setAttributes( { overColor: color } ) }
					/>
				 	<div className="editor-post-featured-image">
					 	<h3>Section Background Image</h3>
						<img src={ mediaUrl }  />
						<MediaUploadCheck>
							<MediaUpload
								onSelect={onSelectMedia}
								value={attributes.mediaId}
								allowedTypes={ ['image'] }
								render={({open}) => (
									<Button
										className={attributes.mediaId == 0 ? 'editor-post-featured-image__toggle' : 'editor-post-featured-image__preview'}
										onClick={open}
									>
										{attributes.mediaId == 0 && __('Choose an image', 'dp')}
										{blockProps.media != undefined &&
											<ResponsiveWrapper
												naturalWidth={ blockProps.media.media_details.width }
												naturalHeight={ blockProps.media.media_details.height }
											>
												<img src={blockProps.media.source_url} />
											</ResponsiveWrapper>
											}
									</Button>
								)}
							/>
						</MediaUploadCheck>
						{attributes.mediaId != 0 &&
							<MediaUploadCheck>
								<MediaUpload
									title={__('Replace image', 'dp')}
									value={attributes.mediaId}
									onSelect={onSelectMedia}
									allowedTypes={['image']}
									render={({open}) => (
										<Button onClick={open} isSecondary>{__('Replace image', 'dp')}</Button>
									)}
								/>
							</MediaUploadCheck>
						}
						{attributes.mediaId != 0 &&
							<MediaUploadCheck>
								<Button onClick={removeMedia} isLink isDestructive>{__('Remove image', 'dp')}</Button>
							</MediaUploadCheck>
						}
					</div>
				</PanelBody>
				<PanelBody title={'Container'} initialOpen={ false }>
					<SelectControl
						label="Container boxed (Theme max-width) / fluid (full 100%)"
						value={ contBoxed }
						options={ [
							{ label: 'Boxed', value: 'container-boxed' },
							{ label: 'Fluid', value: 'container-fluid' },
						] }
						onChange={ ( newStyle ) => setAttributes( { contBoxed: newStyle } ) }
					/>
				</PanelBody>
				<PanelBody title={'Max Width'} initialOpen={ false }>
					<div style={{ "width":"100%","display":"inline-flex","margin-bottom":"-25px" }}>
						<div style={{ "width":"70%", "display":"flex" }}>
							<RangeControl
								label="ALL"
								value={ attributes.rowWidth != 0 && rowWidth }
								min={ 0 }
								max={ 1600 }
								onChange={ ( newStyle ) => setAttributes( {rowWidth: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"30%", "display":"flex","padding-left":"2%","align-self":"center" }}>
							<SelectControl
								label="&nbsp;"
								value={ rowWidthVal }
								options={ [
									{ label: 'px', value: 'px' },
									{ label: '%', value: '%' },
									{ label: 'vw', value: 'vw' },
								] }
								onChange={ ( newStyle ) => setAttributes( { rowWidthVal: newStyle } ) }
							/>
						</div>
					</div>
					<div style={{ "width":"100%","display":"inline-flex","margin-bottom":"-25px" }}>
						<div style={{ "width":"70%", "display":"flex" }}>
							<RangeControl
								label="XL"
								value={ rowWidthXL }
								min={ 0 }
								max={ 1600 }
								onChange={ ( newStyle ) => setAttributes( {rowWidthXL: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"30%", "display":"flex","padding-left":"2%","align-self":"center" }}>
							<SelectControl
								label="&nbsp;"
								value={ rowWidthValXL }
								options={ [
									{ label: 'px', value: 'px' },
									{ label: '%', value: 'p' },
									{ label: 'vw', value: 'vw' },
								] }
								onChange={ ( newStyle ) => setAttributes( { rowWidthValXL: newStyle } ) }
							/>
						</div>
					</div>				
					<div style={{ "width":"100%","display":"inline-flex","margin-bottom":"-25px" }}>
						<div style={{ "width":"70%", "display":"flex" }}>
							<RangeControl
								label="LG"
								value={ rowWidthLG }
								min={ 0 }
								max={ 1600 }
								onChange={ ( newStyle ) => setAttributes( {rowWidthLG: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"30%", "display":"flex","padding-left":"2%","align-self":"center" }}>
							<SelectControl
								label="&nbsp;"
								value={ rowWidthValLG }
								options={ [
									{ label: 'px', value: 'px' },
									{ label: '%', value: 'p' },
									{ label: 'vw', value: 'vw' },
								] }
								onChange={ ( newStyle ) => setAttributes( { rowWidthValLG: newStyle } ) }
							/>
						</div>
					</div>
					<div style={{ "width":"100%","display":"inline-flex","margin-bottom":"-25px" }}>
						<div style={{ "width":"70%", "display":"flex" }}>
							<RangeControl
								label="MD"
								value={ rowWidthMD }
								min={ 0 }
								max={ 1600 }
								onChange={ ( newStyle ) => setAttributes( {rowWidthMD: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"30%", "display":"flex","padding-left":"2%","align-self":"center" }}>
							<SelectControl
								label="&nbsp;"
								value={ rowWidthValMD }
								options={ [
									{ label: 'px', value: 'px' },
									{ label: '%', value: 'p' },
									{ label: 'vw', value: 'vw' },
								] }
								onChange={ ( newStyle ) => setAttributes( { rowWidthValMD: newStyle } ) }
							/>
						</div>
					</div>
					<div style={{ "width":"100%","display":"inline-flex","margin-bottom":"-25px" }}>
						<div style={{ "width":"70%", "display":"flex" }}>
							<RangeControl
								label="SM"
								value={ rowWidthSM }
								min={ 0 }
								max={ 1600 }
								onChange={ ( newStyle ) => setAttributes( {rowWidthSM: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"30%", "display":"flex","padding-left":"2%","align-self":"center" }}>
							<SelectControl
								label="&nbsp;"
								value={ rowWidthValSM }
								options={ [
									{ label: 'px', value: 'px' },
									{ label: '%', value: 'p' },
									{ label: 'vw', value: 'vw' },
								] }
								onChange={ ( newStyle ) => setAttributes( { rowWidthValSM: newStyle } ) }
							/>
						</div>
					</div>
				 </PanelBody>
				 <PanelBody title={'Margins (row)'} initialOpen={ false }>
					<div style={{ "display":"inline-flex","margin-bottom":"15px" }}>
						<div style={{ "width":"20%", "display":"flex", "padding-top":"25px" }}>ALL</div>
						<div style={{ "width":"20%","display":"flex","max-height": "58px","padding-right":"2%" }}>
							<InputControl
								label="&uArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ topMargin }
								type="number"
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {topMargin: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"20%","display":"flex","max-height": "58px","padding-right":"2%" }}>
							<InputControl
								label="&rArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ rightMargin }
								type="number"
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {rightMargin: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"20%","display":"flex","max-height": "58px","padding-right":"2%" }}>
							<InputControl
								label="&dArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ bottomMargin }
								type="number"
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {bottomMargin: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"20%","display":"flex","max-height": "58px" }}>
							<InputControl
								label="&lArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ leftMargin }
								type="number"
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {leftMargin: newStyle} ) }
							/>
						</div>
					</div>
					<div style={{ "display":"inline-flex","margin-bottom":"15px" }}>
						<div style={{ "width":"20%", "display":"flex", "padding-top":"25px" }}>XD</div>
						<div style={{ "width":"20%","display":"flex","max-height": "58px","padding-right":"2%" }}>
							<InputControl
								label="&uArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ topMarginXD }
								type="number"
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {topMarginXD: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"20%","display":"flex","max-height": "58px","padding-right":"2%" }}>
							<InputControl
								label="&rArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ rightMarginXD }
								type="number"
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {rightMarginXD: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"20%","display":"flex","max-height": "58px","padding-right":"2%" }}>
							<InputControl
								label="&dArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ bottomMarginXD }
								type="number"
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {bottomMarginXD: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"20%","display":"flex","max-height": "58px" }}>
							<InputControl
								label="&lArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ leftMarginXD }
								type="number"
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {leftMarginXD: newStyle} ) }
							/>
						</div>
					</div>
					<div style={{ "display":"inline-flex","margin-bottom":"15px" }}>
						<div style={{ "width":"20%", "display":"flex", "padding-top":"25px" }}>LG</div>
						<div style={{ "width":"20%","display":"flex","max-height": "58px","padding-right":"2%" }}>
							<InputControl
								label="&uArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ topMarginLG }
								type="number"
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {topMarginLG: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"20%","display":"flex","max-height": "58px","padding-right":"2%" }}>
							<InputControl
								label="&rArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ rightMarginLG }
								type="number"
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {rightMarginLG: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"20%","display":"flex","max-height": "58px","padding-right":"2%" }}>
							<InputControl
								label="&dArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ bottomMarginLG }
								type="number"
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {bottomMarginLG: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"20%","display":"flex","max-height": "58px" }}>
							<InputControl
								label="&lArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ leftMarginLG }
								type="number"
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {leftMarginLG: newStyle} ) }
							/>
						</div>
					</div>
					<div style={{ "display":"inline-flex","margin-bottom":"15px" }}>
						<div style={{ "width":"20%", "display":"flex", "padding-top":"25px" }}>MD</div>
						<div style={{ "width":"20%","display":"flex","max-height": "58px","padding-right":"2%" }}>
							<InputControl
								label="&uArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ topMarginMD }
								type="number"
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {topMarginMD: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"20%","display":"flex","max-height": "58px","padding-right":"2%" }}>
							<InputControl
								label="&rArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ rightMarginMD }
								type="number"
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {rightMarginMD: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"20%","display":"flex","max-height": "58px","padding-right":"2%" }}>
							<InputControl
								label="&dArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ bottomMarginMD }
								type="number"
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {bottomMarginMD: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"20%","display":"flex","max-height": "58px" }}>
							<InputControl
								label="&lArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ leftMarginMD }
								type="number"
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {leftMarginMD: newStyle} ) }
							/>
						</div>
					</div>
					<div style={{ "display":"inline-flex","margin-bottom":"15px" }}>
						<div style={{ "width":"20%", "display":"flex", "padding-top":"25px" }}>SM</div>
						<div style={{ "width":"20%","display":"flex","max-height": "58px","padding-right":"2%" }}>
							<InputControl
								label="&uArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ topMarginSM }
								type="number"
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {topMarginSM: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"20%","display":"flex","max-height": "58px","padding-right":"2%" }}>
							<InputControl
								label="&rArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ rightMarginSM }
								type="number"
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {rightMarginSM: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"20%","display":"flex","max-height": "58px","padding-right":"2%" }}>
							<InputControl
								label="&dArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ bottomMarginSM }
								type="number"
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {bottomMarginSM: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"20%","display":"flex","max-height": "58px" }}>
							<InputControl
								label="&lArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ leftMarginSM }
								type="number"
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {leftMarginSM: newStyle} ) }
							/>
						</div>
					</div>
				</PanelBody>
				<PanelBody title={'Padding / Gutters / Display (row)'} initialOpen={ false }>
					<div style={{ "display":"inline-flex","margin-bottom":"-15px" }}>
						<div style={{ "width":"20%", "display":"flex", "padding-top":"25px" }}>ALL</div>
						<div style={{ "width":"30%", "display":"flex", "padding-top":"25px" }}>
							<ToggleControl
								label=""
								help={
									allHidden
										? 'Hidden'
										: 'Visible'
								}
								checked={ allHidden }
								onChange={ () => setAttributes( {  allHidden: !allHidden } ) }
							/>
						</div>
						<div style={{ "width":"25%","display":"flex","max-height": "58px","padding-right":"2%" }}>
							<InputControl
								label="&uArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ topPadding }
								type="number"
								min={ 0 }
								max={ 250 }
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {topPadding: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"25%","display":"flex","max-height": "58px" }}>
							<InputControl
								label="&dArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ botPadding }
								type="number"
								min={ 0 }
								max={ 250 }
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {botPadding: newStyle} ) }
							/>
						</div>
					</div>
					<div style={{ "display":"inline-flex","margin-bottom":"-15px" }}>
						<div style={{ "width":"20%", "display":"flex", "padding-top":"25px" }}>XL</div>
						<div style={{ "width":"30%", "display":"flex", "padding-top":"25px" }}>
							<ToggleControl
								label=""
								help={
									xlHidden
										? 'Hidden'
										: 'Visible'
								}
								checked={ xlHidden }
								onChange={ () => setAttributes( {  xlHidden: !xlHidden } ) }
							/>
						</div>
						<div style={{ "width":"25%", "display":"flex","max-height": "58px", "padding-right":"2%" }}>
							<InputControl
								label="&uArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ topPaddingXL }
								type="number"
								min={ 0 }
								max={ 250 }
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {topPaddingXL: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"25%", "display":"flex","max-height": "58px" }}>
							<InputControl
								label="&dArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ botPaddingXL }
								type="number"
								min={ 0 }
								max={ 250 }
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {botPaddingXL: newStyle} ) }
							/>
						</div>
					</div>				
					<div style={{ "display":"inline-flex","margin-bottom":"-15px" }}>
						<div style={{ "width":"20%", "display":"flex", "padding-top":"25px" }}>LG</div>
						<div style={{ "width":"30%", "display":"flex", "padding-top":"25px" }}>
							<ToggleControl
								label=""
								help={
									lgHidden
										? 'Hidden'
										: 'Visible'
								}
								checked={ lgHidden }
								onChange={ () => setAttributes( {  lgHidden: !lgHidden } ) }
							/>
						</div>
						<div style={{ "width":"25%", "display":"flex","max-height": "58px", "padding-right":"2%" }}>
							<InputControl
								label="&uArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ topPaddingLG }
								type="number"
								min={ 0 }
								max={ 250 }
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {topPaddingLG: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"25%", "display":"flex","max-height": "58px" }}>
							<InputControl
								label="&dArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ botPaddingLG }
								type="number"
								min={ 0 }
								max={ 250 }
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {botPaddingLG: newStyle} ) }
							/>
						</div>
					</div>
					<div style={{ "display":"inline-flex","margin-bottom":"-15px" }}>
					<div style={{ "width":"20%", "display":"flex", "padding-top":"25px" }}>MD</div>
						<div style={{ "width":"30%", "display":"flex", "padding-top":"25px" }}>
							<ToggleControl
								label=""
								help={
									mdHidden
										? 'Hidden'
										: 'Visible'
								}
								checked={ mdHidden }
								onChange={ () => setAttributes( {  mdHidden: !mdHidden } ) }
							/>
						</div>
						<div style={{ "width":"25%", "display":"flex","max-height": "58px", "padding-right":"2%" }}>
							<InputControl
								label="&uArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ topPaddingMD }
								type="number"
								min={ 0 }
								max={ 250 }
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {topPaddingMD: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"25%", "display":"flex","max-height": "58px" }}>
							<InputControl
								label="&dArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ botPaddingMD }
								type="number"
								min={ 0 }
								max={ 250 }
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {botPaddingMD: newStyle} ) }
							/>
						</div>
					</div>
					<div style={{ "display":"inline-flex","margin-bottom":"-15px" }}>
					<div style={{ "width":"20%", "display":"flex", "padding-top":"25px" }}>SM</div>
						<div style={{ "width":"30%", "display":"flex", "padding-top":"25px" }}>
							<ToggleControl
								label=""
								help={
									smHidden
										? 'Hidden'
										: 'Visible'
								}
								checked={ smHidden }
								onChange={ () => setAttributes( {  smHidden: !smHidden } ) }
							/>
						</div>
						<div style={{ "width":"25%", "display":"flex","max-height": "58px", "padding-right":"2%" }}>
							<InputControl
								label="&uArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ topPaddingSM }
								type="number"
								min={ 0 }
								max={ 250 }
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {topPaddingSM: newStyle} ) }
							/>
						</div>
						<div style={{ "width":"25%", "display":"flex","max-height": "58px" }}>
							<InputControl
								label="&dArr; px"
								labelPosition="top"
								labelAlign="center"
								style={{ "color":"grey","text-align":"center" }}
								value={ botPaddingSM }
								type="number"
								min={ 0 }
								max={ 250 }
								isPressEnterToChange
								onChange={ ( newStyle ) => setAttributes( {botPaddingSM: newStyle} ) }
							/>
						</div>
					</div>
				</PanelBody>
				<PanelBody title={'Modal Window'} initialOpen={ false }>
					<SelectControl
						label="Modal/PopUp window)"
						value={ isInline }
						options={ [
							{ label: 'no', value: '' },
							{ label: 'boxed', value: 'inline-section mfp-hide' },
							{ label: 'full', value: 'inline-section mfp-hide inline-full' }
						] }
						onChange={ ( newStyle ) => setAttributes( {isInline: newStyle} ) }
					/>
					{/* <a href="#section-services" class="open-popup-link">Show inline popup</a> */}
				</PanelBody>
				<PanelBody title={'Animation / Slider (SwiperJS)'} initialOpen={ false }>
					<ToggleControl
						label="Animated section"
						help={
							isAnimated
								? 'Is animated'
								: 'Not animated'
						}
						checked={ isAnimated }
						onChange={ () => setAttributes( {  isAnimated: ! isAnimated } ) }
					/>
					{ isAnimated && <SelectControl
						label="Animation"
						value={ isAnimation }
						options={ [
							{ label: 'no', value: '' },
							{ label: 'Fade In Up', value: 'fade-in-up animated' },
							{ label: 'Fade In Down', value: 'fade-in-down animated' },
							{ label: 'Fade In', value: 'fade-in animated' }
						] }
						onChange={ ( newStyle ) => setAttributes( {isAnimation: newStyle} ) }
					/> 
					}
					<ToggleControl
						label="Slider section"
						help={
							isSlider
								? 'Is slider'
								: 'Not slider'
						}
						checked={ isSlider }
						onChange={ () => setAttributes( {  isSlider: ! isSlider } ) }
					/>
					<a href='https://swiperjs.com/demos' target='_blank'>Swiper Demos</a>
					{ isSlider && <SelectControl
						label="Swiper Slider"
						value={ isSliderAutoplay }
						options={ [
							{ label: 'Static', value: '' },
							{ label: 'Auoplay', value: 'slider-section--autoplay' }
						] }
						onChange={ ( newStyle ) => setAttributes( {isSliderAutoplay: newStyle} ) }
					/>
					}
				</PanelBody>
			</InspectorControls>
			<div class="row-wrap">
				<i className="scap scap__top"> row/sec </i>
				<div { ...innerBlocksProps } />
				<i className="scap scap__bottom"> //row/sec </i>
			</div>
		 </>
	 );
 }
 
 const ColumnsEditContainerWrapper = withDispatch(
	 ( dispatch, ownProps, registry ) => ( {
 
		 /**
		  * Update all child Column blocks with a new vertical alignment setting
		  * based on whatever alignment is passed in. This allows change to parent
		  * to overide anything set on a individual column basis.
		  *
		  * @param {string} verticalAlignment the vertical alignment setting
		  */
		 updateAlignment( verticalAlignment ) {
		 	const { clientId, setAttributes } = ownProps;
		 	const { updateBlockAttributes } = dispatch( blockEditorStore );
		 	const { getBlockOrder } = registry.select( blockEditorStore );
 
		 	// Update own alignment.
		 	setAttributes( { verticalAlignment } );
 
		 	// Update all child Column Blocks to match
		 	const innerBlockClientIds = getBlockOrder( clientId );
		 	innerBlockClientIds.forEach( ( innerBlockClientId ) => {
		 		updateBlockAttributes( innerBlockClientId, {
		 			verticalAlignment,
		 		} );
		 	} );
		 },
 
		 /**
		  * Updates the column count, including necessary revisions to child Column
		  * blocks to grant required or redistribute available space.
		  *
		  * @param {number} previousColumns Previous column count.
		  * @param {number} newColumns      New column count.
		  */
		 updateColumns( previousColumns, newColumns ) {
		 	const { clientId } = ownProps;
		 	const { replaceInnerBlocks } = dispatch( blockEditorStore );
		 	const { getBlocks } = registry.select( blockEditorStore );
 
		 	let innerBlocks = getBlocks( clientId );
		 	const hasExplicitWidths = hasExplicitPercentColumnWidths(
		 		innerBlocks
		 	);
 
		 	// Redistribute available width for existing inner blocks.
		 	const isAddingColumn = newColumns > previousColumns;
 
		 	if ( isAddingColumn && hasExplicitWidths ) {
		 		// If adding a new column, assign width to the new column equal to
		 		// as if it were `1 / columns` of the total available space.
		 		const newColumnWidth = toWidthPrecision( 100 / newColumns );
 
		 		// Redistribute in consideration of pending block insertion as
		 		// consevent the available working width.
		 		const widths = getRedistributedColumnWidths(
		 			innerBlocks,
		 			100 - newColumnWidth
		 		);
 
		 		innerBlocks = [
		 			...getMappedColumnWidths( innerBlocks, widths ),
		 			...times( newColumns - previousColumns, () => {
		 				return createBlock( 'core/column', {
		 					width: `${ newColumnWidth }%`,
		 				} );
		 			} ),
		 		];
		 	} else if ( isAddingColumn ) {
		 		innerBlocks = [
		 			...innerBlocks,
		 			...times( newColumns - previousColumns, () => {
		 				return createBlock( 'core/column' );
		 			} ),
		 		];
		 	} else {
		 		// The removed column will be the last of the inner blocks.
		 		innerBlocks = dropRight(
		 			innerBlocks,
		 			previousColumns - newColumns
		 		);
 
		 		if ( hasExplicitWidths ) {
		 			// Redistribute as if block is already removed.
		 			const widths = getRedistributedColumnWidths(
		 				innerBlocks,
		 				100
		 			);
 
		 			innerBlocks = getMappedColumnWidths( innerBlocks, widths );
		 		}
		 	}
 
		 	replaceInnerBlocks( clientId, innerBlocks );
		 },
	 } )
 )( ColumnsEditContainer );
 
 function Placeholder( { clientId, name, setAttributes } ) {
	 const { blockType, defaultVariation, variations } = useSelect(
		 ( select ) => {
			 const {
				 getBlockVariations,
				 getBlockType,
				 getDefaultBlockVariation,
			 } = select( blocksStore );
 
			 return {
				 blockType: getBlockType( name ),
				 defaultVariation: getDefaultBlockVariation( name, 'block' ),
				 variations: getBlockVariations( name, 'block' ),
			 };
		 },
		 [ name ]
	 );
	 const { replaceInnerBlocks } = useDispatch( blockEditorStore );
	 const blockProps = useBlockProps();
 
	 return (
		 <div { ...blockProps }>
			 <__experimentalBlockVariationPicker
				 icon={ get( blockType, [ 'icon', 'src' ] ) }
				 label={ get( blockType, [ 'title' ] ) }
				 variations={ variations }
				 onSelect={ ( nextVariation = defaultVariation ) => {
					 if ( nextVariation.attributes ) {
						 setAttributes( nextVariation.attributes );
					 }
					 if ( nextVariation.innerBlocks ) {
						 replaceInnerBlocks(
							 clientId,
							 createBlocksFromInnerBlocksTemplate(
								 nextVariation.innerBlocks
							 ),
							 true
						 );
					 }
				 } }
				 allowSkip
			 />
		 </div>
	 );
 }
 
 const ColumnsEdit = ( props ) => {

	 const { clientId, setAttributes } = props;

	 const hasInnerBlocks = useSelect(
		 ( select ) =>
			 select( blockEditorStore ).getBlocks( clientId ).length > 0,
		 [ clientId ]
	 );

	 const { count } = useSelect(
		 ( select ) => {
			 return {
				 count: select( blockEditorStore ).getBlockCount( clientId ),
			 };
		 },
		 [ clientId ]
	 );
 
	 useEffect(() => {
		 setAttributes({childCount: count});
	 })

	 const Component = hasInnerBlocks
		 ? ColumnsEditContainerWrapper
		 : Placeholder;
 
	 return <Component { ...props } />;
 };
 
 export default ColumnsEdit;