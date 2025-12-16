<?php
    /* Template Name: Produtor Add Party */
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
                            Adicionar Novo Evento
                        </h2>

                        <?php
                            $current_user = wp_get_current_user();
                            $limit_reached = produtor_post_limit_reached();
                        ?>
                        <?php if ( $limit_reached ): ?>
                            <p>Você atingiu o limite de 3 Eventos.</p>
                        <?php else: ?>
                            <form class="add_party_form" method="post">
                                <div class="form-group">
                                    <input type="text" name="post_title" placeholder=" " required>
                                    <label>Nome do Evento:</label>
                                </div>

                                <div class="form-group">
                                    <textarea name="post_content" placeholder=" " required></textarea>
                                    <label>Descrição:</label>
                                </div>

                                <div class="form-group">
                                    <input type="date" name="event_date" required>
                                    <label>Data:</label>
                                </div>

                                <div class="form-group">
                                    <input type="url" name="insta" placeholder=" ">
                                    <label>Instagram:</label>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="location" placeholder=" " required>
                                    <label>Localização:</label>
                                </div>

                                <div class="form-group">
                                    <input type="number" name="lote_1" placeholder=" ">
                                    <label>Lote 1:</label>
                                </div>

                                <div class="form-group">
                                    <input type="number" name="lote_2" placeholder=" ">
                                    <label>Lote 2:</label>
                                </div>

                                <div class="form-group">
                                    <input type="number" name="lote_3" placeholder=" ">
                                    <label>Lote 3:</label>
                                </div>

                                <div class="form-group">
                                    <input type="number" name="lote_4" placeholder=" ">
                                    <label>Lote 4:</label>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="price" placeholder=" " required>
                                    <label>Preço:</label>
                                </div>
                                <input class="add_party" type="submit" name="submit_post" value="Adicionar Evento">
                            </form>
                        <?php endif; ?>

                        <?php
                        if ( isset( $_POST['submit_post'] ) && !$limit_reached ) {
                            $post_data = array(
                                'post_title'    => sanitize_text_field( $_POST['post_title'] ),
                                'post_content'  => sanitize_textarea_field( $_POST['post_content'] ),
                                'post_status'   => 'pending',  // Change to 'publish' if needed
                                'post_author'   => $current_user->ID,
                                'post_type'     => 'post'
                            );

                            $post_id = wp_insert_post( $post_data );

                            if ( $post_id ) {
                                // Save metadata
                                update_post_meta( $post_id, 'event_date', sanitize_text_field( $_POST['event_date'] ) );
                                update_post_meta( $post_id, 'insta', esc_url( $_POST['insta'] ) );
                                update_post_meta( $post_id, 'location', sanitize_text_field( $_POST['location'] ) );
                                update_post_meta( $post_id, 'lote_1', intval( $_POST['lote_1'] ) );
                                update_post_meta( $post_id, 'lote_2', intval( $_POST['lote_2'] ) );
                                update_post_meta( $post_id, 'lote_3', intval( $_POST['lote_3'] ) );
                                update_post_meta( $post_id, 'lote_4', intval( $_POST['lote_4'] ) );
                                update_post_meta( $post_id, 'price', sanitize_text_field( $_POST['price'] ) );
                                update_post_meta( $post_id, 'vote', intval( $_POST['vote'] ) );

                                echo "<p>Seu evento foi enviado para revisão.</p>";
                            } else {
                                echo "<p>Ocorreu um erro. Tente novamente.</p>";
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