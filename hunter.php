<?php
/* Template Name: Hunter */

get_header();

get_template_part( 'parts/navigation' );

// --- INÍCIO DA NOVA LÓGICA (Adicionado) ---
$profile_id = 0;

// 1. Tenta pegar pelo nickname na URL (?u=joao)
if ( isset( $_GET['u'] ) && ! empty( $_GET['u'] ) ) {
    $user_by_login = get_user_by( 'login', sanitize_text_field( $_GET['u'] ) );
    if ( $user_by_login ) {
        $profile_id = $user_by_login->ID;
    }
} 
// 2. Tenta pegar pelo ID numérico na URL (?uid=123)
elseif ( isset( $_GET['uid'] ) && ! empty( $_GET['uid'] ) ) {
    $profile_id = intval( $_GET['uid'] );
}

// 3. Se falhou ou não tem URL, assume o usuário logado
if ( empty( $profile_id ) ) {
    $profile_id = get_current_user_id();
}

// Define se quem está vendo é o dono do perfil
$is_owner = ( get_current_user_id() === $profile_id );
// --- FIM DA NOVA LÓGICA ---
?>
<!-- Cropper.js CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
<style>
    dialog::backdrop{
        background-color: rgb(177 119 251 / 60%);
    }
    .hunter_page{
        display: flex;
        min-height: 50vh;

        .container{
            display: flex;
            flex: 1;
            flex-direction: column;
        }
        .profile-row{
            display: flex;
            flex: 1;
            justify-content: center;
            box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
            background:#fff;
            border-radius: 1rem;
        }
        .profile_card{
            width: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            .profile_cover{
                width: 100%;
                height: 200px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                position: relative;
                overflow: hidden;
                .cover-image{
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }
                .cover-edit-btn{
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
                .cover-edit-btn:hover{
                    background: rgba(255, 255, 255, 1);
                    transform: scale(1.1);
                }
            }
            .profile_card_content{
                padding: 1rem;
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
            }
            .profile_card_top{
                margin-top: -48px;
                position: relative;
                z-index: 1;
                padding-left: 0.5rem;
            }
            .profile_card_top .user-avatar {
                border: 4px solid #fff;
                box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
            }
            .change-avatar{
                border: none;
                background: none;
                cursor: pointer;
                width: fit-content;
                position: relative;
            }
            .pencil_icon{
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
            .user-avatar{
                width: 96px;
                height: 96px;
                overflow: hidden;
                position: relative;
            }
            .user-avatar img{
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
        }
    }
    .upload_profile_image_popup{
        max-width: 600px;
        width: 90%;
        padding: 2.5rem;
        height: 75vh;
        overflow-y: auto;

        .upload_profile_image_popup_align{
            display:flex;
            flex-direction:column;
            gap: 2.5rem;
        }
        .close_button{
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
        
        .modal-section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
            margin: 0;
            padding-bottom: 0.75rem;
        }
        
        .upload_profile_form{
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
        .custom-file-label-text {
            display: none;
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
        
        .image-crop-container .cropper-point.point-se {
            cursor: se-resize;
        }
        
        .image-crop-container .cropper-point.point-sw {
            cursor: sw-resize;
        }
        
        .image-crop-container .cropper-point.point-nw {
            cursor: nw-resize;
        }
        
        .image-crop-container .cropper-point.point-ne {
            cursor: ne-resize;
        }
        
        .image-crop-container .cropper-point.point-n,
        .image-crop-container .cropper-point.point-s {
            cursor: ns-resize;
        }
        
        .image-crop-container .cropper-point.point-e,
        .image-crop-container .cropper-point.point-w {
            cursor: ew-resize;
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
        #file-name {
            margin-left: 10px;
            color: #666;
        }
        
        /* Avatar Shape Selection */
        .avatar-shape-selection {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .avatar-shape-selection h3 {
            margin: 0;
            font-size: 1rem;
            color: #333;
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
        .shape-preview.shape-circle {
            border-radius: 50%;
        }
        .shape-preview.shape-square {
            border-radius: 0;
        }
        .shape-preview.shape-star {
            clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
        }
        .shape-preview.shape-heart {
            clip-path: polygon(50% 15%, 60% 5%, 75% 5%, 85% 15%, 85% 30%, 50% 60%, 15% 30%, 15% 15%, 25% 5%, 40% 5%);
        }
        .shape-preview.shape-diamond {
            clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%);
        }
        .shape-preview.shape-hexagon {
            clip-path: polygon(30% 0%, 70% 0%, 100% 50%, 70% 100%, 30% 100%, 0% 50%);
        }
        .shape-preview.shape-octagon {
            clip-path: polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%);
        }
        .shape-preview.shape-triangle {
            clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
        }
        .shape-preview.shape-pentagon {
            clip-path: polygon(50% 0%, 100% 38%, 82% 100%, 18% 100%, 0% 38%);
        }
        .shape-preview.shape-squircle {
            border-radius: 25% / 25%;
        }
        .shape-preview.shape-blob {
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
        }
        .shape-preview.shape-badge {
            clip-path: polygon(20% 0%, 80% 0%, 100% 20%, 100% 80%, 80% 100%, 20% 100%, 0% 80%, 0% 20%);
        }
        .shape-preview.shape-wavy {
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
        }
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
    }
    
    /* Group Options Styles */
    .group-options-container {
        width: 100%;
        max-width: 800px;
        background: white;
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

    /* My Groups Section Styles */
    .my-groups-container {
        width: 100%;
        max-width: 1200px;
        background: white;
        border-radius: 1.5rem;
        padding: 2.5rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
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
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
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
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
    }

    /* Owned groups styling - different color */
    .group-card.owned {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
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
        
        .profile_card .profile_cover {
            height: 150px;
        }
        
        .profile_card .profile_card_top {
            margin-top: -40px;
        }
        
        .profile_card .user-avatar {
            width: 80px;
            height: 80px;
        }
        
        .shape-options {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    @media (max-width: 480px) {
        .shape-options {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
<section class="hunter_page">
    
    <div class="container">
        <div class="profile-row">
            <div class="profile_card">
                <!-- Cover Picture -->
                <div class="profile_cover">
                    <?php 
                    echo '<div style="background:red; color:white; padding:20px; z-index:9999; position:relative;">';
                        echo 'GET U: ' . (isset($_GET['u']) ? $_GET['u'] : 'Não definido') . '<br>';
                        echo 'GET UID: ' . (isset($_GET['uid']) ? $_GET['uid'] : 'Não definido') . '<br>';
                        echo 'Profile ID calculado: ' . $profile_id;
                        echo '</div>';
                    $cover_image = get_user_meta($profile_id, 'cover_image', true);
                    if ($cover_image) :
                    ?>
                        <img src="<?php echo esc_url($cover_image); ?>" alt="Cover" class="cover-image">
                    <?php endif; ?>
                    
                    <?php if ($is_owner) : ?>
                        <button data-open-cover-modal class="cover-edit-btn" title="Editar foto de capa">
                            <svg viewBox="0 0 16 16" width="20px" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M8.29289 3.70711L1 11V15H5L12.2929 7.70711L8.29289 3.70711Z" fill="#b177fb"></path> <path d="M9.70711 2.29289L13.7071 6.29289L15.1716 4.82843C15.702 4.29799 16 3.57857 16 2.82843C16 1.26633 14.7337 0 13.1716 0C12.4214 0 11.702 0.297995 11.1716 0.828428L9.70711 2.29289Z" fill="#b177fb"></path></g></svg>
                        </button>
                    <?php endif; ?>
                </div>
                   
                
                <div class="profile_card_content">
                <div class="profile_card_top">
                    <div>
                        <?php if ($is_owner) : ?>
                            <button data-open-avatar-modal class="change-avatar" id="change-avatar">
                                <div class="pencil_icon">
                                    <svg viewBox="0 0 16 16" width="16px" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M8.29289 3.70711L1 11V15H5L12.2929 7.70711L8.29289 3.70711Z" fill="#b177fb"></path> <path d="M9.70711 2.29289L13.7071 6.29289L15.1716 4.82843C15.702 4.29799 16 3.57857 16 2.82843C16 1.26633 14.7337 0 13.1716 0C12.4214 0 11.702 0.297995 11.1716 0.828428L9.70711 2.29289Z" fill="#b177fb"></path></g></svg>
                                </div>
                        <?php else: ?>
                            <div class="change-avatar">
                        <?php endif; ?>
                        
                            <?php
                            // AGORA USA $profile_id
                            $avatar_shape = get_user_meta($profile_id, 'avatar_shape', true);
                            $avatar_shape = $avatar_shape ? $avatar_shape : 'circle';
                            ?>
                            <div class="user-avatar shape-<?php echo esc_attr($avatar_shape); ?>">
                                <?php echo(get_avatar($profile_id, 96)); ?>
                            </div>

                        <?php if ($is_owner) : ?>
                            </button>
                        <?php else: ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <h1 style="color: black; margin:0;">
                            <?php 
                            // Exibe o nome do perfil visitado, não o seu
                            $u_info = get_userdata($profile_id);
                            echo $u_info ? $u_info->user_login : 'Usuário'; 
                            ?>
                        </h1>
                        
                        <?php if ($is_owner && is_user_logged_in()) : ?>
                            <?php display_logout_button(); ?>
                        <?php endif; ?>
                    </div>
                </div>                        
                        <?php
                        // Profile description and structured tags for the hunter page
                        // If the loader (rewrite/template_include) set $profile_id or $profile_tags_structured, prefer those
                        if (!isset($profile_id) || empty($profile_id)) {
                            $profile_id = get_current_user_id();
                        }

                        $profile_description = isset($profile_description) ? $profile_description : get_user_meta($profile_id, 'ph_profile_description', true);
                        
                        $profile_tags_structured = isset($profile_tags_structured) ? $profile_tags_structured : get_user_meta($profile_id, 'ph_profile_tags_structured', true);
                        if (empty($profile_tags_structured) || !is_array($profile_tags_structured)) {
                            $profile_tags_structured = [
                                'estado_civil' => '',
                                'estilo_musica' => [],
                                'bebida' => [],
                                'custom' => [],
                                'city' => ''
                            ];
                        }

                        // friend/group counts and ownership checks
                        $current_user = get_current_user_id();
                        $is_owner = ($current_user && $current_user === $profile_id);
                        $friends = (array) get_user_meta($profile_id, 'ph_friends', true);
                        $friends_count = count($friends);
                        $groups = (array) get_user_meta($profile_id, 'ph_groups', true);
                        $groups_count = count($groups);
                        $friend_requests = (array) get_user_meta($profile_id, 'ph_friend_requests', true);
                        $is_friend = ($current_user && in_array($current_user, $friends));
                        $has_requested = ($current_user && in_array($current_user, $friend_requests));
                        ?>

                        <div class="profile-body" style="margin-top:0.6rem;">
                            <div id="ph-profile-description" style="margin-top:0.5rem;color:#444; background:#fff; padding:0.85rem; border-radius:0.5rem;">
                                <?php if (!empty($profile_description)) { echo wpautop(esc_html($profile_description)); } else { echo '<em>Sem descrição.</em>'; } ?>
                            </div>

                            <div id="ph-profile-tags-container" style="margin-top:0.6rem;">
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

                            <?php if ($is_owner) : ?>
                                <div style="margin-top:0.75rem;">
                                    <button id="ph-edit-profile-btn" class="follow-btn">Editar BIO</button>
                                </div>
                            <?php else: ?>
                                <div style="display:flex;gap:0.75rem;align-items:center;justify-content:center;margin-top:0.75rem;flex-wrap:wrap;">
                                    <div style="background:rgba(255,255,255,0.04);padding:6px 10px;border-radius:8px;">
                                        <strong style="display:block;font-size:0.9rem;">Amigos</strong>
                                        <span id="ph-friends-count" style="font-size:0.9rem;"><?php echo intval($friends_count); ?></span>
                                    </div>
                                    <div style="background:rgba(255,255,255,0.04);padding:6px 10px;border-radius:8px;">
                                        <strong style="display:block;font-size:0.9rem;">Grupos</strong>
                                        <span id="ph-groups-count" style="font-size:0.9rem;"><?php echo intval($groups_count); ?></span>
                                    </div>
                                    <?php if (is_user_logged_in()) : ?>
                                        <?php if ($is_friend) : ?>
                                            <button class="follow-btn" disabled>Amigos</button>
                                        <?php elseif ($has_requested) : ?>
                                            <button id="ph-cancel-request-btn" class="follow-btn" data-target="<?php echo intval($profile_id); ?>">Cancelar pedido</button>
                                        <?php else: ?>
                                            <button id="ph-add-friend-btn" class="follow-btn" data-target="<?php echo intval($profile_id); ?>"> Adicionar Amigo </button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <a class="follow-btn" href="<?php echo wp_login_url( get_permalink() ); ?>">Entrar para adicionar</a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div id="ph-profile-edit-form" class="ph-profile-edit" style="display:none;margin-top:0.75rem;background:#fff;padding:0.75rem;border-radius:0.5rem;border:1px solid #eee;">
                                <label for="ph-profile-description-input" style="color: #000; font-weight:700;">Descrição</label>
                                <textarea id="ph-profile-description-input"><?php echo esc_textarea($profile_description); ?></textarea>

                                <div style="margin-top:0.6rem;">
                                    <label style=" color:#000; font-weight:700;">Estado Civil</label>
                                    <select id="ph-estado-select" style="color:#000; width:100%;padding:0.5rem;margin-top:0.25rem;border:1px solid #020202ff;border-radius:0.4rem;">
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
                                    <label style="color:#000; font-weight:700;">Cidade</label>
                                    <input id="ph-city-input" type="text" placeholder="Sua cidade" style="width:100%;padding:0.5rem;margin-top:0.25rem;border:1px solid #000000ff;border-radius:0.4rem;" value="<?php echo esc_attr($profile_tags_structured['city'] ?? ''); ?>" />
                                </div>

                                <div style="margin-top:0.6rem;">
                                    <label style="color:#000; font-weight:700;">Estilo(s) de música</label>
                                    <select id="ph-musica-select" multiple size="4" style="width:100%;padding:0.5rem;margin-top:0.25rem;border:1px solid #0f0f0fff;border-radius:0.4rem;">
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
                                    <label style="color:#000; font-weight:700;">Bebida que gosta</label>
                                    <select id="ph-bebida-select" multiple size="4" style="width:100%;padding:0.5rem;margin-top:0.25rem;border:1px solid #111010ff;border-radius:0.4rem;">
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
                            <?php if ($is_owner) : ?>
                                <div id="ph-incoming-requests" style="margin-top:0.75rem;background:#fff;padding:0.75rem;border-radius:0.5rem;border:1px solid #eee;">
                                    <div style=" color: black;font-weight:700;margin-bottom:0.5rem;">Pedidos de amizade</div>
                                    <div id="ph-requests-list">
                                        <?php
                                        $incoming = (array) get_user_meta($profile_id, 'ph_friend_requests', true);
                                        if (empty($incoming)) {
                                            echo '<div style="color:#666;">Nenhum pedido no momento.</div>';
                                        } else {
                                            foreach ($incoming as $req_id) {
                                                $u = get_user_by('id', intval($req_id));
                                                if (!$u) continue;
                                                echo '<div class="ph-request-item" data-from="' . esc_attr($u->ID) . '" style="display:flex;align-items:center;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid #eee;">';
                                                echo '<div style="display:flex;align-items:center;gap:0.5rem;">' . get_avatar($u->ID, 40) . '<div><strong>' . esc_html($u->display_name) . '</strong></div></div>';
                                                echo '<div style="display:flex;gap:0.5rem;"><button class="ph-accept-btn follow-btn" data-from="' . esc_attr($u->ID) . '">Aceitar</button><button class="ph-decline-btn follow-btn" data-from="' . esc_attr($u->ID) . '">Recusar</button></div>';
                                                echo '</div>';
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
            </div>
        </div>
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
            <form class="upload_profile_form" id="cover-upload-form" method="post" enctype="multipart/form-data">
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
                    <form class="upload_profile_form" id="avatar-upload-form" method="post" enctype="multipart/form-data">
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
                            $current_shape = get_user_meta(get_current_user_id(), 'avatar_shape', true);
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

<script>
    // Cover Modal Controls
    const openCoverButtons = document.querySelectorAll("[data-open-cover-modal]")
    const closeCoverButton = document.querySelector("[data-close-cover-modal]")
    const coverModal = document.querySelector("[data-cover-modal]")

    if (openCoverButtons && coverModal) {
        openCoverButtons.forEach(button => {
            button.addEventListener("click", () => {
                coverModal.showModal()
            })
        })
    }
    
    if (closeCoverButton && coverModal) {
        closeCoverButton.addEventListener("click", ()=>{
            coverModal.close()
        })
    }
    
    // Avatar Modal Controls
    const openAvatarButtons = document.querySelectorAll("[data-open-avatar-modal]")
    const closeAvatarButton = document.querySelector("[data-close-avatar-modal]")
    const avatarModal = document.querySelector("[data-avatar-modal]")

    if (openAvatarButtons && avatarModal) {
        openAvatarButtons.forEach(button => {
            button.addEventListener("click", () => {
                avatarModal.showModal()
                // Get fresh nonce when modal opens
                const saveShapeBtn = document.getElementById('save-shape-btn');
                if (saveShapeBtn) {
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            action: 'get_avatar_shape_nonce'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data.nonce) {
                            saveShapeBtn.setAttribute('data-nonce', data.data.nonce);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching nonce:', error);
                    });
                }
            })
        })
    }
    
    if (closeAvatarButton && avatarModal) {
        closeAvatarButton.addEventListener("click", ()=>{
            avatarModal.close()
        })
    }
    
    // Tab System Functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    function switchTab(targetTab) {
        // Remove active class from all buttons and contents
        tabButtons.forEach(btn => btn.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));
        
        // Add active class to clicked button and corresponding content
        const clickedButton = document.querySelector(`[data-tab="${targetTab}"]`);
        const targetContent = document.getElementById(targetTab + '-section');
        
        if (clickedButton) {
            clickedButton.classList.add('active');
        }
        if (targetContent) {
            targetContent.classList.add('active');
        }
        
        // Get fresh nonce when switching to shape tab
        if (targetTab === 'avatar-shape') {
            const saveShapeBtn = document.getElementById('save-shape-btn');
            if (saveShapeBtn) {
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'get_avatar_shape_nonce'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.nonce) {
                        saveShapeBtn.setAttribute('data-nonce', data.data.nonce);
                    }
                })
                .catch(error => {
                    console.error('Error fetching nonce:', error);
                });
            }
        }
    }
    
    // Add event listeners to tab buttons
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            switchTab(targetTab);
        });
    });
    
    // Auto-open modals if there's an error
    <?php if (isset($_GET['cover_error']) && $_GET['cover_error'] == '1') : ?>
    if (coverModal) {
        coverModal.showModal();
    }
    <?php endif; ?>
    
    <?php if ((isset($_GET['avatar_error']) && $_GET['avatar_error'] == '1') || 
              (isset($_GET['shape_error']) && $_GET['shape_error'] == '1') ||
              (isset($_GET['avatar_updated']) && $_GET['avatar_updated'] == '1') ||
              (isset($_GET['shape_updated']) && $_GET['shape_updated'] == '1')) : ?>
    if (avatarModal) {
        avatarModal.showModal();
        // Get fresh nonce when modal opens automatically
        const saveShapeBtn = document.getElementById('save-shape-btn');
        if (saveShapeBtn) {
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'get_avatar_shape_nonce'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.nonce) {
                    saveShapeBtn.setAttribute('data-nonce', data.data.nonce);
                }
            })
            .catch(error => {
                console.error('Error fetching nonce:', error);
            });
        }
        // Show appropriate tab based on error or success
        <?php if ((isset($_GET['avatar_error']) && $_GET['avatar_error'] == '1') || 
                  (isset($_GET['avatar_updated']) && $_GET['avatar_updated'] == '1')) : ?>
        switchTab('avatar-upload');
        <?php elseif ((isset($_GET['shape_error']) && $_GET['shape_error'] == '1') || 
                      (isset($_GET['shape_updated']) && $_GET['shape_updated'] == '1')) : ?>
        switchTab('avatar-shape');
        <?php endif; ?>
    }
    <?php endif; ?>
    
    // Clean up URL parameters after showing messages
    if (window.location.search.includes('avatar_updated') || window.location.search.includes('avatar_error') || 
        window.location.search.includes('shape_updated') || window.location.search.includes('shape_error') ||
        window.location.search.includes('cover_updated') || window.location.search.includes('cover_error')) {
        setTimeout(function() {
            const url = new URL(window.location);
            url.searchParams.delete('avatar_updated');
            url.searchParams.delete('avatar_error');
            url.searchParams.delete('shape_updated');
            url.searchParams.delete('shape_error');
            url.searchParams.delete('cover_updated');
            url.searchParams.delete('cover_error');
            window.history.replaceState({}, '', url);
        }, 3000);
    }
</script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const addBtn = document.getElementById('ph-add-friend-btn');
    const cancelBtn = document.getElementById('ph-cancel-request-btn');
    function attachCancelHandler(btn){
        if (!btn) return;
        btn.addEventListener('click', function(){
            const target = this.getAttribute('data-target');
            if (!target) return;
            const b = this;
            b.disabled = true;
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST', headers: {'Content-Type':'application/x-www-form-urlencoded'},
                body: new URLSearchParams({ action: 'ph_cancel_friend_request', nonce: '<?php echo wp_create_nonce('social_nonce'); ?>', target_id: target })
            }).then(r=>r.json()).then(data=>{
                if (data.success) {
                    // swap to Add Friend button
                    const parent = b.parentElement;
                    if (parent) {
                        b.remove();
                        const newBtn = document.createElement('button');
                        newBtn.id = 'ph-add-friend-btn'; newBtn.className='follow-btn'; newBtn.setAttribute('data-target', target); newBtn.textContent = 'Adicionar Amigo';
                        parent.appendChild(newBtn);
                        // attach add handler to new button
                        attachAddHandler(newBtn);
                    }
                } else {
                    alert(data.data || 'Erro ao cancelar pedido');
                    b.disabled = false;
                }
            }).catch(()=>{ alert('Erro de rede'); b.disabled=false; });
        });
    }

    function attachAddHandler(btn){
        if (!btn) return;
        btn.addEventListener('click', function(){
            const target = this.getAttribute('data-target');
            if (!target) return;
            const btn = this;
            btn.disabled = true;
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {'Content-Type':'application/x-www-form-urlencoded'},
                body: new URLSearchParams({ action: 'ph_send_friend_request', nonce: '<?php echo wp_create_nonce('social_nonce'); ?>', target_id: target })
            }).then(r=>r.json()).then(data=>{
                if (data.success) {
                    // replace with cancel button
                    const parent = btn.parentElement;
                    if (parent) {
                        btn.remove();
                        const newBtn = document.createElement('button');
                        newBtn.id = 'ph-cancel-request-btn'; newBtn.className='follow-btn'; newBtn.setAttribute('data-target', target); newBtn.textContent = 'Cancelar pedido';
                        parent.appendChild(newBtn);
                        attachCancelHandler(newBtn);
                    }
                } else {
                    alert(data.data || 'Erro ao enviar pedido');
                }
            }).catch(()=>{ alert('Erro de rede'); }).finally(()=>{ btn.disabled = false; });
        });
    }

    if (cancelBtn) attachCancelHandler(cancelBtn);
    if (!addBtn) return;
    attachAddHandler(addBtn);
});
</script>
<!-- Cropper.js JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
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
                    
                    // Clear any previous error
                    if (errorMessage) {
                        errorMessage.style.display = 'none';
                        errorMessage.textContent = '';
                    }
                    
                    // Destroy existing cropper if any
                    if (cropper) {
                        cropper.destroy();
                        cropper = null;
                    }
                    
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        // For GIFs, skip crop and show preview directly
                        if (isGif) {
                            avatarPreview.src = e.target.result;
                            avatarLabel.classList.add('has-image');
                            avatarIcon.style.display = 'none';
                            avatarCropContainer.classList.remove('active');
                            avatarSubmitBtn.style.display = 'block';
                            fileNameSpan.textContent = file.name;
                            return;
                        }
                        
                        // For other images, show crop interface with Cropper.js
                        avatarCropImage.src = e.target.result;
                        avatarCropContainer.classList.add('active');
                        avatarSubmitBtn.style.display = 'none';
                        fileNameSpan.textContent = file.name;
                        
                        // Initialize Cropper.js
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
                                // Center the crop box
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
            
            // Confirm crop
            if (avatarCropConfirm) {
                avatarCropConfirm.addEventListener('click', function() {
                    if (!cropper) return;
                    
                    // Get cropped canvas (square, no circular mask)
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
                    
                    // Create a new canvas with white background to avoid black borders
                    const finalCanvas = document.createElement('canvas');
                    finalCanvas.width = 400;
                    finalCanvas.height = 400;
                    const finalCtx = finalCanvas.getContext('2d');
                    
                    // Fill with white background
                    finalCtx.fillStyle = '#ffffff';
                    finalCtx.fillRect(0, 0, 400, 400);
                    
                    // Draw the cropped image on top
                    finalCtx.drawImage(canvas, 0, 0);
                    
                    // Convert final canvas to blob (square image, no circular mask, white background)
                    finalCanvas.toBlob(function(blob) {
                        if (!blob) {
                            console.error('Failed to create blob from canvas');
                            if (errorMessage) {
                                errorMessage.style.display = 'block';
                                errorMessage.textContent = 'Erro ao processar imagem. Tente novamente.';
                            }
                            return;
                        }
                        
                        // Create a new file from the blob
                        const file = new File([blob], 'avatar.jpg', { type: 'image/jpeg' });
                        
                        // Create a new FileList using DataTransfer
                        try {
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);
                            
                            // Update the input's files
                            userAvatarInput.files = dataTransfer.files;
                            
                            // Verify the file was set
                            if (userAvatarInput.files.length === 0) {
                                throw new Error('Failed to set file in input');
                            }
                        } catch(e) {
                            console.error('Error setting file:', e);
                            if (errorMessage) {
                                errorMessage.style.display = 'block';
                                errorMessage.textContent = 'Erro ao processar arquivo. Tente novamente.';
                            }
                            return;
                        }
                        
                        // Create circular preview for display (but save square image)
                        const previewCanvas = document.createElement('canvas');
                        previewCanvas.width = 400;
                        previewCanvas.height = 400;
                        const previewCtx = previewCanvas.getContext('2d');
                        
                        // Create circular clipping path for preview only
                        previewCtx.beginPath();
                        previewCtx.arc(200, 200, 200, 0, 2 * Math.PI);
                        previewCtx.clip();
                        
                        // Draw the final canvas (with white background) on preview
                        previewCtx.drawImage(finalCanvas, 0, 0);
                        
                        // Update preview using circular canvas data URL (for display only)
                        const previewDataUrl = previewCanvas.toDataURL('image/jpeg', 0.95);
                        avatarPreview.src = previewDataUrl;
                        avatarLabel.classList.add('has-image');
                        if (avatarIcon) {
                            avatarIcon.style.display = 'none';
                        }
                        
                        // Destroy cropper and hide crop container
                        cropper.destroy();
                        cropper = null;
                        avatarCropContainer.classList.remove('active');
                        avatarSubmitBtn.style.display = 'block';
                        
                        if (fileNameSpan) {
                            fileNameSpan.textContent = 'Imagem selecionada';
                        }
                        
                        // Clear any previous errors
                        if (errorMessage) {
                            errorMessage.style.display = 'none';
                            errorMessage.textContent = '';
                        }
                    }, 'image/jpeg', 0.95);
                });
            }
            
            // Cancel crop
            if (avatarCropCancel) {
                avatarCropCancel.addEventListener('click', function() {
                    if (cropper) {
                        cropper.destroy();
                        cropper = null;
                    }
                    avatarCropContainer.classList.remove('active');
                    userAvatarInput.value = '';
                    if (fileNameSpan) {
                        fileNameSpan.textContent = 'Nenhum arquivo selecionado';
                    }
                    avatarLabel.classList.remove('has-image');
                    if (avatarIcon) {
                        avatarIcon.style.display = 'block';
                    }
                    avatarPreview.src = '';
                });
            }
        }
        
        if (avatarUploadForm) {
            avatarUploadForm.addEventListener('submit', function(event) {
                const fileInput = document.getElementById('user_avatar');
                const errorMessage = document.getElementById('upload-error');

                if (!fileInput || !fileInput.files.length) {
                    // Prevent form submission
                    event.preventDefault();

                    // Show the error message
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
                // Clear any previous error
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
                    // Prevent form submission
                    event.preventDefault();

                    // Show the error message
                    if (errorMessage) {
                        errorMessage.style.display = 'block';
                        errorMessage.textContent = "Por favor selecione uma Imagem";
                    }
                }
            });
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
            msg.style.display = 'none';

            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({ action: 'create_user_post', nonce: '<?php echo wp_create_nonce('social_nonce'); ?>', content: content, visibility: 'followers' })
            }).then(r=>r.json()).then(data=>{
                if (data.success) {
                    msg.style.display = 'block';
                    msg.style.color = '#155724';
                    msg.textContent = 'Post publicado para seus seguidores.';
                    document.getElementById('followers-post-content').value = '';
                    // Optionally reload part of the feed
                    setTimeout(()=> location.reload(), 800);
                } else {
                    msg.style.display = 'block';
                    msg.style.color = '#721c24';
                    msg.textContent = data.data || 'Erro ao publicar.';
                }
            }).catch(()=>{
                msg.style.display = 'block';
                msg.style.color = '#721c24';
                msg.textContent = 'Erro de rede.';
            });
        });
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
                
                // Create group via AJAX
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
        
        // Alterei o texto para ficar claro que aceita ID também
        const rawInput = prompt('Cole o Link ou o ID do grupo aqui:');
        
        if (rawInput && rawInput.trim() !== '') {
            
            // 1. Limpeza inicial: remove espaços e prefixo "ID:" caso exista
            // Ex: "ID: 12345" vira "12345"
            let cleanInput = rawInput.trim().replace(/^ID:\s*/i, '');
            
            // 2. Remove barra no final se o usuário copiou com ela (ex: .../grupo/123/)
            cleanInput = cleanInput.replace(/\/$/, '');

            let groupId = '';

            // 3. A MÁGICA (Equivalente ao basename do PHP)
            if (cleanInput.includes('/')) {
                // Se tem barra, é um link (http://... ou partyhunter.com.br/...)
                // Quebramos nas barras e pegamos o último pedaço
                const parts = cleanInput.split('/');
                groupId = parts[parts.length - 1];
            } else {
                // Se NÃO tem barra, o usuário digitou apenas o código
                groupId = cleanInput;
            }
            
            // 4. Redireciona
            if (groupId) {
                // Adicionei a barra '/' no final 
                window.location.href = '<?php echo home_url('/grupo/'); ?>' + groupId + '/';
            } else {
                alert('Código ou Link inválido. Tente novamente.');
            }
        }
    });
}
</script>
<?php 
// Get current avatar shape for JavaScript
$js_current_shape = get_user_meta(get_current_user_id(), 'avatar_shape', true);
$js_current_shape = $js_current_shape ? $js_current_shape : 'circle';
?>
<script>
    // Avatar shape selection functionality in modal
    document.addEventListener('DOMContentLoaded', function() {
        const modalShapeOptions = document.querySelectorAll('#modal-shape-options .shape-option');
        const saveShapeBtn = document.getElementById('save-shape-btn');
        const shapeSaveMessage = document.getElementById('shape-save-message');
        const userAvatar = document.querySelector('.user-avatar');
        let selectedShape = '<?php echo esc_js($js_current_shape); ?>';
        let originalShape = '<?php echo esc_js($js_current_shape); ?>';
        
        // Track selected shape (visual only, no save)
        modalShapeOptions.forEach(option => {
            option.addEventListener('click', function() {
                const shape = this.dataset.shape;
                
                // Remove active class from all options
                modalShapeOptions.forEach(opt => opt.classList.remove('active'));
                // Add active class to selected option
                this.classList.add('active');
                
                // Update selected shape variable
                selectedShape = shape;
                
                // Hide any previous messages
                if (shapeSaveMessage) {
                    shapeSaveMessage.style.display = 'none';
                }
            });
        });
        
        // Save shape when save button is clicked
        if (saveShapeBtn) {
            saveShapeBtn.addEventListener('click', function() {
                const btn = this;
                const originalText = btn.textContent;
                const nonce = btn.getAttribute('data-nonce');
                
                if (!nonce) {
                    alert('Erro: Nonce não encontrado. Por favor, recarregue a página.');
                    return;
                }
                
                // Disable button and show loading state
                btn.disabled = true;
                btn.textContent = 'Salvando...';
                
                // Save shape preference via AJAX
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'save_avatar_shape',
                        nonce: nonce,
                        shape: selectedShape
                    })
                })
                .then(response => response.json())
                .then(data => {
                    btn.disabled = false;
                    btn.textContent = originalText;
                    
                    if (data.success) {
                        // Show success message
                        if (shapeSaveMessage) {
                            shapeSaveMessage.style.display = 'block';
                            shapeSaveMessage.style.background = '#d4edda';
                            shapeSaveMessage.style.color = '#155724';
                            shapeSaveMessage.style.border = '1px solid #c3e6cb';
                            shapeSaveMessage.textContent = 'Forma do avatar salva com sucesso!';
                        }
                        
                        // Update avatar on page
                        if (userAvatar) {
                            userAvatar.className = 'user-avatar shape-' + selectedShape;
                        }
                        
                        // Update original shape
                        originalShape = selectedShape;
                        
                        // Reload page after a short delay to show updated avatar
                        setTimeout(() => {
                            window.location.href = window.location.pathname + '?shape_updated=1';
                        }, 1000);
                    } else {
                        // Show error message
                        if (shapeSaveMessage) {
                            shapeSaveMessage.style.display = 'block';
                            shapeSaveMessage.style.background = '#f8d7da';
                            shapeSaveMessage.style.color = '#721c24';
                            shapeSaveMessage.style.border = '1px solid #f5c6cb';
                            shapeSaveMessage.textContent = data.data?.message || 'Erro ao salvar forma do avatar.';
                        }
                        
                        // Revert selection
                        modalShapeOptions.forEach(opt => {
                            opt.classList.remove('active');
                            if (opt.dataset.shape === originalShape) {
                                opt.classList.add('active');
                            }
                        });
                        selectedShape = originalShape;
                    }
                })
                .catch(error => {
                    console.error('Network error:', error);
                    btn.disabled = false;
                    btn.textContent = originalText;
                    
                    // Show error message
                    if (shapeSaveMessage) {
                        shapeSaveMessage.style.display = 'block';
                        shapeSaveMessage.style.background = '#f8d7da';
                        shapeSaveMessage.style.color = '#721c24';
                        shapeSaveMessage.style.border = '1px solid #f5c6cb';
                        shapeSaveMessage.textContent = 'Erro de rede. Por favor, tente novamente.';
                    }
                    
                    // Revert selection
                    modalShapeOptions.forEach(opt => {
                        opt.classList.remove('active');
                        if (opt.dataset.shape === originalShape) {
                            opt.classList.add('active');
                        }
                    });
                    selectedShape = originalShape;
                });
            });
        }
    });
</script>

<?php get_footer(); ?>
<script>
document.addEventListener('DOMContentLoaded', function(){
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
    const badgesContainer = document.getElementById('ph-profile-badges');
    const descView = document.getElementById('ph-profile-description');

    function renderBadges(){ if (!badgesContainer) return; badgesContainer = badgesContainer; }

    function renderEditCustom(){ if (!editTagsContainer) return; editTagsContainer.innerHTML=''; (phStructured.custom||[]).forEach(t=>{ const span=document.createElement('span'); span.className='ph-profile-tag'; span.setAttribute('data-tag', t); span.innerHTML = t + ' <button class="ph-tag-remove" data-tag="'+t+'" style="margin-left:6px;border:none;background:transparent;cursor:pointer;">&times;</button>'; editTagsContainer.appendChild(span); }); }

    function populateSelects(){ const estado=document.getElementById('ph-estado-select'); const musica=document.getElementById('ph-musica-select'); const bebida=document.getElementById('ph-bebida-select'); const cityInput = document.getElementById('ph-city-input'); if (estado) estado.value = phStructured.estado_civil || ''; if (musica && phStructured.estilo_musica) { for (const opt of musica.options) opt.selected = phStructured.estilo_musica.indexOf(opt.value)!==-1; } if (bebida && phStructured.bebida) { for (const opt of bebida.options) opt.selected = phStructured.bebida.indexOf(opt.value)!==-1; } if (cityInput) cityInput.value = phStructured.city || ''; }

    // Emoji picker
    const _emojis = ['😀','😃','😄','😁','😆','😅','😂','🤣','😊','😉','😍','😘','😎','🤩','🤔','🤨','😇','🥳','😉','😜','🤪','👍','👎','👏','🙏','💃','🕺','🎵','🎸','🎧','🍺','🍷','🍸','☕','🌆','🏙️','🏡'];
    function buildEmojiPicker(){ if (!emojiPicker) return; emojiPicker.innerHTML=''; _emojis.forEach(e=>{ const b=document.createElement('button'); b.type='button'; b.className='ph-emoji-btn'; b.textContent = e; b.addEventListener('click', function(ev){ ev.stopPropagation(); if (!tagInput) return; insertAtCaret(tagInput, e); tagInput.focus(); emojiPicker.style.display='none'; }); emojiPicker.appendChild(b); }); }
    function insertAtCaret(input, text) { try { const start = input.selectionStart || 0; const end = input.selectionEnd || 0; const v = input.value; input.value = v.slice(0,start)+text+v.slice(end); input.selectionStart = input.selectionEnd = start + text.length; } catch(err) { input.value = input.value + text; } }
    if (emojiBtn) { emojiBtn.addEventListener('click', function(e){ e.preventDefault(); e.stopPropagation(); if (!emojiPicker || emojiPicker.style.display==='block') { if (emojiPicker) emojiPicker.style.display = 'none'; } else { buildEmojiPicker(); emojiPicker.style.display = 'block'; } }); document.addEventListener('click', function(){ if (emojiPicker) emojiPicker.style.display = 'none'; }); if (emojiPicker) { emojiPicker.addEventListener('click', function(e){ e.stopPropagation(); }); } }

    if (editBtn && editForm) {
        populateSelects(); renderEditCustom();
        editBtn.addEventListener('click', function(){ editForm.style.display = editForm.style.display === 'none' ? 'block' : 'none'; populateSelects(); renderEditCustom(); });
        addTagBtn.addEventListener('click', function(){ const v = tagInput.value.trim(); if (!v) return; if (!phStructured.custom.includes(v)) phStructured.custom.push(v); tagInput.value=''; renderEditCustom(); });
        tagInput.addEventListener('keydown', function(e){ if (e.key==='Enter') { e.preventDefault(); addTagBtn.click(); } });
        editTagsContainer.addEventListener('click', function(e){ if (e.target && e.target.classList.contains('ph-tag-remove')) { const tg=e.target.getAttribute('data-tag'); phStructured.custom=(phStructured.custom||[]).filter(x=>x!==tg); renderEditCustom(); } });
        document.getElementById('ph-cancel-profile-btn').addEventListener('click', function(){ editForm.style.display='none'; location.reload(); });
        document.getElementById('ph-save-profile-btn').addEventListener('click', function(){ const btn=this; btn.disabled=true; const desc = descInput.value.trim(); const estado = document.getElementById('ph-estado-select') ? document.getElementById('ph-estado-select').value : ''; const musica = Array.from(document.getElementById('ph-musica-select') ? document.getElementById('ph-musica-select').selectedOptions : []).map(o=>o.value); const bebida = Array.from(document.getElementById('ph-bebida-select') ? document.getElementById('ph-bebida-select').selectedOptions : []).map(o=>o.value); const city = document.getElementById('ph-city-input') ? document.getElementById('ph-city-input').value.trim() : ''; phStructured.estado_civil=estado; phStructured.estilo_musica=musica; phStructured.bebida=bebida; phStructured.city = city; fetch('<?php echo admin_url('admin-ajax.php'); ?>', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: new URLSearchParams({ action: 'ph_update_profile', nonce: '<?php echo wp_create_nonce('social_nonce'); ?>', user_id: '<?php echo esc_js($profile_id); ?>', description: desc, structured_tags: JSON.stringify(phStructured) }) }).then(r=>r.json()).then(data=>{ btn.disabled=false; if (data.success) { if (descView) descView.innerHTML = data.data.description || ''; if (data.data.tags_html && viewTagsContainer) viewTagsContainer.innerHTML = data.data.tags_html; location.reload(); } else { alert(data.data || 'Erro ao salvar perfil'); } }).catch(()=>{ btn.disabled=false; alert('Erro de rede'); }); });
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    // owner incoming requests handlers
    const acceptButtons = document.querySelectorAll('.ph-accept-btn');
    const declineButtons = document.querySelectorAll('.ph-decline-btn');

    function handleResponse(el, action) {
        const from = el.getAttribute('data-from');
        if (!from) return;
        el.disabled = true;
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: new URLSearchParams({ action: action, nonce: '<?php echo wp_create_nonce('social_nonce'); ?>', from_id: from })
        }).then(r=>r.json()).then(data=>{
            if (data.success) {
                const item = document.querySelector('.ph-request-item[data-from="' + from + '"]');
                if (item) item.remove();
                const fcount = document.getElementById('ph-friends-count');
                if (fcount && action === 'ph_accept_friend_request') {
                    fcount.textContent = parseInt(fcount.textContent||0,10) + 1;
                }
            } else {
                alert(data.data || 'Erro');
                el.disabled = false;
            }
        }).catch(()=>{ alert('Erro de rede'); el.disabled = false; });
    }

    acceptButtons.forEach(b=> b.addEventListener('click', function(){ handleResponse(this, 'ph_accept_friend_request'); }));
    declineButtons.forEach(b=> b.addEventListener('click', function(){ handleResponse(this, 'ph_decline_friend_request'); }));
});
</script>