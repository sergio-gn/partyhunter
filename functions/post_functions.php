<?php
// Adicionar ou atualizar campos personalizados ao salvar o post
function add_custom_fields($post_id) {
    // Get current user ID
    $current_user_id = get_current_user_id();
    
    if (!get_post_meta($post_id, 'user_id', true)) {
        add_post_meta($post_id, 'user_id', $current_user_id, true);
    }
    if (!get_post_meta($post_id, 'vote', true)) {
        add_post_meta($post_id, 'vote', 0, true);
    }
    if (!get_post_meta($post_id, 'date', true)) {
        add_post_meta($post_id, 'date', '', true);
    }
    if (!get_post_meta($post_id, 'location', true)) {
        add_post_meta($post_id, 'location', '', true);
    }
    if (!get_post_meta($post_id, 'insta', true)) {
        add_post_meta($post_id, 'insta', '', true);
    }
    if (!get_post_meta($post_id, 'price', true)) {
        add_post_meta($post_id, 'price', '', true);
    }
    if (!get_post_meta($post_id, 'lote_1', true)) {
        add_post_meta($post_id, 'lote_1', '', true);
    }
    if (!get_post_meta($post_id, 'lote_2', true)) {
        add_post_meta($post_id, 'lote_2', '', true);
    }
    if (!get_post_meta($post_id, 'lote_3', true)) {
        add_post_meta($post_id, 'lote_3', '', true);
    }
    if (!get_post_meta($post_id, 'lote_4', true)) {
        add_post_meta($post_id, 'lote_4', '', true);
    }
}
add_action('save_post', 'add_custom_fields');

function custom_comment_callback($comment, $args, $depth) {
    ?>
    <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
        <div class="comment-body">
            <div class="comment-meta">
                <div class="comment-author vcard">
                    <?php echo get_avatar($comment, $args['avatar_size']); ?>
                </div>
                <div class="comment-content">
                    <div class="comment_author"><?php comment_author_link(); ?></div>
                    <?php comment_text(); ?>
                </div>
            </div>
            <div class="reply">
                ↳
                <?php
                comment_reply_link(array_merge($args, array(
                    'depth' => $depth, 
                    'max_depth' => $args['max_depth']
                ))); 
                ?>
            </div>
        </div>
    </li>
    <?php
}

add_filter('comment_form_logged_in', function($logged_in_text, $commenter, $user_identity) {
    return '<p class="logged-in-as">Comentar como: <strong>' . esc_html($user_identity) . '</strong></a></p>';
}, 10, 3);

function disable_comment_cookies_consent($fields) {
    if (isset($fields['cookies'])) {
        unset($fields['cookies']); // Ensure it's removed
    }
    return $fields;
}
add_filter('comment_form_default_fields', 'disable_comment_cookies_consent', 20, 1);

function enable_threaded_comments() {
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'enable_threaded_comments');

add_filter('pre_comment_approved', '__return_true');
