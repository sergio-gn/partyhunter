<?php
function handle_vote_ajax() {
    if (!isset($_POST['post_id'], $_POST['vote_action'])) {
        wp_send_json_error(['message' => 'Dados inválidos.']);
    }

    $post_id = intval($_POST['post_id']);
    $vote_action = sanitize_text_field($_POST['vote_action']);
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $current_vote = get_post_meta($post_id, 'vote', true);
    $user_vote_key = 'user_vote_' . $user_ip;
    $previous_vote = get_post_meta($post_id, $user_vote_key, true);

    if ($vote_action === 'increase') {
        if ($previous_vote === 'up') {
            wp_send_json_error(['message' => 'Você já votou para cima.']);
        }

        if ($previous_vote === 'down') {
            update_post_meta($post_id, 'vote', $current_vote + 2);
        } else {
            update_post_meta($post_id, 'vote', $current_vote + 1);
        }

        update_post_meta($post_id, $user_vote_key, 'up');
    } elseif ($vote_action === 'decrease') {
        if ($previous_vote === 'down') {
            wp_send_json_error(['message' => 'Você já votou para baixo.']);
        }

        if ($previous_vote === 'up') {
            update_post_meta($post_id, 'vote', $current_vote - 2);
        } else {
            update_post_meta($post_id, 'vote', $current_vote - 1);
        }

        update_post_meta($post_id, $user_vote_key, 'down');
    } else {
        wp_send_json_error(['message' => 'Ação inválida.']);
    }

    $new_vote_count = get_post_meta($post_id, 'vote', true);
    wp_send_json_success(['vote_count' => $new_vote_count]);
}
add_action('wp_ajax_vote_post', 'handle_vote_ajax');
add_action('wp_ajax_nopriv_vote_post', 'handle_vote_ajax');