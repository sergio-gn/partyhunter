<?php
/**
 * Social functions: user search, follow/unfollow and posts with visibility
 */

/**
 * Register Custom Post Type for Mural (Profile Messages)
 */
function register_mural_post_type() {
    $labels = array(
        'name'                  => 'Murais',
        'singular_name'         => 'Mural',
        'menu_name'             => 'Murais',
        'add_new'               => 'Adicionar Novo',
        'add_new_item'          => 'Adicionar Novo Mural',
        'edit_item'             => 'Editar Mural',
        'new_item'              => 'Novo Mural',
        'view_item'             => 'Ver Mural',
        'search_items'          => 'Buscar Murais',
        'not_found'             => 'Nenhum mural encontrado',
        'not_found_in_trash'    => 'Nenhum mural encontrado na lixeira',
    );
    
    $args = array(
        'labels'                => $labels,
        'public'                => false, // Don't show in archives, search, etc.
        'publicly_queryable'    => false, // Not queryable via URL
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_icon'             => 'dashicons-format-chat',
        'query_var'             => false,
        'rewrite'               => false,
        'capability_type'       => 'post',
        'map_meta_cap'          => true,
        'has_archive'           => false,
        'hierarchical'          => false,
        'menu_position'         => 25,
        'supports'              => array('title', 'comments'),
        'show_in_rest'          => false,
        'exclude_from_search'   => true,
        'comment_status'        => 'open', // Enable comments for mural messages
    );

    register_post_type('mural', $args);
}
add_action('init', 'register_mural_post_type');

/**
 * Add custom columns to mural admin list
 */
function add_mural_admin_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = 'Título do Mural';
    $new_columns['user'] = 'Usuário';
    $new_columns['comments'] = 'Mensagens';
    $new_columns['date'] = $columns['date'];
    return $new_columns;
}
add_filter('manage_mural_posts_columns', 'add_mural_admin_columns');

/**
 * Populate custom columns for mural
 */
function populate_mural_admin_columns($column, $post_id) {
    switch ($column) {
        case 'user':
            $user_id = get_post_meta($post_id, 'ph_mural_user_id', true);
            if ($user_id) {
                $user = get_user_by('id', $user_id);
                if ($user) {
                    echo esc_html($user->display_name ?: $user->user_login);
                } else {
                    echo 'N/A';
                }
            } else {
                echo 'N/A';
            }
            break;
        case 'comments':
            $comment_count = get_comments_number($post_id);
            echo $comment_count . ' mensagem(ns)';
            break;
    }
}
add_action('manage_mural_posts_custom_column', 'populate_mural_admin_columns', 10, 2);

// Search users by name or email
function ph_search_users($query, $limit = 20) {
    $query = sanitize_text_field($query);
    if (empty($query)) {
        return [];
    }

    $args = [
        'search'         => "*{$query}*",
        'search_columns' => ['user_login','user_nicename','display_name','user_email'],
        'number'         => $limit,
        'fields'         => ['ID','display_name','user_email']
    ];

    $users = get_users($args);
    return $users;
}

// Follow a user: current user follows $target_id
function ph_follow_user($target_id, $user_id = null) {
    if (!$user_id) $user_id = get_current_user_id();
    $target_id = intval($target_id);
    if (!$user_id || !$target_id || $user_id === $target_id) {
        return new WP_Error('invalid', 'Invalid user IDs');
    }

    $following = (array) get_user_meta($user_id, 'following_users', true);
    $followers = (array) get_user_meta($target_id, 'followers_users', true);

    if (!in_array($target_id, $following)) {
        $following[] = $target_id;
        update_user_meta($user_id, 'following_users', $following);
    }

    if (!in_array($user_id, $followers)) {
        $followers[] = $user_id;
        update_user_meta($target_id, 'followers_users', $followers);
    }

    return true;
}

function ph_unfollow_user($target_id, $user_id = null) {
    if (!$user_id) $user_id = get_current_user_id();
    $target_id = intval($target_id);
    if (!$user_id || !$target_id || $user_id === $target_id) {
        return new WP_Error('invalid', 'Invalid user IDs');
    }

    $following = (array) get_user_meta($user_id, 'following_users', true);
    $followers = (array) get_user_meta($target_id, 'followers_users', true);

    if (in_array($target_id, $following)) {
        $following = array_values(array_diff($following, [$target_id]));
        update_user_meta($user_id, 'following_users', $following);
    }

    if (in_array($user_id, $followers)) {
        $followers = array_values(array_diff($followers, [$user_id]));
        update_user_meta($target_id, 'followers_users', $followers);
    }

    return true;
}

function ph_is_following($target_id, $user_id = null) {
    if (!$user_id) $user_id = get_current_user_id();
    if (!$user_id || !$target_id) return false;
    $following = (array) get_user_meta($user_id, 'following_users', true);
    return in_array(intval($target_id), $following);
}

function ph_get_followed_users($user_id = null) {
    if (!$user_id) $user_id = get_current_user_id();
    if (!$user_id) return [];
    $following = (array) get_user_meta($user_id, 'following_users', true);
    // 1. Convert all values to integers
    $following = array_map('intval', $following);
    // 2. 🚨 Ignore/Remove any values that evaluate to false (i.e., 0)
    $following = array_filter($following);
    // Returns the cleaned array of follower IDs
    return $following;
}

function ph_get_followers($user_id = null) {
    if (!$user_id) $user_id = get_current_user_id();
    if (!$user_id) return [];
    
    // Retrieves the raw list of follower IDs from user metadata
    $followers = (array) get_user_meta($user_id, 'followers_users', true);
    
    // 1. Convert all values to integers
    $followers = array_map('intval', $followers);
    
    // 2. 🚨 Ignore/Remove any values that evaluate to false (i.e., 0)
    $followers = array_filter($followers);
    
    // Returns the cleaned array of follower IDs
    return $followers;
}

/**
 * Get or create a mural post for a user's profile messages
 * Each user has a special "mural" post type that acts as their message wall
 */
function ph_get_or_create_wall_post($user_id) {
    if (!$user_id) return null;
    
    // Check if mural post already exists
    $mural_post_id = get_user_meta($user_id, 'ph_mural_post_id', true);
    
    if ($mural_post_id) {
        $post = get_post($mural_post_id);
        if ($post && $post->post_status === 'publish' && $post->post_type === 'mural') {
            return $post;
        }
    }
    
    // Create a new mural post
    $user = get_user_by('id', $user_id);
    if (!$user) return null;
    
    $post_data = [
        'post_author'     => $user_id,
        'post_content'    => '', // Empty content, it's just a container for comments
        'post_status'     => 'publish',
        'post_title'      => 'Mural: ' . ($user->display_name ?: $user->user_login),
        'post_type'       => 'mural', // Use custom post type
        'comment_status' => 'open' // Enable comments for messages
    ];
    
    $mural_post_id = wp_insert_post($post_data, true);
    if (is_wp_error($mural_post_id)) return null;
    
    // Mark it as a mural post and link to user
    update_post_meta($mural_post_id, 'ph_is_mural', true);
    update_post_meta($mural_post_id, 'ph_mural_user_id', $user_id);
    update_user_meta($user_id, 'ph_mural_post_id', $mural_post_id);
    
    return get_post($mural_post_id);
}

/**
 * Get profile comments (comments on user's wall post)
 */
function ph_get_profile_comments($user_id, $limit = 20) {
    $wall_post = ph_get_or_create_wall_post($user_id);
    if (!$wall_post) return [];
    
    $args = [
        'post_id' => $wall_post->ID,
        'status' => 'approve',
        'number' => $limit,
        'orderby' => 'comment_date',
        'order' => 'DESC'
    ];
    
    return get_comments($args);
}

// Create a post with visibility meta: 'group' or 'followers'
function ph_create_user_post($content, $visibility = 'followers', $group_id = null) {
    if (!is_user_logged_in()) return new WP_Error('unauth', 'User not logged in');
    $user_id = get_current_user_id();
    $content = wp_kses_post($content);

    // If posting to a group, ensure the user is member of that group
    if ($visibility === 'group') {
        if (empty($group_id)) {
            return new WP_Error('missing_group', 'Group ID required for group posts');
        }
        if (!function_exists('is_user_in_group') || !is_user_in_group($group_id, $user_id)) {
            return new WP_Error('not_member', 'Você não é membro deste grupo');
        }
    }

    $post_data = [
        'post_author'  => $user_id,
        'post_content' => $content,
        'post_status'  => 'publish',
        'post_title'   => wp_trim_words(wp_strip_all_tags($content), 6, '...'),
        'post_type'    => 'post'
    ];

    $post_id = wp_insert_post($post_data, true);
    if (is_wp_error($post_id)) return $post_id;

    update_post_meta($post_id, 'post_visibility', $visibility);
    if ($visibility === 'group' && $group_id) {
        update_post_meta($post_id, 'group_id', sanitize_text_field($group_id));
    }

    return $post_id;
}

// Get posts belonging to a group
function ph_get_group_posts($group_id, $limit = 20) {
    $args = [
        'post_type' => 'post',
        'meta_query' => [
            ['key' => 'post_visibility', 'value' => 'group'],
            ['key' => 'group_id', 'value' => sanitize_text_field($group_id)]
        ],
        'posts_per_page' => $limit,
        'orderby' => 'date',
        'order' => 'DESC'
    ];
    $q = new WP_Query($args);
    return $q->posts;
}

// Get posts from users the given user follows where visibility = 'followers'
function ph_get_followed_users_posts($user_id = null, $limit = 20) {
    if (!$user_id) $user_id = get_current_user_id();
    if (!$user_id) return [];
    $followed = ph_get_followed_users($user_id);
    if (empty($followed)) return [];

    $args = [
        'post_type' => 'post',
        'author' => $followed,
        'meta_query' => [
            ['key' => 'post_visibility', 'value' => 'followers']
        ],
        'posts_per_page' => $limit,
        'orderby' => 'date',
        'order' => 'DESC'
    ];

    $q = new WP_Query($args);
    return $q->posts;
}

/* AJAX handlers */
function ph_ajax_search_users() {
    if (!is_user_logged_in()) wp_send_json_error('Unauthorized', 401);
    check_ajax_referer('social_nonce', 'nonce');

    $q = isset($_POST['q']) ? sanitize_text_field($_POST['q']) : '';
    $users = ph_search_users($q, 30);
    $result = [];
    foreach ($users as $u) {
        $result[] = [
            'ID' => $u->ID,
            'display_name' => $u->display_name,
            'email' => $u->user_email,
            'is_following' => ph_is_following($u->ID),
            'profile_url' => ph_get_hunter_url($u->ID)
        ];
    }
    wp_send_json_success($result);
}
add_action('wp_ajax_search_users', 'ph_ajax_search_users');

function ph_ajax_follow_user() {
    if (!is_user_logged_in()) wp_send_json_error('Unauthorized', 401);
    check_ajax_referer('social_nonce', 'nonce');
    $target = isset($_POST['target_id']) ? intval($_POST['target_id']) : 0;
    if (!$target) wp_send_json_error('Missing target');
    $current = get_current_user_id();
    $res = ph_follow_user($target, $current);
    if (is_wp_error($res)) wp_send_json_error($res->get_error_message());

    // Build follower list HTML for the target
    $followers = ph_get_followers($target);
    $followers_count = count($followers);
    $followers_html = '';
    foreach ($followers as $fid) {
        $u = get_user_by('id', $fid);
        if (!$u) continue;
        $followers_html .= '<a href="' . esc_url(home_url('/' . $u->user_nicename . '/')) .'" style="display:flex;align-items:center;gap:0.5rem;">' . get_avatar($u->ID, 32) . '<span style="font-size:0.9rem;">' . esc_html($u->display_name) . '</span></a>';
    }

    $current_following = ph_get_followed_users($current);
    $following_count = count($current_following);

    wp_send_json_success([
        'following' => true,
        'target' => $target,
        'followers_count' => $followers_count,
        'followers_html' => $followers_html,
        'following_count' => $following_count
    ]);
}
add_action('wp_ajax_follow_user', 'ph_ajax_follow_user');

function ph_ajax_unfollow_user() {
    if (!is_user_logged_in()) wp_send_json_error('Unauthorized', 401);
    check_ajax_referer('social_nonce', 'nonce');
    $target = isset($_POST['target_id']) ? intval($_POST['target_id']) : 0;
    if (!$target) wp_send_json_error('Missing target');
    $current = get_current_user_id();
    $res = ph_unfollow_user($target, $current);
    if (is_wp_error($res)) wp_send_json_error($res->get_error_message());

    // Build follower list HTML for the target
    $followers = ph_get_followers($target);
    $followers_count = count($followers);
    $followers_html = '';
    foreach ($followers as $fid) {
        $u = get_user_by('id', $fid);
        if (!$u) continue;
        $followers_html .= '<a href="' . esc_url(get_author_posts_url($u->ID)) . '" style="display:flex;align-items:center;gap:0.5rem;">' . get_avatar($u->ID, 32) . '<span style="font-size:0.9rem;">' . esc_html($u->display_name) . '</span></a>';
    }

    $current_following = ph_get_followed_users($current);
    $following_count = count($current_following);

    wp_send_json_success([
        'following' => false,
        'target' => $target,
        'followers_count' => $followers_count,
        'followers_html' => $followers_html,
        'following_count' => $following_count
    ]);
}
add_action('wp_ajax_unfollow_user', 'ph_ajax_unfollow_user');

/**
 * AJAX handler: Create a comment on user's profile wall (instead of a post)
 */
function ph_ajax_create_profile_comment() {
    if (!is_user_logged_in()) wp_send_json_error('Unauthorized', 401);
    check_ajax_referer('social_nonce', 'nonce');
    
    $content = isset($_POST['content']) ? wp_kses_post($_POST['content']) : '';
    $user_id = get_current_user_id(); // The user posting on their own wall
    
    if (empty($content)) wp_send_json_error('Content is empty');
    
    // Get or create wall post for this user
    $wall_post = ph_get_or_create_wall_post($user_id);
    if (!$wall_post) wp_send_json_error('Could not create wall post');
    
    // Create comment on the wall post
    $commentdata = [
        'comment_post_ID' => $wall_post->ID,
        'comment_author' => wp_get_current_user()->display_name ?: wp_get_current_user()->user_login,
        'comment_author_email' => wp_get_current_user()->user_email,
        'user_id' => $user_id,
        'comment_content' => $content,
        'comment_approved' => 1,
    ];
    
    $comment_id = wp_insert_comment($commentdata);
    if (!$comment_id || is_wp_error($comment_id)) {
        wp_send_json_error('Erro ao criar comentário');
    }
    
    // Build comment HTML
    $user = get_user_by('id', $user_id);
    $author = esc_html($user->display_name ?: $user->user_login);
    $time_label = 'Agora';
    $content_html = wpautop(esc_html($content));
    
    $comment_html = '<div class="profile-comment" data-comment-id="' . esc_attr($comment_id) . '" style="padding:0.75rem;border:1px solid #eee;border-radius:0.5rem;margin-bottom:0.5rem;background:#fff;">';
    $comment_html .= '<div style="display:flex;justify-content:space-between;align-items:center;">';
    $comment_html .= '<strong>' . $author . '</strong>';
    $comment_html .= '<small style="color:#666;">' . $time_label . '</small>';
    $comment_html .= '</div>';
    $comment_html .= '<div style="margin-top:0.5rem;color:#444;">' . $content_html . '</div>';
    
    // Add delete button if user is author
    $can_delete = ($user_id == get_current_user_id()) || current_user_can('administrator');
    if ($can_delete) {
        $comment_html .= '<div style="margin-top:0.5rem;">';
        $comment_html .= '<button class="ph-delete-profile-comment-btn" data-comment-id="' . esc_attr($comment_id) . '" style="background:#f8d7da;color:#721c24;border:none;padding:0.5rem 0.75rem;border-radius:0.5rem;cursor:pointer;font-size:0.9rem;">Excluir</button>';
        $comment_html .= '</div>';
    }
    
    $comment_html .= '</div>';
    
    wp_send_json_success(['comment_id' => $comment_id, 'comment_html' => $comment_html]);
}
add_action('wp_ajax_create_profile_comment', 'ph_ajax_create_profile_comment');

// Keep the old function for backward compatibility (group posts, etc.)
function ph_ajax_create_user_post() {
    if (!is_user_logged_in()) wp_send_json_error('Unauthorized', 401);
    check_ajax_referer('social_nonce', 'nonce');
    $content = isset($_POST['content']) ? wp_kses_post($_POST['content']) : '';
    $visibility = isset($_POST['visibility']) ? sanitize_text_field($_POST['visibility']) : 'followers';
    $group_id = isset($_POST['group_id']) ? sanitize_text_field($_POST['group_id']) : null;

    if (empty($content)) wp_send_json_error('Content is empty');

    $post_id = ph_create_user_post($content, $visibility, $group_id);
    if (is_wp_error($post_id)) wp_send_json_error($post_id->get_error_message());
    // Build a small HTML snippet for the created post to return to the client
    $post = get_post($post_id);
    $author = get_user_by('id', $post->post_author);
    $post_html = '';
    if ($post) {
        $post_content = wp_trim_words(wp_strip_all_tags($post->post_content), 40, '...');
        $time_label = 'Agora';
        $post_html = '<div class="post-card" data-post-id="' . esc_attr($post_id) . '">';
        $post_html .= '<div style="display:flex;justify-content:space-between;align-items:center;">';
        $post_html .= '<strong>' . esc_html(get_the_title($post)) . '</strong>';
        $post_html .= '<small style="color:#666;">' . esc_html($time_label) . '</small>';
        $post_html .= '</div>';
        $post_html .= '<div style="margin-top:0.5rem;color:#444;">' . esc_html($post_content) . '</div>';
        $post_html .= '</div>';
    }

    wp_send_json_success(['post_id' => $post_id, 'post_html' => $post_html]);
}
add_action('wp_ajax_create_user_post', 'ph_ajax_create_user_post');

/**
 * Get comments for a group (comments on the group post)
 */
function ph_get_group_comments($group_id, $limit = 50) {
    if (empty($group_id)) return [];
    if (!function_exists('get_group_post_by_id')) return [];
    $group_post = get_group_post_by_id($group_id);
    if (!$group_post) return [];

    $args = [
        'post_id' => $group_post->ID,
        'status' => 'approve',
        'number' => $limit,
        'orderby' => 'comment_date',
        'order' => 'DESC'
    ];
    $comments = get_comments($args);
    return $comments;
}

/**
 * AJAX handler: create a comment on a group post, optionally with an image upload
 */
function ph_ajax_create_group_comment() {
    if (!is_user_logged_in()) wp_send_json_error('Unauthorized', 401);
    check_ajax_referer('social_nonce', 'nonce');

    $group_id = isset($_POST['group_id']) ? sanitize_text_field($_POST['group_id']) : '';
    $content = isset($_POST['content']) ? wp_kses_post($_POST['content']) : '';

    if (empty($group_id)) wp_send_json_error('Missing group');
    if (empty($content) && empty($_FILES['image'])) wp_send_json_error('Empty comment');

    if (!function_exists('get_group_post_by_id')) wp_send_json_error('Group functions unavailable');
    $group_post = get_group_post_by_id($group_id);
    if (!$group_post) wp_send_json_error('Group not found');

    // Ensure user is member
    $user_id = get_current_user_id();
    if (!function_exists('is_user_in_group') || !is_user_in_group($group_id, $user_id)) {
        wp_send_json_error('Você não é membro deste grupo');
    }

    $commentdata = [
        'comment_post_ID' => $group_post->ID,
        'comment_author' => wp_get_current_user()->display_name ?: wp_get_current_user()->user_login,
        'user_id' => $user_id,
        'comment_content' => $content,
        'comment_approved' => 1,
    ];

    $comment_id = wp_insert_comment($commentdata);
    if (!$comment_id) wp_send_json_error('Erro ao criar comentário');

    $attachment_id = 0;
    if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $file = $_FILES['image'];
        $overrides = ['test_form' => false];
        $moved = wp_handle_upload($file, $overrides);
        if (!empty($moved['file'])) {
            $filetype = wp_check_filetype($moved['file']);
            $attachment = [
                'post_mime_type' => $moved['type'],
                'post_title' => sanitize_file_name(basename($moved['file'])),
                'post_content' => '',
                'post_status' => 'inherit'
            ];
            $attach_id = wp_insert_attachment($attachment, $moved['file'], 0);
            if (!is_wp_error($attach_id)) {
                $attach_data = wp_generate_attachment_metadata($attach_id, $moved['file']);
                wp_update_attachment_metadata($attach_id, $attach_data);
                update_comment_meta($comment_id, 'comment_image', $attach_id);
                $attachment_id = $attach_id;
            }
        }
    }

    // Build comment HTML to return
    $user = get_user_by('id', $user_id);
    if (!$user) {
        wp_send_json_error('User not found');
    }
    
    $profile_url = site_url('/' . $user->user_nicename . '/');
    $author = esc_html($user->display_name ?: $user->user_login);
    $time_label = 'Agora';
    $content_html = wpautop(esc_html($content));
    $image_html = '';
    if ($attachment_id) {
        $image_html = wp_get_attachment_image($attachment_id, 'medium');
    }

    $comment_html = '<div class="group-comment" data-comment-id="' . esc_attr($comment_id) . '" style="padding:0.75rem;border:1px solid #eee;border-radius:0.5rem;margin-bottom:0.5rem;background:#fff;">';
    $comment_html .= '<div style="display:flex;gap:0.75rem;align-items:flex-start;">';
    $comment_html .= '<div style="flex:1;">';
    $comment_html .= '<div style="display:flex;justify-content:space-between;align-items:center;">';
    $comment_html .= '<strong><a href="' . esc_url($profile_url) . '">' . $author . '</a></strong>';
    $comment_html .= '<small style="color:#666;">' . $time_label . '</small>';
    $comment_html .= '</div>';
    $comment_html .= '<div style="margin-top:0.5rem;color:#444;">' . $content_html . '</div>';
    if ($image_html) $comment_html .= '<div style="margin-top:0.5rem;">' . $image_html . '</div>';
    
    // Add delete button if user is author or admin
    $can_delete = ($user_id == get_current_user_id()) || current_user_can('administrator');
    if ($can_delete) {
        $comment_html .= '<div style="margin-top:0.5rem;">';
        $comment_html .= '<button class="ph-delete-comment-btn" data-comment-id="' . esc_attr($comment_id) . '" style="background:#f8d7da;color:#721c24;border:none;padding:0.5rem 0.75rem;border-radius:0.5rem;cursor:pointer;">Excluir</button>';
        $comment_html .= '</div>';
    }
    
    $comment_html .= '</div></div></div>';

    wp_send_json_success(['comment_id' => $comment_id, 'comment_html' => $comment_html]);
}
add_action('wp_ajax_create_group_comment', 'ph_ajax_create_group_comment');

/**
 * AJAX handler: delete a group comment (author or admin)
 */
function ph_ajax_delete_group_comment() {
    if (!is_user_logged_in()) wp_send_json_error('Unauthorized', 401);
    check_ajax_referer('social_nonce', 'nonce');

    $comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;
    if (!$comment_id) wp_send_json_error('Missing comment id');

    $comment = get_comment($comment_id);
    if (!$comment) wp_send_json_error('Comentário não encontrado');

    $current = get_current_user_id();
    $is_author = ($comment->user_id && intval($comment->user_id) === $current);
    $is_admin = current_user_can('administrator');
    if (!$is_author && !$is_admin) wp_send_json_error('Sem permissão para excluir este comentário');

    // If comment has attached image, delete attachment
    $attach_id = get_comment_meta($comment_id, 'comment_image', true);
    if ($attach_id) {
        // delete attachment file
        if (function_exists('wp_delete_attachment')) {
            wp_delete_attachment(intval($attach_id), true);
        }
        delete_comment_meta($comment_id, 'comment_image');
    }

    $deleted = wp_delete_comment($comment_id, true);
    if (!$deleted) wp_send_json_error('Erro ao deletar comentário');

    wp_send_json_success(['deleted' => true, 'comment_id' => $comment_id]);
}
add_action('wp_ajax_delete_group_comment', 'ph_ajax_delete_group_comment');

/**
 * AJAX handler: delete a profile comment (author or admin)
 */
function ph_ajax_delete_profile_comment() {
    if (!is_user_logged_in()) wp_send_json_error('Unauthorized', 401);
    check_ajax_referer('social_nonce', 'nonce');

    $comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;
    if (!$comment_id) wp_send_json_error('Missing comment id');

    $comment = get_comment($comment_id);
    if (!$comment) wp_send_json_error('Comment not found');

    $user_id = get_current_user_id();
    $is_author = ($comment->user_id == $user_id);
    $is_admin = current_user_can('administrator');

    if (!$is_author && !$is_admin) {
        wp_send_json_error('Você não tem permissão para excluir este comentário');
    }

    $deleted = wp_delete_comment($comment_id, true);
    if (!$deleted) wp_send_json_error('Erro ao deletar comentário');

    wp_send_json_success(['deleted' => true, 'comment_id' => $comment_id]);
}
add_action('wp_ajax_delete_profile_comment', 'ph_ajax_delete_profile_comment');

/**
 * AJAX handler: admin delete any post
 */
function ph_ajax_admin_delete_post() {
    if (!is_user_logged_in()) wp_send_json_error('Unauthorized', 401);
    check_ajax_referer('social_nonce', 'nonce');

    if (!current_user_can('administrator')) wp_send_json_error('Sem permissão');

    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    if (!$post_id) wp_send_json_error('Missing post id');

    $post = get_post($post_id);
    if (!$post) wp_send_json_error('Post não encontrado');

    $deleted = wp_delete_post($post_id, true);
    if (!$deleted) wp_send_json_error('Erro ao deletar post');

    wp_send_json_success(['deleted' => true, 'post_id' => $post_id]);
}
add_action('wp_ajax_admin_delete_post', 'ph_ajax_admin_delete_post');
/**
 * AJAX handler: update user profile description and tags (owner only)
 */
function ph_ajax_update_profile() {
    if (!is_user_logged_in()) wp_send_json_error('Unauthorized', 401);
    check_ajax_referer('social_nonce', 'nonce');

    $current = get_current_user_id();
    $target = isset($_POST['user_id']) ? intval($_POST['user_id']) : $current;
    if ($target !== $current) wp_send_json_error('Sem permissão', 403);

    $description = isset($_POST['description']) ? wp_kses_post(wp_unslash($_POST['description'])) : '';

    // Prefer new structured payload if provided
    $structured_raw = isset($_POST['structured_tags']) ? wp_unslash($_POST['structured_tags']) : '';
    $structured = [
        'estado_civil' => '',
        'estilo_musica' => [],
        'bebida' => [],
        'custom' => [],
        'city' => ''
    ];

    if (!empty($structured_raw)) {
        $decoded = json_decode($structured_raw, true);
        if (is_array($decoded)) {
            $structured['estado_civil'] = isset($decoded['estado_civil']) ? sanitize_text_field($decoded['estado_civil']) : '';
            $structured['estilo_musica'] = isset($decoded['estilo_musica']) && is_array($decoded['estilo_musica']) ? array_values(array_map('sanitize_text_field', $decoded['estilo_musica'])) : [];
            $structured['bebida'] = isset($decoded['bebida']) && is_array($decoded['bebida']) ? array_values(array_map('sanitize_text_field', $decoded['bebida'])) : [];
            $structured['custom'] = isset($decoded['custom']) && is_array($decoded['custom']) ? array_values(array_map('sanitize_text_field', $decoded['custom'])) : [];
            $structured['city'] = isset($decoded['city']) ? sanitize_text_field($decoded['city']) : '';
        }
    } else {
        // backwards-compat: accept flat 'tags' param
        $tags_raw = isset($_POST['tags']) ? wp_unslash($_POST['tags']) : '';
        $tags = [];
        if (!empty($tags_raw)) {
            $decoded = json_decode($tags_raw, true);
            if (is_array($decoded)) {
                foreach ($decoded as $t) { $t = sanitize_text_field($t); if ($t !== '') $tags[] = $t; }
            } else {
                $parts = preg_split('/[,;]+/', $tags_raw);
                foreach ($parts as $p) { $t = trim($p); if ($t !== '') $tags[] = sanitize_text_field($t); }
            }
        }
        // try to heuristically map flat tags into structured fields
        foreach ($tags as $t) {
            $low = mb_strtolower($t, 'UTF-8');
            if (in_array($low, ['solteiro','namorando','casado','enrolado','separado','viuvo','viúva','viúvo'])) {
                $structured['estado_civil'] = $low; continue;
            }
            if (in_array($low, ['rock','sertanejo','pagode','pop','eletronica','funk','mpb','forro'])) { $structured['estilo_musica'][] = $low; continue; }
            if (in_array($low, ['cerveja','vodka','whisky','vinho','tequila','rum','cachaça','cachaca'])) { $structured['bebida'][] = $low; continue; }
            // If tag contains a city-like pattern (City: Name) map to city, otherwise custom
            if (preg_match('/^city[:\-]\s*(.+)$/iu', $t, $m)) {
                $structured['city'] = sanitize_text_field($m[1]);
            } else {
                $structured['custom'][] = $t;
            }
        }
    }

    // persist
    update_user_meta($current, 'ph_profile_description', $description);
    update_user_meta($current, 'ph_profile_tags_structured', $structured);
    // also keep a flat array for compatibility
    $flat = [];
    if (!empty($structured['estado_civil'])) $flat[] = $structured['estado_civil'];
    if (!empty($structured['city'])) $flat[] = $structured['city'];
    $flat = array_merge($flat, (array) $structured['estilo_musica'], (array) $structured['bebida'], (array) $structured['custom']);
    update_user_meta($current, 'ph_profile_tags', array_values($flat));
    // also store city separately for easy access if needed
    if (!empty($structured['city'])) {
        update_user_meta($current, 'ph_profile_city', sanitize_text_field($structured['city']));
    } else {
        delete_user_meta($current, 'ph_profile_city');
    }

    // Build tags html (custom + other selected)
    $tags_html = '';
    if (!empty($structured['estado_civil'])) {
        $t = $structured['estado_civil'];
        $tags_html .= '<span class="ph-profile-tag" data-tag="' . esc_attr($t) . '">' . esc_html($t) . '</span>';
    }
    if (!empty($structured['city'])) {
        $t = $structured['city'];
        $tags_html .= '<span class="ph-profile-tag ph-profile-city" data-city="' . esc_attr($t) . '">' . esc_html($t) . '</span>';
    }
    foreach (array_merge($structured['estilo_musica'], $structured['bebida']) as $t) {
        $tags_html .= '<span class="ph-profile-tag" data-tag="' . esc_attr($t) . '">' . esc_html($t) . '</span>';
    }
    foreach ($structured['custom'] as $t) {
        $tags_html .= '<span class="ph-profile-tag" data-tag="' . esc_attr($t) . '">' . esc_html($t) . '<button class="ph-tag-remove" data-tag="' . esc_attr($t) . '" style="margin-left:6px;border:none;background:transparent;cursor:pointer;">&times;</button></span>';
    }

    wp_send_json_success([
        'description' => wpautop(esc_html($description)),
        'structured' => $structured,
        'tags_html' => $tags_html
    ]);
}
add_action('wp_ajax_ph_update_profile', 'ph_ajax_update_profile');

/**
 * Keep `user_nicename` in sync when profile display name changes.
 * This ensures `/hunter/{nicename}` URL changes when user updates their name.
 */
function ph_sync_user_nicename_on_profile_update($user_id, $old_user_data) {
    $user = get_user_by('id', $user_id);
    if (!$user) return;
    $old_display = $old_user_data->display_name;
    $new_display = $user->display_name;
    if (empty($new_display)) return;
    if ($old_display === $new_display) return;

    // Generate a safe nicename from display name
    $new_nicename = sanitize_title($new_display);
    if (empty($new_nicename)) return;

    // Avoid overriding nicename if it's equal already
    if ($user->user_nicename === $new_nicename) return;

    wp_update_user(['ID' => $user_id, 'user_nicename' => $new_nicename]);
}
add_action('profile_update', 'ph_sync_user_nicename_on_profile_update', 10, 2);

function ph_hunter_query_vars($vars) {
    $vars[] = 'hunter_username';
    return $vars;
}
add_filter('query_vars', 'ph_hunter_query_vars');

/**
 * Friend request endpoints and helpers
 */
function ph_get_friend_count($user_id) {
    $friends = (array) get_user_meta($user_id, 'ph_friends', true);
    return count($friends);
}

function ph_get_group_count($user_id) {
    $groups = (array) get_user_meta($user_id, 'ph_groups', true);
    return count($groups);
}

function ph_ajax_send_friend_request() {
    if (!is_user_logged_in()) wp_send_json_error('Unauthorized', 401);
    check_ajax_referer('social_nonce', 'nonce');

    $from = get_current_user_id();
    $to = isset($_POST['target_id']) ? intval($_POST['target_id']) : 0;
    if (!$to || $to === $from) wp_send_json_error('Invalid target');

    $user = get_user_by('id', $to);
    if (!$user) wp_send_json_error('User not found');

    // existing friends
    $friends = (array) get_user_meta($to, 'ph_friends', true);
    if (in_array($from, $friends)) wp_send_json_error('Already friends');

    // pending requests
    $requests = (array) get_user_meta($to, 'ph_friend_requests', true);
    if (!in_array($from, $requests)) {
        $requests[] = $from;
        update_user_meta($to, 'ph_friend_requests', array_values($requests));
    }

    // optional simple notification count
    $notif = (int) get_user_meta($to, 'ph_friend_requests_count', true);
    update_user_meta($to, 'ph_friend_requests_count', $notif + 1);

    wp_send_json_success(['sent' => true, 'target' => $to]);
}
add_action('wp_ajax_ph_send_friend_request', 'ph_ajax_send_friend_request');

/**
 * Create mutual friendship helper
 */
function ph_add_friendship($user_a, $user_b) {
    $user_a = intval($user_a);
    $user_b = intval($user_b);
    if (!$user_a || !$user_b || $user_a === $user_b) return false;

    $fa = (array) get_user_meta($user_a, 'ph_friends', true);
    $fb = (array) get_user_meta($user_b, 'ph_friends', true);

    if (!in_array($user_b, $fa)) {
        $fa[] = $user_b;
        update_user_meta($user_a, 'ph_friends', array_values($fa));
    }
    if (!in_array($user_a, $fb)) {
        $fb[] = $user_a;
        update_user_meta($user_b, 'ph_friends', array_values($fb));
    }

    // cleanup pending requests
    $req_a = (array) get_user_meta($user_a, 'ph_friend_requests', true);
    $req_b = (array) get_user_meta($user_b, 'ph_friend_requests', true);
    if (in_array($user_b, $req_a)) { $req_a = array_values(array_diff($req_a, [$user_b])); update_user_meta($user_a, 'ph_friend_requests', $req_a); }
    if (in_array($user_a, $req_b)) { $req_b = array_values(array_diff($req_b, [$user_a])); update_user_meta($user_b, 'ph_friend_requests', $req_b); }

    return true;
}

function ph_ajax_get_friend_requests() {
    if (!is_user_logged_in()) wp_send_json_error('Unauthorized', 401);
    check_ajax_referer('social_nonce', 'nonce');

    $current = get_current_user_id();
    $requests = (array) get_user_meta($current, 'ph_friend_requests', true);
    $result = [];
    foreach ($requests as $uid) {
        $u = get_user_by('id', intval($uid));
        if (!$u) continue;
        $result[] = ['ID' => $u->ID, 'display_name' => $u->display_name, 'avatar' => get_avatar($u->ID, 40), 'profile_url' => get_author_posts_url($u->ID)];
    }
    wp_send_json_success($result);
}
add_action('wp_ajax_ph_get_friend_requests', 'ph_ajax_get_friend_requests');

function ph_ajax_accept_friend_request() {
    if (!is_user_logged_in()) wp_send_json_error('Unauthorized', 401);
    check_ajax_referer('social_nonce', 'nonce');

    $current = get_current_user_id();
    $from = isset($_POST['from_id']) ? intval($_POST['from_id']) : 0;
    if (!$from) wp_send_json_error('Missing from id');

    $requests = (array) get_user_meta($current, 'ph_friend_requests', true);
    if (!in_array($from, $requests)) wp_send_json_error('Request not found');

    // establish friendship both ways
    ph_add_friendship($current, $from);

    // remove request
    $requests = array_values(array_diff($requests, [$from]));
    update_user_meta($current, 'ph_friend_requests', $requests);

    // decrement request count if present
    $cnt = max(0, (int) get_user_meta($current, 'ph_friend_requests_count', true) - 1);
    update_user_meta($current, 'ph_friend_requests_count', $cnt);

    wp_send_json_success(['accepted' => true, 'from' => $from]);
}
add_action('wp_ajax_ph_accept_friend_request', 'ph_ajax_accept_friend_request');

function ph_ajax_decline_friend_request() {
    if (!is_user_logged_in()) wp_send_json_error('Unauthorized', 401);
    check_ajax_referer('social_nonce', 'nonce');

    $current = get_current_user_id();
    $from = isset($_POST['from_id']) ? intval($_POST['from_id']) : 0;
    if (!$from) wp_send_json_error('Missing from id');

    $requests = (array) get_user_meta($current, 'ph_friend_requests', true);
    if (!in_array($from, $requests)) wp_send_json_error('Request not found');

    $requests = array_values(array_diff($requests, [$from]));
    update_user_meta($current, 'ph_friend_requests', $requests);

    // decrement request count if present
    $cnt = max(0, (int) get_user_meta($current, 'ph_friend_requests_count', true) - 1);
    update_user_meta($current, 'ph_friend_requests_count', $cnt);

    wp_send_json_success(['declined' => true, 'from' => $from]);
}
add_action('wp_ajax_ph_decline_friend_request', 'ph_ajax_decline_friend_request');

/**
 * Cancel an outgoing friend request (requester can cancel)
 */
function ph_ajax_cancel_friend_request() {
    if (!is_user_logged_in()) wp_send_json_error('Unauthorized', 401);
    check_ajax_referer('social_nonce', 'nonce');

    $current = get_current_user_id();
    $to = isset($_POST['target_id']) ? intval($_POST['target_id']) : 0;
    if (!$to) wp_send_json_error('Missing target');

    $requests = (array) get_user_meta($to, 'ph_friend_requests', true);
    if (!in_array($current, $requests)) wp_send_json_error('Request not found');

    $requests = array_values(array_diff($requests, [$current]));
    update_user_meta($to, 'ph_friend_requests', $requests);

    // decrement request count if present
    $cnt = max(0, (int) get_user_meta($to, 'ph_friend_requests_count', true) - 1);
    update_user_meta($to, 'ph_friend_requests_count', $cnt);

    wp_send_json_success(['cancelled' => true, 'target' => $to]);
}
add_action('wp_ajax_ph_cancel_friend_request', 'ph_ajax_cancel_friend_request');

/**
 * Topbar widget for incoming friend requests (renders minimal HTML + JS in footer)
 */
/**
 * Return the friend-requests widget HTML/JS so it can be embedded in templates.
 * Use `echo ph_get_friend_requests_widget();` where you want it.
 */
function ph_get_friend_requests_widget() {
    if (!is_user_logged_in()) return '';
    $nonce = wp_create_nonce('social_nonce');
    ob_start();
    ?>
    <div id="ph-requests-topbar" class="ph-requests-topbar">
        <button id="ph-requests-toggle" class="ph-requests-toggle">
            <span id="ph-requests-badge" class="ph-requests-badge" style="display:none;">0</span>
            <span class="ph-requests-label">Pedidos</span>
        </button>
        <div id="ph-requests-dropdown" class="ph-requests-dropdown" style="display:none;">
            <div id="ph-requests-loading" class="ph-requests-loading">Carregando...</div>
            <div id="ph-requests-items" class="ph-requests-items" style="display:none;"> </div>
        </div>
    </div>
    <script>
    (function(){
        const btn = document.getElementById('ph-requests-toggle');
        const dropdown = document.getElementById('ph-requests-dropdown');
        const badge = document.getElementById('ph-requests-badge');
        const items = document.getElementById('ph-requests-items');
        const loading = document.getElementById('ph-requests-loading');
        let loaded = false;

        function fetchRequests(){
            loading.style.display='block'; items.style.display='none';
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: new URLSearchParams({ action: 'ph_get_friend_requests', nonce: '<?php echo esc_js($nonce); ?>' }) })
            .then(r=>r.json()).then(data=>{
                loading.style.display='none';
                items.style.display='block';
                items.innerHTML='';
                if (!data.success || !data.data || data.data.length===0) {
                    items.innerHTML = '<div class="ph-no-requests">Nenhum pedido no momento.</div>';
                    badge.style.display='none';
                    return;
                }
                const list = data.data;
                badge.style.display = list.length ? 'inline-block' : 'none';
                badge.textContent = list.length;
                list.forEach(u=>{
                    const el = document.createElement('div'); el.className='ph-request-entry';
                    const avatarLink = u.profile_url ? ('<a href="'+u.profile_url+'" class="ph-request-avatar-link">'+u.avatar+'</a>') : u.avatar;
                    el.innerHTML = '<div class="ph-request-info">'+avatarLink+'<div class="ph-request-name"><strong>'+ (u.display_name||'Usuario') +'</strong></div></div>';
                    const controls = document.createElement('div'); controls.className='ph-request-controls';
                    const a = document.createElement('button'); a.textContent='Aceitar'; a.className='ph-top-accept'; a.dataset.from = u.ID;
                    const d = document.createElement('button'); d.textContent='Recusar'; d.className='ph-top-decline'; d.dataset.from = u.ID;
                    controls.appendChild(a); controls.appendChild(d);
                    el.appendChild(controls);
                    items.appendChild(el);
                });
                attachHandlers();
            }).catch(()=>{ loading.style.display='none'; items.style.display='block'; items.innerHTML='<div class="ph-error">Erro ao carregar.</div>'; });
        }

        function attachHandlers(){
            const acc = items.querySelectorAll('.ph-top-accept');
            const dec = items.querySelectorAll('.ph-top-decline');
            acc.forEach(b=> b.addEventListener('click', function(){ handle(b,'ph_accept_friend_request'); }));
            dec.forEach(b=> b.addEventListener('click', function(){ handle(b,'ph_decline_friend_request'); }));
        }

        function handle(button, action) {
            const from = button.dataset.from; if (!from) return; button.disabled = true;
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: new URLSearchParams({ action: action, nonce: '<?php echo esc_js($nonce); ?>', from_id: from }) })
            .then(r=>r.json()).then(data=>{
                if (data.success) { fetchRequests(); }
                else { alert(data.data || 'Erro'); button.disabled = false; }
            }).catch(()=>{ alert('Erro de rede'); button.disabled = false; });
        }

        btn.addEventListener('click', function(){ if (dropdown.style.display==='block') { dropdown.style.display='none'; } else { dropdown.style.display='block'; if (!loaded) { fetchRequests(); loaded = true; } } });

        // initial badge update (silent)
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: new URLSearchParams({ action: 'ph_get_friend_requests', nonce: '<?php echo esc_js($nonce); ?>' }) })
        .then(r=>r.json()).then(data=>{ if (data.success && data.data && data.data.length) { badge.style.display='inline-block'; badge.textContent = data.data.length; } });
    })();
    </script>
    <?php
    return ob_get_clean();
}


