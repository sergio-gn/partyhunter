<?php
function hide_add_new_button_for_produtor() {
    $user = wp_get_current_user();
    
    if (in_array('produtor', $user->roles)) {
        $num_posts = count_user_posts($user->ID, 'post');
        $num_pages = count_user_posts($user->ID, 'page');

        if ($num_posts >= 5) {
            echo '<style>#wp-admin-bar-new-content, .page-title-action[href*="post-new.php"] { display: none !important; }</style>';
        }

        if ($num_pages >= 1) {
            echo '<style>.page-title-action[href*="post-new.php?post_type=page"] { display: none !important; }</style>';
        }
    }
}
add_action('admin_head', 'hide_add_new_button_for_produtor');

function handle_produtor_application() {
    if (!is_user_logged_in() || !isset($_POST['produtor_application'])) {
        return;
    }

    $user_id = get_current_user_id();
    $user = get_userdata($user_id);

    // Check if the user is already a "produtor"
    if (in_array('produtor', $user->roles)) {
        return;
    }

    // Store the request as user meta
    update_user_meta($user_id, 'produtor_application_status', 'pending');

    // Notify the admin
    wp_mail(get_option('admin_email'), 'Nova Solicitação de Produtor', "O usuário {$user->user_email} solicitou para se tornar um produtor.");
}
add_action('init', 'handle_produtor_application');

function produtor_post_limit_reached() {
    $current_user = wp_get_current_user();
    $post_count = count_user_posts( $current_user->ID, 'post' ); 
    return $post_count >= 3;
}