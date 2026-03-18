<?php
$text = $attributes['text'] ?? 'Click Me';
$url = $attributes['url'] ?? '#';
$classes = $attributes['classes'] ?? ['btn', 'btn-primary'];
$open_in_new_tab = $attributes['openInNewTab'] ?? false;

$class_string = esc_attr(implode(' ', $classes));
$target = $open_in_new_tab ? ' target="_blank" rel="noopener noreferrer"' : '';

printf(
    '<a href="%s" class="%s"%s>%s</a>',
    esc_url($url),
    $class_string,
    $target,
    esc_html($text)
);
?>