import { __ } from '@wordpress/i18n';

export const ROW_TEMPLATES = [
    { name: 'equal-2', label: __('2 Columns (50/50)', 'wp-bblocks'), columns: [['wpbb/column',{xs:12,md:6}],['wpbb/column',{xs:12,md:6}]] },
    { name: 'equal-3', label: __('3 Columns (33/33/33)', 'wp-bblocks'), columns: [['wpbb/column',{xs:12,md:4}],['wpbb/column',{xs:12,md:4}],['wpbb/column',{xs:12,md:4}]] },
    { name: 'equal-4', label: __('4 Columns (25/25/25/25)', 'wp-bblocks'), columns: [['wpbb/column',{xs:12,sm:6,lg:3}],['wpbb/column',{xs:12,sm:6,lg:3}],['wpbb/column',{xs:12,sm:6,lg:3}],['wpbb/column',{xs:12,sm:6,lg:3}]] },
    { name: '1-2', label: __('1/3 + 2/3', 'wp-bblocks'), columns: [['wpbb/column',{xs:12,md:4}],['wpbb/column',{xs:12,md:8}]] },
    { name: '2-1', label: __('2/3 + 1/3', 'wp-bblocks'), columns: [['wpbb/column',{xs:12,md:8}],['wpbb/column',{xs:12,md:4}]] },
    { name: 'sidebar-left', label: __('Sidebar Left', 'wp-bblocks'), columns: [['wpbb/column',{xs:12,lg:4,xl:3}],['wpbb/column',{xs:12,lg:8,xl:9}]] },
    { name: 'sidebar-right', label: __('Sidebar Right', 'wp-bblocks'), columns: [['wpbb/column',{xs:12,lg:8,xl:9}],['wpbb/column',{xs:12,lg:4,xl:3}]] },
    { name: 'custom', label: __('Custom', 'wp-bblocks'), columns: [['wpbb/column',{xs:12}]] }
];

export const GUTTER_OPTIONS = [
    { label: '0', value: 'gx-0' },
    { label: '1 (0.25rem)', value: 'gx-1' },
    { label: '2 (0.5rem)', value: 'gx-2' },
    { label: '3 (1rem)', value: 'gx-3' },
    { label: '4 (1.5rem)', value: 'gx-4' },
    { label: '5 (3rem)', value: 'gx-5' }
];

export const ALIGN_OPTIONS = [
    { label: __('Start', 'wp-bblocks'), value: 'start' },
    { label: __('Center', 'wp-bblocks'), value: 'center' },
    { label: __('End', 'wp-bblocks'), value: 'end' },
    { label: __('Space Between', 'wp-bblocks'), value: 'between' },
    { label: __('Space Around', 'wp-bblocks'), value: 'around' }
];

export const VERTICAL_ALIGN_OPTIONS = [
    { label: __('Start', 'wp-bblocks'), value: 'start' },
    { label: __('Center', 'wp-bblocks'), value: 'center' },
    { label: __('End', 'wp-bblocks'), value: 'end' },
    { label: __('Stretch', 'wp-bblocks'), value: 'stretch' }
];