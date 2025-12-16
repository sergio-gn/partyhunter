<?php
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">
    <?php 
        if (have_comments()) : ?>
            <p class="comments-title">
                <?php
                $comment_count = get_comments_number();
                printf(
                    esc_html(_n('%d Comentários', '%d Comentários', $comment_count, 'your-text-domain')),
                    $comment_count
                );
                ?>
            </p>
            <ol class="comment-list">
                <?php
                    wp_list_comments(array(
                        'style'      => 'ol',
                        'short_ping' => true,
                        'avatar_size' => 50,
                        'reply_text'  => 'Responder',
                        'max_depth'   => 4,
                        'callback'   => 'custom_comment_callback'
                    ));
                ?>
            </ol>

            <?php
            the_comments_navigation();
        endif;

        if ( ! is_user_logged_in() ) {
            // Modify comment form for non-logged-in users
            $comment_form_args = array(
                'title_reply'          => '', // Change the title
                'label_submit'         => '>', // Change submit button text
                'comment_notes_after'  => '', // Remove extra notes below the form
                'comment_notes_before' => '', // Remove "Your email address will not be published" message
                'fields' => array(
                    'author' => '<p style="display:none" class="comment-form-author">
                                    <label for="author">Name</label> 
                                    <input id="author" name="author" type="text" value="Anonimo" required>
                                </p>',
                    'email'  => '<p style="display:none" class="comment-form-email">
                                    <label for="email">Email</label> 
                                    <input id="email" name="email" type="email" value="anonymous@mailanonymous.com" required>
                                </p>',
                    'cookies' => '<p style="display:none" class="comment-form-cookies-consent">
                                    <input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes" checked>
                                    <label for="wp-comment-cookies-consent">Salvar meus dados neste navegador para a próxima vez que eu comentar.</label>
                                </p>'
                ),
                'comment_field' => '
                    <div class="comment-textarea-submit">
                        <textarea id="comment" name="comment" rows="5" placeholder="Fazer um comentário..." required></textarea>
                        <div class="form-submit">
                            <input name="submit" type="submit" id="submit" class="submit" value=">">
                            <input type="hidden" name="comment_post_ID" value="' . get_the_ID() . '" id="comment_post_ID">
                            <input type="hidden" name="comment_parent" id="comment_parent" value="0">
                        </div>
                    </div>',
                'submit_field' => ''
            );
        } else {
            // Comment form for logged-in users, you can keep the default fields
            $comment_form_args = array(
                'title_reply'          => '', // Change the title
                'label_submit'         => '>', // Change submit button text
                'comment_notes_after'  => '', // Remove extra notes below the form
                'comment_notes_before' => '', // Remove "Your email address will not be published" message
                'fields' => array(
                    'author' => '<p class="comment-form-author"><label for="author">Name</label> ' .
                                '<input id="author" name="author" type="text" required></p>',
                    'email'  => '<p class="comment-form-email"><label for="email">Email</label> ' .
                                '<input id="email" name="email" type="email" required></p>',
                ),
                'comment_field' => '
                    <div class="comment-textarea-submit">
                        <textarea id="comment" name="comment" rows="5" placeholder="Fazer um comentário..." required></textarea>
                        <div class="form-submit">
                            <input name="submit" type="submit" id="submit" class="submit" value=">">
                            <input type="hidden" name="comment_post_ID" value="' . get_the_ID() . '" id="comment_post_ID">
                            <input type="hidden" name="comment_parent" id="comment_parent" value="0">
                        </div>
                    </div>',
                'submit_field' => '' // Remove default submit button
            );
        }

        // Display the comment form with the modified arguments
        comment_form($comment_form_args);
    ?>
</div>