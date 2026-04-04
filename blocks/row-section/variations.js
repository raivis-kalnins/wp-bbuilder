/**
 * WordPress dependencies
 */
 import { Path, SVG } from '@wordpress/components';
 import { __ } from '@wordpress/i18n';
 
 /** @typedef {import('@wordpress/blocks').WPBlockVariation} WPBlockVariation */
 
 /**
  * Template option choices for predefined columns layouts.
  *
  * @type {WPBlockVariation[]}
  */
 const variations = [
	 {
		 name: 'single-column',
		 title: __( '1 column' ),
		 description: __( 'One column' ),
		 icon: (
			 <SVG
				 width="48"
				 height="48"
				 viewBox="0 0 48 48"
				 xmlns="http://www.w3.org/2000/svg"
			 >
				 <Path
					 fillRule="evenodd"
					 clipRule="evenodd"
					 d="M39 12C40.1046 12 41 12.8954 41 14V34C41 35.1046 40.1046 36 39 36H9C7.89543 36 7 35.1046 7 34V14C7 12.8954 7.89543 12 9 12H39ZM39 34V14H25V34H39ZM23 34H9V14H23V34Z"
				 />
			 </SVG>
		 ),
		 isDefault: true,
		 innerBlocks: [ [ 'dp/column' , {width: 'full'} ] ],
		 scope: [ 'block' ],
	 },
	 {
		 name: 'two-columns-equal',
		 title: __( '2 columns' ),
		 description: __( 'Two columns' ),
		 icon: (
			 <SVG
				 width="48"
				 height="48"
				 viewBox="0 0 48 48"
				 xmlns="http://www.w3.org/2000/svg"
			 >
				 <Path
					 fillRule="evenodd"
					 clipRule="evenodd"
					 d="M39 12C40.1046 12 41 12.8954 41 14V34C41 35.1046 40.1046 36 39 36H9C7.89543 36 7 35.1046 7 34V14C7 12.8954 7.89543 12 9 12H39ZM39 34V14H25V34H39ZM23 34H9V14H23V34Z"
				 />
			 </SVG>
		 ),
		 isDefault: true,
		 innerBlocks: [ [ 'dp/column' , {width: 'half'} ], [ 'dp/column' , {width: 'half'} ] ],
		 scope: [ 'block' ],
	 },
	 {
		name: 'three-columns-equal',
		title: __( '3 columns' ),
		description: __( 'Three columns' ),
		icon: (
			<SVG
				width="48"
				height="48"
				viewBox="0 0 48 48"
				xmlns="http://www.w3.org/2000/svg"
			>
				<Path
					fillRule="evenodd"
					clipRule="evenodd"
					d="M39 12C40.1046 12 41 12.8954 41 14V34C41 35.1046 40.1046 36 39 36H9C7.89543 36 7 35.1046 7 34V14C7 12.8954 7.89543 12 9 12H39ZM39 34V14H25V34H39ZM23 34H9V14H23V34Z"
				/>
			</SVG>
		),
		isDefault: true,
		innerBlocks: [ [ 'dp/column' , {width: 'three'} ], [ 'dp/column' , {width: 'three'} ], [ 'dp/column' , {width: 'three'} ] ],
		scope: [ 'block' ],
	},
	{
		name: 'four-columns-equal',
		title: __( '4 columns' ),
		description: __( 'Four columns' ),
		icon: (
			<SVG
				width="48"
				height="48"
				viewBox="0 0 48 48"
				xmlns="http://www.w3.org/2000/svg"
			>
				<Path
					fillRule="evenodd"
					clipRule="evenodd"
					d="M39 12C40.1046 12 41 12.8954 41 14V34C41 35.1046 40.1046 36 39 36H9C7.89543 36 7 35.1046 7 34V14C7 12.8954 7.89543 12 9 12H39ZM39 34V14H25V34H39ZM23 34H9V14H23V34Z"
				/>
			</SVG>
		),
		isDefault: true,
		innerBlocks: [ [ 'dp/column' , {width: 'quarter'}], [ 'dp/column' , {width: 'quarter'} ], [ 'dp/column' , {width: 'quarter'} ], [ 'dp/column' , {width: 'quarter'} ] ],
		scope: [ 'block' ],
	},
	{
		name: 'five-columns-equal',
		title: __( '5 columns' ),
		description: __( 'Five columns' ),
		icon: (
			<SVG
				width="48"
				height="48"
				viewBox="0 0 48 48"
				xmlns="http://www.w3.org/2000/svg"
			>
				<Path
					fillRule="evenodd"
					clipRule="evenodd"
					d="M39 12C40.1046 12 41 12.8954 41 14V34C41 35.1046 40.1046 36 39 36H9C7.89543 36 7 35.1046 7 34V14C7 12.8954 7.89543 12 9 12H39ZM39 34V14H25V34H39ZM23 34H9V14H23V34Z"
				/>
			</SVG>
		),
		isDefault: true,
		innerBlocks: [ [ 'dp/column' , {width: 'five'}], [ 'dp/column' , {width: 'five'} ], [ 'dp/column' , {width: 'five'} ], [ 'dp/column' , {width: 'five'} ], [ 'dp/column' , {width: 'five'} ] ],
		scope: [ 'block' ],
	},
	{
		name: 'six-columns-equal',
		title: __( '6 columns' ),
		description: __( 'Six columns' ),
		icon: (
			<SVG
				width="48"
				height="48"
				viewBox="0 0 48 48"
				xmlns="http://www.w3.org/2000/svg"
			>
				<Path
					fillRule="evenodd"
					clipRule="evenodd"
					d="M39 12C40.1046 12 41 12.8954 41 14V34C41 35.1046 40.1046 36 39 36H9C7.89543 36 7 35.1046 7 34V14C7 12.8954 7.89543 12 9 12H39ZM39 34V14H25V34H39ZM23 34H9V14H23V34Z"
				/>
			</SVG>
		),
		isDefault: true,
		innerBlocks: [ [ 'dp/column' , {width: 'six'}], [ 'dp/column' , {width: 'six'} ], [ 'dp/column' , {width: 'six'} ], [ 'dp/column' , {width: 'six'} ], [ 'dp/column' , {width: 'six'} ], [ 'dp/column' , {width: 'six'} ] ],
		scope: [ 'block' ],
	},
	{
		name: 'seven-columns-equal',
		title: __( '7 columns' ),
		description: __( 'Seven columns' ),
		icon: (
			<SVG
				width="48"
				height="48"
				viewBox="0 0 48 48"
				xmlns="http://www.w3.org/2000/svg"
			>
				<Path
					fillRule="evenodd"
					clipRule="evenodd"
					d="M39 12C40.1046 12 41 12.8954 41 14V34C41 35.1046 40.1046 36 39 36H9C7.89543 36 7 35.1046 7 34V14C7 12.8954 7.89543 12 9 12H39ZM39 34V14H25V34H39ZM23 34H9V14H23V34Z"
				/>
			</SVG>
		),
		isDefault: true,
		innerBlocks: [ [ 'dp/column' , {width: 'seven'}], [ 'dp/column' , {width: 'seven'} ], [ 'dp/column' , {width: 'seven'} ], [ 'dp/column' , {width: 'seven'} ], [ 'dp/column' , {width: 'seven'} ], [ 'dp/column' , {width: 'seven'} ], [ 'dp/column' , {width: 'seven'} ] ],
		scope: [ 'block' ],
	},
	{
		name: 'eight-columns-equal',
		title: __( '8 columns' ),
		description: __( 'Eight columns' ),
		icon: (
			<SVG
				width="48"
				height="48"
				viewBox="0 0 48 48"
				xmlns="http://www.w3.org/2000/svg"
			>
				<Path
					fillRule="evenodd"
					clipRule="evenodd"
					d="M39 12C40.1046 12 41 12.8954 41 14V34C41 35.1046 40.1046 36 39 36H9C7.89543 36 7 35.1046 7 34V14C7 12.8954 7.89543 12 9 12H39ZM39 34V14H25V34H39ZM23 34H9V14H23V34Z"
				/>
			</SVG>
		),
		isDefault: true,
		innerBlocks: [ [ 'dp/column' , {width: 'eight'}], [ 'dp/column' , {width: 'eight'} ], [ 'dp/column' , {width: 'eight'} ], [ 'dp/column' , {width: 'eight'} ], [ 'dp/column' , {width: 'eight'} ], [ 'dp/column' , {width: 'eight'} ], [ 'dp/column' , {width: 'eight'} ], [ 'dp/column' , {width: 'eight'} ] ],
		scope: [ 'block' ],
	},
	{
		name: 'nine-columns-equal',
		title: __( '9 columns' ),
		description: __( 'Nine columns' ),
		icon: (
			<SVG
				width="48"
				height="48"
				viewBox="0 0 48 48"
				xmlns="http://www.w3.org/2000/svg"
			>
				<Path
					fillRule="evenodd"
					clipRule="evenodd"
					d="M39 12C40.1046 12 41 12.8954 41 14V34C41 35.1046 40.1046 36 39 36H9C7.89543 36 7 35.1046 7 34V14C7 12.8954 7.89543 12 9 12H39ZM39 34V14H25V34H39ZM23 34H9V14H23V34Z"
				/>
			</SVG>
		),
		isDefault: true,
		innerBlocks: [ [ 'dp/column' , {width: 'nine'}], [ 'dp/column' , {width: 'nine'} ], [ 'dp/column' , {width: 'nine'} ], [ 'dp/column' , {width: 'nine'} ], [ 'dp/column' , {width: 'nine'} ], [ 'dp/column' , {width: 'nine'} ], [ 'dp/column' , {width: 'nine'} ], [ 'dp/column' , {width: 'nine'} ], [ 'dp/column' , {width: 'nine'} ] ],
		scope: [ 'block' ],
	},
	{
		name: 'ten-columns-equal',
		title: __( '10 columns' ),
		description: __( 'Ten columns' ),
		icon: (
			<SVG
				width="48"
				height="48"
				viewBox="0 0 48 48"
				xmlns="http://www.w3.org/2000/svg"
			>
				<Path
					fillRule="evenodd"
					clipRule="evenodd"
					d="M39 12C40.1046 12 41 12.8954 41 14V34C41 35.1046 40.1046 36 39 36H9C7.89543 36 7 35.1046 7 34V14C7 12.8954 7.89543 12 9 12H39ZM39 34V14H25V34H39ZM23 34H9V14H23V34Z"
				/>
			</SVG>
		),
		isDefault: true,
		innerBlocks: [ [ 'dp/column' , {width: 'ten'}], [ 'dp/column' , {width: 'ten'} ], [ 'dp/column' , {width: 'ten'} ], [ 'dp/column' , {width: 'ten'} ], [ 'dp/column' , {width: 'ten'} ], [ 'dp/column' , {width: 'ten'} ], [ 'dp/column' , {width: 'ten'} ], [ 'dp/column' , {width: 'ten'} ], [ 'dp/column' , {width: 'ten'} ], [ 'dp/column' , {width: 'ten'} ] ],
		scope: [ 'block' ],
	},
	{
		name: 'eleven-columns-equal',
		title: __( '11 columns' ),
		description: __( 'Eleven columns' ),
		icon: (
			<SVG
				width="48"
				height="48"
				viewBox="0 0 48 48"
				xmlns="http://www.w3.org/2000/svg"
			>
				<Path
					fillRule="evenodd"
					clipRule="evenodd"
					d="M39 12C40.1046 12 41 12.8954 41 14V34C41 35.1046 40.1046 36 39 36H9C7.89543 36 7 35.1046 7 34V14C7 12.8954 7.89543 12 9 12H39ZM39 34V14H25V34H39ZM23 34H9V14H23V34Z"
				/>
			</SVG>
		),
		isDefault: true,
		innerBlocks: [ [ 'dp/column' , {width: 'eleven'}], [ 'dp/column' , {width: 'eleven'} ], [ 'dp/column' , {width: 'eleven'} ], [ 'dp/column' , {width: 'eleven'} ], [ 'dp/column' , {width: 'eleven'} ], [ 'dp/column' , {width: 'eleven'} ], [ 'dp/column' , {width: 'eleven'} ], [ 'dp/column' , {width: 'eleven'} ], [ 'dp/column' , {width: 'eleven'} ], [ 'dp/column' , {width: 'eleven'} ], [ 'dp/column' , {width: 'eleven'} ] ],
		scope: [ 'block' ],
	},
	{
		name: 'twelve-columns-equal',
		title: __( '12 columns' ),
		description: __( 'Twelve columns' ),
		icon: (
			<SVG
				width="48"
				height="48"
				viewBox="0 0 48 48"
				xmlns="http://www.w3.org/2000/svg"
			>
				<Path
					fillRule="evenodd"
					clipRule="evenodd"
					d="M39 12C40.1046 12 41 12.8954 41 14V34C41 35.1046 40.1046 36 39 36H9C7.89543 36 7 35.1046 7 34V14C7 12.8954 7.89543 12 9 12H39ZM39 34V14H25V34H39ZM23 34H9V14H23V34Z"
				/>
			</SVG>
		),
		isDefault: true,
		innerBlocks: [ [ 'dp/column' , {width: 'twelve'}], [ 'dp/column' , {width: 'twelve'} ], [ 'dp/column' , {width: 'twelve'} ], [ 'dp/column' , {width: 'twelve'} ], [ 'dp/column' , {width: 'twelve'} ], [ 'dp/column' , {width: 'twelve'} ], [ 'dp/column' , {width: 'twelve'} ], [ 'dp/column' , {width: 'twelve'} ], [ 'dp/column' , {width: 'twelve'} ], [ 'dp/column' , {width: 'twelve'} ], [ 'dp/column' , {width: 'twelve'} ], [ 'dp/column' , {width: 'twelve'} ] ],
		scope: [ 'block' ],
	},
 ];
 
 export default variations;
 