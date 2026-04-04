import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
export default function save( { attributes } ) {
	const { icoMarginRight, icoHeight, icoColor, icoColorHover, fbHidden, xtwHidden, lnHidden, wappHidden, telegHidden, emailHidden, linklHidden } = attributes;
	return (
		<div id="wp-soc-share-wrap" { ...useBlockProps.save() }>
			<div className="wp-soc-share">
				{!fbHidden && (<div className='wp-soc-share_item wp-soc-share_item--fb'><a href='' rel="noopener"></a></div>)}
				{!xtwHidden && (<div className="wp-soc-share_item wp-soc-share_item--xtw"><a href='' rel="noopener"></a></div>)}
				{!lnHidden && (<div className="wp-soc-share_item wp-soc-share_item--ln"><a href='' rel="noopener"></a></div>)}
				{!wappHidden && (<div className="wp-soc-share_item wp-soc-share_item--wapp"><a href='' rel='noopener'></a></div>)}
				{!telegHidden && (<div className="wp-soc-share_item wp-soc-share_item--teleg"><a href='' rel="noopener"></a></div>)}
				{!emailHidden && (<div className="wp-soc-share_item wp-soc-share_item--email"><a href=''></a></div>)}
				{!linklHidden && (<div className="wp-soc-share_item wp-soc-share_item--link"><span><input type='text' value='' id='copyInput'/></span></div>)}
				<style>{("#wp-soc-share-wrap .wp-soc-share_item{margin-right:"+icoMarginRight+"px}#wp-soc-share-wrap .wp-soc-share_item a,#wp-soc-share-wrap .wp-soc-share_item span{height:"+ icoHeight +"px !important}#wp-soc-share-wrap .wp-soc-share_item svg path{fill:"+icoColor+" !important} #wp-soc-share-wrap .wp-soc-share_item a:hover svg path,#wp-soc-share-wrap .wp-soc-share_item span:hover svg path{fill:"+icoColorHover+" !important}")}</style>
			</div>
		</div>
	);
}