<?php
namespace WP\Media;

use WP\Media\ThumbnailSize;

final class ImageSizeRegistry
{
    /** @var ThumbnailSize[] */
    private array $sizes;

    /**
     * @param ThumbnailSize[] $sizes
     */
    public function __construct(array $sizes = [])
    {
        $this->sizes = array_values($sizes);
    }

    /** Build registry from simple arrays (filter-friendly). */
    public static function fromArrays(array $defs): self
    {
        $out = [];
        foreach ($defs as $def) {
            try {
                $out[] = ThumbnailSize::from((array) $def);
            } catch (\Throwable $e) {
                // Skip invalid entries. Uncomment to log:
                // error_log('[ImageSizeRegistry] '.$e->getMessage());
            }
        }
        return new self($out);
    }

    /** Register theme supports + sizes, and hook editor dropdown. */
    public function boot(): void
    {
        add_action('after_setup_theme', function () {
            // Ensure thumbnails are enabled before sizes are added.
            add_theme_support('post-thumbnails');
            foreach ($this->sizes as $size) {
                $size->register();
            }
        });

        add_filter('image_size_names_choose', function (array $choices): array {
            foreach ($this->sizes as $size) {
                if ($pair = $size->editorChoice()) {
                    $choices += $pair;
                }
            }
            return $choices;
        });
    }

    /** Expose sizes if you need them elsewhere. */
    public function all(): array
    {
        return $this->sizes;
    }
}
