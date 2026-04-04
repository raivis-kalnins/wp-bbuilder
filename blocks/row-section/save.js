/**
 * External dependencies
 */
 import classnames from 'classnames';

 /**
  * WordPress dependencies
  */
 import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
 
 export default function save( { attributes } ) {

	 const { bgAttach, mediaUrl, overColor, contBoxed, rowWidth, rowWidthXL, rowWidthLG, rowWidthMD, rowWidthSM, rowWidthVal, rowWidthValXL, rowWidthValLG, rowWidthValMD, rowWidthValSM, bgStyle, topPadding, topPaddingXL, topPaddingLG, topPaddingMD, topPaddingSM, botPadding, botPaddingXL, botPaddingLG, botPaddingMD, botPaddingSM, topMargin, rightMargin, bottomMargin, leftMargin, topMarginXD, rightMarginXD, bottomMarginXD, leftMarginXD, topMarginLG, rightMarginLG, bottomMarginLG, leftMarginLG, topMarginMD, rightMarginMD, bottomMarginMD, leftMarginMD, topMarginSM, rightMarginSM, bottomMarginSM, leftMarginSM, isAnimated, isAnimation, isSlider, isSliderAutoplay, isInline, allHidden, xlHidden, lgHidden, mdHidden, smHidden, childCount, verticalAlignment, isStackedOnMobile } = attributes;
 
	 let layoutClass;
 
	 switch (childCount) {

		case 2:
			layoutClass = 'row two-column-layout';
		break;

		case 3:
			layoutClass = 'row three-column-layout';
		break;

		case 4:
			layoutClass = 'row four-column-layout';
		break;

		case 5:
			layoutClass = 'row five-column-layout';
		break;

		case 6:
			layoutClass = 'row three-column-layout';
		break;

		case 7:
			layoutClass = 'row seven-column-layout';
		break;

		case 8:
			layoutClass = 'row eight-column-layout';
		break;

		case 9:
			layoutClass = 'row nine-column-layout';
		break;

		case 10:
			layoutClass = 'row ten-column-layout';
		break;

		case 11:
			layoutClass = 'row eleven-column-layout';
		break;

		case 12:
			layoutClass = 'row twelve-column-layout';
		break;

		default:
			layoutClass = 'row one-column-layout';
	 }

	const classNameRow = classnames(
		isInline,
		contBoxed && (contBoxed),
		//rowWidth && ('w-' + rowWidth + '-' + rowWidthVal),
		rowWidthXL && ('w-xl-' + rowWidthXL + '-' + rowWidthValXL),
		rowWidthLG && ('w-lg-' + rowWidthLG + '-' +  rowWidthValLG),
		rowWidthMD && ('w-md-' + rowWidthMD + '-' +  rowWidthValMD),
		rowWidthSM && ('w-sm-' + rowWidthSM + '-' +  rowWidthValSM),
		//topPadding && ('pt-' + topPadding),
		topPaddingXL && ('pt-xl-' + topPaddingXL), 
		topPaddingLG && ('pt-lg-' + topPaddingLG), 
		topPaddingMD && ('pt-md-' + topPaddingMD), 
		topPaddingSM && ('pt-sm-' + topPaddingSM),
		//botPadding && ('pb-' + botPadding),
		botPaddingXL && ('pb-xl-' + botPaddingXL), 
		botPaddingLG && ('pb-lg-' + botPaddingLG), 
		botPaddingMD && ('pb-md-' + botPaddingMD), 
		botPaddingSM && ('pb-sm-' + botPaddingSM),
		isAnimation,
		isSliderAutoplay,
		//topMargin && ('mt-' + topMargin),
		//rightMargin && ('mr-' + rightMargin),
		//bottomMargin && ('mb-' + bottomMargin),
		//leftMargin && ('ml-' + leftMargin),
		topMarginXD && ('mt-xd-' + topMarginXD),
		rightMarginXD && ('mr-xd-' + rightMarginXD),
		bottomMarginXD && ('mb-xd-' + bottomMarginXD),
		leftMarginXD && ('ml-xd-' + leftMarginXD),
		topMarginLG && ('mt-lg-' + topMarginLG),
		rightMarginLG && ('mr-lg-' + rightMarginLG),
		bottomMarginLG && ('mb-lg-' + bottomMarginLG),
		leftMarginLG && ('ml-lg-' + leftMarginLG),
		topMarginMD && ('mt-md-' + topMarginMD),
		rightMarginMD && ('mr-md-' + rightMarginMD),
		bottomMarginMD && ('mb-md-' + bottomMarginMD),
		leftMarginMD && ('ml-md-' + leftMarginMD),
		topMarginSM && ('mt-sm-' + topMarginSM),
		rightMarginSM && ('mr-sm-' + rightMarginSM),
		bottomMarginSM && ('mb-sm-' + bottomMarginSM),
		leftMarginSM && ('ml-sm-' + leftMarginSM),
		allHidden && ('d-none'),
		xlHidden && ('d-xl-none'),
		lgHidden && ('d-lg-none'),
		mdHidden && ('d-md-none'),
		smHidden && ('d-sm-none'), {
			[ `are-vertically-aligned-${ verticalAlignment }` ]: verticalAlignment,
			[ `is-not-stacked-on-mobile` ]: ! isStackedOnMobile,
			[ `animated-section` ]: isAnimated,
			[ `slider-section` ]: isSlider,
		} 
	);

	const overlayStyle = {
		backgroundColor: overColor
	};

	return (
		 <div { ...useBlockProps.save({ className: classNameRow, style: (topMargin && ('margin-top:' + topMargin + 'px;')) + (rightMargin && ('margin-right:' + rightMargin + 'px;')) + (bottomMargin && ('margin-bottom:' + bottomMargin + 'px;')) + (leftMargin && ('margin-left:' + leftMargin + 'px;')) + (rowWidth && ('max-width:' + rowWidth + rowWidthVal + ';')) + (topPadding && ('padding-top:' + topPadding + 'px;')) + (botPadding && ('padding-bottom:' + botPadding + 'px;')) + ((bgStyle) && ('background-color:' + bgStyle + ';')) + ((mediaUrl) && ( 'background-attachment:' + ( bgAttach && ( `fixed` ) || `inherit` ) + '; background-image: url(' + mediaUrl + ');')) }) }  >
			{ overColor && ( <div className="row-bg-overlay" style={ overlayStyle }></div> ) }
			<div className={ layoutClass }>
				<InnerBlocks.Content />
			</div>
		 </div>
	 );
 }