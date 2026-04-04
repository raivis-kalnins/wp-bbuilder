// Run logic after WordPress editor is ready
wp.domReady(() => {

    // Move core blocks into custom categories
    const blocksToMove = {
        'core/paragraph': 'WP-content',
        'core/heading': 'WP-content',
        'core/list': 'WP-content',
        'core/image': 'WP-content',
        'core/file': 'WP-content',
        'core/media-text': 'WP-layout',
        'core/video': 'WP-content',
        'core/separator': 'WP-layout',
        'core/spacer': 'WP-layout',
        'core/shortcode': 'WP-content',
    };

    Object.entries(blocksToMove).forEach(([blockName, newCategory]) => {
        const block = wp.blocks.getBlockType(blockName);
        if (block) {
            wp.blocks.unregisterBlockType(blockName);
            wp.blocks.registerBlockType(blockName, { ...block, category: newCategory });
        }
    });


});

// Define plugin blocks to move to custom categories
const pluginBlocksToMove = {
    'contact-form-7/contact-form-selector': 'WP-content',
    'cb/carousel-v2': 'WP-layout',
};

// Attempt to move plugin blocks, retrying until registered
function moveBlockWhenAvailable(blockName, newCategory, retries = 20) {
    const block = wp.blocks.getBlockType(blockName);

    if (block) {
        wp.blocks.unregisterBlockType(blockName);
        wp.blocks.registerBlockType(blockName, { ...block, category: newCategory });
    } else if (retries > 0) {
        setTimeout(() => moveBlockWhenAvailable(blockName, newCategory, retries - 1), 2000);
    }
}

// Start moving plugin blocks
Object.entries(pluginBlocksToMove).forEach(([blockName, newCategory]) => {
    moveBlockWhenAvailable(blockName, newCategory);
});

// Style custom block category headings by title text
function styleCategoryHeadingsWhenTheyAppear() {
    const targetSelector = '.block-editor-inserter__panel-title';

    // Styling function (can be reused)
    function styleIfMatches(text, element) {
        if (/WP Stylised/i.test(text)) {
            element.style.borderBottom = '4px solid #007cba';
            element.style.fontWeight = 'bold';
            element.style.fontSize = '14px';
            element.style.paddingBottom = '4px';
        } else if (/ex/i.test(text)) {
            element.style.borderTop = '2px dashed black';
            element.style.paddingTop = '64px';
            element.style.marginTop = '32px';
            element.style.width = '240px';
        }
    }

    // Initial styling attempt for any headings already present
    document.querySelectorAll(targetSelector).forEach(h2 => {
        styleIfMatches(h2.textContent.trim(), h2);
    });

    // Watch for any future headings being added
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            mutation.addedNodes.forEach(node => {
                if (
                    node.nodeType === Node.ELEMENT_NODE &&
                    node.matches?.(targetSelector)
                ) {
                    styleIfMatches(node.textContent.trim(), node);
                } else if (
                    node.nodeType === Node.ELEMENT_NODE
                ) {
                    // In case new nodes contain matching elements deeper
                    node.querySelectorAll?.(targetSelector)?.forEach(h2 => {
                        styleIfMatches(h2.textContent.trim(), h2);
                    });
                }
            });
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });
}

wp.domReady(() => {
    styleCategoryHeadingsWhenTheyAppear();
});

