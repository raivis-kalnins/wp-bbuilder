import { __experimentalInputControl as InputControl, TextControl, PanelBody, ColorPalette }  from '@wordpress/components';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import classnames from 'classnames';
import './editor.scss';

export default function edit( { attributes, setAttributes } ) {
	const { srcUrl, srcHeight, srcBorderWidth, srcBorderColor } = attributes;
	const colors = [
		{ name: 'transparent', color: 'transparent' },
		{ name: 'white', color: '#fff' },		
	];
	const wrapperClasses = classnames( 'wp-video__wrap' );
	const blockProps = useBlockProps( {
		className: classnames( {
		} ),
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ 'Settings' }>
					<TextControl
						label="YouTube URL"
						value={ srcUrl }
						onChange={ ( newSrcUrl ) => setAttributes({ srcUrl: newSrcUrl }) }
					/>
					<InputControl
						label="YouTube Iframe Height px"
						value={ srcHeight }
						type="number"
						min={ 0 }
						isPressEnterToChange
						onChange={ ( newSrcH ) => setAttributes({ srcHeight: newSrcH }) }
					/>
					<InputControl
						label="YouTube Iframe Border size"
						value={ srcBorderWidth }
						type="number"
						min={ 0 }
						isPressEnterToChange
						onChange={ ( newSrcBorderWidth ) => setAttributes({ srcBorderWidth: newSrcBorderWidth }) }
					/>
					<ColorPalette
						label="YouTube Border Colour"
						colors={ colors }
						value={ srcBorderColor }
						enableAlpha
						onChange={ ( colors ) => setAttributes( { srcBorderColor: colors } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div className="wp-video" { ...blockProps }>
				<hr />
				<p className="wp-video--code"><b>YouTube URL</b> ( Current height: { srcHeight }px ): <a href={ 'https://www.youtube.com/embed/' + srcUrl } target="_blank">Open Video</a> | Path: ".../embed/{ srcUrl }"</p>
				<hr />
				<div className={ wrapperClasses }><iframe src={'https://www.youtube.com/embed/'+ srcUrl } width="100%" height={ srcHeight } title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen style={{ 'border': 'solid ' + srcBorderColor + ' ' + srcBorderWidth + 'px' }}></iframe></div>
			</div>
		</>
	)
}