import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { isCssGrid, gridColumns } = attributes;
    const classes = isCssGrid ? ['grid'] : ['row'];
    if (isCssGrid && gridColumns) classes.push(`grid-cols-${gridColumns}`);
    
    const blockProps = useBlockProps.save({ className: classes.join(' ') });
    const innerProps = useInnerBlocksProps.save({ className: isCssGrid ? 'grid-inner' : 'row-inner' });
    
    const styles = isCssGrid && gridColumns ? { '--bs-columns': gridColumns } : {};
    
    return (
        <div {...blockProps} style={styles}>
            <div {...innerProps} />
        </div>
    );
}