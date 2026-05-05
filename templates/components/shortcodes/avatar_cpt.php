<?php 

function avatar_cpt_shortcode($atts) {
    $atts = shortcode_atts(array(
        'show_avatar' => true,
        'class' => 'float-left'
    ), $atts);
    
    $author_id = get_the_author_meta('ID');
    $output = '<div class="case-study-author ' . esc_attr($atts['class']) . '">';
    
    if ($atts['show_avatar']) {
        $output .= get_avatar($author_id, 64);
    }
    
    $output .= '<span class="author-name">' . get_the_author() . '</span>';
    $output .= '</div>';
    
    return $output;
}

add_shortcode('avatar_cpt', 'avatar_cpt_shortcode');