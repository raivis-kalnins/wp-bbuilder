import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

export default function save() {
    const blockProps = useBlockProps.save();
    const innerProps = useInnerBlocksProps.save();
    
    return (
        <div {...blockProps}>
            <div {...innerProps} />
        </div>
    );
}