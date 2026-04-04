import { registerBlockType } from '@wordpress/blocks';
import { columns as icon } from '@wordpress/icons';
import deprecated from './deprecated';
import variations from './variations';
import metadata from './block.json';
import edit from './edit';
import save from './save';

const { name, attributes, parent } = metadata;

registerBlockType( metadata.name, {
	variations,
	deprecated,
	attributes,
	parent,
	name,
	icon,
	edit,
	save
} );