<?php
function display_produtor_requests() {
    if (!current_user_can('administrator')) {
        return;
    }

    $users = get_users(array(
        'meta_key'   => 'produtor_application_status',
        'meta_value' => 'pending'
    ));

    echo '<h2>Solicitações para se tornar Produtor</h2>';
    echo '<ul>';
    
    foreach ($users as $user) {
        echo "<li>{$user->user_email} 
            <a href='" . admin_url("admin-post.php?action=approve_produtor&user_id={$user->ID}") . "'>Aprovar</a> | 
            <a href='" . admin_url("admin-post.php?action=reject_produtor&user_id={$user->ID}") . "'>Rejeitar</a>
        </li>";
    }

    echo '</ul>';
}

// Add this to an admin menu
function add_produtor_menu() {
    add_menu_page('Aprovar Produtores', 'Aprovar Produtores', 'administrator', 'approve-produtor', 'display_produtor_requests');
}
add_action('admin_menu', 'add_produtor_menu');

function approve_produtor() {
    if (!current_user_can('administrator') || !isset($_GET['user_id'])) {
        return;
    }

    $user_id = intval($_GET['user_id']);
    $user = new WP_User($user_id);
    $user->set_role('produtor');

    delete_user_meta($user_id, 'produtor_application_status');

    wp_redirect(admin_url('admin.php?page=approve-produtor'));
    exit;
}
add_action('admin_post_approve_produtor', 'approve_produtor');

function reject_produtor() {
    if (!current_user_can('administrator') || !isset($_GET['user_id'])) {
        return;
    }

    $user_id = intval($_GET['user_id']);
    delete_user_meta($user_id, 'produtor_application_status');

    wp_redirect(admin_url('admin.php?page=approve-produtor'));
    exit;
}
add_action('admin_post_reject_produtor', 'reject_produtor');
