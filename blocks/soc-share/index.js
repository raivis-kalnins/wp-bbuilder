import { registerBlockType } from '@wordpress/blocks';
import { share as icon } from '@wordpress/icons';
import metadata from './block.json';
import edit from './edit';
import save from './save';

const { attributes } = metadata;

registerBlockType( metadata.name, {
    attributes,
    icon,
    edit,
    save
} );