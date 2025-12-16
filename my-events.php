<?php
    /* Template Name: Produtor My Events */
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
                    <h2>Meus eventos</h2>
                        <?php
                        $current_user = wp_get_current_user();
                        $args = array(
                            'author' => $current_user->ID,
                            'post_type' => 'post',
                            'posts_per_page' => -1,
                            'orderby' => 'date',
                            'order' => 'DESC'
                        );
                        
                        $user_events = new WP_Query($args);
                        
                        if ($user_events->have_posts()) : ?>
                            <ul class="events-list">
                            <?php while ($user_events->have_posts()) : $user_events->the_post(); ?>
                                <li class="event-item">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_title(); ?>
                                    </a>
                                    <div class="event-actions">
                                        <a href="/produtor-edit/?post_id=<?php the_ID(); ?>" class="edit-event">Editar</a>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                            </ul>
                        <?php else : ?>
                            <p>Você ainda não tem eventos cadastrados.</p>
                        <?php 
                        endif;
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
            </div>
        </section>
    </body>
    <?php wp_footer();?>
</html>