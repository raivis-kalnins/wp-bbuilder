import { useBlockProps } from '@wordpress/block-editor';
import classnames from 'classnames';

export default function save( { attributes } ) {
	const wrapperClasses = classnames( 'wp-video__wrap' );
	const { srcUrl, srcHeight, srcBorderWidth, srcBorderColor } = attributes;
	return (
		<div { ...useBlockProps.save( { className: wrapperClasses } ) }><iframe src={'https://www.youtube.com/embed/'+ srcUrl } width="100%" height={ srcHeight } title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen style={{ 'border': 'solid ' + srcBorderColor + ' ' + srcBorderWidth + 'px' }}></iframe></div>
	);
}