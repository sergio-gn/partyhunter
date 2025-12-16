<?php
/* Template Name: Group */

// --- BLOCO 1: Intercepta a busca e MONTA O LINK ---
if (isset($_POST['group-input']) && !empty($_POST['group-input'])) {
    $input_value = trim($_POST['group-input']);   
    // 1. Remove o prefixo "ID:" ou "id:" caso o usuário tenha copiado o rótulo junto
    $input_value = preg_replace('/^ID:\s*/i', '', $input_value);
    // 2. Garante que pegamos só o código (caso ele tenha colado uma URL sem querer)
    $clean_group_id = basename($input_value);
    // 3. Monta o link completo e joga o usuário para lá
    // Transforma "95qH3wJVueVK" em "https://partyhunter.com.br/grupo/95qH3wJVueVK/"
    if (!empty($clean_group_id)) {
        wp_redirect(home_url('/grupo/' . $clean_group_id . '/'));
        exit;
    }
}
get_header();
get_template_part( 'parts/navigation' );

// Get group ID from URL
$group_id = get_query_var('group_id', '');
if (empty($group_id)) {
    // Try to get from URL path
    $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $path_parts = explode('/', $path);
    $group_index = array_search('grupo', $path_parts);
    if ($group_index !== false && isset($path_parts[$group_index + 1])) {
        $group_id = $path_parts[$group_index + 1];
    }
}
// --- COLAR AQUI ---
////echo "<h1>DEBUG ID:</h1>";
////var_dump($group_id);
////die(); // Mata o script aqui

// Se não tiver ID na URL, mostra o campo para digitar
if (empty($group_id) && is_page('grupo')) {
    ?>
    <div style="text-align: center; padding: 3rem; max-width: 500px; margin: 0 auto;">
        <h2>Encontrar Grupo</h2>
        <p>Digite o ID do grupo (ex: 95qH3wJVueVK) para entrar:</p>
        
        <form method="post">
            <div style="display: flex; gap: 10px; justify-content: center; margin-top: 1rem;">
                <input type="text" name="group_input" placeholder="Cole o ID aqui..." style="padding: 10px; width: 100%; border: 1px solid #ccc; border-radius: 4px;" required>
                <button type="submit" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Ir</button>
            </div>
        </form>
    </div>
    <?php
    get_footer();
    exit;
}

// Handle join group action
if (isset($_POST['join_group']) && !empty($group_id) && is_user_logged_in()){
    $result = join_group($group_id);
    if ($result['success']) {
        $join_success = true;
        $join_message = $result['message'];
    } else {
        $join_error = true;
        $join_message = $result['message'];
    }
}

$group = get_group($group_id);
//echo "<h1>DEBUG GROUP DATA:</h1>";
//echo "<pre>";
//var_dump($group);
//echo "</pre>";
//die();
$current_user_id = get_current_user_id();
//Not logged, accepts that the user is not the owner
$is_logged_in = is_user_logged_in();
$is_member = $is_logged_in ? is_user_in_group($group_id) : false;
$members = ($is_member || $is_logged_in) ? get_group_members($group_id) : [];
//Show members only if the user is logged

// Lógica de Título Seguro (Resolve o erro 500)
$group_title = '';
if ($group) {
    // Tenta pegar o título independentemente se é array ou objeto
    if (is_object($group) && isset($group->post_title)) {
        $group_title = $group->post_title;
    } elseif (is_array($group) && isset($group['title'])) {
        $group_title = $group['title'];
    } else {
        $group_title = get_the_title($group->ID ?? 0);
    }
}
//AÇÃO DE ENTRAR NO GRUPO
if (isset($_POST['join_group']) && !empty($group_id) && $is_logged_in) {
    $result = join_group($group_id);
    if ($result['success']) {
        $join_success = true;
        $join_message = $result['message'];
        $is_member = true; // Atualiza status imediatamente
    } else {
        $join_error = true;
        $join_message = $result['message'];
    }
}

//AÇÃO DE RENOMEAR (Back-end)
$rename_message = '';
$rename_error = '';
if ($is_logged_in && isset($_POST['new_group_name']) && isset($_POST['rename_nonce'])) {
    if (wp_verify_nonce($_POST['rename_nonce'], 'rename_group_action')) {
        $new_name = sanitize_text_field($_POST['new_group_name']);
        // Precisamos do ID numérico para atualizar
        $target_id = is_object($group) ? $group->ID : ($group['ID'] ?? 0);
        
        if ($target_id) {
            $updated_post = array('ID' => $target_id, 'post_title' => $new_name);
            $res = wp_update_post($updated_post);
            if (!is_wp_error($res)) {
                $group_title = $new_name; // Atualiza visualmente agora
                $rename_message = 'Nome atualizado!';
            } else {
                $rename_error = 'Erro ao atualizar.';
            }
        }
    }
}
?>


<style>
    .group-page-container {
        min-height: 80vh;
        padding: 2rem 1rem;
    }

    .group-content {
        max-width: 1200px;
        margin: 0 auto;
    }

    .group-header {
        background: white;
        border-radius: 1.5rem;
        padding: 2.5rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        text-align: center;
        margin-bottom: 2rem;
    }

    .group-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #1a1a1a;
    }

    .group-header p {
        color: #666;
        font-size: 1rem;
        margin: 0.5rem 0;
    }

    .group-id {
        background: #f5f5f5;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-family: monospace;
        font-size: 0.9rem;
        display: inline-block;
        margin: 1rem 0;
    }

    .group-actions {
        margin-top: 2rem;
    }

    .btn-group-action {
        padding: 1rem 2rem;
        border: none;
        border-radius: 0.75rem;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-join {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
    }

    .btn-join:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(79, 172, 254, 0.5);
    }

    .btn-leave {
        background: #f5f5f5;
        color: #666;
    }

    .btn-leave:hover {
        background: #e8e8e8;
    }

    .alert {
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .members-section {
        background: white;
        border-radius: 1.5rem;
        padding: 2.5rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .members-section h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: #1a1a1a;
    }

    .members-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .member-card {
        text-align: center;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 1rem;
        transition: all 0.3s ease;
    }
    .member-card a {
        text-decoration: none;
        color: black;
    }
    .member-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .member-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        margin: 0 auto 1rem;
        overflow: hidden;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .member-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .member-name {
        font-weight: 600;
        color: #1a1a1a;
        margin: 0;
    }

    .no-group {
        text-align: center;
        padding: 3rem;
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .no-group h2 {
        color: #1a1a1a;
        margin-bottom: 1rem;
    }

    .no-group p {
        color: #666;
    }

        .blur-content {
        filter: blur(8px);
        pointer-events: none;
        user-select: none;
        opacity: 0.6;
        transition: all 0.3s ease;
    }
    /* Modal Overlay */
    .ph-auth-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.85);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(5px);
    }

    /* Modal Box */
    .ph-auth-modal {
        background: white;
        padding: 0;
        border-radius: 12px;
        width: 90%;
        max-width: 400px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        overflow: hidden;
        animation: slideUp 0.4s ease;
    }

    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    /* Tabs do Modal */
    .ph-auth-tabs {
        display: flex;
        background: #f8f9fa;
        border-bottom: 1px solid #eee;
    }
    .ph-auth-tab {
        flex: 1;
        padding: 15px;
        text-align: center;
        cursor: pointer;
        font-weight: 600;
        color: #666;
        transition: 0.2s;
    }
    .ph-auth-tab.active {
        background: white;
        color: #007bff;
        border-bottom: 2px solid #007bff;
    }

    /* Form Body */
    .ph-auth-body { padding: 25px; }
    .ph-form-group { margin-bottom: 15px; }
    .ph-form-group label { display: block; margin-bottom: 5px; font-size: 0.9rem; color: #333; }
    .ph-form-input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; }
    .ph-btn-submit { width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.2s; }
    .ph-btn-submit:hover { background: #0056b3; }
    
    /* Botões de Compartilhamento */
    .share-buttons { margin-top: 10px; display: flex; gap: 10px; justify-content: center; }
    .btn-share { padding: 8px 12px; border-radius: 20px; text-decoration: none; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 5px; cursor: pointer; border: none; transition: transform 0.1s;}
    .btn-share:active { transform: scale(0.95); }
    .btn-whatsapp { background: #25D366; color: white; }
    .btn-copy { background: #f0f0f0; color: #333; }
</style>

<section class="group-page-container <?php echo !$is_logged_in ? 'blur-content' : ''; ?>">
    <div class="container">
        <?php if (!$group): ?>
            <div class="no-group">
                <h2>Grupo não encontrado</h2>
                <p>O grupo que você está procurando não existe ou foi removido.</p>
                <a href="<?php echo home_url(); ?>" class="btn-group-action btn-join" style="margin-top: 1rem;">Voltar</a>
            </div>
        <?php else: ?>
    <div class="group-header">
    <?php 
    // Lógica para verificar dono
    $is_owner = $is_logged_in && (get_current_user_id() == $group_author); 
    ?>
    
    <div style="display: flex; align-items: center; gap: 10px;justify-content: center;">
        <h1 style="text-align:center;">
            <?php echo $group['title'];?>
        </h1> 
        <?php if ($is_owner): ?>
            <button onclick="document.getElementById('rename-form').style.display='block'" style="font-size: 1.2rem; background:none; border:none; cursor:pointer;" title="Editar nome">
                ✏️
            </button>
        <?php endif; ?>
    </div>
    
    <div class="group-id">ID: <?php echo esc_html($group_id); ?></div>
    
    <div class="share-buttons">
        <?php 
        $group_url = home_url('/grupo/' . $group_id . '/');
        $share_text = urlencode("Junte-se ao grupo '{$group['title']}' no Party Hunter! Confira: " . $group_url);
        $whatsapp_link = "https://wa.me/?text=" . urlencode($share_text . "" . $group_url);
        ?>

        <a href="<?php echo $whatsapp_link; ?>" target="_blank" class="btn-share btn-whatsapp">
                        📱 WhatsApp
        </a>
        <button onclick="copyGroupLink('<?php echo $group_url; ?>')" class="btn-share btn-copy">
                        🔗 Copiar Link
        </button>
    </div>

    <?php if (!empty($rename_message)): ?>
        <div class="alert alert-success" style="margin-bottom: 1rem;"> 
            Sucesso 
            <?php echo esc_html($rename_message); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($rename_error)): ?>
        <div class="alert alert-error" style="margin-bottom: 1rem;"> Erro 
            <?php echo esc_html($rename_error); ?>
    </div>
    <?php endif; ?>
    <?php if ($is_owner): ?>
        <div id="rename-form" style="display:none; margin: 10px 0; background: #f0f0f0; padding: 15px; border-radius: 5px;">
            <form method="post" style="display:flex; gap:10px;">
                <?php wp_nonce_field('rename_group_action', 'rename_nonce'); ?>
                <input type="text" name="new_group_name" value="<?php echo esc_attr($group->post_title); ?>" required placeholder="Novo nome do grupo" style="flex:1;">
                <button type="submit" class="btn-group-action">Salvar</button>
                <button type="button" class="btn-group-action" style="background:#ccc;" onclick="document.getElementById('rename-form').style.display='none'">Cancelar</button>
            </form>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($join_success) && isset($join_success)): ?>
        <div class="alert alert-success">
            <?php echo esc_html($join_message); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($join_message) && isset($join_error)): ?>
        <div class="alert alert-error"> Join Error
            <?php echo esc_html($join_message); ?>
        </div>
    <?php endif; ?>
    
    <div class="group-actions" style="margin-top: 1.5rem;">
        <?php if (!$is_member): ?>
            <form method="post" style="display: inline;">
                <input type="hidden" name="join_group" value="1">
                <button type="submit" class="btn-group-action btn-join">Entrar no Grupo</button>
            </form>
        <?php else: ?>
            <p style="color: #28a745; font-weight: 600;">✓ Você é membro deste grupo</p>
            <form method="post" action="<?php echo esc_url(home_url('/grupo/' . $group_id . '/sair')); ?>" style="display: inline; margin-top: 1rem;">
                <input type="hidden" name="leave_group" value="1">
                <button type="submit" class="btn-group-action btn-leave" onclick="return confirm('Tem certeza que deseja sair do grupo?');">Sair do Grupo</button>
            </form>
        <?php endif; ?>
    </div>
</div>

            <?php if ($is_member): ?>
                <div class="members-section" style="margin-top:1.5rem;">
                    <h2>Comentar no Grupo</h2>
                    <form id="group-comment-form" enctype="multipart/form-data">
                        <textarea name="content" id="group-comment-content" rows="4" style="width:100%; padding:1rem; border-radius:0.5rem; border:1px solid #e0e0e0;" placeholder="Escreva um comentário para o grupo..."></textarea>
                        <div style="margin-top:0.5rem; display:flex; gap:0.5rem; align-items:center;">
                            <label style="cursor:pointer; padding:0.5rem 0.75rem; border-radius:0.5rem; background:#f5f5f5;">Adicionar foto<input type="file" id="group-comment-image" name="image" accept="image/*" style="display:none;"></label>
                            <button type="submit" class="btn-group-action btn-join">Comentar</button>
                        </div>
                        <div id="group-comment-msg" style="margin-top:0.75rem; display:none;"></div>
                    </form>
                </div>
                
                <script>
                document.addEventListener('DOMContentLoaded', function(){
                    const commentForm = document.getElementById('group-comment-form');
                    if (commentForm) {
                        commentForm.addEventListener('submit', function(e){
                            e.preventDefault();
                            const content = document.getElementById('group-comment-content').value;
                            const imageInput = document.getElementById('group-comment-image');
                            const msg = document.getElementById('group-comment-msg');
                            const submitBtn = commentForm.querySelector('button[type="submit"]');
                            const originalBtnText = submitBtn.textContent;
                            
                            msg.style.display = 'none';
                            
                            if (!content.trim() && (!imageInput || !imageInput.files || !imageInput.files[0])) {
                                msg.style.display = 'block';
                                msg.style.color = '#721c24';
                                msg.textContent = 'Por favor, escreva um comentário ou adicione uma imagem.';
                                return;
                            }

                            submitBtn.disabled = true;
                            submitBtn.textContent = 'Publicando...';

                            const fd = new FormData();
                            fd.append('action', 'create_group_comment');
                            fd.append('nonce', '<?php echo wp_create_nonce('social_nonce'); ?>');
                            fd.append('group_id', '<?php echo esc_js($group_id); ?>');
                            fd.append('content', content);
                            if (imageInput && imageInput.files && imageInput.files[0]) {
                                fd.append('image', imageInput.files[0]);
                            }

                            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                                method: 'POST',
                                body: fd
                            }).then(r=>r.json()).then(data=>{
                                submitBtn.disabled = false;
                                submitBtn.textContent = originalBtnText;
                                
                                if (data.success) {
                                    msg.style.display = 'block';
                                    msg.style.color = '#155724';
                                    msg.textContent = 'Comentário publicado.';
                                    document.getElementById('group-comment-content').value = '';
                                    if (imageInput) imageInput.value = '';
                                    
                                    if (data.data && data.data.comment_html) {
                                        const list = document.getElementById('ph-group-posts-list');
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
                                    msg.textContent = data.data || 'Erro ao publicar comentário.';
                                }
                            }).catch(err=>{
                                submitBtn.disabled = false;
                                submitBtn.textContent = originalBtnText;
                                msg.style.display = 'block';
                                msg.style.color = '#721c24';
                                msg.textContent = 'Erro de rede. Tente novamente.';
                                console.error('Comment submission error:', err);
                            });
                        });
                    }
                    
                    const postsList = document.getElementById('ph-group-posts-list');
                    if(postsList) {
                        postsList.addEventListener('click', function(e){
                            const btn = e.target.closest('.ph-delete-comment-btn');
                            if (!btn) return;
                            
                            const commentId = btn.dataset.commentId;
                            if (!commentId) return;
                            
                            if (!confirm('Deseja realmente excluir este comentário?')) return;
                            
                            btn.disabled = true;
                            const originalText = btn.textContent;
                            btn.textContent = 'Excluindo...';
                            
                            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                                method: 'POST',
                                headers: {'Content-Type':'application/x-www-form-urlencoded'},
                                body: new URLSearchParams({
                                    action: 'delete_group_comment',
                                    nonce: '<?php echo wp_create_nonce('social_nonce'); ?>',
                                    comment_id: commentId
                                })
                            }).then(r=>r.json()).then(data=>{
                                btn.disabled = false;
                                btn.textContent = originalText;
                                
                                if (data.success) {
                                    const el = document.querySelector('.group-comment[data-comment-id="' + commentId + '"]');
                                    if (el) el.remove();
                                } else {
                                    alert(data.data || 'Erro ao excluir comentário.');
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
            <?php endif; ?>

<?php if ($is_member && !empty($members)): ?>
    <div class="members-section" style="margin-top:1.5rem;">
        <h2>Membros do Grupo (<?php echo count($members); ?>)</h2>
            <div class="members-list">
                <?php foreach ($members as $member): ?>
                    <?php 
                        // 1. Get the User Object (Same behavior as "Followers" code)
                        $mid = intval($member['id']); 
                        $actualUser = get_user_by('id', $mid);
                        // Safety check: if user doesn't exist in DB, skip this iteration
                        if (!$actualUser) continue;
                        // 2. Build the URL (Using site_url and nicename, exactly like your working code)
                        $profile_url = site_url('/' . $actualUser->user_nicename . '/');
                        // 3. Get Meta (Your custom shape logic)
                        $mshape = get_user_meta($mid, 'avatar_shape', true) ?: 'circle';
                    ?>
                <div class="member-card">
                    <div class="member-avatar user-avatar shape-<?php echo esc_attr($mshape); ?>">
                        <a href="<?php echo esc_url($profile_url); ?>" title="Ver perfil de <?php echo esc_attr($actualUser->display_name); ?>">
                            <?php echo get_avatar($mid, 80); ?>
                        </a>
                    </div>
                    <p class="member-name">
                        <a href="<?php echo esc_url($profile_url); ?>">
                            <?php echo esc_html($actualUser->display_name); ?> 
                        </a>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
    </div>
<?php endif; ?>

<?php // Group comments section - show comments belonging to this group post ?>
<div class="members-section" style="margin-top:1.5rem;">
    <h2>Comentários do Grupo</h2>
    <div id="ph-group-posts-list">
        <?php
// IMPORTANTE: load comments only if logged and member
if ($is_logged_in && function_exists('ph_get_group_comments')) {
    
    $group_comments = ph_get_group_comments($group_id, 200);

    if (!empty($group_comments)) {
        foreach ($group_comments as $c) {   
            $author = get_user_by('id', $c->user_id);
            // $avatar = get_avatar($c->user_id, 48); // Estava comentado no original
            $image_id = get_comment_meta($c->comment_ID, 'comment_image', true);
            $image_html = '';
            
            if ($image_id) {
                $image_html = wp_get_attachment_image($image_id, 'medium');
            }

            // Início do container do comentário
            echo '<div class="group-comment" data-comment-id="' . esc_attr($c->comment_ID) . '" style="padding:0.75rem;border:1px solid #eee;border-radius:0.5rem;margin-bottom:0.5rem;background:#fff;">';
                
                // Container Flex
                echo '<div style="display:flex;gap:0.75rem;align-items:flex-start;">';
                    
                    // Coluna de conteúdo (Flex 1)
                    echo '<div style="flex:1;">';
                        
                        // Cabeçalho (Nome e Data)
                        echo '<div style="display:flex;justify-content:space-between;align-items:center;">';
                            echo '<strong>' . esc_html($author ? $author->display_name : $c->comment_author) . '</strong>';
                            echo '<small style="color:#666;">' . human_time_diff(strtotime($c->comment_date)) . ' atrás</small>';
                        echo '</div>'; // Fecha cabeçalho

                        // Conteúdo do texto
                        echo '<div style="margin-top:0.5rem;color:#444;">' . wpautop(esc_html($c->comment_content)) . '</div>';
                        
                        // Imagem do comentário (se houver)
                        if ($image_html) {
                            echo '<div style="margin-top:0.5rem;">' . $image_html . '</div>';
                        }

                        // Botão de Excluir (Autor ou Admin)
                        $can_delete = (get_current_user_id() && (get_current_user_id() == intval($c->user_id))) || current_user_can('administrator');
                        
                        if ($can_delete) {
                            echo '<div style="margin-top:0.5rem;">';
                                echo '<button class="ph-delete-comment-btn" data-comment-id="' . esc_attr($c->comment_ID) . '" style="background:#f8d7da;color:#721c24;border:none;padding:0.5rem 0.75rem;border-radius:0.5rem;cursor:pointer;">Excluir</button>';
                            echo '</div>';
                        }
                    echo '</div>'; // Fecha div style="flex:1;"
                echo '</div>'; // Fecha div style="display:flex..."
            echo '</div>'; // Fecha div class="group-comment"
        }
    } else {
        // Caso não haja comentários (Else do !empty)
        echo '<p>Sem comentários no grupo ainda.</p>';
    }
} else {
    // Caso não esteja logado ou a função não exista (Else do $is_logged_in)
    echo '<p>Por favor, faça login e entre no grupo para ver os comentários.</p>';
}
?>
    </div>
</div>

        <?php endif; ?>
    </div>
</section>
<?php 
if (!$is_logged_in): 
    echo do_shortcode('[ph_auth_modal]');
endif; ?>
<?php get_footer(); ?>