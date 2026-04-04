/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import {
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';

export default function edit(props) {
    const blockProps = useBlockProps( {
		className: classnames( {
		} ),
	} );
    return (
        <>
            <hr />
                <div { ...blockProps } className="php-block-parts__admin-content">.: Sitemap Content :.</div>
            <hr />
        </>
    )
}