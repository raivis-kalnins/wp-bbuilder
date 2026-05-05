<?php
namespace WP\Media;

use InvalidArgumentException;

/**
 * Immutable value object describing a single image size.
 */
final class ThumbnailSize
{
    public readonly string $id;
    public readonly string $title;
    public readonly int $width;
    public readonly int $height;
    /** @var bool|array{0:string,1:string} */
    public readonly bool|array $crop;
    public readonly bool $showInEditor;

    /**
     * @param bool|array{0:string,1:string} $crop  false|[x, y] where x in left|center|right and y in top|center|bottom
     */
    public function __construct(
        string $id,
        string $title,
        int $width,
        int $height,
        bool|array $crop = false,
        bool $showInEditor = true
    ) {
        $id = sanitize_key($id);
        if ($id === '') {
            throw new InvalidArgumentException('Image size "id" must be a non-empty key.');
        }
        if ($width <= 0) {
            throw new InvalidArgumentException('Image size "width" must be > 0.');
        }
        if ($height < 0) {
            throw new InvalidArgumentException('Image size "height" must be >= 0.');
        }

        if (is_array($crop)) {
            $crop = array_values($crop);
            $x = $crop[0] ?? 'center';
            $y = $crop[1] ?? 'center';
            $allowedX = ['left','center','right'];
            $allowedY = ['top','center','bottom'];
            if (!in_array($x, $allowedX, true) || !in_array($y, $allowedY, true)) {
                throw new InvalidArgumentException('Invalid crop array. Use [left|center|right, top|center|bottom].');
            }
            $crop = [$x, $y];
        }

        $this->id          = $id;
        $this->title       = $title;
        $this->width       = (int) $width;
        $this->height      = (int) $height;
        $this->crop        = $crop;
        $this->showInEditor = $showInEditor;
    }

    public static function from(array $a): self
    {
        return new self(
            (string) ($a['size_id'] ?? ''),
            (string) ($a['title'] ?? ''),
            (int) ($a['x-size'] ?? 0),
            (int) ($a['y-size'] ?? 0),
            $a['crop'] ?? false,
            (bool) ($a['show_in_editor'] ?? true),
        );
    }

    /** Register with WordPress. */
    public function register(): void
    {
        add_image_size($this->id, $this->width, $this->height, $this->crop);
    }

    /** Key-value for editor dropdown, or null if not shown. */
    public function editorChoice(): ?array
    {
        if (!$this->showInEditor || $this->title === '') {
            return null;
        }
        return [$this->id => esc_html($this->title)];
    }

    /** Array form (handy for debugging or exposing via filters). */
    public function toArray(): array
    {
        return [
            'size_id'        => $this->id,
            'title'          => $this->title,
            'x-size'         => $this->width,
            'y-size'         => $this->height,
            'crop'           => $this->crop,
            'show_in_editor' => $this->showInEditor,
        ];
    }
}