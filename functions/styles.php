<?php
function my_theme_enqueue_styles() {
    wp_enqueue_style(
        'main-styles',
        get_template_directory_uri() . '/assets/main.css',
        array(),
        filemtime(get_template_directory() . '/assets/main.css')
    );
      wp_enqueue_style(
        'hunter-styles',
        get_template_directory_uri() . '/assets/hunter.css',
        array(),
        filemtime(get_template_directory() . '/assets/hunter.css')
    );
      wp_enqueue_style(
        'group-styles',
        get_template_directory_uri() . '/assets/group.css',
        array(),
        filemtime(get_template_directory() . '/assets/group.css')
    );
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');
?>