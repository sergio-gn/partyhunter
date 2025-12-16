<?php
/**
 * Group Management Functions
 * Handles creating groups, joining groups, and managing group members
 * Groups are stored as a custom post type 'partyhunter_group'
 */

/**
 * Register Custom Post Type for Groups
 */
function register_group_post_type() {
    $labels = array(
        'name'                  => 'Grupos',
        'singular_name'         => 'Grupo',
        'menu_name'             => 'Grupos',
        'add_new'               => 'Adicionar Novo',
        'add_new_item'          => 'Adicionar Novo Grupo',
        'edit_item'             => 'Editar Grupo',
        'new_item'              => 'Novo Grupo',
        'view_item'             => 'Ver Grupo',
        'search_items'          => 'Buscar Grupos',
        'not_found'             => 'Nenhum grupo encontrado',
        'not_found_in_trash'    => 'Nenhum grupo encontrado na lixeira',
    );
    
    $args = array(
        'labels'                => $labels,
        'public'                => false, // Don't show in archives, search, etc.
        'publicly_queryable'    => true,  // Allow querying via rewrite rules
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_icon'             => 'dashicons-groups',
        'query_var'             => true,
        'rewrite'               => false, // We handle rewrites manually
        'capability_type'       => 'partyhunter_group',
        'map_meta_cap'          => true,
        'has_archive'           => false,
        'hierarchical'          => false,
        'menu_position'         => 20,
        'supports'              => array('title'),
        'show_in_rest'          => false,
        'exclude_from_search'   => true,  // Don't show in search results
    );

    register_post_type('partyhunter_group', $args);
}
add_action('init', 'register_group_post_type');

/**
 * Allow logged-in users to create groups (bypass capability check)
 * Ensure admins have full access to all groups
 */
function allow_group_creation_cap($allcaps, $caps, $args, $user) {
    // Grant full capabilities to administrators
    if (isset($user->roles) && in_array('administrator', $user->roles)) {
        $allcaps['edit_partyhunter_groups'] = true;
        $allcaps['edit_others_partyhunter_groups'] = true;  
        $allcaps['publish_partyhunter_groups'] = true;
        $allcaps['delete_partyhunter_groups'] = true;
        $allcaps['delete_others_partyhunter_groups'] = true;
        $allcaps['read_partyhunter_groups'] = true;
        $allcaps['read_private_partyhunter_groups'] = true;
        $allcaps['create_partyhunter_groups'] = true;
        $allcaps['edit_private_partyhunter_groups'] = true;
        $allcaps['edit_published_partyhunter_groups'] = true;
    }
    // Grant basic capabilities for other logged-in users
    elseif (is_user_logged_in()) {
        $allcaps['edit_partyhunter_groups'] = true;
        $allcaps['publish_partyhunter_groups'] = true;
        $allcaps['delete_partyhunter_groups'] = true;
        $allcaps['read_partyhunter_groups'] = true;
        $allcaps['create_partyhunter_groups'] = true;
    }
    return $allcaps;
}
// Add filter permanently so admins always have access to view all groups
add_filter('user_has_cap', 'allow_group_creation_cap', 10, 4);

/**
 * Generate a unique group ID
 */
function generate_unique_group_id() {
    do {
        $group_id = wp_generate_password(12, false);
    } while (group_exists($group_id));
    
    return $group_id;
}

/**
 * Check if a group exists
 */
function group_exists($group_id) {
    $args = array(
        'post_type' => 'partyhunter_group',
        'meta_query' => array(
            array(
                'key' => 'group_unique_id',
                'value' => $group_id,
                'compare' => '='
            )
        ),
        'posts_per_page' => 1,
        'post_status' => 'publish'
    );
    
    $query = new WP_Query($args);
    return $query->have_posts();
}

/**
 * Create a new group
 */
function create_group($user_id) {
    // Validate user
    if (empty($user_id)) {
        error_log('create_group: Empty user_id');
        return false;
    }
    
    if (!is_user_logged_in()) {
        error_log('create_group: User not logged in');
        return false;
    }
    
    // Ensure current user matches
    if (get_current_user_id() != $user_id) {
        error_log('create_group: User ID mismatch. Current: ' . get_current_user_id() . ', Requested: ' . $user_id);
        return false;
    }
    if (!post_type_exists('partyhunter_group')){
        error_log('crate_group: Post type partyhunter_group does not exist');
        return false;
    } 
    
    // Generate unique group ID
    $group_id = generate_unique_group_id();
    if (empty($group_id)) {
        error_log('create_group: Failed to generate unique group ID');
        return false;
    }
    
    // Get user info
    $user = get_user_by('id', $user_id);
    if (!$user) {
        error_log('create_group: User not found. ID: ' . $user_id);
        return false;
    }
    
    $group_name = ($user->display_name ? $user->display_name : $user->user_login) . '\'s Group';
    
    // Add filter to bypass capability check for this post type
    add_filter('user_has_cap', 'allow_group_creation_cap', 10, 4);
    
    // Create new group post
    $post_data = array(
        'post_title'    => sanitize_text_field($group_name),
        'post_status'   => 'publish',
        'post_type'     => 'partyhunter_group',
        'post_author'   => $user_id,
    );
    
    $post_id = wp_insert_post($post_data, true);
    
    // Remove filter
    remove_filter('user_has_cap', 'allow_group_creation_cap', 10);
    
    if (is_wp_error($post_id)) {
        error_log('create_group: wp_insert_post error: ' . $post_id->get_error_message());
        return false;
    }
    
    if (!$post_id || $post_id === 0) {
        error_log('create_group: wp_insert_post returned invalid post_id: ' . $post_id);
        return false;
    }
    
    // Store group unique ID and metadata
    $meta_results = array();
    $meta_results['unique_id'] = update_post_meta($post_id, 'group_unique_id', $group_id);
    $meta_results['creator_id'] = update_post_meta($post_id, 'group_creator_id', $user_id);
    $meta_results['created_at'] = update_post_meta($post_id, 'group_created_at', current_time('mysql'));
    $meta_results['members'] = update_post_meta($post_id, 'group_members', array($user_id)); // Creator is automatically a member
    
    // Verify meta was saved
    if (!$meta_results['unique_id']) {
        error_log('create_group: Failed to save group_unique_id meta');
        wp_delete_post($post_id, true);
        return false;
    }
    
    // Store group ID in user meta
    $user_groups = get_user_meta($user_id, 'user_groups', true);
    if (!is_array($user_groups)) {
        $user_groups = [];
    }
    if (!in_array($group_id, $user_groups)) {
        $user_groups[] = $group_id;
        update_user_meta($user_id, 'user_groups', $user_groups);
    }
    
    error_log('create_group: Successfully created group. ID: ' . $group_id . ', Post ID: ' . $post_id);
    return $group_id;
}

/**
 * Join a group
 */
function join_group($group_id, $user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    
    if (!is_user_logged_in() || get_current_user_id() != $user_id) {
        return ['success' => false, 'message' => 'Você precisa estar logado para entrar em um grupo.'];
    }

    if (isset($_POST['new_group_name']) && !empty($group_id)){
        $new_name = $_POST['new_group_name'];
        //Chama a função criada
        $rename_result = update_group_title_by_unique_id($group_id, $new_name);

        if ($rename_result['success'] === false){
            //recarrega os dados do grupo para mostrar o nome novo. 

            $group = get_group($group_id);
            $rename_message = $rename_result['message'];
        } else {
            $rename_error = $rename_result['message'];
        }
    }
    
    // Get group post by unique ID
    $group_post = get_group_post_by_id($group_id);
    if (!$group_post) {
        return ['success' => false, 'message' => 'Grupo não encontrado.'];
    }
   
    $post_id = $group_post->ID;
    $members = get_post_meta($post_id, 'group_members', true);
    if (!is_array($members)) {
        $members = [];
    }
    
    // Check if user is already a member
    if (in_array($user_id, $members)) {
        return ['success' => false, 'message' => 'Você já é membro deste grupo.'];
    }
    
    // Add user to group
    $members[] = $user_id;
    update_post_meta($post_id, 'group_members', $members);
    
    // Store group ID in user meta
    $user_groups = get_user_meta($user_id, 'user_groups', true);
    if (!is_array($user_groups)) {
        $user_groups = [];
    }
    if (!in_array($group_id, $user_groups)) {
        $user_groups[] = $group_id;
        update_user_meta($user_id, 'user_groups', $user_groups);
    }    
    return ['success' => true, 'message' => 'Você entrou no grupo com sucesso!'];
}

/**
 * Get group post by unique ID
 */
function get_group_post_by_id($group_id) {
    $args = array(
        'post_type' => 'partyhunter_group',
        'meta_query' => array(
            array(
                'key' => 'group_unique_id',
                'value' => $group_id,
                'compare' => '='
            )
        ),
        'posts_per_page' => 1,
        'post_status' => 'publish'
    );
    
    $query = new WP_Query($args);
    if ($query->have_posts()) {
        $query->the_post();
        $post = get_post();
        wp_reset_postdata();
        return $post;
    }
    
    return null;
}

/**
 * Get group information
 */
function get_group($group_id) {
    $group_post = get_group_post_by_id($group_id);
    if (!$group_post) {
        return null;
    }
    
    return array(
        'id' => $group_id,
        'post_id' => $group_post->ID,
        'title' => $group_post->post_title,
        'creator_id' => get_post_meta($group_post->ID, 'group_creator_id', true),
        'created_at' => get_post_meta($group_post->ID, 'group_created_at', true),
        'members' => get_post_meta($group_post->ID, 'group_members', true) ?: [],
    );
}

/**
 * Get all members of a group
 */
function get_group_members($group_id) {
    $group_post = get_group_post_by_id($group_id);
    if (!$group_post) {
        return [];
    }
    
    $members_ids = get_post_meta($group_post->ID, 'group_members', true);
    if (!is_array($members_ids)) {
        return [];
    }
    
    $members = [];
    foreach ($members_ids as $user_id) {
        $user = get_user_by('id', $user_id);
        if ($user) {
            $members[] = [
                'id' => $user_id,
                'name' => $user->display_name ? $user->display_name : $user->user_login,
                'avatar' => get_avatar_url($user_id),
                'email' => $user->user_email,
            ];
        }
    }
    
    return $members;
}

/**
 * Get all groups a user belongs to
 */
function get_user_groups($user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    
    $user_groups = get_user_meta($user_id, 'user_groups', true);
    if (!is_array($user_groups)) {
        return [];
    }
    
    $groups = [];
    foreach ($user_groups as $group_id) {
        $group = get_group($group_id);
        if ($group) {
            $group['member_count'] = count($group['members']);
            $groups[] = $group;
        }
    }
    
    return $groups;
}

/**
 * Check if user is member of a group
 */
function is_user_in_group($group_id, $user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    
    $group_post = get_group_post_by_id($group_id);
    if (!$group_post) {
        return false;
    }
    
    $members = get_post_meta($group_post->ID, 'group_members', true);
    if (!is_array($members)) {
        return false;
    }
    
    return in_array($user_id, $members);
}

/**
 * Leave a group
 */
function leave_group($group_id, $user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    
    if (!is_user_logged_in() || get_current_user_id() != $user_id) {
        return ['success' => false, 'message' => 'Você precisa estar logado.'];
    }
    
    $group_post = get_group_post_by_id($group_id);
    if (!$group_post) {
        return ['success' => false, 'message' => 'Grupo não encontrado.'];
    }
    
    $post_id = $group_post->ID;
    $members = get_post_meta($post_id, 'group_members', true);
    if (!is_array($members)) {
        $members = [];
    }
    
    // Remove user from group members
    $members = array_diff($members, [$user_id]);
    $members = array_values($members); // Re-index array
    
    // If no members left, delete the group post
    if (empty($members)) {
        wp_delete_post($post_id, true);
    } else {
        update_post_meta($post_id, 'group_members', $members);
    }
    
    // Remove group from user meta
    $user_groups = get_user_meta($user_id, 'user_groups', true);
    if (is_array($user_groups)) {
        $user_groups = array_diff($user_groups, [$group_id]);
        $user_groups = array_values($user_groups);
        update_user_meta($user_id, 'user_groups', $user_groups);
    }
    
    return ['success' => true, 'message' => 'Você saiu do grupo.'];
}

/**
 * Handle AJAX request to create a group
 */
function ajax_create_group() {
    // Ensure post type is registered
    if (!post_type_exists('partyhunter_group')) {
        wp_send_json_error(['message' => 'Erro: Tipo de post não registrado. Recarregue a página.']);
        return;
    }
    
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'create_group_nonce')) {
        wp_send_json_error(['message' => 'Erro de segurança. Por favor, recarregue a página e tente novamente.']);
        return;
    }
    
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Você precisa estar logado para criar um grupo.']);
        return;
    }
    
    $user_id = get_current_user_id();
    
    if (empty($user_id)) {
        wp_send_json_error(['message' => 'Erro ao identificar usuário. Por favor, faça login novamente.']);
        return;
    }
    
    // Try to create the group
    $group_id = create_group($user_id);
    
    if ($group_id) {
        $group_link = home_url('/grupo/' . $group_id);
        wp_send_json_success([
            'group_id' => $group_id,
            'group_link' => $group_link,
            'message' => 'Grupo criado com sucesso!'
        ]);
    } else {
        error_log('ajax_create_group: create_group returned false for user_id: ' . $user_id);
        wp_send_json_error(['message' => 'Erro ao criar grupo. Verifique se você está logado e tente novamente. Verifique os logs do servidor para mais detalhes.']);
    }
}
add_action('wp_ajax_create_group', 'ajax_create_group');

/**
 * Handle AJAX request to join a group
 */
function ajax_join_group() {
    check_ajax_referer('join_group_nonce', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Você precisa estar logado.']);
    }
    
    $group_id = sanitize_text_field($_POST['group_id']);
    
    if (empty($group_id)) {
        wp_send_json_error(['message' => 'ID do grupo é obrigatório.']);
    }
    
    $result = join_group($group_id);
    
    if ($result['success']) {
        wp_send_json_success($result);
    } else {
        wp_send_json_error($result);
    }
}
add_action('wp_ajax_join_group', 'ajax_join_group');

/**
 * Add custom columns to groups admin list
 */
function add_group_admin_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = 'Nome do Grupo';
    $new_columns['group_id'] = 'ID do Grupo';
    $new_columns['creator'] = 'Criador';
    $new_columns['members'] = 'Membros';
    $new_columns['created'] = 'Criado em';
    $new_columns['date'] = $columns['date'];
    return $new_columns;
}
add_filter('manage_partyhunter_group_posts_columns', 'add_group_admin_columns');

/**
 * Populate custom columns
 */
function populate_group_admin_columns($column, $post_id) {
    switch ($column) {
        case 'group_id':
            $group_id = get_post_meta($post_id, 'group_unique_id', true);
            echo '<code>' . esc_html($group_id) . '</code>';
            break;
        case 'creator':
            $creator_id = get_post_meta($post_id, 'group_creator_id', true);
            if ($creator_id) {
                $creator = get_user_by('id', $creator_id);
                if ($creator) {
                    echo esc_html($creator->display_name ? $creator->display_name : $creator->user_login);
                } else {
                    echo 'N/A';
                }
            } else {
                echo 'N/A';
            }
            break;
        case 'members':
            $members = get_post_meta($post_id, 'group_members', true);
            $count = is_array($members) ? count($members) : 0;
            echo $count . ' membro(s)';
            break;
        case 'created':
            $created = get_post_meta($post_id, 'group_created_at', true);
            echo $created ? esc_html($created) : 'N/A';
            break;
    }
}
add_action('manage_partyhunter_group_posts_custom_column', 'populate_group_admin_columns', 10, 2);

/**
 * Ensure admins can see all groups in the admin list
 */
function allow_admin_see_all_groups($query) {
    // Only modify admin queries for the groups post type
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'partyhunter_group') {
        return;
    }
    
    // If user is an administrator, show all groups (remove author restriction)
    $user = wp_get_current_user();
    if (isset($user->roles) && in_array('administrator', $user->roles)) {
        // Remove any author restrictions
        $query->set('author', '');
        // Ensure all published groups are shown
        $query->set('post_status', 'any');
        // Remove any meta query restrictions that might limit visibility
        $meta_query = $query->get('meta_query');
        if (empty($meta_query)) {
            $query->set('meta_query', array());
        }
    }
}
add_action('pre_get_posts', 'allow_admin_see_all_groups');

/**
 * Remove author restrictions from admin group queries for administrators
 */
function remove_group_author_restriction($where, $query) {
    global $wpdb;
    
    // Only modify admin queries for the groups post type
    if (!is_admin() || !$query->is_main_query()) {
        return $where;
    }
    
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'partyhunter_group') {
        return $where;
    }
    
    // If user is an administrator, remove author restrictions
    $user = wp_get_current_user();
    if (isset($user->roles) && in_array('administrator', $user->roles)) {
        // Remove any author-based WHERE clauses
        $where = preg_replace('/\s*AND\s*' . $wpdb->posts . '\.post_author\s*=\s*\d+/i', '', $where);
    }
    
    return $where;
}
add_filter('posts_where', 'remove_group_author_restriction', 10, 2);

/**
 * Map meta capabilities for groups - ensure admins can edit/delete any group
 */
function map_group_meta_cap($caps, $cap, $user_id, $args) {
    // If user is an administrator, grant all capabilities
    $user = get_userdata($user_id);
    if ($user && isset($user->roles) && in_array('administrator', $user->roles)) {
        // For edit/delete operations, check if it's a group post type
        if (isset($args[0])) {
            $post = get_post($args[0]);
            if ($post && $post->post_type === 'partyhunter_group') {
                // Grant all capabilities to admins
                if (in_array($cap, ['edit_partyhunter_group', 'delete_partyhunter_group', 'read_partyhunter_group'])) {
                    return ['administrator'];
                }
            }
        }
    }
    return $caps;
}
add_filter('map_meta_cap', 'map_group_meta_cap', 10, 4);

/**
 * Add meta box to show group details
 */
function add_group_meta_box() {
    add_meta_box(
        'group_details',
        'Detalhes do Grupo',
        'display_group_meta_box',
        'partyhunter_group',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_group_meta_box');

/**
 * Display group meta box content
 */
function display_group_meta_box($post) {
    $group_id = get_post_meta($post->ID, 'group_unique_id', true);
    $creator_id = get_post_meta($post->ID, 'group_creator_id', true);
    $members = get_post_meta($post->ID, 'group_members', true);
    $created = get_post_meta($post->ID, 'group_created_at', true);
    
    echo '<table class="form-table">';
    echo '<tr><th>ID Único do Grupo:</th><td><code>' . esc_html($group_id) . '</code></td></tr>';
    echo '<tr><th>Link do Grupo:</th><td><a href="' . esc_url(home_url('/grupo/' . $group_id)) . '" target="_blank">' . esc_url(home_url('/grupo/' . $group_id)) . '</a></td></tr>';
    
    if ($creator_id) {
        $creator = get_user_by('id', $creator_id);
        echo '<tr><th>Criador:</th><td>' . esc_html($creator ? ($creator->display_name ? $creator->display_name : $creator->user_login) : 'N/A') . '</td></tr>';
    }
    
    echo '<tr><th>Criado em:</th><td>' . esc_html($created ? $created : 'N/A') . '</td></tr>';
    
    if (is_array($members) && !empty($members)) {
        echo '<tr><th>Membros (' . count($members) . '):</th><td><ul>';
        foreach ($members as $member_id) {
            $member = get_user_by('id', $member_id);
            if ($member) {
                echo '<li>' . esc_html($member->display_name ? $member->display_name : $member->user_login) . ' (' . esc_html($member->user_email) . ')</li>';
            }
        }
        echo '</ul></td></tr>';
    } else {
        echo '<tr><th>Membros:</th><td>Nenhum membro</td></tr>';
    }
    
    echo '</table>';
}

/**
 * Add rewrite rules for group URLs
 * Note: These rules need to be flushed after adding. Go to Settings > Permalinks and click Save.
 */
function add_group_rewrite_rules() {
    // Use 'top' priority to ensure these rules are checked before page rules
    add_rewrite_rule('^grupo/([^/]+)/sair/?$', 'index.php?group_id=$matches[1]&group_action=leave', 'top');
    add_rewrite_rule('^grupo/([^/]+)/?$', 'index.php?group_id=$matches[1]', 'top');
}
add_action('init', 'add_group_rewrite_rules');

/**
 * Flush rewrite rules when theme is activated
 */
function flush_group_rewrite_rules_on_activation() {
    add_group_rewrite_rules();
    flush_rewrite_rules();
    update_option('partyhunter_group_rewrite_flushed', true);
}
add_action('after_switch_theme', 'flush_group_rewrite_rules_on_activation');

/**
 * One-time flush of rewrite rules on next page load
 * This ensures rewrite rules are active even if theme was already active
 * Also flushes when post type settings change
 */
function one_time_flush_group_rewrite_rules() {
    $flushed = get_option('partyhunter_group_rewrite_flushed', false);
    $last_version = get_option('partyhunter_group_rewrite_version', '1.0');
    $current_version = '2.0'; // Increment when rewrite rules change
    
    // Flush if never flushed, or if version changed
    if (!$flushed || $last_version !== $current_version) {
        add_group_rewrite_rules();
        flush_rewrite_rules(true); // Hard flush
        update_option('partyhunter_group_rewrite_flushed', true);
        update_option('partyhunter_group_rewrite_version', $current_version);
    }
}
add_action('init', 'one_time_flush_group_rewrite_rules', 999);

/**
 * Force flush rewrite rules - can be called manually if needed
 * Call this function once to ensure rewrite rules are active
 */
function force_flush_group_rewrite_rules() {
    add_group_rewrite_rules();
    flush_rewrite_rules(true);
    update_option('partyhunter_group_rewrite_flushed', true);
}

/**
 * Add query vars for group pages
 */
function add_group_query_vars($vars) {
    $vars[] = 'group_id';
    $vars[] = 'group_action';
    return $vars;
}
add_filter('query_vars', 'add_group_query_vars');

/**
 * Parse request early to catch group URLs
 * This helps ensure group URLs are recognized before WordPress tries to query posts
 */
function parse_group_request($wp) {
    // Check if this is a group URL
    $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $path = trim(parse_url($request_uri, PHP_URL_PATH), '/');
    
    // Check for /grupo/{group_id} pattern
    if (preg_match('#^grupo/([^/]+)(?:/sair)?/?$#', $path, $matches)) {
        $group_id = $matches[1];
        $group_action = (strpos($path, '/sair') !== false) ? 'leave' : '';
        
        // Set query vars
        $wp->set_query_var('group_id', $group_id);
        if ($group_action) {
            $wp->set_query_var('group_action', $group_action);
        }
    }
}
add_action('parse_request', 'parse_group_request', 1);

/**
 * Handle group page template
 * This runs early to catch group URLs before WordPress returns 404
 */
function group_template_redirect() {
    $group_id = get_query_var('group_id');
    $group_action = get_query_var('group_action');
    
    if ($group_id) {
        // Verify the group exists
        $group_post = get_group_post_by_id($group_id);
        if (!$group_post) {
            // Group doesn't exist, let WordPress handle 404
            return;
        }
        
        // Handle leave group action
        if ($group_action === 'leave' && isset($_POST['leave_group'])) {
            $result = leave_group($group_id);
            if ($result['success']) {
                wp_redirect(home_url('/hunter/?left_group=1'));
                exit;
            }
        }
        
        // Prevent 404 status
        global $wp_query;
        $wp_query->is_404 = false;
        $wp_query->is_page = true;
        $wp_query->is_singular = true;
        
        // Set template to group.php
        add_filter('template_include', function($template) {
            $group_template = locate_template(['group.php']);
            if ($group_template) {
                return $group_template;
            }
            return $template;
        }, 99);
    }
}
add_action('template_redirect', 'group_template_redirect', 1);

/**
 * Flush rewrite rules when needed
 * Call this function once after theme activation or when needed
 * This can be called manually if needed
 */
function flush_group_rewrite_rules() {
    add_group_rewrite_rules();
    flush_rewrite_rules();
    update_option('partyhunter_group_rewrite_flushed', true);
}

/**
 * ==========================================
 * FUNÇÕES AUXILIARES E AUTH (ADICIONAR NO FINAL)
 * ==========================================
 */

/**
 * Update group title by unique ID (Função que faltava)
 */
function update_group_title_by_unique_id($group_id, $new_title) {
    if (!is_user_logged_in()) {
        return ['success' => false, 'message' => 'Login necessário.'];
    }

    $group_post = get_group_post_by_id($group_id);
    if (!$group_post) {
        return ['success' => false, 'message' => 'Grupo não encontrado.'];
    }

    // Verifica se é o dono
    if ($group_post->post_author != get_current_user_id()) {
        return ['success' => false, 'message' => 'Apenas o dono pode renomear.'];
    }

    // Atualiza
    $updated = wp_update_post([
        'ID' => $group_post->ID,
        'post_title' => sanitize_text_field($new_title)
    ]);

    if (is_wp_error($updated)) {
        return ['success' => false, 'message' => 'Erro ao atualizar post.'];
    }

    return ['success' => true, 'message' => 'Nome atualizado com sucesso!'];
}

/**
 * AJAX Login Handler
 */
function ph_ajax_login_handler() {
    // Verifica Nonce de segurança (vamos criar esse nonce no JS depois)
    check_ajax_referer('ph_auth_nonce', 'nonce');

    $info = array();
    $info['user_login'] = $_POST['log'];
    $info['user_password'] = $_POST['pwd'];
    $info['remember'] = true;

    // Tenta logar usando função nativa do WP
    $user_signon = wp_signon($info, false);

    if (is_wp_error($user_signon)) {
        wp_send_json_error(['message' => 'Usuário ou senha incorretos.']);
    } else {
        wp_send_json_success(['message' => 'Login realizado! Redirecionando...']);
    }
}
add_action('wp_ajax_ph_login', 'ph_ajax_login_handler');
add_action('wp_ajax_nopriv_ph_login', 'ph_ajax_login_handler'); // Permite acesso para deslogados

/**
 * AJAX Register Handler (Com Auto-Login)
 */
function ph_ajax_register_handler() {
    check_ajax_referer('ph_auth_nonce', 'nonce');

    $username = sanitize_user($_POST['user_login']);
    $email    = sanitize_email($_POST['user_email']);
    $password = $_POST['user_pass'];

    // Validações básicas
    if (username_exists($username)) {
        wp_send_json_error(['message' => 'Este nome de usuário já existe.']);
    }
    if (email_exists($email)) {
        wp_send_json_error(['message' => 'Este email já está cadastrado.']);
    }
    if (empty($password)) {
        wp_send_json_error(['message' => 'A senha é obrigatória.']);
    }

    // 1. Cria o usuário
    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        wp_send_json_error(['message' => $user_id->get_error_message()]);
    } else {
        // 2. MÁGICA: Loga o usuário automaticamente
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
        
        // Opcional: Se quiser que ele já entre no grupo automaticamente ao registrar,
        // podemos pegar o $_POST['group_id'] aqui e chamar join_group($group_id, $user_id).
        // Por enquanto, vamos apenas logar e recarregar a página.
        
        wp_send_json_success(['message' => 'Conta criada! Entrando...']);
    }
}
add_action('wp_ajax_ph_register', 'ph_ajax_register_handler');
add_action('wp_ajax_nopriv_ph_register', 'ph_ajax_register_handler');