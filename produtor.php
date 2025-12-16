<?php
    /* Template Name: Produtor */
    get_header();
    ?>
    <body>
        <?php get_template_part( 'parts/navigation-sidebar-produtor' );?>
        <section class="hunter_page produtor_page">
            <div class="container">
                <div class="row">
                    <div class="profile_card">
                        <?php if (is_user_logged_in()) : ?>
                            <button data-open-modal class="change-avatar" id="change-avatar">
                                <div class="pencil_icon">
                                    <svg viewBox="0 0 16 16" width="16px" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M8.29289 3.70711L1 11V15H5L12.2929 7.70711L8.29289 3.70711Z" fill="#b177fb"></path> <path d="M9.70711 2.29289L13.7071 6.29289L15.1716 4.82843C15.702 4.29799 16 3.57857 16 2.82843C16 1.26633 14.7337 0 13.1716 0C12.4214 0 11.702 0.297995 11.1716 0.828428L9.70711 2.29289Z" fill="#b177fb"></path></g></svg>
                                </div>
                                <div class="user-avatar">
                                    <?php echo get_avatar(get_current_user_id(), 96); ?>
                                </div>
                            </button>
                        <?php endif; ?>

                        <h1>Bem vindo Produtor! <br> <?php echo wp_get_current_user()->user_login; ?></h1>
                    </div>
                    <div class="card">
                        <h2>
                            Adicionar Novo Evento
                        </h2>
                        <a href="/adicionar-evento/" class="add_party">+ Adicionar</a>
                    </div>
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
                    <div class="card">
                        <h2>Editar Perfil da Produtora</h2>
                    </div>
                </div>
            </div>
        </section>
        <dialog class="upload_profile_image_popup" data-modal id="avatar-upload-modal">
            <div class="upload_profile_image_popup_align">
                <button class="close_button" data-close-modal>X</button>
                <form class="upload_profile_form" id="avatar-upload-form" method="post" enctype="multipart/form-data">
                    <label for="user_avatar" class="custom-file-label">Escolher imagem</label>
                    <input type="file" id="user_avatar" name="user_avatar" accept="image/*" hidden>
                    <span id="file-name">Nenhum arquivo selecionado</span>
                    <input class="purple_btn" type="submit" name="upload_avatar" value="Upload">
                </form>
            </div>
        </dialog>
        <script>
            const openButton = document.querySelector("[data-open-modal]")
            const closeButton = document.querySelector("[data-close-modal]")
            const modal = document.querySelector("[data-modal]")

            openButton.addEventListener("click", () => {
                modal.showModal()
            })
            closeButton.addEventListener("click", ()=>{
                modal.close()
            })
        </script>
        <script>
            document.getElementById('user_avatar').addEventListener('change', function() {
                let fileName = this.files.length > 0 ? this.files[0].name : "Nenhum arquivo selecionado";
                document.getElementById('file-name').textContent = fileName;
            });
        </script>
    </body>
    <?php wp_footer();?>
</html>