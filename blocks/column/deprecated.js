/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
 import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

const deprecated = [
	{
		attributes: {
			verticalAlignment: {
				type: 'string',
			},
			width: {
				type: 'number',
				min: 0,
				max: 100,
			},
		},
		isEligible( { width } ) {
			return isFinite( width );
		},
		migrate( attributes ) {
			return {
				...attributes,
				width: `${ attributes.width }%`,
			};
		},
		save( { attributes } ) {
			const { verticalAlignment, width } = attributes;

			const wrapperClasses = classnames( {
				[ `is-vertically-aligned-${ verticalAlignment }` ]: verticalAlignment,
			} );

			const style = { flexBasis: width + '%' };

			return (
				<div className={ wrapperClasses } style={ style }>
					<InnerBlocks.Content />
				</div>
			);
		},
	},
	{
		attributes: {
			verticalAlignment: {
				type: "string"
			},
			width: {
				type: "string"
			},
			templateLock: {
				enum: [ "all", "insert", false ]
			}
		},
		migrate( attributes ) {
			return {
				...attributes,
				verticalAlign: 'align-self-start'
			};
		},
		save( { attributes } ) {
			const { verticalAlignment, width } = attributes;

			let sizeClasses;
			switch (width) {
				case 'twelve': // 12 columns
					sizeClasses = ['col-12', 'row-c12'];
					break;
				case 'eleven': // 11 columns
					sizeClasses = ['col-12', 'row-c11'];
					break;
				case 'ten': // 10 columns
					sizeClasses = ['col-12', 'row-c10'];
					break;
				case 'nine': // 9 columns
					sizeClasses = ['col-12', 'row-c9'];
					break;
				case 'eight': // 8 columns
					sizeClasses = ['col-12', 'row-c8'];
					break;
				case 'seven': // 7 columns
					sizeClasses = ['col-12', 'row-c7'];
					break;
				case 'six': // 6 columns
					sizeClasses = ['col-12', 'row-c6'];
					break;
				case 'five': // 5 columns
					sizeClasses = ['col-12', 'row-c5'];
					break;
				case 'quarter': // 4 columns
					sizeClasses = ['col-12', 'row-c4'];
					break;
				case 'three': // 3 columns
					sizeClasses = ['col-12','row-c3'];
					break;
				case 'half': // 2 columns
					sizeClasses = ['col-12','row-c2'];
					break;
				default:
					sizeClasses = ['col-12','row-c1'];
			}

			const wrapperClasses = classnames( ...sizeClasses, {
				[ `is-vertically-aligned-${ verticalAlignment }` ]: verticalAlignment,
			} );

			return (
				<div
					{ ...useBlockProps.save( {
						className: wrapperClasses,
					} ) }
				>
					<InnerBlocks.Content />
				</div>
			);
		}

	}
];

export default deprecated;
