<?php
/**
 * Theme media bootstrap.
 */

namespace WP\Theme;

use WP\Media\ImageSizeRegistry;

/**
 * Declare default sizes for this base theme.
 * Keep these "plain array" so external code can filter them easily.
 */
function wp_default_image_sizes(): array
{
    return [
        [
            'size_id'        => 'card-image',
            'title'          => __('Card Image', 'wp-theme'),
            'x-size'         => 372,
            'y-size'         => 183,
            'crop'           => true,
            'show_in_editor' => true,
        ],
        // [
        //     'size_id'        => 'hero-1600x900',
        //     'title'          => __('Hero 16:9', 'wp-theme'),
        //     'x-size'         => 1600,
        //     'y-size'         => 900,
        //     'crop'           => ['center','top'],
        //     'show_in_editor' => true,
        // ],
        // // Example of a backend/automation helper size (not shown in editor):
        // [
        //     'size_id'        => 'og-image',
        //     'title'          => __('OpenGraph', 'wp-theme'),
        //     'x-size'         => 1200,
        //     'y-size'         => 630,
        //     'crop'           => true,
        //     'show_in_editor' => false,
        // ],
    ];
}

/**
 * Build the registry from defaults + external filters, then boot it.
 */
(function () {
    $defs = apply_filters('wp/image_sizes', wp_default_image_sizes());
    $registry = ImageSizeRegistry::fromArrays((array) $defs);
    $registry->boot();
})();
