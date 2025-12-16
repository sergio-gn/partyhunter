<?php
function custom_theme_register_menus() {
    register_nav_menus( array(
        'primary_menu' => esc_html__( 'Primary Menu', 'menu-config' ),
        'footer-1'     => esc_html__( 'Footer 1', 'menu-config' ),
        'footer-2'     => esc_html__( 'Footer 2', 'menu-config' ),
        'footer-3'     => esc_html__( 'Footer 3', 'menu-config' ),
        'footer-4'     => esc_html__( 'Footer 4', 'menu-config' ),
    ) );
}
add_action( 'after_setup_theme', 'custom_theme_register_menus' );

function title_tag(){
    add_theme_support( 'title-tag' );
}
add_action( 'after_setup_theme', 'title_tag' );

function theme_prefix_setup() {
    add_theme_support('custom-logo');
}
add_action('after_setup_theme', 'theme_prefix_setup');

function add_menu_toggle($item_output, $item, $depth, $args) {
    if (in_array('menu-item-has-children', $item->classes)) {
        $item_output = '<div class="menu_item_wrapper">' . $item_output;
        $item_output .= '<button class="sub-menu-toggle" aria-expanded="false">⌵</button>';
        $item_output .= '</div>';
    }
    return $item_output;
}
add_filter('walker_nav_menu_start_el', 'add_menu_toggle', 10, 4);

function allow_svg_upload( $mimes ) {
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
}
add_filter( 'upload_mimes', 'allow_svg_upload' );

function theme_support_admin_bar() {
    add_theme_support( 'admin-bar', array( 'callback' => '__return_true' ) );
}

add_action( 'after_setup_theme', 'theme_support_admin_bar' );

// add internal styling on wordpress backend
if ( ! function_exists('tdav_css') ) {
    function tdav_css($wp) {
        $wp .= ',' . get_bloginfo('stylesheet_directory') . '/internal.css';
        return $wp;
    }
}
add_filter( 'mce_css', 'tdav_css' );

add_theme_support('post-thumbnails');

add_filter('acf/settings/remove_wp_meta_box', '__return_false');