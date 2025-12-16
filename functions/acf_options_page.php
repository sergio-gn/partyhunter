<?php // Creates the Theme General Settings page
if ( function_exists('acf_add_options_page') ) {
    // Add a top-level menu page
    acf_add_options_page(array(
        'page_title'    => 'Theme General Settings',
        'menu_title'    => 'Theme Settings',
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'edit_posts',
        'redirect'      => true
    ));

    // Add a subpage under the top-level menu page
    acf_add_options_sub_page(array(
        'page_title'    => 'Locations',
        'menu_title'    => 'Locations',
        'parent_slug'   => 'theme-general-settings',
        'capability'    => 'edit_posts'
    ));
}
?>