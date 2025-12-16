<?php
    /* Template Name: Produtor Edit Party */
    get_header();
?>
    <body>
        <?php get_template_part( 'parts/navigation-sidebar-produtor' );?>
        <style>
            .hunter_page .card{
                height: auto;
                width: 50vw;
            }
            .add_party_form{
                margin: 1rem;
                width: 100%;

                .add_party{
                    margin: 1rem 0;
                    width: 100%;
                }
                label, input, textarea{
                    width: 100%;
                }
            }
            .form-group {
                position: relative;
                margin-bottom: 20px;
            }

            .form-group input,
            .form-group textarea {
                width: 100%;
                padding: 12px 10px;
                font-size: 16px;
                border: 2px solid #ccc;
                border-radius: 5px;
                background: none;
                outline: none;
                transition: 0.3s;
                box-sizing: border-box;
            }

            .form-group textarea {
                min-height: 100px;
                resize: vertical;
            }

            .form-group label {
                position: absolute;
                top: 50%;
                left: 10px;
                transform: translateY(-50%);
                font-size: 16px;
                color: #999;
                pointer-events: none;
                transition: 0.3s ease-out;
            }

            .form-group input:focus,
            .form-group textarea:focus,
            .form-group input:not(:placeholder-shown),
            .form-group textarea:not(:placeholder-shown) {
                border-color: #007bff;
            }

            .form-group input:focus + label,
            .form-group textarea:focus + label,
            .form-group input:not(:placeholder-shown) + label,
            .form-group textarea:not(:placeholder-shown) + label {
                top: 5px;
                font-size: 12px;
                color: #007bff;
            }

            input, textarea {
                padding-top: 18px;
            }

            .form-group input::placeholder,
            .form-group textarea::placeholder {
                color: transparent;
            }
            @media(orientation:portrait){
                .hunter_page .card{
                    width: 100%;
                    padding: 1rem;
                    box-sizing: border-box;
                    margin: 1rem;
                }
            }
        </style>
        <section class="hunter_page produtor_page">
            <div class="container">
                <div class="row">
                    <div class="card">
                        <h2>
                            Editar Evento
                        </h2>
                        <?php
                            $current_user = wp_get_current_user();
                            
                            // Get the post ID from URL parameter
                            $post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
                            
                            // Get the post
                            $post = get_post($post_id);

                            // Verify post exists and current user is the author
                            if (!$post || $post->post_author != $current_user->ID) {
                                echo "<p>Evento não encontrado ou você não tem permissão para editá-lo.</p>";
                            } else {
                                // Get post meta data
                                $event_date = get_post_meta($post_id, 'event_date', true);
                                $insta = get_post_meta($post_id, 'insta', true);
                                $location = get_post_meta($post_id, 'location', true);
                                $lote_1 = get_post_meta($post_id, 'lote_1', true);
                                $lote_2 = get_post_meta($post_id, 'lote_2', true);
                                $lote_3 = get_post_meta($post_id, 'lote_3', true);
                                $lote_4 = get_post_meta($post_id, 'lote_4', true);
                                $price = get_post_meta($post_id, 'price', true);
                        ?>
                            <form class="add_party_form" method="post">
                                <div class="form-group">
                                    <input type="text" name="post_title" placeholder=" " value="<?php echo esc_attr($post->post_title); ?>" required>
                                    <label>Nome do Evento:</label>
                                </div>
                                <div class="form-group">
                                    <textarea name="post_content" placeholder=" " required><?php echo esc_textarea($post->post_content); ?></textarea>
                                    <label>Descrição:</label>
                                </div>
                                <div class="form-group">
                                    <input type="date" name="event_date" value="<?php echo esc_attr($event_date); ?>" required>
                                    <label>Data:</label>
                                </div>
                                <div class="form-group">
                                    <input type="url" name="insta" placeholder=" " value="<?php echo esc_url($insta); ?>">
                                    <label>Instagram:</label>
                                </div>
                                <div class="form-group">
                                    <input type="text" name="location" placeholder=" " value="<?php echo esc_attr($location); ?>" required>
                                    <label>Localização:</label>
                                </div>
                                <div class="form-group">
                                    <input type="number" name="lote_1" placeholder=" " value="<?php echo esc_attr($lote_1); ?>">
                                    <label>Lote 1:</label>
                                </div>
                                <div class="form-group">
                                    <input type="number" name="lote_2" placeholder=" " value="<?php echo esc_attr($lote_2); ?>">
                                    <label>Lote 2:</label>
                                </div>
                                <div class="form-group">
                                    <input type="number" name="lote_3" placeholder=" " value="<?php echo esc_attr($lote_3); ?>">
                                    <label>Lote 3:</label>
                                </div>
                                <div class="form-group">
                                    <input type="number" name="lote_4" placeholder=" " value="<?php echo esc_attr($lote_4); ?>">
                                    <label>Lote 4:</label>
                                </div>
                                <div class="form-group">
                                    <input type="text" name="price" placeholder=" " value="<?php echo esc_attr($price); ?>" required>
                                    <label>Preço:</label>
                                </div>
                                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                                <input class="add_party" type="submit" name="submit_post" value="Atualizar Evento">
                            </form>
                        <?php
                            }
                            if (isset($_POST['submit_post']) && isset($_POST['post_id'])) {
                                $post_id = intval($_POST['post_id']);
                               
                                // Verify post exists and current user is the author
                                $post = get_post($post_id);
                                if ($post && $post->post_author == $current_user->ID) {
                                    $post_data = array(
                                        'ID' => $post_id,
                                        'post_title' => sanitize_text_field($_POST['post_title']),
                                        'post_content' => sanitize_textarea_field($_POST['post_content']),
                                    );

                                    $updated = wp_update_post($post_data);

                                    if ($updated) {
                                        // Update metadata
                                        update_post_meta($post_id, 'event_date', sanitize_text_field($_POST['event_date']));
                                        update_post_meta($post_id, 'insta', esc_url($_POST['insta']));
                                        update_post_meta($post_id, 'location', sanitize_text_field($_POST['location']));
                                        update_post_meta($post_id, 'lote_1', intval($_POST['lote_1']));
                                        update_post_meta($post_id, 'lote_2', intval($_POST['lote_2']));
                                        update_post_meta($post_id, 'lote_3', intval($_POST['lote_3']));
                                        update_post_meta($post_id, 'lote_4', intval($_POST['lote_4']));
                                        update_post_meta($post_id, 'price', sanitize_text_field($_POST['price']));

                                        echo "<p>Evento atualizado com sucesso!</p>";
                                    } else {
                                        echo "<p>Ocorreu um erro ao atualizar o evento. Tente novamente.</p>";
                                    }
                                }
                            }
                        ?>
                    </div>
                </div>
            </div>
        </section>
    </body>
    <?php wp_footer();?>
</html>