<?php
/* Template Name: Frontpage */

get_header();

if ( !wp_is_mobile() ) {
    get_template_part( 'parts/navigation' );
}
?>


<?php if (!is_user_logged_in()) : ?>
    <section>
        <div>
            <div class="welcome-container">
                <div class="welcome-header">
                    <?php 
                        if (wp_is_mobile() ){
                            $custom_logo_id = get_theme_mod( 'custom_logo' );
                            $custom_logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
                            if ( $custom_logo_url ) {
                                echo '<img src="' . esc_url( $custom_logo_url ) . '" alt="' . get_bloginfo( 'name' ) . '">';
                            }
                        }
                    ?>
                </div>
                <div class="welcome-actions">
                    <a href="<?php echo home_url('/melhores-festas/'); ?>" class="welcome-card welcome-card-primary">
                        <div class="welcome-card-content">
                            <h3>Ranking de Festas</h3>
                            <p>Encontre e vote nas melhores festas da sua cidade</p>
                        </div>
                    </a>
                    <a href="<?php echo home_url('/registrar/'); ?>" class="welcome-card welcome-card-secondary">
                        <div class="welcome-card-content">
                            <h3>Criar Grupo</h3>
                            <p>Crie ou entre um grupo</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <?php 
            if (wp_is_mobile() ){
            ?>
                <a class="login_button_mobile" href="/login/">
                    Login
                </a>
            <?php
            }
        ?>
    </section>
<?php endif; ?>

<style>
    body{
        background: url(<?php echo get_template_directory_uri(); ?>/assets/images/bg_party_hunter.png) no-repeat center center fixed;
        background-size: cover;
    }
    .login_button_mobile{
        position:absolute;
        bottom:1rem;
        right: 0;
        left: 0;
        margin: 0 5vw;
        background:#8C65D8;
        border-radius:40px;
        color:#fff;
        text-decoration:none;
        padding:15px;
        text-align:center;
        font-weight:600;
        font-size:1rem;
        transition:all 0.3s ease;
    }
    .welcome-container{
        margin: 0 5vw;
    }
    .welcome_wrapper{
        display:flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .best_parties_btn{
        background: #fff;
        color: #1a1a1a;
        padding: 1rem 2rem;
        border-radius: 10rem;
        text-decoration: none;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    .live-pulse-circle {
        display: inline-block;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #19e239;
        box-shadow: 0 0 0 rgba(25,226,57,0.6);
        position: relative;
        animation: pulse-green 1.5s infinite cubic-bezier(0.66, 0, 0, 1);
        margin-right: 0.2rem;
    }

    @keyframes pulse-green {
        0% {
            box-shadow: 0 0 0 0 rgba(25,226,57,0.6);
        }
        70% {
            box-shadow: 0 0 0 8px rgba(25,226,57,0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(25,226,57,0.6);
        }
    }
    /* Group Options Styles */
    .group-options-container {
        background: #fff;
        border-radius: 1.5rem;
        padding: 2.5rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .group-options-header {
        margin-bottom: 2rem;
    }

    .group-options-header h2 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #1a1a1a;
    }

    .group-options-header p {
        color: #666;
        font-size: 1rem;
    }

    .options-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .option-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 1rem;
        padding: 2rem;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        position: relative;
        overflow: hidden;
    }

    .option-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.1);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .option-card:hover::before {
        opacity: 1;
    }

    .option-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(102, 126, 234, 0.4);
    }

    .option-card:active {
        transform: translateY(-2px);
    }

    .option-icon {
        width: 64px;
        height: 64px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        position: relative;
        z-index: 1;
    }

    .option-content {
        position: relative;
        z-index: 1;
    }

    .option-content h3 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .option-content p {
        font-size: 0.95rem;
        opacity: 0.9;
        margin: 0;
    }

    .option-card.create-group {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .option-card.join-group {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .option-card.create-group:hover {
        box-shadow: 0 12px 24px rgba(245, 87, 108, 0.4);
    }

    .option-card.join-group:hover {
        box-shadow: 0 12px 24px rgba(79, 172, 254, 0.4);
    }

    @media (min-width: 768px) {
        .options-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 600px) {
        .group-options-container {
            padding: 2rem 1.5rem;
        }

        .group-options-header h2 {
            font-size: 1.5rem;
        }

        .option-card {
            padding: 1.5rem;
        }
    }

    /* Welcome Section Styles */
    .welcome-section {
        background: #271A4E;
        min-height: 60vh;
        display: flex;
        align-items: center;
        flex-direction: column;
    }

    .welcome-header {
        margin-top: 3rem;
        margin-bottom: 3rem;
        color: #fff;
        display: flex;
        flex-direction: row;
        justify-content: center;
    }

    .welcome-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #fff;
    }

    .welcome-header p {
        font-size: 1.25rem;
        opacity: 0.95;
        color: #fff;
    }

    .welcome-actions {
        display: grid;
        gap: 2rem;
    }

    .welcome-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 46px;
        padding: 2.5rem;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .welcome-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .welcome-card:hover::before {
        opacity: 1;
    }

    .welcome-card:hover {
        transform: translateY(-8px);
    }

    .welcome-card-primary {
        background: #BB96FF;
    }

    .welcome-card-secondary {
        background: #8C65D8;
    }

    .welcome-card-icon {
        font-size: 4rem;
        position: relative;
        z-index: 1;
    }

    .welcome-card-content {
        position: relative;
        z-index: 1;
        color: #1a1a1a;
    }

    .welcome-card-content h3 {
        font-size: 1.75rem;
        font-weight: 600;
        margin: 0;
        color: #fff;
    }

    .welcome-card-content p {
        font-size: 1.1rem;
        color: #ffffff;
        margin: 0;
        line-height: 1.6;
    }

    @media (min-width: 768px) {
        .welcome-actions {
            grid-template-columns: 1fr 1fr;
        }

        .welcome-header h1 {
            font-size: 3.5rem;
        }

        .welcome-header p {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 600px) {
        .welcome-section {
            min-height: 50vh;
        }

        .welcome-header h1 {
            font-size: 2rem;
        }

        .welcome-header p {
            font-size: 1.1rem;
        }

        .welcome-card-icon {
            font-size: 3rem;
        }

        .welcome-card-content h3 {
            font-size: 1.5rem;
        }

        .welcome-card-content p {
            font-size: 1rem;
        }
    }

    /* My Groups Section Styles */
    .my-groups-container {
        background: white;
        border-radius: 1.5rem;
        padding: 2.5rem;
        margin-top: 2rem;
    }

    .my-groups-header {
        margin-bottom: 2rem;
        text-align: center;
    }

    .my-groups-header h2 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #1a1a1a;
    }

    .my-groups-header p {
        color: #666;
        font-size: 1rem;
    }

    .groups-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .group-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 1rem;
        padding: 1.5rem;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        position: relative;
        overflow: hidden;
    }

    .group-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.1);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .group-card:hover::before {
        opacity: 1;
    }

    .group-card:hover {
        transform: translateY(-4px);
    }

    /* Owned groups styling - different color */
    .group-card.owned {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .group-card.owned:hover {
        box-shadow: 0 8px 25px rgba(245, 87, 108, 0.5);
    }

    .group-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        position: relative;
        z-index: 1;
    }

    .group-card-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
        flex: 1;
    }

    .group-owner-badge {
        background: rgba(255, 255, 255, 0.3);
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        white-space: nowrap;
        margin-left: 0.5rem;
    }

    .group-card-info {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .group-card-meta {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .group-card-meta svg {
        width: 16px;
        height: 16px;
        fill: currentColor;
    }

    .no-groups-message {
        text-align: center;
        padding: 3rem 1rem;
        color: #666;
    }

    .no-groups-message p {
        font-size: 1.1rem;
        margin: 0.5rem 0;
    }

    @media (max-width: 768px) {
        .groups-list {
            grid-template-columns: 1fr;
        }

        .my-groups-container {
            padding: 2rem 1.5rem;
        }
    }
</style>
<?php if (is_user_logged_in()) : ?>
    <section class="hunter_page">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="welcome-container">
                        <div>
                            <h1>Dashboard</h1>
                            <a class="best_parties_btn" href="<?php echo home_url('/melhores-festas/'); ?>" style="display: inline-flex; align-items: center; gap: 0.7rem;">
                                <span class="live-pulse-circle"></span>
                                Ver Melhores Festas Ao Vivo <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>    
            </div>
            <!-- My Groups Section -->
            <?php
                $current_user_id = get_current_user_id();
                $user_groups = get_user_groups($current_user_id);
            ?>
            <div class="row" style="margin-top: 3rem;">
                <div class="my-groups-container">
                    <div class="my-groups-header">
                        <h2>Meus Grupos</h2>
                        <p>Grupos que você faz parte</p>
                    </div>
                    
                    <?php if (!empty($user_groups)) : ?>
                        <div class="groups-list">
                            <?php foreach ($user_groups as $group) : 
                                $is_owner = ($group['creator_id'] == $current_user_id);
                                $group_link = home_url('/grupo/' . $group['id']);
                            ?>
                                <a href="<?php echo esc_url($group_link); ?>" class="group-card <?php echo $is_owner ? 'owned' : ''; ?>">
                                    <div class="group-card-header">
                                        <h3 class="group-card-title"><?php echo esc_html($group['title']); ?></h3>
                                        <?php if ($is_owner) : ?>
                                            <span class="group-owner-badge">Dono</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="group-card-info">
                                        <div class="group-card-meta">
                                            <span style="font-size: 1.2rem;">👥</span>
                                            <span><?php echo esc_html($group['member_count']); ?> membro(s)</span>
                                        </div>
                                        <div class="group-card-meta">
                                            <span style="font-size: 1.2rem;">📅</span>
                                            <span><?php echo esc_html($group['created_at'] ? date('d/m/Y', strtotime($group['created_at'])) : 'N/A'); ?></span>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="no-groups-message">
                            <p>Você ainda não faz parte de nenhum grupo.</p>
                            <p style="font-size: 0.9rem; color: #999;">Crie um grupo ou entre em um grupo existente para começar!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row" style="margin-top: 3rem;">
                <div class="my-groups-container">
                    <h2>Mensagem no Meu Perfil</h2>
                    <form id="followers-post-form">
                        <textarea id="followers-post-content" rows="4" style="width:100%;box-sizing: border-box;padding:1rem; border-radius:0.5rem; border:1px solid #e0e0e0;" placeholder="Adicione uma mensagem no seu perfil..."></textarea>
                        <button type="submit" class="btn-group-action btn-join" style="margin-top:0.75rem;">Adicionar Mensagem</button>
                        <div id="followers-post-msg" style="margin-top:0.75rem; display:none;"></div>
                    </form>

                    <h3 style="margin-top:1.5rem;">Mural</h3>
                    <div id="followed-posts-list" class="mural_list">
                        <?php
                            if (function_exists('ph_get_profile_comments')) {
                                $profile_comments = ph_get_profile_comments(get_current_user_id(), 20);
                                if (!empty($profile_comments)) {
                                    foreach ($profile_comments as $comment) {
                                        $author = get_user_by('id', $comment->user_id);
                                        $author_name = $author ? ($author->display_name ?: $author->user_login) : $comment->comment_author;
                                        $time_ago = human_time_diff(strtotime($comment->comment_date), current_time('timestamp'));
                                        
                                        echo '<div class="profile-comment" data-comment-id="' . esc_attr($comment->comment_ID) . '" style="padding:0.75rem;border:1px solid #eee;border-radius:0.5rem;margin-bottom:0.5rem;background:#fff;">';
                                        echo '<div style="display:flex;justify-content:space-between;align-items:center;">';
                                        echo '<strong>' . esc_html($author_name) . '</strong>';
                                        echo '<small style="color:#666;">' . esc_html($time_ago) . ' atrás</small>';
                                        echo '</div>';
                                        echo '<div style="margin-top:0.5rem;color:#444;">' . wpautop(esc_html($comment->comment_content)) . '</div>';
                                        
                                        // Delete button
                                        $can_delete = (get_current_user_id() == intval($comment->user_id)) || current_user_can('administrator');
                                        if ($can_delete) {
                                            echo '<div style="margin-top:0.5rem;">';
                                            echo '<button class="ph-delete-profile-comment-btn" data-comment-id="' . esc_attr($comment->comment_ID) . '" style="background:#f8d7da;color:#721c24;border:none;padding:0.5rem 0.75rem;border-radius:0.5rem;cursor:pointer;font-size:0.9rem;">Excluir</button>';
                                            echo '</div>';
                                        }
                                        
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<p>Nenhuma mensagem ainda. Adicione uma mensagem acima!</p>';
                                }
                            } else {
                                echo '<p>Função não disponível.</p>';
                            }
                        ?>
                    </div>
                </div>
            </div>
            <!-- Group Options Section -->
            <div class="row" style="margin-top: 3rem;">
                <div class="group-options-container">
                    <div class="group-options-header">
                        <h2>Grupos</h2>
                        <p>O que você gostaria de fazer?</p>
                    </div>
                    
                    <div class="options-grid">
                        <a href="#" class="option-card create-group" id="createGroupBtn">
                            <div class="option-icon">➕</div>
                            <div class="option-content">
                                <h3>Criar Grupo</h3>
                                <p>Crie um link para compartilhar com seus amigos</p>
                            </div>
                        </a>

                        <a href="#" class="option-card join-group" id="joinGroupBtn">
                            <div class="option-icon">🔗</div>
                            <div class="option-content">
                                <h3>Entrar em Grupo</h3>
                                <p>Junte-se a um grupo usando um link</p>
                            </div>
                        </a>
                    </div>
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
            <p id="upload-error" style="color: red;"></p>
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
        document.getElementById('avatar-upload-form').addEventListener('submit', function(event) {
            const fileInput = document.getElementById('user_avatar');
            const errorMessage = document.getElementById('upload-error');

            if (!fileInput.files.length) {
                // Prevent form submission
                event.preventDefault();

                // Show the error message
                errorMessage.textContent = "Por favor selecione uma Imagem";
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const form = document.getElementById('followers-post-form');
            if (!form) return;
            
            form.addEventListener('submit', function(e){
                e.preventDefault();
                const content = document.getElementById('followers-post-content').value;
                const msg = document.getElementById('followers-post-msg');
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.textContent;
                
                msg.style.display = 'none';
                
                if (!content.trim()) {
                    msg.style.display = 'block';
                    msg.style.color = '#721c24';
                    msg.textContent = 'Por favor, escreva uma mensagem.';
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.textContent = 'Adicionando...';

                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({ 
                        action: 'create_profile_comment', 
                        nonce: '<?php echo wp_create_nonce('social_nonce'); ?>', 
                        content: content 
                    })
                }).then(r=>r.json()).then(data=>{
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalBtnText;
                    
                    if (data.success) {
                        msg.style.display = 'block';
                        msg.style.color = '#155724';
                        msg.textContent = 'Mensagem adicionada ao seu perfil.';
                        document.getElementById('followers-post-content').value = '';
                        
                        // Add the new comment to the list
                        if (data.data && data.data.comment_html) {
                            const list = document.getElementById('followed-posts-list');
                            if (list) {
                                const wrapper = document.createElement('div');
                                wrapper.innerHTML = data.data.comment_html;
                                if (list.firstChild) {
                                    list.insertBefore(wrapper.firstChild, list.firstChild);
                                } else {
                                    list.appendChild(wrapper.firstChild);
                                }
                            }
                        }
                    } else {
                        msg.style.display = 'block';
                        msg.style.color = '#721c24';
                        msg.textContent = data.data || 'Erro ao adicionar mensagem.';
                    }
                }).catch(()=>{
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalBtnText;
                    msg.style.display = 'block';
                    msg.style.color = '#721c24';
                    msg.textContent = 'Erro de rede.';
                });
            });
            
            // Delete profile comment handler
            const commentsList = document.getElementById('followed-posts-list');
            if(commentsList) {
                commentsList.addEventListener('click', function(e){
                    const btn = e.target.closest('.ph-delete-profile-comment-btn');
                    if (!btn) return;
                    
                    const commentId = btn.dataset.commentId;
                    if (!commentId) return;
                    
                    if (!confirm('Deseja realmente excluir esta mensagem?')) return;
                    
                    btn.disabled = true;
                    const originalText = btn.textContent;
                    btn.textContent = 'Excluindo...';
                    
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        headers: {'Content-Type':'application/x-www-form-urlencoded'},
                        body: new URLSearchParams({
                            action: 'delete_profile_comment',
                            nonce: '<?php echo wp_create_nonce('social_nonce'); ?>',
                            comment_id: commentId
                        })
                    }).then(r=>r.json()).then(data=>{
                        btn.disabled = false;
                        btn.textContent = originalText;
                        
                        if (data.success) {
                            const el = document.querySelector('.profile-comment[data-comment-id="' + commentId + '"]');
                            if (el) el.remove();
                        } else {
                            alert(data.data || 'Erro ao excluir mensagem.');
                        }
                    }).catch(()=>{
                        btn.disabled = false;
                        btn.textContent = originalText;
                        alert('Erro de rede. Tente novamente.');
                    });
                });
            }
        });
    </script>
    <script>
        // Group options functionality
        document.addEventListener('DOMContentLoaded', function() {
            const createGroupBtn = document.getElementById('createGroupBtn');
            const joinGroupBtn = document.getElementById('joinGroupBtn');

            // Show success message if user left a group
            <?php if (isset($_GET['left_group'])): ?>
                alert('Você saiu do grupo com sucesso.');
            <?php endif; ?>

            // Create Group functionality
            if (createGroupBtn) {
                createGroupBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Show loading state
                    createGroupBtn.style.opacity = '0.6';
                    createGroupBtn.style.pointerEvents = 'none';
                    createGroupBtn.querySelector('.option-content h3').textContent = 'Criando...'; 
                    //prevenindo clique duplo
                    
                    // Create group via AJAX
                    // Envia um POST para admin-ajax.php (a URL é impressa pelo PHP via admin_url)
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            action: 'create_group',
                            nonce: '<?php echo wp_create_nonce('create_group_nonce'); ?>'
                        })
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        console.log('Response ok:', response.ok);
                        
                        if (!response.ok) {
                            // Try to get error message from response
                            return response.text().then(text => {
                                console.error('Error response text:', text);
                                throw new Error('Network response was not ok: ' + response.status);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        if (data.success) {
                            // Show the group link in a modal or alert
                            const groupLink = data.data.group_link;
                            const message = 'Grupo criado com sucesso!\n\nLink do grupo:\n' + groupLink + '\n\nCopie este link e compartilhe com seus amigos.';
                            
                            if (confirm(message + '\n\nDeseja copiar o link para a área de transferência?')) {
                                navigator.clipboard.writeText(groupLink).then(() => {
                                    alert('Link copiado! Agora você pode compartilhar com seus amigos.');
                                }).catch(() => {
                                    // Fallback: select text
                                    const textarea = document.createElement('textarea');
                                    textarea.value = groupLink;
                                    document.body.appendChild(textarea);
                                    textarea.select();
                                    document.execCommand('copy');
                                    document.body.removeChild(textarea);
                                    alert('Link copiado!');
                                });
                            }
                            
                            // Redirect to group page
                            window.location.href = groupLink;
                        } else {
                            const errorMsg = data.data?.message || 'Erro desconhecido. Tente novamente.';
                            console.error('Error creating group:', data);
                            alert('Erro ao criar grupo: ' + errorMsg);
                            createGroupBtn.style.opacity = '1';
                            createGroupBtn.style.pointerEvents = 'auto';
                            createGroupBtn.querySelector('.option-content h3').textContent = 'Criar Grupo';
                        }
                    })
                    .catch(error => {
                        console.error('Error details:', error);
                        console.error('Error message:', error.message);
                        console.error('Error stack:', error.stack);
                        
                        let errorMsg = 'Erro ao criar grupo. ';
                        if (error.message) {
                            errorMsg += error.message;
                        } else {
                            errorMsg += 'Verifique sua conexão e tente novamente.';
                        }
                        
                        alert(errorMsg + '\n\nVerifique o console do navegador (F12) para mais detalhes.');
                        createGroupBtn.style.opacity = '1';
                        createGroupBtn.style.pointerEvents = 'auto';
                        createGroupBtn.querySelector('.option-content h3').textContent = 'Criar Grupo';
                    });
                });
            }

            // Join Group functionality
            if (joinGroupBtn) {
                joinGroupBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Alteramos o texto para indicar que aceita ID também
                    const userInput = prompt('Cole o link do grupo ou digite o ID aqui:');
                    
                    if (userInput && userInput.trim() !== '') {
                        const inputClean = userInput.trim();
                        let groupId = '';

                        // LÓGICA 1: Tenta extrair de uma URL completa
                        try {
                            // Verifica se parece uma URL (tem http ou www ou .com)
                            if (inputClean.includes('http') || inputClean.includes('www.') || inputClean.includes('.com')) {
                                const url = new URL(inputClean.startsWith('http') ? inputClean : 'https://' + inputClean);
                                const pathParts = url.pathname.split('/');
                                const grupoIndex = pathParts.indexOf('grupo');
                                if (grupoIndex !== -1 && pathParts[grupoIndex + 1]) {
                                    groupId = pathParts[grupoIndex + 1];
                                }
                            }
                        } catch (e) {
                            // Falha silenciosa no URL parser, tentamos o próximo método
                        }

                        // LÓGICA 2: Tenta achar o padrão /grupo/ID via Regex (caso a URL esteja incompleta)
                        if (!groupId) {
                            const match = inputClean.match(/grupo\/([^\/\s]+)/);
                            if (match) {
                                groupId = match[1];
                            }
                        }

                        // LÓGICA 3 (NOVA): Se não achou ID acima e não tem "cara" de link, assume que é o próprio ID
                        // Verificamos se não tem barras '/' para evitar erros de digitação de URL
                        if (!groupId && !inputClean.includes('/')) {
                            groupId = inputClean;
                        }
                        
                        // Finalização: Redireciona
                        if (groupId) {
                            // Remove caracteres inválidos (segurança básica para garantir que é só alfanumérico/hífens)
                            // Isso evita que alguém tente injetar código malicioso via prompt
                            groupId = groupId.replace(/[^a-zA-Z0-9-_]/g, '');

                            // Redireciona para www.partyhunter.com.br/grupo/ID
                            window.location.href = '<?php echo home_url('/grupo/'); ?>' + groupId;
                        } else {
                            alert('Link ou ID inválido. Por favor, verifique e tente novamente.');
                        }
                    }
                });
            }
        });
    </script>
<?php endif; ?>
<?php 
    get_footer();
?>