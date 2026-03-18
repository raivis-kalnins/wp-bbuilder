import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';
import edit from './edit';
import save from './save';

registerBlockType(metadata.name, {
    ...metadata,
    edit,
    save,
    transforms: {
        from: [{
            type: 'block',
            blocks: ['core/columns'],
            transform: (attrs, innerBlocks) => {
                const columns = innerBlocks.map(col => {
                    const width = parseInt(col.attributes.width) || 50;
                    const size = Math.round((width / 100) * 12) || 6;
                    return wp.blocks.createBlock('wpbb/column', { xs: 12, md: size }, col.innerBlocks);
                });
                return wp.blocks.createBlock('wpbb/row', { template: 'custom' }, columns);
            }
        }]
    }
});