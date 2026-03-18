(function(wp) {
    const { createElement: el, Component, Fragment } = wp.element;
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, Button, TextControl, SelectControl, ToggleControl, IconButton } = wp.components;
    const { useState, useEffect } = wp.element;
    const { dispatch, useSelect } = wp.data;

    // Drag and Drop Components using WordPress native drag handles
    const SortableItem = ({ field, index, onUpdate, onRemove, moveItem }) => {
        const [isDragging, setIsDragging] = useState(false);
        
        return el('div', {
            className: 'bblocks-field-item' + (isDragging ? ' is-dragging' : ''),
            'data-index': index,
            draggable: true,
            onDragStart: (e) => {
                setIsDragging(true);
                e.dataTransfer.setData('text/plain', index);
                e.dataTransfer.effectAllowed = 'move';
            },
            onDragEnd: () => setIsDragging(false),
            onDragOver: (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
            },
            onDrop: (e) => {
                e.preventDefault();
                const fromIndex = parseInt(e.dataTransfer.getData('text/plain'));
                const toIndex = index;
                if (fromIndex !== toIndex) {
                    moveItem(fromIndex, toIndex);
                }
            }
        }, [
            // Drag Handle
            el('div', { 
                className: 'bblocks-drag-handle',
                title: 'Drag to reorder'
            }, '⋮⋮'),
            
            // Field Header
            el('div', { className: 'bblocks-field-header' }, [
                el('strong', {}, field.field_label || 'Untitled Field'),
                el('span', { className: 'field-type' }, ' (' + field.field_type + ')')
            ]),
            
            // Field Controls
            el('div', { className: 'bblocks-field-controls' }, [
                el(SelectControl, {
                    label: 'Field Type',
                    value: field.field_type,
                    options: [
                        { label: 'Text', value: 'text' },
                        { label: 'Email', value: 'email' },
                        { label: 'Textarea', value: 'textarea' },
                        { label: 'Select Dropdown', value: 'select' },
                        { label: 'Checkboxes', value: 'checkbox' },
                        { label: 'Radio Buttons', value: 'radio' },
                        { label: 'File Upload', value: 'file' },
                        { label: 'Date Picker', value: 'date' },
                        { label: 'Captcha (hCaptcha/reCaptcha)', value: 'captcha' }
                    ],
                    onChange: (value) => onUpdate(index, { ...field, field_type: value })
                }),
                
                el(TextControl, {
                    label: 'Field Label',
                    value: field.field_label,
                    onChange: (value) => onUpdate(index, { ...field, field_label: value })
                }),
                
                el(TextControl, {
                    label: 'Field Name (ID)',
                    value: field.field_name,
                    help: 'Unique identifier for this field',
                    onChange: (value) => onUpdate(index, { ...field, field_name: value.replace(/\s+/g, '_').toLowerCase() })
                }),
                
                el(ToggleControl, {
                    label: 'Required Field',
                    checked: field.field_required,
                    onChange: (checked) => onUpdate(index, { ...field, field_required: checked })
                }),
                
                // Conditional: Show options for select/radio/checkbox
                (field.field_type === 'select' || field.field_type === 'radio' || field.field_type === 'checkbox') && 
                    el(TextControl, {
                        label: 'Options (value:label per line)',
                        value: field.field_options,
                        onChange: (value) => onUpdate(index, { ...field, field_options: value }),
                        multiline: true,
                        rows: 3
                    }),
                
                // Conditional: Captcha type selector
                field.field_type === 'captcha' &&
                    el(SelectControl, {
                        label: 'Captcha Provider',
                        value: field.captcha_type || 'hcaptcha',
                        options: [
                            { label: 'hCaptcha', value: 'hcaptcha' },
                            { label: 'reCaptcha v2 (Checkbox)', value: 'recaptcha_v2' },
                            { label: 'reCaptcha v3 (Invisible)', value: 'recaptcha_v3' }
                        ],
                        onChange: (value) => onUpdate(index, { ...field, captcha_type: value })
                    }),
                
                // Remove Button
                el(Button, {
                    isDestructive: true,
                    isSmall: true,
                    onClick: () => onRemove(index),
                    className: 'bblocks-remove-field'
                }, 'Remove Field')
            ])
        ]);
    };

    // Main Edit Component
    const Edit = ({ attributes, setAttributes, clientId }) => {
        const blockProps = useBlockProps({
            className: 'bblocks-form-editor'
        });

        // Get ACF fields via meta
        const { form_fields = [] } = useSelect((select) => {
            const meta = select('core/editor').getEditedPostAttribute('meta') || {};
            return {
                form_fields: meta.form_fields || []
            };
        }, []);

        // Update ACF meta
        const updateMeta = (newFields) => {
            dispatch('core/editor').editPost({
                meta: { form_fields: newFields }
            });
        };

        const addField = () => {
            const newField = {
                field_type: 'text',
                field_label: 'New Field',
                field_name: 'new_field_' + Date.now(),
                field_required: false,
                field_options: '',
                captcha_type: 'hcaptcha'
            };
            updateMeta([...form_fields, newField]);
        };

        const updateField = (index, updatedField) => {
            const newFields = [...form_fields];
            newFields[index] = updatedField;
            updateMeta(newFields);
        };

        const removeField = (index) => {
            const newFields = form_fields.filter((_, i) => i !== index);
            updateMeta(newFields);
        };

        const moveItem = (fromIndex, toIndex) => {
            const newFields = [...form_fields];
            const [movedItem] = newFields.splice(fromIndex, 1);
            newFields.splice(toIndex, 0, movedItem);
            updateMeta(newFields);
        };

        return el(Fragment, {}, [
            // Inspector Controls (Sidebar)
            el(InspectorControls, {}, 
                el(PanelBody, { title: 'Form Settings', initialOpen: true }, [
                    el(Button, {
                        isPrimary: true,
                        onClick: addField,
                        className: 'bblocks-add-field-btn'
                    }, '+ Add Form Field'),
                    
                    el('div', { className: 'bblocks-fields-list' },
                        form_fields.map((field, index) => 
                            el(SortableItem, {
                                key: index,
                                field: field,
                                index: index,
                                onUpdate: updateField,
                                onRemove: removeField,
                                moveItem: moveItem
                            })
                        )
                    ),
                    
                    form_fields.length === 0 && 
                        el('p', { className: 'no-fields-message' }, 'Click "Add Form Field" to start building your form.')
                ])
            ),
            
            // Block Preview
            el('div', blockProps, [
                el('div', { className: 'bblocks-form-preview' }, [
                    el('h3', {}, 'Dynamic Form Preview'),
                    el('div', { className: 'form-preview-note' }, 
                        form_fields.length + ' field(s) configured. View on frontend to see full form with captcha.'
                    ),
                    el('div', { className: 'fields-summary' },
                        form_fields.map((field, i) => 
                            el('div', { 
                                key: i, 
                                className: 'field-summary-item' 
                            }, [
                                el('span', { className: 'field-icon' }, 
                                    field.field_type === 'captcha' ? '🛡️' : 
                                    field.field_type === 'email' ? '📧' : 
                                    field.field_type === 'textarea' ? '📝' : '📝'
                                ),
                                el('span', {}, field.field_label),
                                field.field_required && el('span', { className: 'required-badge' }, '*')
                            ])
                        )
                    )
                ])
            ])
        ]);
    };

    // Save returns null because this is a dynamic block
    const save = () => null;

    // Register the block
    registerBlockType('bblocks/dynamic-form', {
        edit: Edit,
        save: save
    });

})(window.wp);