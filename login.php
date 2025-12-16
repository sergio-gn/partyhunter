<?php
/* Template Name: Login */

get_header();

get_template_part( 'parts/navigation' );
?>
<style>
    .container.login{
        height: 80vh;

        .row{
            display: flex;
            justify-content: center;
            align-content: center;
            flex-wrap: wrap;
            height: 100%;
        }
    }
    .form_box{
        width: 25vw;
        background: white;
        border-radius: .5rem;
        display: flex;
        flex-direction: column;
        padding: 1rem 2rem;
        box-shadow: rgb(177 119 251 / 40%) 5px 5px, rgb(177 119 251 / 30%) 10px 10px, rgb(177 119 251 / 20%) 15px 15px, rgb(177 119 251 / 10%) 20px 20px, rgb(177 119 251 / 5%) 25px 25px;
    }
    form{
        .input_label{
            display: flex;
            flex-direction: column;
        }
        label{
            position:relative;
        }
        input[type="submit"]{
            background: var(--main_colour);
            border: none;
            padding: 1rem;
            color: var(--white_tone);
            border-radius: .5rem;
            cursor: pointer;
            margin: 1rem;
        }
    }

    /* Input*/
    .input_group > input, .input_group > select{
        border: none;
        border-bottom: 0.125rem solid var(--black_tone);
        width: 100%;
        height: 2rem;
        font-size: 1.0625rem;
        padding-left: 0.875rem;
        line-height: 147.6%;
        padding-top: 0.825rem;
        padding-bottom: 0.5rem;
        margin: 1rem 0;
    }

    .input_group > input:focus{
        outline: none;
    }

    .input_group > .omrs-input-label{
        position: absolute;
        top: 0.9375rem;
        left: 0.875rem;
        line-height: 147.6%;
        color: var(--grey_tone);
        transition: top .2s;
    }

    .input_group > svg {
        position: absolute;
        top: 0.9375rem;
        right: 0.875rem;
        fill: var(--grey_tone);
    }

    .input_group > .omrs-input-helper{
        font-size: 0.9375rem;
        color: var(--grey_tone);
        letter-spacing: 0.0275rem;
        margin: 0.125rem 0.875rem;
    }

    .input_group > input:hover{
        background: rgba(171, 73, 224, 0.12);
        border-color: var(--grey_tone);
    }

    .input_group > input:focus + .omrs-input-label,
    .input_group > input:valid + .omrs-input-label{
        top: 0;
        font-size: 0.9375rem;
        margin-bottom: 32px;;
    }

    .input_group:not(.omrs-input-danger) > input:focus + .omrs-input-label{
        color: var(--main_colour);
    }

    .input_group:not(.omrs-input-danger) > input:focus{
        border-color: var(--main_colour);
    }

    .input_group:not(.omrs-input-danger) > input:focus ~ svg{
        fill: var(--black_tone);
    }
    @media(orientation:portrait){
        .form_box{
            width: 75vw;
        }
    }
    .row{
        display: flex;
        flex: 1;
        align-items: center;
        justify-content: center;
    }
    .profile_card{
        background: #fff;
        padding: 1rem;
        border-radius: .5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
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
            border-radius: 10rem;
            overflow: hidden;
        }
        img{
            object-fit: cover;
        }
    }
    .upload_profile_image_popup{
        height: 10rem;
        padding: 4rem;

        .upload_profile_image_popup_align{
            display:flex;
            flex-direction:column;
            justify-content: center;
            height: 100%;
        }
        .close_button{
            position: absolute;
            height: 2rem;
            right: .5rem;
            top: .5rem;
        }
        .upload_profile_form{
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .custom-file-label {
            background-color: #6a0dad;
            color: white;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            display: inline-block;
        }
        .custom-file-label:hover {
            background-color: #580c91;
        }
        #file-name {
            margin-left: 10px;
        }
    }
</style>
<section>
    <div class="container login">
        <?php if (is_user_logged_in()) : ?>
            <div class="row">
                <div class="profile_card">
                    <button data-open-modal class="change-avatar" id="change-avatar">
                        <div class="pencil_icon">
                            <svg viewBox="0 0 16 16" width="16px" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M8.29289 3.70711L1 11V15H5L12.2929 7.70711L8.29289 3.70711Z" fill="#b177fb"></path> <path d="M9.70711 2.29289L13.7071 6.29289L15.1716 4.82843C15.702 4.29799 16 3.57857 16 2.82843C16 1.26633 14.7337 0 13.1716 0C12.4214 0 11.702 0.297995 11.1716 0.828428L9.70711 2.29289Z" fill="#b177fb"></path></g></svg>
                        </div>
                        <div class="user-avatar">
                            <?php echo get_avatar(get_current_user_id(), 96); ?>
                        </div>
                    </button>
                    
                    <h1>Bem vindo! <br> <?php echo wp_get_current_user()->user_login; ?></h1>
                    <?php if (is_user_logged_in()) : ?>
                        <!-- Logout Button -->
                        <?php display_logout_button(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="form_box">
                <?php echo do_shortcode('[custom_login_form]')?>
            </div>
        </div>
    </div>
</section>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let errorDiv = document.querySelector('.login-error');
        if (errorDiv) {
            alert(errorDiv.textContent);
        }
    });
</script>
<?php get_footer(); ?>