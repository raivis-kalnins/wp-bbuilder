/**
 * External dependencies
 */
 import classnames from 'classnames';
 
 /**
  * WordPress dependencies
  */
 import { InnerBlocks, getColorClassName, useBlockProps } from '@wordpress/block-editor';
 
 export default [
	 {
		 attributes: {
			 verticalAlignment: {
				 type: "string"
			 },
			 isStackedOnMobile: {
				 type: "boolean",
				 default: true
			 },
			 bgStyle: {
				 type: "string"
			 },
			 childCount: {
				 type: "integer"
			 }
		 },
		 migrate ( attributes, innerBlocks) {
			 attributes = {
				 ...attributes,
				 topPadding: 'pt-100',
				 botPadding: 'pb-100',
			 }
			 return [attributes, innerBlocks];
		 },
		 save( { attributes, props } ) {
			 const { bgStyle, childCount, isStackedOnMobile, verticalAlignment } = attributes;
 
			 let layoutClass;
 
			 switch (childCount) {
				
				case 2:
					layoutClass = 'two-column-layout';
				break;

				case 3:
					layoutClass = 'three-column-layout';
				break;

				case 4:
					layoutClass = 'four-column-layout';
				break;
				
				case 5:
					layoutClass = 'five-column-layout';
				break;
		
				case 6:
					layoutClass = 'three-column-layout';
				break;
		
				case 7:
					layoutClass = 'seven-column-layout';
				break;
		
				case 8:
					layoutClass = 'eight-column-layout';
				break;
		
				case 9:
					layoutClass = 'nine-column-layout';
				break;
		
				case 10:
					layoutClass = 'ten-column-layout';
				break;
		
				case 11:
					layoutClass = 'eleven-column-layout';
				break;
		
				case 12:
					layoutClass = 'twelve-column-layout';
				break;
					
				 default:
					 layoutClass = 'one-column-layout';
			 }
 
			 let classes = [
				 layoutClass,'py-100', bgStyle
			 ]
			 const className = classnames(
				 ...classes,
				 {
					 [ `are-vertically-aligned-${ verticalAlignment }` ]: verticalAlignment,
					 [ `is-not-stacked-on-mobile` ]: ! isStackedOnMobile,
			 } );
 
			 return (
				 <div { ...useBlockProps.save( { className } ) }>
					 <div className="container">
						 <div className="row">
							 <InnerBlocks.Content />
						 </div>
					 </div>
				 </div>
			 )
		 },
	 },
 ];
 