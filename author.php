<?php

/**
 * Author profile template
 */
get_header();
get_template_part('parts/navigation');
?>
<!-- Cropper.js CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
<style>
    dialog::backdrop{
        background-color: rgb(177 119 251 / 60%);
    }
    .profile-page {
        min-height: 50vh;
    }
    .profile-header {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        background: #fff;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
        position: relative;
    }
    .profile-cover-container {
        width: 100%;
        height: 200px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
        border-radius: 0.75rem;
        margin-bottom: 1rem;
    }
    .cover-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .cover-edit-btn {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
        transition: all 0.3s ease;
    }
    .cover-edit-btn:hover {
        background: rgba(255, 255, 255, 1);
        transform: scale(1.1);
    }
    .profile-avatar-container {
        margin-top: -48px;
        position: relative;
        z-index: 1;
        display: flex;
        align-items: flex-end;
        gap: 1rem;
    }
    .change-avatar {
        border: none;
        background: none;
        cursor: pointer;
        width: fit-content;
        position: relative;
    }
    .pencil_icon {
        position: absolute;
        z-index: 2;
        right: 0;
        top: 0;
        margin: auto;
        border-radius: 10rem;
        padding: .5rem;
        background: #fff;
        box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
    }
    .user-avatar {
        width: 96px;
        height: 96px;
        overflow: hidden;
        position: relative;
        border: 4px solid #fff;
        box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
    }
    .user-avatar img {
        object-fit: cover;
        width: 100%;
        height: 100%;
    }
    /* Avatar Shape Styles */
    .user-avatar.shape-circle {
        border-radius: 10rem;
    }
    .user-avatar.shape-square {
        border-radius: 0;
    }
    .user-avatar.shape-star {
        clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
    }
    .user-avatar.shape-wavy {
        border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
        animation: wavy-shape 8s ease-in-out infinite;
    }
    .user-avatar.shape-heart {
        clip-path: polygon(50% 15%, 60% 5%, 75% 5%, 85% 15%, 85% 30%, 50% 60%, 15% 30%, 15% 15%, 25% 5%, 40% 5%);
    }
    .user-avatar.shape-diamond {
        clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%);
    }
    .user-avatar.shape-hexagon {
        clip-path: polygon(30% 0%, 70% 0%, 100% 50%, 70% 100%, 30% 100%, 0% 50%);
    }
    .user-avatar.shape-octagon {
        clip-path: polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%);
    }
    .user-avatar.shape-triangle {
        clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
    }
    .user-avatar.shape-pentagon {
        clip-path: polygon(50% 0%, 100% 38%, 82% 100%, 18% 100%, 0% 38%);
    }
    .user-avatar.shape-squircle {
        border-radius: 25% / 25%;
    }
    .user-avatar.shape-blob {
        border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
    }
    .user-avatar.shape-badge {
        clip-path: polygon(20% 0%, 80% 0%, 100% 20%, 100% 80%, 80% 100%, 20% 100%, 0% 80%, 0% 20%);
    }
    @keyframes wavy-shape {
        0%, 100% { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
        25% { border-radius: 70% 30% 30% 70% / 70% 70% 30% 30%; }
        50% { border-radius: 30% 70% 70% 30% / 70% 30% 30% 70%; }
        75% { border-radius: 70% 30% 30% 70% / 30% 70% 70% 30%; }
    }
    .upload_profile_image_popup {
        max-width: 600px;
        width: 90%;
        padding: 2.5rem;
        height: 75vh;
        overflow-y: auto;
    }
    .upload_profile_image_popup_align {
        display: flex;
        flex-direction: column;
        gap: 2.5rem;
    }
    .close_button {
        position: absolute;
        height: 2rem;
        width: 2rem;
        right: .5rem;
        top: .5rem;
        border: none;
        background: #f0f0f0;
        border-radius: 50%;
        cursor: pointer;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .close_button:hover {
        background: #e0e0e0;
    }
    /* Tab System */
    .tab-container {
        display: flex;
        flex-direction: column;
        width: 100%;
    }
    .tab-buttons {
        display: flex;
        gap: 0;
        background: #f8f9fa;
        border-radius: 0.5rem 0.5rem 0 0;
        padding: 0.5rem 0.5rem 0 0.5rem;
    }
    .tab-button {
        flex: 1;
        padding: 0.875rem 1.5rem;
        background: transparent;
        border: none;
        border-bottom: 3px solid transparent;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 500;
        color: #666;
        transition: all 0.3s ease;
        position: relative;
        border-radius: 0.5rem 0.5rem 0 0;
    }
    .tab-button:hover {
        color: #6a0dad;
        background: rgba(106, 13, 173, 0.05);
    }
    .tab-button.active {
        color: #6a0dad;
        background: #fff;
        border-top: 1px solid #6a0dad;
        font-weight: 600;
    }
    .tab-button.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 2px;
        background: #fff;
    }
    .tab-content {
        display: none;
        animation: fadeIn 0.3s ease;
    }
    .tab-content.active {
        display: block;
        background: #ffffff;
        padding: 1rem;
        border-left: 1px solid #e0e0e0;
        border-right: 1px solid #e0e0e0;
        border-bottom: 1px solid #e0e0e0;
    }
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .modal-section {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    .upload_profile_form {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        align-items: center;
    }
    .custom-file-label {
        background-color: #6a0dad;
        color: white;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        transition: all 0.3s ease;
        border: 3px solid #fff;
        box-shadow: 0 4px 15px rgba(106, 13, 173, 0.3);
        position: relative;
    }
    .custom-file-label:hover {
        background-color: #580c91;
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(106, 13, 173, 0.4);
    }
    .custom-file-label svg {
        width: 60px;
        height: 60px;
        fill: white;
    }
    .custom-file-label.has-image {
        background-color: transparent;
        border: 3px solid #6a0dad;
        overflow: hidden;
        padding: 0;
    }
    .custom-file-label.has-image:hover {
        background-color: transparent;
        transform: scale(1);
    }
    .custom-file-label.has-image svg {
        display: none;
    }
    .custom-file-label img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: none;
    }
    .custom-file-label.has-image img {
        display: block;
    }
    /* Crop Container */
    .image-crop-container {
        display: none;
        width: 100%;
        max-width: 500px;
        margin: 1.5rem auto 0;
        position: relative;
    }
    .image-crop-container.active {
        display: block;
    }
    /* Cropper.js Custom Styles */
    .image-crop-container .cropper-container {
        direction: ltr;
        font-size: 0;
        line-height: 0;
        position: relative;
        touch-action: none;
        user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
    }
    .image-crop-container .cropper-view-box {
        outline: 2px solid #6a0dad;
        outline-color: rgba(106, 13, 173, 0.75);
    }
    .image-crop-container .cropper-face {
        background-color: rgba(106, 13, 173, 0.1);
    }
    .image-crop-container .cropper-line {
        background-color: #6a0dad;
    }
    .image-crop-container .cropper-point {
        background-color: #6a0dad;
        width: 10px;
        height: 10px;
    }
    .crop-controls {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-top: 1.5rem;
        align-items: center;
    }
    .crop-buttons {
        display: flex;
        gap: 1rem;
        width: 100%;
        max-width: 300px;
    }
    .crop-btn {
        flex: 1;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .crop-btn.confirm {
        background-color: #6a0dad;
        color: white;
    }
    .crop-btn.confirm:hover {
        background-color: #580c91;
    }
    .crop-btn.cancel {
        background-color: #e0e0e0;
        color: #333;
    }
    .crop-btn.cancel:hover {
        background-color: #d0d0d0;
    }
    /* Avatar Shape Selection */
    .avatar-shape-selection {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    .shape-options {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 0.75rem;
    }
    .shape-option {
        aspect-ratio: 1;
        border: 2px solid #ddd;
        border-radius: 0.5rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        background: #f5f5f5;
        position: relative;
    }
    .shape-option:hover {
        border-color: #6a0dad;
        transform: scale(1.05);
    }
    .shape-option.active {
        border-color: #6a0dad;
        background: #6a0dad;
        box-shadow: 0 0 0 3px rgba(106, 13, 173, 0.2);
    }
    .shape-preview {
        width: 60%;
        height: 60%;
        background: #333;
    }
    .shape-preview.shape-circle { border-radius: 50%; }
    .shape-preview.shape-square { border-radius: 0; }
    .shape-preview.shape-star { clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%); }
    .shape-preview.shape-heart { clip-path: polygon(50% 15%, 60% 5%, 75% 5%, 85% 15%, 85% 30%, 50% 60%, 15% 30%, 15% 15%, 25% 5%, 40% 5%); }
    .shape-preview.shape-diamond { clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%); }
    .shape-preview.shape-hexagon { clip-path: polygon(30% 0%, 70% 0%, 100% 50%, 70% 100%, 30% 100%, 0% 50%); }
    .shape-preview.shape-octagon { clip-path: polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%); }
    .shape-preview.shape-triangle { clip-path: polygon(50% 0%, 0% 100%, 100% 100%); }
    .shape-preview.shape-pentagon { clip-path: polygon(50% 0%, 100% 38%, 82% 100%, 18% 100%, 0% 38%); }
    .shape-preview.shape-squircle { border-radius: 25% / 25%; }
    .shape-preview.shape-blob { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
    .shape-preview.shape-badge { clip-path: polygon(20% 0%, 80% 0%, 100% 20%, 100% 80%, 80% 100%, 20% 100%, 0% 80%, 0% 20%); }
    .shape-preview.shape-wavy { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
    .shape-option.active .shape-preview {
        background: #fff;
    }
    .purple_btn {
        background-color: #6a0dad;
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 500;
        transition: background-color 0.3s ease;
        width: 100%;
    }
    .purple_btn:hover:not(:disabled) {
        background-color: #580c91;
    }
    .purple_btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>
<?php

                    $queried_object = get_queried_object();
                    if (!$queried_object || !isset($queried_object->ID)) {
                        echo '<div style="padding:2rem;text-align:center;"><h2>Usuário não encontrado</h2></div>';
                        get_footer();
                        exit;
                    }

                    $profile_user = $queried_object; // WP_User
                    $profile_id = $profile_user->ID;
                    $current_user_id = get_current_user_id();
                    $is_owner = ($current_user_id && $current_user_id === $profile_id);
                    $is_following = function_exists('ph_is_following') ? ph_is_following($profile_id) : false;
                    $followers = function_exists('ph_get_followers') ? ph_get_followers($profile_id) : [];
                    $following = function_exists('ph_get_followed_users') ? ph_get_followed_users($profile_id) : [];
                    $profile_description = get_user_meta($profile_id, 'ph_profile_description', true);
                    $profile_tags = get_user_meta($profile_id, 'ph_profile_tags', true);
                    if (!is_array($profile_tags)) $profile_tags = [];

                    // Structured tags (try to load existing structured meta or normalize flat tags)
                    $profile_tags_structured = get_user_meta($profile_id, 'ph_profile_tags_structured', true);
                    if (empty($profile_tags_structured) || !is_array($profile_tags_structured)) {
                        $profile_tags_structured = [
                            'estado_civil' => '',
                            'estilo_musica' => [],
                            'bebida' => [],
                            'custom' => [],
                            'city' => ''
                        ];
                        $flat = $profile_tags;
                        if (!empty($flat) && is_array($flat)) {
                            $estado_options = ['solteiro','namorando','casado','enrolado','separado','viuvo','viúva','viúvo'];
                            $musica_options = ['rock','sertanejo','pagode','pop','eletronica','funk','mpb','forro'];
                            $bebida_options = ['cerveja','vodka','whisky','vinho','tequila','rum','cachaça','cachaca'];
                            foreach ($flat as $t) {
                                $low = mb_strtolower($t, 'UTF-8');
                                $low_s = preg_replace('/[^a-z0-9áéíóúãõçâêôü ]/iu','',$low);
                                if (in_array($low_s, $estado_options)) { $profile_tags_structured['estado_civil'] = $low_s; continue; }
                                if (in_array($low_s, $musica_options)) { $profile_tags_structured['estilo_musica'][] = $low_s; continue; }
                                if (in_array($low_s, $bebida_options)) { $profile_tags_structured['bebida'][] = $low_s; continue; }
                                // try to parse city tag like "City: São Paulo"
                                if (preg_match('/^city[:\-]\s*(.+)$/iu', $t, $m)) {
                                    $profile_tags_structured['city'] = sanitize_text_field($m[1]);
                                } else {
                                    $profile_tags_structured['custom'][] = $t;
                                }
                            }
                        }
                    }

                    // Get cover image and avatar shape
                    $cover_image = get_user_meta($profile_id, 'cover_image', true);
                    $avatar_shape = get_user_meta($profile_id, 'avatar_shape', true);
                    $avatar_shape = $avatar_shape ? $avatar_shape : 'circle';
                    ?>
                    <section class="profile-page">
                        <div class="profile-header">
                            <!-- Cover Image -->
                            <div class="profile-cover-container">
                                <?php if ($cover_image) : ?>
                                    <img src="<?php echo esc_url($cover_image); ?>" alt="Cover" class="cover-image">
                                <?php endif; ?>
                                
                                <?php if ($is_owner) : ?>
                                    <button data-open-cover-modal class="cover-edit-btn" title="Editar foto de capa">
                                        <svg viewBox="0 0 16 16" width="20px" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M8.29289 3.70711L1 11V15H5L12.2929 7.70711L8.29289 3.70711Z" fill="#b177fb"></path> <path d="M9.70711 2.29289L13.7071 6.29289L15.1716 4.82843C15.702 4.29799 16 3.57857 16 2.82843C16 1.26633 14.7337 0 13.1716 0C12.4214 0 11.702 0.297995 11.1716 0.828428L9.70711 2.29289Z" fill="#b177fb"></path></g></svg>
                                    </button>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Avatar Container -->
                            <div class="profile-avatar-container">
                                <div>
                                    <?php if ($is_owner) : ?>
                                        <button data-open-avatar-modal class="change-avatar" id="change-avatar">
                                            <div class="pencil_icon">
                                                <svg viewBox="0 0 16 16" width="16px" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M8.29289 3.70711L1 11V15H5L12.2929 7.70711L8.29289 3.70711Z" fill="#b177fb"></path> <path d="M9.70711 2.29289L13.7071 6.29289L15.1716 4.82843C15.702 4.29799 16 3.57857 16 2.82843C16 1.26633 14.7337 0 13.1716 0C12.4214 0 11.702 0.297995 11.1716 0.828428L9.70711 2.29289Z" fill="#b177fb"></path></g></svg>
                                            </div>
                                    <?php else: ?>
                                        <div class="change-avatar">
                                    <?php endif; ?>
                                    
                                        <div class="user-avatar shape-<?php echo esc_attr($avatar_shape); ?>">
                                            <?php echo get_avatar($profile_id, 96); ?>
                                        </div>

                                    <?php if ($is_owner) : ?>
                                        </button>
                                    <?php else: ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="profile-meta">
                <div style="display:flex;align-items:center;gap:0.6rem;">
                                    <h2 style="margin:0;"><?php echo esc_html($profile_user->display_name ? $profile_user->display_name : $profile_user->user_login); ?></h2>
                                    <div id="ph-profile-badges" style="display:flex;gap:0.4rem;align-items:center;"></div>
                </div>
                                <p style="color:#666;margin:0.25rem 0;">@<?php echo esc_html($profile_user->user_login); ?></p>
                <div style="display:flex;gap:1rem;align-items:center;margin-top:0.5rem;">
                                    <div><strong id="ph-followers-count-top"><?php echo count($followers); ?></strong> seguidores</div>
                                    <div><strong id="ph-following-count-top"><?php echo count($following); ?></strong> seguindo</div>
                    </div>

                                <div id="ph-profile-description" style="margin-top:0.75rem;color:#444;">
                    <?php if (!empty($profile_description)) { echo wpautop(esc_html($profile_description)); } else { echo '<em>Sem descrição.</em>'; } ?>
                </div>

                <div id="ph-profile-tags-container" style="margin-top:0.5rem;">
                                    <div style="font-size:0.9rem;color:#666;margin-bottom:0.35rem;">Tags</div>
                    <div id="ph-profile-tags" class="ph-profile-tags">
                        <?php
                        if (!empty($profile_tags_structured['estado_civil'])) {
                                            echo '<span class="ph-profile-tag" data-tag="' . esc_attr($profile_tags_structured['estado_civil']) . '">' . esc_html($profile_tags_structured['estado_civil']) . '</span>';
                        }
                        if (!empty($profile_tags_structured['city'])) {
                                            echo '<span class="ph-profile-tag ph-profile-city" data-city="' . esc_attr($profile_tags_structured['city']) . '">' . esc_html($profile_tags_structured['city']) . '</span>';
                        }
                        foreach (array_merge((array)$profile_tags_structured['estilo_musica'], (array)$profile_tags_structured['bebida']) as $t) {
                                            echo '<span class="ph-profile-tag" data-tag="' . esc_attr($t) . '">' . esc_html($t) . '</span>';
                        }
                        foreach ($profile_tags_structured['custom'] as $t) {
                                            echo '<span class="ph-profile-tag" data-tag="' . esc_attr($t) . '">' . esc_html($t) . '</span>';
                        }
                        ?>
                    </div>
                </div>

                <?php if ($is_owner): ?>
                    <div style="margin-top:0.75rem;">
                        <button id="ph-edit-profile-btn" class="follow-btn">Editar perfil</button>
                    </div>
                    <div id="ph-profile-edit-form" class="ph-profile-edit" style="display:none;margin-top:0.75rem;background:#fff;padding:0.75rem;border-radius:0.5rem;border:1px solid #eee;">
                        <label for="ph-profile-description-input" style="font-weight:700;">Descrição</label>
                        <textarea id="ph-profile-description-input"><?php echo esc_textarea($profile_description); ?></textarea>

                        <div style="margin-top:0.6rem;">
                            <label style="font-weight:700;">Estado civil</label>
                            <select id="ph-estado-select" style="width:100%;padding:0.5rem;margin-top:0.25rem;border:1px solid #e0e0e0;border-radius:0.4rem;">
                                <option value="">-- Selecionar --</option>
                                <option value="solteiro">Solteiro(a)</option>
                                <option value="namorando">Namorando</option>
                                <option value="casado">Casado(a)</option>
                                <option value="enrolado">Enrolado</option>
                                <option value="separado">Separado(a)</option>
                                <option value="viuvo">Viúvo(a)</option>
                            </select>
                        </div>

                        <div style="margin-top:0.6rem;">
                            <label style="font-weight:700;">Cidade</label>
                            <input id="ph-city-input" type="text" placeholder="Sua cidade" style="width:100%;padding:0.5rem;margin-top:0.25rem;border:1px solid #e0e0e0;border-radius:0.4rem;" value="<?php echo esc_attr($profile_tags_structured['city'] ?? ''); ?>" />
                        </div>

                        <div style="margin-top:0.6rem;">
                            <label style="font-weight:700;">Estilo(s) de música</label>
                            <select id="ph-musica-select" multiple size="4" style="width:100%;padding:0.5rem;margin-top:0.25rem;border:1px solid #e0e0e0;border-radius:0.4rem;">
                                <option value="rock">Rock</option>
                                <option value="sertanejo">Sertanejo</option>
                                <option value="pagode">Pagode</option>
                                <option value="pop">Pop</option>
                                <option value="eletronica">Eletrônica</option>
                                <option value="funk">Funk</option>
                                <option value="mpb">MPB</option>
                                <option value="forro">Forró</option>
                            </select>
                        </div>

                        <div style="margin-top:0.6rem;">
                            <label style="font-weight:700;">Bebida que gosta</label>
                            <select id="ph-bebida-select" multiple size="4" style="width:100%;padding:0.5rem;margin-top:0.25rem;border:1px solid #e0e0e0;border-radius:0.4rem;">
                                <option value="cerveja">Cerveja</option>
                                <option value="vodka">Vodka</option>
                                <option value="whisky">Whisky</option>
                                <option value="vinho">Vinho</option>
                                <option value="tequila">Tequila</option>
                                <option value="rum">Rum</option>
                                <option value="cachaca">Cachaça</option>
                            </select>
                        </div>

                        <div class="ph-tags-input" style="margin-top:0.5rem; position:relative;">
                            <input id="ph-profile-tag-input" placeholder="Adicionar tag personalizada (pode conter emoji)" />
                            <button id="ph-emoji-btn" class="follow-btn" type="button" title="Inserir emoji">😊</button>
                            <div id="ph-emoji-picker" class="ph-emoji-picker" style="display:none;" aria-hidden="true"></div>
                            <button id="ph-add-tag-btn" class="follow-btn" type="button">Adicionar</button>
                        </div>
                        <div id="ph-profile-edit-tags" class="ph-profile-tags" style="margin-top:0.5rem;">
                            <?php foreach ($profile_tags_structured['custom'] as $t) { echo '<span class="ph-profile-tag" data-tag="' . esc_attr($t) . '">' . esc_html($t) . '<button class="ph-tag-remove" data-tag="' . esc_attr($t) . '" style="margin-left:6px;border:none;background:transparent;cursor:pointer;">&times;</button></span>'; } ?>
                        </div>
                        <div style="margin-top:0.5rem;display:flex;gap:0.5rem;">
                            <button id="ph-save-profile-btn" class="follow-btn">Salvar</button>
                            <button id="ph-cancel-profile-btn" class="follow-btn">Cancelar</button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div>
                <?php if (is_user_logged_in() && !$is_owner): ?>
                    <button id="ph-follow-btn" class="follow-btn <?php echo $is_following ? 'following' : ''; ?>">
                        <?php echo $is_following ? 'Seguindo' : 'Seguir'; ?>
                    </button>
                <?php endif; ?>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 320px;gap:1rem;margin-top:1rem;">
        <div>
            <?php if ($is_owner): ?>
                <div style="background:#fff;padding:1rem;border-radius:0.75rem;margin-bottom:1rem;">
                                        <h3>Mensagem no Meu Perfil</h3>
                    <form id="profile-post-form">
                                            <textarea id="profile-post-content" rows="4" style="width:100%;border-radius:0.5rem;border:1px solid #e0e0e0;" placeholder="Adicione uma mensagem no seu perfil..."></textarea>
                                            <button type="submit" class="btn-group-action btn-join" style="margin-top:0.5rem;">Adicionar Mensagem</button>
                        <div id="profile-post-msg" style="margin-top:0.5rem;display:none;"></div>
                    </form>
                </div>
            <?php endif; ?>

            <div class="posts-list">
                                    <h3>Mensagens</h3>
                                    <div id="profile-comments-list">
                <?php
                                        if (function_exists('ph_get_profile_comments')) {
                                            $profile_comments = ph_get_profile_comments($profile_id, 20);
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
                                                    
                                                    // Delete button (only for owner or admin)
                                                    $can_delete = ($current_user_id == intval($comment->user_id)) || current_user_can('administrator');
                                                    if ($can_delete) {
                                                        echo '<div style="margin-top:0.5rem;">';
                                                        echo '<button class="ph-delete-profile-comment-btn" data-comment-id="' . esc_attr($comment->comment_ID) . '" style="background:#f8d7da;color:#721c24;border:none;padding:0.5rem 0.75rem;border-radius:0.5rem;cursor:pointer;font-size:0.9rem;">Excluir</button>';
                                                        echo '</div>';
                                                    }
                                                    
                                                    echo '</div>';
                                                }
                } else {
                                                echo '<p>Nenhuma mensagem ainda.</p>';
                                            }
                                        } else {
                                            echo '<p>Função não disponível.</p>';
                }
                ?>
                                    </div>
            </div>
        </div>

        <aside>
            <div style="background:#fff;padding:1rem;border-radius:0.75rem;">
                                    <h4>Seguidores (<span id="ph-followers-count-side"><?php echo count($followers); ?></span>)</h4>
                <div class="small-list" id="ph-followers-list">
                                        <?php foreach ($followers as $fid) {
                                            $u = get_user_by('id', $fid);
                                            if (!$u) continue;
                                            $link = get_author_posts_url($u->ID);
                                            echo '<a href="' . esc_url($link) . '">' . get_avatar($u->ID, 32) . '<span style="font-size:0.9rem;">' . esc_html($u->display_name) . '</span></a>';
                    } ?>
                </div>
            </div>

            <div style="background:#fff;padding:1rem;border-radius:0.75rem;margin-top:0.75rem;">
                <h4>Seguindo (<span id="ph-following-count-side"><?php echo count($following); ?></span>)</h4>
                <div class="small-list" id="ph-following-list">
                                        <?php foreach ($following as $fid) {
                                            $u = get_user_by('id', $fid);
                                            if (!$u) continue;
                                            $link = get_author_posts_url($u->ID);
                                            echo '<a href="' . esc_url($link) . '">' . get_avatar($u->ID, 32) . '<span style="font-size:0.9rem;">' . esc_html($u->display_name) . '</span></a>';
                    } ?>
                </div>
            </div>
        </aside>
    </div>
                    </section>

<!-- Modal para Foto de Capa -->
<dialog class="upload_profile_image_popup" data-cover-modal id="cover-upload-modal">
    <div class="upload_profile_image_popup_align">
        <button class="close_button" data-close-cover-modal>×</button>
        
        <?php if (isset($_GET['cover_updated']) && $_GET['cover_updated'] == '1') : ?>
            <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid #c3e6cb;">
                Foto de capa atualizada com sucesso!
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['cover_error']) && $_GET['cover_error'] == '1') : ?>
            <div style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid #f5c6cb;">
                Erro ao atualizar foto de capa. Por favor, tente novamente.
            </div>
        <?php endif; ?>
        
        <!-- Cover Picture Upload Section -->
        <div class="modal-section">
            <form class="upload_profile_form" id="cover-upload-form" method="post" enctype="multipart/form-data" action="<?php echo admin_url('admin-post.php'); ?>">
                <?php wp_nonce_field('upload_cover_image', 'cover_image_nonce'); ?>
                <input type="hidden" name="action" value="upload_cover_image">
                <label for="cover_image" class="custom-file-label">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="currentColor"/>
                        <path d="M12.0002 14.5C6.99016 14.5 2.91016 17.86 2.91016 22C2.91016 22.28 3.13016 22.5 3.41016 22.5H20.5902C20.8702 22.5 21.0902 22.28 21.0902 22C21.0902 17.86 17.0102 14.5 12.0002 14.5Z" fill="currentColor"/>
                    </svg>
                    <span class="custom-file-label-text">Escolher imagem</span>
                </label>
                <input type="file" id="cover_image" name="cover_image" accept="image/*,.gif" hidden>
                <span id="cover-file-name">Nenhum arquivo selecionado</span>
                <div id="cover-upload-error" style="color: #c92048; margin-top: 0.5rem; display: none;"></div>
                <input class="purple_btn" type="submit" name="upload_cover" value="Salvar Foto de Capa">
            </form>
        </div>
    </div>
</dialog>

<!-- Modal para Foto de Perfil e Forma do Avatar -->
<dialog class="upload_profile_image_popup" data-avatar-modal id="avatar-upload-modal">
    <div class="upload_profile_image_popup_align">
        <button class="close_button" data-close-avatar-modal>×</button>
        
        <?php if (isset($_GET['avatar_updated']) && $_GET['avatar_updated'] == '1') : ?>
            <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid #c3e6cb;">
                Foto de perfil atualizada com sucesso!
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['avatar_error']) && $_GET['avatar_error'] == '1') : ?>
            <div style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid #f5c6cb;">
                Erro ao atualizar foto de perfil. Por favor, tente novamente.
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['shape_updated']) && $_GET['shape_updated'] == '1') : ?>
            <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid #c3e6cb;">
                Forma do avatar atualizada com sucesso!
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['shape_error']) && $_GET['shape_error'] == '1') : ?>
            <div style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid #f5c6cb;">
                Erro ao atualizar forma do avatar. Por favor, tente novamente.
            </div>
        <?php endif; ?>
        
        <!-- Tab System -->
        <div class="tab-container">
            <div class="tab-buttons">
                <button class="tab-button active" type="button" id="show-avatar-upload-btn" data-tab="avatar-upload">Alterar foto de Perfil</button>
                <button class="tab-button" type="button" id="show-shape-selection-btn" data-tab="avatar-shape">Alterar Forma da foto de Perfil</button>
            </div>
            
            <!-- Photo Upload Tab Content -->
            <div class="tab-content active" id="avatar-upload-section">
                <div class="modal-section">
                    <form class="upload_profile_form" id="avatar-upload-form" method="post" enctype="multipart/form-data" action="<?php echo admin_url('admin-post.php'); ?>">
                        <?php wp_nonce_field('upload_avatar_image', 'avatar_image_nonce'); ?>
                        <input type="hidden" name="action" value="upload_avatar_image">
                        <label for="user_avatar" class="custom-file-label" id="avatar-label">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" id="avatar-icon">
                                <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="currentColor"/>
                                <path d="M12.0002 14.5C6.99016 14.5 2.91016 17.86 2.91016 22C2.91016 22.28 3.13016 22.5 3.41016 22.5H20.5902C20.8702 22.5 21.0902 22.28 21.0902 22C21.0902 17.86 17.0102 14.5 12.0002 14.5Z" fill="currentColor"/>
                            </svg>
                            <img id="avatar-preview" src="" alt="Preview">
                            <span class="custom-file-label-text">Escolher imagem</span>
                        </label>
                        <input type="file" id="user_avatar" name="user_avatar" accept="image/*,.gif" hidden>
                        
                        <!-- Crop Container -->
                        <div class="image-crop-container" id="avatar-crop-container">
                            <img id="avatar-crop-image" src="" alt="Crop" style="max-width: 100%; display: block;">
                            <div class="crop-controls">
                                <div class="crop-buttons">
                                    <button type="button" class="crop-btn confirm" id="avatar-crop-confirm">Confirmar</button>
                                    <button type="button" class="crop-btn cancel" id="avatar-crop-cancel">Cancelar</button>
                                </div>
                            </div>
                        </div>
                        
                        <span id="file-name">Nenhum arquivo selecionado</span>
                        <div id="upload-error" style="color: #c92048; margin-top: 0.5rem; display: none;"></div>
                        <input class="purple_btn" type="submit" name="upload_avatar" value="Salvar Foto" id="avatar-submit-btn" style="display: none;">
                    </form>
                </div>
            </div>
            
            <!-- Avatar Shape Selection Tab Content -->
            <div class="tab-content" id="avatar-shape-section">
                <div class="modal-section">
                    <div class="avatar-shape-selection">
                        <div class="shape-options" id="modal-shape-options">
                            <?php 
                            $current_shape = get_user_meta($profile_id, 'avatar_shape', true);
                            $current_shape = $current_shape ? $current_shape : 'circle';
                            $shapes = [
                                'circle' => 'Círculo',
                                'square' => 'Quadrado',
                                'star' => 'Estrela',
                                'heart' => 'Coração',
                                'diamond' => 'Diamante',
                                'hexagon' => 'Hexágono',
                                'octagon' => 'Octógono',
                                'triangle' => 'Triângulo',
                                'pentagon' => 'Pentágono',
                                'squircle' => 'Arredondado',
                                'blob' => 'Blob',
                                'badge' => 'Emblema',
                                'wavy' => 'Ondulado'
                            ];
                            foreach ($shapes as $shape_key => $shape_label) :
                            ?>
                            <div class="shape-option <?php echo $current_shape === $shape_key ? 'active' : ''; ?>" 
                                 data-shape="<?php echo esc_attr($shape_key); ?>"
                                 title="<?php echo esc_attr($shape_label); ?>">
                                <div class="shape-preview shape-<?php echo esc_attr($shape_key); ?>"></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="purple_btn" type="button" id="save-shape-btn" data-nonce="<?php echo wp_create_nonce('avatar_shape_nonce'); ?>">Salvar Forma</button>
                        <div id="shape-save-message" style="display: none; padding: 0.75rem; border-radius: 0.5rem; margin-top: 0.5rem;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</dialog>

<!-- Cropper.js JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
<script>
    // Cover Modal Controls
    const openCoverButtons = document.querySelectorAll("[data-open-cover-modal]");
    const closeCoverButton = document.querySelector("[data-close-cover-modal]");
    const coverModal = document.querySelector("[data-cover-modal]");

    if (openCoverButtons && coverModal) {
        openCoverButtons.forEach(button => {
            button.addEventListener("click", () => {
                coverModal.showModal();
            });
        });
    }
    
    if (closeCoverButton && coverModal) {
        closeCoverButton.addEventListener("click", ()=>{
            coverModal.close();
        });
    }
    
    // Avatar Modal Controls
    const openAvatarButtons = document.querySelectorAll("[data-open-avatar-modal]");
    const closeAvatarButton = document.querySelector("[data-close-avatar-modal]");
    const avatarModal = document.querySelector("[data-avatar-modal]");

    if (openAvatarButtons && avatarModal) {
        openAvatarButtons.forEach(button => {
            button.addEventListener("click", () => {
                avatarModal.showModal();
            });
        });
    }
    
    if (closeAvatarButton && avatarModal) {
        closeAvatarButton.addEventListener("click", ()=>{
            avatarModal.close();
        });
    }
    
    // Tab System Functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    function switchTab(targetTab) {
        tabButtons.forEach(btn => btn.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));
        
        const clickedButton = document.querySelector(`[data-tab="${targetTab}"]`);
        const targetContent = document.getElementById(targetTab + '-section');
        
        if (clickedButton) clickedButton.classList.add('active');
        if (targetContent) targetContent.classList.add('active');
    }
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            switchTab(targetTab);
        });
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const userAvatarInput = document.getElementById('user_avatar');
    const avatarUploadForm = document.getElementById('avatar-upload-form');
    const fileNameSpan = document.getElementById('file-name');
    const errorMessage = document.getElementById('upload-error');
    
    // Avatar Image Crop and Preview using Cropper.js
    if (userAvatarInput) {
        let cropper = null;
        
        const avatarLabel = document.getElementById('avatar-label');
        const avatarIcon = document.getElementById('avatar-icon');
        const avatarPreview = document.getElementById('avatar-preview');
        const avatarCropContainer = document.getElementById('avatar-crop-container');
        const avatarCropImage = document.getElementById('avatar-crop-image');
        const avatarCropConfirm = document.getElementById('avatar-crop-confirm');
        const avatarCropCancel = document.getElementById('avatar-crop-cancel');
        const avatarSubmitBtn = document.getElementById('avatar-submit-btn');
        
        if (userAvatarInput && fileNameSpan) {
            userAvatarInput.addEventListener('change', function() {
                if (!this.files || !this.files[0]) return;
                
                const file = this.files[0];
                const isGif = file.type === 'image/gif';
                
                if (errorMessage) {
                    errorMessage.style.display = 'none';
                    errorMessage.textContent = '';
                }
                
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    if (isGif) {
                        avatarPreview.src = e.target.result;
                        avatarLabel.classList.add('has-image');
                        avatarIcon.style.display = 'none';
                        avatarCropContainer.classList.remove('active');
                        avatarSubmitBtn.style.display = 'block';
                        fileNameSpan.textContent = file.name;
                        return;
                    }
                    
                    avatarCropImage.src = e.target.result;
                    avatarCropContainer.classList.add('active');
                    avatarSubmitBtn.style.display = 'none';
                    fileNameSpan.textContent = file.name;
                    
                    cropper = new Cropper(avatarCropImage, {
                        aspectRatio: 1,
                        viewMode: 1,
                        dragMode: 'move',
                        autoCropArea: 0.8,
                        restore: false,
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleable: false,
                        zoomable: true,
                        scalable: true,
                        rotatable: false,
                        minCropBoxWidth: 200,
                        minCropBoxHeight: 200,
                        ready: function() {
                            const containerData = cropper.getContainerData();
                            const cropBoxData = cropper.getCropBoxData();
                            cropper.setCropBoxData({
                                left: (containerData.width - cropBoxData.width) / 2,
                                top: (containerData.height - cropBoxData.height) / 2
                            });
                        }
                    });
                };
                reader.readAsDataURL(file);
            });
        }
        
        if (avatarCropConfirm) {
            avatarCropConfirm.addEventListener('click', function() {
                if (!cropper) return;
                
                let canvas = cropper.getCroppedCanvas({
                    width: 400,
                    height: 400,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });
                
                if (!canvas) {
                    if (errorMessage) {
                        errorMessage.style.display = 'block';
                        errorMessage.textContent = 'Erro ao processar imagem. Tente novamente.';
                    }
                    return;
                }
                
                const finalCanvas = document.createElement('canvas');
                finalCanvas.width = 400;
                finalCanvas.height = 400;
                const finalCtx = finalCanvas.getContext('2d');
                
                finalCtx.fillStyle = '#ffffff';
                finalCtx.fillRect(0, 0, 400, 400);
                finalCtx.drawImage(canvas, 0, 0);
                
                finalCanvas.toBlob(function(blob) {
                    if (!blob) {
                        if (errorMessage) {
                            errorMessage.style.display = 'block';
                            errorMessage.textContent = 'Erro ao processar imagem. Tente novamente.';
                        }
                        return;
                    }
                    
                    const file = new File([blob], 'avatar.jpg', { type: 'image/jpeg' });
                    
                    try {
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        userAvatarInput.files = dataTransfer.files;
                    } catch(e) {
                        if (errorMessage) {
                            errorMessage.style.display = 'block';
                            errorMessage.textContent = 'Erro ao processar arquivo. Tente novamente.';
                        }
                        return;
                    }
                    
                    const previewCanvas = document.createElement('canvas');
                    previewCanvas.width = 400;
                    previewCanvas.height = 400;
                    const previewCtx = previewCanvas.getContext('2d');
                    
                    previewCtx.beginPath();
                    previewCtx.arc(200, 200, 200, 0, 2 * Math.PI);
                    previewCtx.clip();
                    previewCtx.drawImage(finalCanvas, 0, 0);
                    
                    const previewDataUrl = previewCanvas.toDataURL('image/jpeg', 0.95);
                    avatarPreview.src = previewDataUrl;
                    avatarLabel.classList.add('has-image');
                    if (avatarIcon) avatarIcon.style.display = 'none';
                    
                    cropper.destroy();
                    cropper = null;
                    avatarCropContainer.classList.remove('active');
                    avatarSubmitBtn.style.display = 'block';
                    fileNameSpan.textContent = 'Imagem selecionada';
                    
                    if (errorMessage) {
                        errorMessage.style.display = 'none';
                        errorMessage.textContent = '';
                    }
                }, 'image/jpeg', 0.95);
            });
        }
        
        if (avatarCropCancel) {
            avatarCropCancel.addEventListener('click', function() {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                avatarCropContainer.classList.remove('active');
                userAvatarInput.value = '';
                if (fileNameSpan) fileNameSpan.textContent = 'Nenhum arquivo selecionado';
                avatarLabel.classList.remove('has-image');
                if (avatarIcon) avatarIcon.style.display = 'block';
                avatarPreview.src = '';
            });
        }
    }
    
    if (avatarUploadForm) {
        avatarUploadForm.addEventListener('submit', function(event) {
            const fileInput = document.getElementById('user_avatar');
            const errorMessage = document.getElementById('upload-error');

            if (!fileInput || !fileInput.files.length) {
                event.preventDefault();
                if (errorMessage) {
                    errorMessage.style.display = 'block';
                    errorMessage.textContent = "Por favor selecione uma Imagem";
                }
            }
        });
    }
    
    // Cover picture upload handling
    const coverImageInput = document.getElementById('cover_image');
    const coverUploadForm = document.getElementById('cover-upload-form');
    const coverFileNameSpan = document.getElementById('cover-file-name');
    const coverErrorMessage = document.getElementById('cover-upload-error');
    
    if (coverImageInput && coverFileNameSpan) {
        coverImageInput.addEventListener('change', function() {
            let fileName = this.files.length > 0 ? this.files[0].name : "Nenhum arquivo selecionado";
            coverFileNameSpan.textContent = fileName;
            if (coverErrorMessage) {
                coverErrorMessage.style.display = 'none';
                coverErrorMessage.textContent = '';
            }
        });
    }
    
    if (coverUploadForm) {
        coverUploadForm.addEventListener('submit', function(event) {
            const fileInput = document.getElementById('cover_image');
            const errorMessage = document.getElementById('cover-upload-error');

            if (!fileInput || !fileInput.files.length) {
                event.preventDefault();
                if (errorMessage) {
                    errorMessage.style.display = 'block';
                    errorMessage.textContent = "Por favor selecione uma Imagem";
                }
            }
        });
    }
    
    // Avatar shape selection functionality
    const modalShapeOptions = document.querySelectorAll('#modal-shape-options .shape-option');
    const saveShapeBtn = document.getElementById('save-shape-btn');
    const shapeSaveMessage = document.getElementById('shape-save-message');
    const userAvatar = document.querySelector('.user-avatar');
    let selectedShape = '<?php echo esc_js($avatar_shape); ?>';
    let originalShape = '<?php echo esc_js($avatar_shape); ?>';
    
    modalShapeOptions.forEach(option => {
        option.addEventListener('click', function() {
            modalShapeOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            selectedShape = this.getAttribute('data-shape');
        });
    });
    
    if (saveShapeBtn) {
        saveShapeBtn.addEventListener('click', function() {
            if (selectedShape === originalShape) {
                if (shapeSaveMessage) {
                    shapeSaveMessage.style.display = 'block';
                    shapeSaveMessage.style.background = '#fff3cd';
                    shapeSaveMessage.style.color = '#856404';
                    shapeSaveMessage.textContent = 'Esta forma já está selecionada.';
                }
                return;
            }
            
            const btn = this;
            const nonce = btn.getAttribute('data-nonce');
            btn.disabled = true;
            btn.textContent = 'Salvando...';
            
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({
                    action: 'save_avatar_shape',
                    nonce: nonce,
                    shape: selectedShape
                })
            })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                btn.textContent = 'Salvar Forma';
                
                if (data.success) {
                    if (shapeSaveMessage) {
                        shapeSaveMessage.style.display = 'block';
                        shapeSaveMessage.style.background = '#d4edda';
                        shapeSaveMessage.style.color = '#155724';
                        shapeSaveMessage.textContent = 'Forma atualizada com sucesso!';
                    }
                    
                    if (userAvatar) {
                        userAvatar.className = 'user-avatar shape-' + selectedShape;
                    }
                    
                    originalShape = selectedShape;
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    if (shapeSaveMessage) {
                        shapeSaveMessage.style.display = 'block';
                        shapeSaveMessage.style.background = '#f8d7da';
                        shapeSaveMessage.style.color = '#721c24';
                        shapeSaveMessage.textContent = data.data || 'Erro ao atualizar forma.';
                    }
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.textContent = 'Salvar Forma';
                if (shapeSaveMessage) {
                    shapeSaveMessage.style.display = 'block';
                    shapeSaveMessage.style.background = '#f8d7da';
                    shapeSaveMessage.style.color = '#721c24';
                    shapeSaveMessage.textContent = 'Erro de rede. Tente novamente.';
                }
            });
        });
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    
    // --- LÓGICA DO BOTÃO SEGUIR ---
    const followBtn = document.getElementById('ph-follow-btn');
    if (followBtn) {
        followBtn.addEventListener('click', function(){
            const following = this.classList.contains('following');
            const action = following ? 'unfollow_user' : 'follow_user'; // Isso bate com seus add_action('wp_ajax_follow_user')
            const btn = this;
            
            btn.disabled = true;
            
            // Debug: Ver o que está sendo enviado
            console.log('Enviando:', { action: action, target_id: '<?php echo esc_js($profile_id); ?>' });

                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                headers: {'Content-Type':'application/x-www-form-urlencoded'},
                        body: new URLSearchParams({
                    action: action, 
                    nonce: '<?php echo wp_create_nonce('social_nonce'); ?>', 
                    target_id: '<?php echo esc_js($profile_id); ?>' 
                })
            })
            .then(r => {
                // Se o servidor der erro 500, pegamos o texto do erro
                if (!r.ok) throw new Error('Erro HTTP: ' + r.status);
                return r.text(); // Pegamos como texto primeiro para debug
            })
            .then(text => {
                try {
                    const data = JSON.parse(text); // Tenta converter o JSON
                    
                    btn.disabled = false;
                    
                    if (data.success) {
                        // Atualiza botão
                        if (data.data.following) { 
                            btn.classList.add('following'); 
                            btn.textContent = 'Seguindo'; 
                        } else { 
                            btn.classList.remove('following'); 
                            btn.textContent = 'Seguir'; 
                        }
                        
                        // Atualiza contadores e listas (Seu PHP retorna tudo isso, está perfeito)
                        if (typeof data.data.followers_count !== 'undefined') {
                            const v = data.data.followers_count;
                            const top = document.getElementById('ph-followers-count-top');
                            const side = document.getElementById('ph-followers-count-side');
                            if (top) top.textContent = v; 
                            if (side) side.textContent = v;
                        }
                        if (typeof data.data.following_count !== 'undefined') {
                            const v = data.data.following_count;
                            const top = document.getElementById('ph-following-count-top');
                            const side = document.getElementById('ph-following-count-side');
                            if (top) top.textContent = v; 
                            if (side) side.textContent = v;
                        }
                        if (typeof data.data.followers_html !== 'undefined') {
                            const list = document.getElementById('ph-followers-list'); 
                            if (list) list.innerHTML = data.data.followers_html;
                        }
                    } else {
                        console.error('Erro Lógico PHP:', data);
                        alert(data.data || 'Erro ao atualizar');
                    }
                } catch (e) {
                    console.error('Erro de JSON:', e, text);
                    btn.disabled = false;
                    alert('Erro no servidor (Resposta inválida). Veja o console.');
                }
            })
            .catch(err => { 
                console.error('Erro de Rede/Fetch:', err);
                btn.disabled = false; 
                alert('Erro de conexão: ' + err.message); 
            });
        });
    }

    // Profile: create comment on profile wall
    const profileForm = document.getElementById('profile-post-form');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e){
            e.preventDefault();
            const content = document.getElementById('profile-post-content').value;
            const msg = document.getElementById('profile-post-msg');
            const submitBtn = profileForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.textContent;
            
            if (msg) msg.style.display = 'none';
            
            if (!content.trim()) {
                if (msg) {
                    msg.style.display = 'block';
                    msg.style.color = '#721c24';
                    msg.textContent = 'Por favor, escreva uma mensagem.';
                    }
                    return;
                }
                
            submitBtn.disabled = true;
            submitBtn.textContent = 'Adicionando...';
            
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {'Content-Type':'application/x-www-form-urlencoded'},
                body: new URLSearchParams({ 
                    action: 'create_profile_comment', 
                    nonce: '<?php echo wp_create_nonce('social_nonce'); ?>', 
                    content: content 
                })
            }).then(r=>r.json()).then(data=>{
                submitBtn.disabled = false;
                submitBtn.textContent = originalBtnText;
                
                if (data.success) {
                    if (msg) {
                        msg.style.display = 'block';
                        msg.style.color = '#155724';
                        msg.textContent = 'Mensagem adicionada ao seu perfil.';
                    }
                    document.getElementById('profile-post-content').value = '';
                    
                    // Add the new comment to the list
                    if (data.data && data.data.comment_html) {
                        const list = document.getElementById('profile-comments-list');
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
                    if (msg) {
                        msg.style.display = 'block';
                        msg.style.color = '#721c24';
                        msg.textContent = data.data || 'Erro ao adicionar mensagem.';
                    }
                }
            }).catch(()=>{
                submitBtn.disabled = false;
                submitBtn.textContent = originalBtnText;
                if (msg) {
                    msg.style.display = 'block';
                    msg.style.color = '#721c24';
                    msg.textContent = 'Erro de rede.';
                }
            });
        });
    }
    
    // Delete profile comment handler
    const commentsList = document.getElementById('profile-comments-list');
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

    // Structured profile tags edit
    const phStructured = <?php echo json_encode($profile_tags_structured); ?> || { estado_civil:'', estilo_musica:[], bebida:[], custom:[], city: '' };
    const editBtn = document.getElementById('ph-edit-profile-btn');
    const editForm = document.getElementById('ph-profile-edit-form');
    const descInput = document.getElementById('ph-profile-description-input');
    const tagInput = document.getElementById('ph-profile-tag-input');
    const addTagBtn = document.getElementById('ph-add-tag-btn');
    const emojiBtn = document.getElementById('ph-emoji-btn');
    const emojiPicker = document.getElementById('ph-emoji-picker');
    const editTagsContainer = document.getElementById('ph-profile-edit-tags');
    const viewTagsContainer = document.getElementById('ph-profile-tags');
    const descView = document.getElementById('ph-profile-description');

    function renderEditCustom(){ if (!editTagsContainer) return; editTagsContainer.innerHTML=''; (phStructured.custom||[]).forEach(t=>{ const span=document.createElement('span'); span.className='ph-profile-tag'; span.setAttribute('data-tag', t); span.innerHTML = t + ' <button class="ph-tag-remove" data-tag="'+t+'" style="margin-left:6px;border:none;background:transparent;cursor:pointer;">&times;</button>'; editTagsContainer.appendChild(span); }); }

    function populateSelects(){ const estado=document.getElementById('ph-estado-select'); const musica=document.getElementById('ph-musica-select'); const bebida=document.getElementById('ph-bebida-select'); const cityInput = document.getElementById('ph-city-input'); if (estado) estado.value = phStructured.estado_civil || ''; if (musica && phStructured.estilo_musica) { for (const opt of musica.options) opt.selected = phStructured.estilo_musica.indexOf(opt.value)!==-1; } if (bebida && phStructured.bebida) { for (const opt of bebida.options) opt.selected = phStructured.bebida.indexOf(opt.value)!==-1; } if (cityInput) cityInput.value = phStructured.city || ''; }

    // Emoji picker (lightweight inline)
    const _emojis = ['😀','😃','😄','😁','😆','😅','😂','🤣','😊','😉','😍','😘','😎','🤩','🤔','🤨','😇','🥳','😉','😜','🤪','👍','👎','👏','🙏','💃','🕺','🎵','🎸','🎧','🍺','🍷','🍸','☕','🌆','🏙️','🏡'];
    function buildEmojiPicker(){ if (!emojiPicker) return; emojiPicker.innerHTML=''; _emojis.forEach(e=>{ const b=document.createElement('button'); b.type='button'; b.className='ph-emoji-btn'; b.textContent = e; b.addEventListener('click', function(ev){ ev.stopPropagation(); if (!tagInput) return; insertAtCaret(tagInput, e); tagInput.focus(); emojiPicker.style.display='none'; }); emojiPicker.appendChild(b); }); }
    function insertAtCaret(input, text) { try { const start = input.selectionStart || 0; const end = input.selectionEnd || 0; const v = input.value; input.value = v.slice(0,start)+text+v.slice(end); input.selectionStart = input.selectionEnd = start + text.length; } catch(err) { input.value = input.value + text; } }
    if (emojiBtn) {
        emojiBtn.addEventListener('click', function(e){ e.preventDefault(); e.stopPropagation(); if (!emojiPicker || emojiPicker.style.display==='block') { if (emojiPicker) emojiPicker.style.display = 'none'; } else { buildEmojiPicker(); emojiPicker.style.display = 'block'; } });
        // close picker on outside click
        document.addEventListener('click', function(){ if (emojiPicker) emojiPicker.style.display = 'none'; });
        if (emojiPicker) { emojiPicker.addEventListener('click', function(e){ e.stopPropagation(); }); }
    }

    if (editBtn && editForm) {
        populateSelects(); renderEditCustom();
        editBtn.addEventListener('click', function(){ editForm.style.display = editForm.style.display === 'none' ? 'block' : 'none'; populateSelects(); renderEditCustom(); });
        addTagBtn.addEventListener('click', function(){ const v=tagInput.value.trim(); if (!v) return; if (!phStructured.custom.includes(v)) phStructured.custom.push(v); tagInput.value=''; renderEditCustom(); });
        tagInput.addEventListener('keydown', function(e){ if (e.key==='Enter') { e.preventDefault(); addTagBtn.click(); } });
        editTagsContainer.addEventListener('click', function(e){ if (e.target && e.target.classList.contains('ph-tag-remove')) { const tg=e.target.getAttribute('data-tag'); phStructured.custom=(phStructured.custom||[]).filter(x=>x!==tg); renderEditCustom(); } });
        document.getElementById('ph-cancel-profile-btn').addEventListener('click', function(){ editForm.style.display='none'; location.reload(); });
        document.getElementById('ph-save-profile-btn').addEventListener('click', function(){ const btn=this; btn.disabled=true; const desc = descInput.value.trim(); const estado = document.getElementById('ph-estado-select') ? document.getElementById('ph-estado-select').value : ''; const musica = Array.from(document.getElementById('ph-musica-select') ? document.getElementById('ph-musica-select').selectedOptions : []).map(o=>o.value); const bebida = Array.from(document.getElementById('ph-bebida-select') ? document.getElementById('ph-bebida-select').selectedOptions : []).map(o=>o.value); const city = document.getElementById('ph-city-input') ? document.getElementById('ph-city-input').value.trim() : ''; phStructured.estado_civil=estado; phStructured.estilo_musica=musica; phStructured.bebida=bebida; phStructured.city = city; fetch('<?php echo admin_url('admin-ajax.php'); ?>', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: new URLSearchParams({ action: 'ph_update_profile', nonce: '<?php echo wp_create_nonce('social_nonce'); ?>', user_id: '<?php echo esc_js($profile_id); ?>', description: desc, structured_tags: JSON.stringify(phStructured) }) }).then(r=>r.json()).then(data=>{ btn.disabled=false; if (data.success) { if (descView) descView.innerHTML = data.data.description || ''; if (data.data.tags_html && viewTagsContainer) viewTagsContainer.innerHTML = data.data.tags_html; location.reload(); } else { alert(data.data || 'Erro ao salvar perfil'); } }).catch(()=>{ btn.disabled=false; alert('Erro de rede'); }); });
    }
});
</script>
<?php get_footer();