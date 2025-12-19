<?php
function profile_picture($atts) {
    ob_start(); 
?>
        <div class="profile_picture_container">
            <?php if (is_user_logged_in()) : ?>
                <a href="<?php echo get_role_redirect_url(); ?>">
                    <div class="user-avatar">
                        <?php echo get_avatar(get_current_user_id(), 32); ?>
                    </div>
                </a>
                
            <?php else : ?>
              
                <a class="cta_button" href="/login/">
                    <svg width="18px" height="18px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 13H16C17.7107 13 19.1506 14.2804 19.3505 15.9795L20 21.5M8 13C5.2421 12.3871 3.06717 10.2687 2.38197 7.52787L2 6M8 13V18C8 19.8856 8 20.8284 8.58579 21.4142C9.17157 22 10.1144 22 12 22C13.8856 22 14.8284 22 15.4142 21.4142C16 20.8284 16 19.8856 16 18V17" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"/><circle cx="12" cy="6" r="4" stroke="#ffffff" stroke-width="1.5"/></svg>
                    <span class="cta_button_text">Login</span>
                </a>
            <?php endif; ?>
        </div>
<?php return ob_get_clean();}
add_shortcode('profile_picture', 'profile_picture');

function custom_registration_form() {
    if (is_user_logged_in()) {  
        return '<p>Você está logado.</p>';
    }

    // Variáveis para persistência de dados e erros
    $inline_error = '';
    $form_email = '';
    $form_name = '';

    // Processar o formulário
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_register'])) {
        
        // Debug (mantido conforme seu original)
        if (isset($_GET['debug']) && $_GET['debug'] == '1') {
            error_reporting(E_ALL); ini_set('display_errors', 1);
            if (session_status() !== PHP_SESSION_ACTIVE) session_start();
            $_SESSION['registration_debug_errors'] = [];
            if (!function_exists('reg_debug')) { function reg_debug($msg) { $_SESSION['registration_debug_errors'][] = $msg; } }
        } else {
            if (!function_exists('reg_debug')) { function reg_debug($msg) {} }
        }

        // Resgatar dados
        $step1_data = isset($_POST['step1_data']) ? json_decode(stripslashes($_POST['step1_data']), true) : [];
        $display_name = isset($step1_data['display_name']) ? sanitize_text_field($step1_data['display_name']) : '';
        $email = isset($step1_data['email']) ? sanitize_email($step1_data['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

        // Preenche variáveis para não perder o que foi digitado se der erro PHP
        $form_email = $email;
        $form_name = $display_name;

        // 1. Validação de Senha
        if ($password !== $password_confirm) {
            $inline_error = '<div class="registration-error" style="color: #c33; margin-bottom: 1rem;">As senhas não coincidem.</div>';
        }
        // 2. Validação de Email (Se existir, mostra o erro e botões)
        elseif (email_exists($email)) {
            $inline_error = '
            <div class="registration-error" style="background: #fee; color: #c33; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #fcc;">
                <p style="margin: 0 0 1rem 0; font-weight: 600;">Este e-mail já está cadastrado.</p>
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <a href="' . esc_url(wp_lostpassword_url()) . '" class="btn-secondary" style="flex: 1; text-align: center; padding: 0.5rem; border-radius:4px; background:#fff; border:1px solid #ccc; text-decoration:none; color:#333;">Recuperar senha</a>
                    <a href="' . esc_url(home_url('/login/')) . '" class="btn-primary" style="flex: 1; text-align: center; padding: 0.5rem; border-radius:4px; background:#667eea; color:#fff; text-decoration:none;">Fazer Login</a>   
                </div>
            </div>';
        } 
        else {
            // 3. Email livre: Criar Usuário
            $user_id = wp_create_user($email, $password, $email);

            if (is_wp_error($user_id)) {
                reg_debug('Erro wp_create_user:' . $user_id->get_error_message());
                $inline_error = '<div class="registration-error">Erro ao criar conta: ' . $user_id->get_error_message() . '</div>';
            } else {
                // SUCESSO AO CRIAR USUÁRIO
                
                // Atualizar Nome e Role
                wp_update_user(['ID' => $user_id, 'display_name' => $display_name, 'role' => 'hunter']);

                // Processar Upload da Foto (Try/Catch)
                if (
                    isset($_FILES['profile_photo'])
                    && is_array($_FILES['profile_photo'])
                    && !empty($_FILES['profile_photo']['name'])
                    && $_FILES['profile_photo']['error'] !== UPLOAD_ERR_NO_FILE
                ) {
                    try {
                        // Carregar bibliotecas do WP se necessário
                        if (!function_exists('media_handle_upload')) {
                            require_once(ABSPATH . 'wp-admin/includes/image.php');
                            require_once(ABSPATH . 'wp-admin/includes/file.php');
                            require_once(ABSPATH . 'wp-admin/includes/media.php');
                        }

                        // Verificar erro básico de upload
                        if ($_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK) {
                            throw new Exception('Erro no upload do arquivo: ' . $_FILES['profile_photo']['error']);
                        }

                        // Tentar processar o arquivo
                        $file_return = media_handle_upload('profile_photo', 0);

                        if (is_wp_error($file_return)) {
                            reg_debug('Falha no upload da imagem: ' . $file_return->get_error_message());
                        } else {
                            // Sucesso no upload: Salvar metadados
                            update_user_meta($user_id, 'profile_image_id', $file_return);
                            $image_url = wp_get_attachment_url($file_return);
                            
                            if ($image_url) {
                                update_user_meta($user_id, 'custom_avatar', esc_url_raw($image_url));
                                update_user_meta($user_id, 'custom_profile_image', esc_url_raw($image_url));
                                update_user_meta($user_id, 'avatar_updated', time());
                            }
                            reg_debug('Upload da imagem bem-sucedido. ID: ' . $file_return);
                        }

                    } catch (Throwable $e) {
                        reg_debug('Erro Fatal/Exception no upload: ' . $e->getMessage());
                    } catch (Exception $e) {
                        reg_debug('Exception no upload: ' . $e->getMessage());
                    }
                } // Fim do IF profile_photo

                // --- LOGIN E REDIRECT ---
                // Isso deve ficar fora do bloco de upload, mas dentro do bloco de sucesso do usuário
                wp_set_auth_cookie($user_id, true);
                wp_redirect(get_role_redirect_url($user_id));
                exit;

            } // Fim do Else (Sucesso create_user)
        } // Fim do Else (Email livre)
    } // Fim do IF POST

    // --- VIEW (HTML) ---
    ob_start();
    
    // Se houver erro inline (Senha ou Email Existente), exibe aqui
    if (!empty($inline_error)) {
        echo $inline_error;
    }
    
    ?>
    <div class="multi-step-registration" id="registrationForm">
        <div id="step1-error-container"></div>

        <div class="registration-step active" data-step="1">
            <div class="step-header">
                <h2>Crie sua conta</h2>
                <p class="step-indicator">Passo 1 de 2</p>
            </div>
            
            <form id="step1Form" enctype="multipart/form-data" autocomplete="off" onsubmit="return false">
                <div class="photo-upload-section">
                    <div class="photo-preview-container">
                        <label class="photo-upload-label" id="photoUploadLabel">
                            <div class="photo-preview" id="photoPreview">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" stroke="currentColor" stroke-width="2"/>
                                    <path d="M20.59 22C20.59 18.13 16.74 15 12 15C7.26 15 3.41 18.13 3.41 22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                <span class="photo-placeholder-text">Adicionar foto</span>
                            </div>
                        </label>
                    </div>
                    <p class="photo-hint">💡 Você pode fazer upload de GIFs também!</p>
                </div>
                
                <div class="input_label">
                    <label for="display_name" class="input_group">
                        <input type="text" id="display_name" name="display_name" value="<?php echo esc_attr($form_name); ?>" required>
                        <span class="omrs-input-label">Nome completo</span>
                    </label>
                </div>
                
                <div class="input_label">
                    <label for="email" class="input_group">
                        <input type="email" id="email" name="email" value="<?php echo esc_attr($form_email); ?>" required>
                        <span class="omrs-input-label">Email</span>
                    </label>
                </div>
                
                <button type="button" class="btn-primary" id="nextToStep2">Continuar</button>
            </form>
        </div>

        <div class="registration-step" data-step="2">
            <div class="step-header">
                <h2>Defina sua senha</h2>
                <p class="step-indicator">Passo 2 de 2</p>
            </div>
            
            <form id="step2Form" method="post" enctype="multipart/form-data" autocomplete="off">
                <div class="input_label">
                    <label for="password" class="input_group">
                        <input type="password" id="password" name="password" required minlength="6" autocomplete="new-password">
                        <span class="omrs-input-label">Senha</span>
                    </label>
                </div>
                
                <div class="input_label">
                    <label for="password_confirm" class="input_group">
                        <input type="password" id="password_confirm" name="password_confirm" required minlength="6" autocomplete="new-password">
                        <span class="omrs-input-label">Confirmar senha</span>
                    </label>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-secondary" id="backToStep1">Voltar</button>
                    <input type="hidden" name="role" value="hunter">
                    <input type="hidden" id="step1_data" name="step1_data">
                    <input type="file" id="profile_photo" name="profile_photo" accept="image/*,.gif" style="position: absolute; width: 1px; height: 1px; opacity: 0; left: -9999px;">
                    <button type="submit" name="custom_register" class="btn-primary">Finalizar cadastro</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const step1Form = document.getElementById('step1Form');
        const nextBtn = document.getElementById('nextToStep2');
        const backBtn = document.getElementById('backToStep1');
        const photoInput = document.getElementById('profile_photo');
        const photoPreview = document.getElementById('photoPreview');
        const photoUploadLabel = document.getElementById('photoUploadLabel');
        const step1Data = document.getElementById('step1_data');
        const errorContainer = document.getElementById('step1-error-container');

        // Photo Upload Logic (Mantido)
        photoUploadLabel.addEventListener('click', function(e) {
            const step1 = document.querySelector('[data-step="1"]');
            if (step1 && step1.classList.contains('active')) {
                e.preventDefault();
                photoInput.click();
            }
        });
        
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                    photoPreview.classList.add('has-image');
                };
                reader.readAsDataURL(file);
            }
        });

        // --- LÓGICA DO BOTÃO CONTINUAR (PRÉ-VALIDAÇÃO) ---
        nextBtn.addEventListener('click', function() {
            const displayName = document.getElementById('display_name').value;
            const email = document.getElementById('email').value;
            
            errorContainer.innerHTML = ''; // Limpa erros anteriores

            if (!displayName || !email) {
                alert('Por favor, preencha todos os campos.');
                return;
            }

            // UI de carregamento
            const originalText = nextBtn.innerText;
            nextBtn.innerText = 'Verificando...';
            nextBtn.disabled = true;

            // AJAX Check
            const formData = new FormData();
            formData.append('action', 'ph_check_email');
            formData.append('email', email);

            fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                nextBtn.innerText = originalText;
                nextBtn.disabled = false;

                if (!data.success) {
                    // EMAIL JÁ EXISTE! Mostra o HTML customizado
                    errorContainer.innerHTML = `
                    <div class="registration-error" style="background: #fee; color: #c33; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #fcc; animation: fadeIn 0.3s;">
                        <p style="margin: 0 0 1rem 0; font-weight: 600;">${data.data.message}</p>
                        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                            <a href="<?php echo home_url('/recuperar-senha/'); ?>" target="_blank" class="btn-secondary" style="flex: 1; text-align: center; padding: 0.75rem; border-radius: 0.5rem; background: #fff; border: 1px solid #ddd; color: #666; font-weight: 500; text-decoration:none;">Recuperar senha</a>
                            <a href="<?php echo esc_url(home_url('/login/')); ?>" class="btn-primary" style="flex: 1; text-align: center; padding: 0.75rem; border-radius: 0.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 600; text-decoration:none;">Fazer Login</a>
                        </div>
                    </div>`;
                    
                    // Rola suavemente para o erro
                    errorContainer.scrollIntoView({behavior: "smooth"});
                } else {
                    // SUCESSO! Passa para o Step 2
                    const step1DataObj = {
                        display_name: displayName,
                        email: email
                    };
                    step1Data.value = JSON.stringify(step1DataObj);
                    
                    document.querySelector('[data-step="1"]').classList.remove('active');
                    document.querySelector('[data-step="2"]').classList.add('active');
                }
            })
            .catch(err => {
                console.error(err);
                nextBtn.innerText = originalText;
                nextBtn.disabled = false;
                alert('Erro ao verificar conexão.');
            });
        });
        
        // Botão Voltar
        backBtn.addEventListener('click', function() {
            document.querySelector('[data-step="2"]').classList.remove('active');
            document.querySelector('[data-step="1"]').classList.add('active');
        });
        
        // Confirmação de Senha
        document.getElementById('password_confirm').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirm = this.value;
            if (password !== confirm) {
                this.setCustomValidity('As senhas não coincidem');
            } else {
                this.setCustomValidity('');
            }
        });
    });
    </script>
    <?php 
    return ob_get_clean();
}
add_shortcode('custom_registration_form', 'custom_registration_form');

add_action('wp_ajax_nopriv_ph_simple_recovery', 'ph_simple_recovery');
add_action('wp_ajax_ph_simple_recovery', 'ph_simple_recovery');

function ph_simple_recovery() {
    // Verifica nonce de segurança (opcional, mas recomendado)
    // check_ajax_referer('ph-recovery-nonce', 'security');

    $user_login = isset($_POST['user_login']) ? sanitize_text_field($_POST['user_login']) : '';

    if (empty($user_login)) {
        wp_send_json_error(['message' => 'Digite seu e-mail ou nome de usuário.']);
    }

    // Tenta recuperar os dados do usuário pelo email ou login
    if (is_email($user_login)) {
        $user = get_user_by('email', $user_login);
    } else {
        $user = get_user_by('login', $user_login);
    }

    if (!$user) {
        // Por segurança, você pode dizer "Email enviado" mesmo se não existir
        // Mas para facilitar o debug agora:
        wp_send_json_error(['message' => 'Usuário não encontrado.']);
    }

    // --- A MÁGICA NATIVA DO WORDPRESS ---
    // Isso gera a chave de reset e envia o e-mail padrão do WP
    // (Aquele que vai para wp-login.php?action=rp...)
    $result = retrieve_password($user->user_login);

    if (is_wp_error($result)) {
        wp_send_json_error(['message' => $result->get_error_message()]);
    } else {
        wp_send_json_success(['message' => 'Verifique seu e-mail! Enviamos um link para redefinir sua senha.']);
    }
}
function simple_recovery_form() {
    if (is_user_logged_in()) {
        return '<p class="text-center">Você já está logado.</p>';
    }
    ob_start();
    ?>
    
    <div class="multi-step-registration" id="simpleRecoveryForm">
        <div id="recovery-msg" style="display:none; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; text-align: center;"></div>

        <div class="registration-step active">
            <div class="step-header">
                <h2>Recuperar Senha</h2>
                <p class="step-indicator">Enviaremos um link para o seu e-mail</p>
            </div>
            
            <form id="phRecoveryForm" onsubmit="return false">
                <div class="input_label">
                    <label for="user_login" class="input_group">
                        <input type="text" id="user_login" name="user_login" required>
                        <span class="omrs-input-label">E-mail ou Usuário</span>
                    </label>
                </div>
                
                <div class="form-actions">
                     <a href="<?php echo home_url('/login/'); ?>" class="btn-secondary" style="text-align:center; text-decoration:none;">Voltar</a>
                    <button type="button" class="btn-primary" id="sendLinkBtn">Enviar Link</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('sendLinkBtn');
        const msg = document.getElementById('recovery-msg');
        const input = document.getElementById('user_login');

        btn.addEventListener('click', function() {
            if (!input.value) {
                alert('Preencha o campo.');
                return;
            }

            const originalText = btn.innerText;
            btn.innerText = 'Enviando...';
            btn.disabled = true;
            msg.style.display = 'none';

            const formData = new FormData();
            formData.append('action', 'ph_simple_recovery');
            formData.append('user_login', input.value);

            fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.innerText = originalText;
                btn.disabled = false;

                msg.style.display = 'block';
                msg.innerText = data.data.message;

                if (data.success) {
                    msg.style.background = '#e6fffa';
                    msg.style.color = '#2c7a7b';
                    msg.style.border = '1px solid #b2f5ea';
                    // Limpa o campo
                    input.value = '';
                } else {
                    msg.style.background = '#fee';
                    msg.style.color = '#c33';
                    msg.style.border = '1px solid #fcc';
                }
            })
            .catch(err => {
                btn.innerText = originalText;
                btn.disabled = false;
                alert('Erro de conexão.');
            });
        });
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('simple_recovery_form', 'simple_recovery_form');

function become_producer_button() {
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        if (in_array('produtor', (array) $user->roles)) {
            return '<p>Você já é um produtor.</p>';
        } else {
            return '<a href="' . site_url('/aplicar-produtor') . '" class="button">Você é um produtor? Aplique aqui.</a>';
        }
    }
}
add_shortcode('become_producer_button', 'become_producer_button');

function custom_login_form() {
    if (is_user_logged_in()) {
        return '<p>You are already logged in. <a href="'. esc_url(home_url('?custom_logout=1')) .'">Logout</a></p>';
    }

    ob_start();

    // Check if the login failed
    if (isset($_GET['login']) && $_GET['login'] == 'failed') {
        echo '<p style="color: #c92048; font-weight: bold;">Usuário ou Senha Incorretos.</p>';
    }
    
    ?>
    <form action="" method="post">
        <div class="input_label">
            <label for="log" class="input_group">
                <input type="text" name="log" required />
                <span class="omrs-input-label">Email</span>
            </label>
        </div>
        <div class="input_label">
            <label for="pwd" class="input_group">
                <input type="password" name="pwd" required />
                <span class="omrs-input-label">Password</span>
            </label>
        </div>
        <div class="input_label">
            <input type="submit" name="custom_login" value="Login" />
        </div>
    </form>
    <div>
        Ainda não tem conta?
        <a href="<?php echo home_url('/registrar/'); ?>">Registrar</a>
    </div>
    <div>
        <a href="<?php echo home_url('/recuperar-senha/'); ?>">Esqueceu sua senha?</a>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('custom_login_form', 'custom_login_form');

function display_logout_button() {
    // Check if the user is logged in
    if (is_user_logged_in()) {
        ?>
        <form action="<?php echo esc_url(home_url('?custom_logout=1')); ?>" method="post">
            <button class="grey_btn" type="submit">Sair</button>
        </form>
        <?php
    }
}

function custom_logout_redirect() {
    wp_logout();
    wp_redirect(home_url());
    exit();
}
add_action('init', function () {
    if (isset($_GET['custom_logout'])) {
        custom_logout_redirect();
    }
});

function custom_login_handler() {
    // Check if the login form was submitted
    if (isset($_POST['custom_login'])) {
        // Get credentials from the form
        $credentials = array(
            'user_login'    => $_POST['log'],
            'user_password' => $_POST['pwd'],
            'remember'      => isset($_POST['remember_me']) ? true : false,
        );

        // Attempt to sign the user in
        $user = wp_signon($credentials, false);

        // If there's an error with login
        if (is_wp_error($user)) {
            // Redirect to the login page with error
            wp_redirect(home_url('/login/?login=failed'));
            exit;
        } else {
            // Redirect to hunter page
            wp_redirect(home_url($user->user_nicename));
            exit;
        }
    }
}
add_action('init', 'custom_login_handler');

function block_wp_login() {
    // Allow AJAX requests to pass through
    if (defined('DOING_AJAX') && DOING_AJAX) {
        return;
    }
    
    // Also check if this is an admin-ajax.php request
    if (strpos($_SERVER['REQUEST_URI'], 'admin-ajax.php') !== false) {
        return;
    }
    
    // If the user is not logged in and trying to access wp-login.php or wp-admin, redirect them
    if (!is_user_logged_in()) {
        return; // Let non-logged-in users access the login page
    }

    // Check if the user is on wp-login.php or wp-admin, and if they are not an administrator
    if ((strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false || strpos($_SERVER['REQUEST_URI'], 'wp-admin') !== false) && !current_user_can('administrator')) {
        wp_redirect(home_url('/login/')); // Redirect non-admin users to the custom login page
        exit;
    }
}
add_action('init', 'block_wp_login');




function ph_auth_modal_shortcode($atts = array()) {
    ob_start();
    ?>
    <div class="ph-auth-overlay">
    <div class="ph-auth-modal">
        <div class="ph-auth-tabs">
            <div class="ph-auth-tab active" onclick="switchAuthTab('login')">Entrar</div>
            <div class="ph-auth-tab" onclick="switchAuthTab('register')">Cadastrar</div>
        </div>
        
        <div class="ph-auth-body">
            <form id="ph-login-form" style="display:block;">
                <h3 style="margin-bottom: 15px; text-align: center;">Bem-vindo de volta!</h3>
                <div class="ph-form-group">
                    <label>Usuário ou Email</label>
                    <input type="text" name="log" class="ph-form-input" required>
                </div>
                <div class="ph-form-group">
                    <label>Senha</label>
                    <input type="password" name="pwd" class="ph-form-input" required>
                </div>
                <button type="submit" class="ph-btn-submit">Entrar no Grupo</button>
                <div id="login-msg" style="margin-top:10px; text-align:center; font-size:0.9rem;"></div>
            </form>
            <form id="ph-register-form" style="display:none;">
                <h3 style="margin-bottom: 15px; text-align: center;">Crie sua conta grátis</h3>
                <div class="ph-form-group">
                    <label>Nome de Usuário</label>
                    <input type="text" name="user_login" class="ph-form-input" required>
                </div>
                <div class="ph-form-group">
                    <label>Email</label>
                    <input type="email" name="user_email" class="ph-form-input" required>
                </div>
                <div class="ph-form-group">
                    <label>Senha</label>
                    <input type="password" name="user_pass" class="ph-form-input" required>
                </div>
                <button type="submit" class="ph-btn-submit">Criar conta e Entrar</button>
                <div id="register-msg" style="margin-top:10px; text-align:center; font-size:0.9rem;"></div>
            </form>
        </div>
    </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        
        // --- 1. Lógica do Modal de Auth (Abas) ---
        window.switchAuthTab = function(tab) {
            const loginForm = document.getElementById('ph-login-form');
            const registerForm = document.getElementById('ph-register-form');
            const tabs = document.querySelectorAll('.ph-auth-tab');
            const msgDivs = document.querySelectorAll('#login-msg, #register-msg');
            
            // Limpa mensagens ao trocar de aba
            msgDivs.forEach(div => { div.style.display = 'none'; div.innerHTML = ''; });

            if (tab === 'login') {
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
                tabs[0].classList.add('active');
                tabs[1].classList.remove('active');
            } else {
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
                tabs[0].classList.remove('active');
                tabs[1].classList.add('active');
            }
        }

        // --- 2. AJAX Login Handler ---
        const loginForm = document.getElementById('ph-login-form');
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = loginForm.querySelector('button[type="submit"]');
                const msg = document.getElementById('login-msg');
                const originalText = btn.textContent;
                
                // UI Loading state
                btn.disabled = true;
                btn.textContent = 'Verificando...';
                msg.style.display = 'none';
                const fd = new FormData(loginForm);
                fd.append('action', 'ph_login');
                // Usamos o nonce social_nonce que já existe no seu código, ou criamos um específico
                fd.append('nonce', '<?php echo wp_create_nonce('ph_auth_nonce'); ?>');

                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: fd
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        msg.style.display = 'block';
                        msg.style.color = 'green';
                        msg.innerHTML = 'Login realizado! Entrando...';
                        // SUCESSO: Recarrega a página para sair do modo "Blur" e mostrar o grupo
                        window.location.reload();
                    } else {
                        btn.disabled = false;
                        btn.textContent = originalText;
                        msg.style.display = 'block';
                        msg.style.color = 'red';
                        msg.innerHTML = data.data.message || 'Erro ao fazer login.';
                    }
                })
                .catch(error => {
                    btn.disabled = false;
                    btn.textContent = originalText;
                    msg.style.display = 'block';
                    msg.style.color = 'red';
                    msg.innerHTML = 'Erro de conexão. Tente novamente.';
                });
            });
        }

        // --- 3. AJAX Register Handler ---
        const registerForm = document.getElementById('ph-register-form');
        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = registerForm.querySelector('button[type="submit"]');
                const msg = document.getElementById('register-msg');
                const originalText = btn.textContent;
                
                // UI Loading state
                btn.disabled = true;
                btn.textContent = 'Criando conta...';
                msg.style.display = 'none';

                const fd = new FormData(registerForm);
                fd.append('action', 'ph_register');
                fd.append('nonce', '<?php echo wp_create_nonce('ph_auth_nonce'); ?>');

                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: fd
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        msg.style.display = 'block';
                        msg.style.color = 'green';
                        msg.innerHTML = 'Conta criada! Acessando grupo...';
                        // SUCESSO: O PHP já logou o usuário (wp_set_auth_cookie).
                        // Recarregamos a página e ele já estará logado.
                        window.location.reload();
                    } else {
                        btn.disabled = false;
                        btn.textContent = originalText;
                        msg.style.display = 'block';
                        msg.style.color = 'red';
                        msg.innerHTML = data.data.message || 'Erro ao criar conta.';
                    }
                })
                .catch(error => {
                    btn.disabled = false;
                    btn.textContent = originalText;
                    msg.style.display = 'block';
                    msg.style.color = 'red';
                    msg.innerHTML = 'Erro de conexão.';
                });
            });
        }

        // --- 4. Função Copiar Link (Global) ---
        window.copyGroupLink = function(text) {
            if(navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => {
                    alert('Link copiado para a área de transferência!');
                });
            } else {
                var textArea = document.createElement("textarea");
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand("Copy");
                textArea.remove();
                alert('Link copiado!');
            }
        }

        // --- 5. Lógica de Comentários e Busca (Seus scripts originais) ---
        // (Mantendo a compatibilidade com o que você já tinha)
        const commentForm = document.getElementById('group-comment-form');
        if (commentForm) {
            commentForm.addEventListener('submit', function(e){
                e.preventDefault();
                const content = document.getElementById('group-comment-content').value;
                const imageInput = document.getElementById('group-comment-image');
                const msg = document.getElementById('group-comment-msg');
                msg.style.display = 'none';

                const fd = new FormData();
                fd.append('action', 'create_group_comment');
                fd.append('nonce', '<?php echo wp_create_nonce('social_nonce'); ?>');
                fd.append('group_id', '<?php echo isset($group_id) ? esc_js($group_id) : ""; ?>');
                fd.append('content', content);
                if (imageInput && imageInput.files && imageInput.files[0]) fd.append('image', imageInput.files[0]);

                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: fd
                }).then(r=>r.json()).then(data=>{
                    if (data.success) {
                        msg.style.display = 'block'; msg.style.color = '#155724'; msg.textContent = 'Comentário publicado.';
                        document.getElementById('group-comment-content').value = '';
                        if (imageInput) imageInput.value = '';
                        if (data.data && data.data.comment_html) {
                            const list = document.getElementById('ph-group-posts-list');
                            if (list) {
                                const wrapper = document.createElement('div');
                                wrapper.innerHTML = data.data.comment_html;
                                if (list.firstChild) list.insertBefore(wrapper.firstChild, list.firstChild);
                                else list.appendChild(wrapper.firstChild);
                            }
                        }
                    } else {
                        msg.style.display = 'block'; msg.style.color = '#721c24'; msg.textContent = data.data || 'Erro ao publicar.';
                    }
                }).catch(err=>{
                    msg.style.display = 'block'; msg.style.color = '#721c24'; msg.textContent = 'Erro de rede.';
                });
            });
            
            // Delegate delete buttons
            const postsList = document.getElementById('ph-group-posts-list');
            if(postsList) {
                postsList.addEventListener('click', function(e){
                    const btn = e.target.closest('.ph-delete-comment-btn');
                    if (!btn) return;
                    const commentId = btn.dataset.commentId;
                    if (!confirm('Deseja realmente excluir?')) return;
                    btn.disabled = true;
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        headers: {'Content-Type':'application/x-www-form-urlencoded'},
                        body: new URLSearchParams({ action: 'delete_group_comment', nonce: '<?php echo wp_create_nonce('social_nonce'); ?>', comment_id: commentId })
                    }).then(r=>r.json()).then(data=>{
                        btn.disabled = false;
                        if (data.success) {
                            const el = document.querySelector('.group-comment[data-comment-id="' + commentId + '"]');
                            if (el) el.remove();
                        } else { alert(data.data || 'Erro ao excluir'); }
                    }).catch(()=>{ btn.disabled = false; alert('Erro de rede'); });
                });
            }
        }
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('ph_auth_modal', 'ph_auth_modal_shortcode');

function ph_check_email_availability() {
    // Verifica se o email foi enviado
    if (!isset($_POST['email'])) {
        wp_send_json_error(['message' => 'Email não fornecido']);
    }

    $email = sanitize_email($_POST['email']);

    // Verifica validação básica de formato de email
    if (!is_email($email)) {
         wp_send_json_error(['message' => 'Formato de e-mail inválido.']);
    }

    if (email_exists($email)) {
        // Retorna ERRO se o email JÁ EXISTE (para impedir o cadastro)
        wp_send_json_error([
            'exists' => true,
            'message' => 'Este e-mail já está cadastrado.'
        ]);
    } else {
        // Sucesso: Email está livre para uso
        wp_send_json_success();
    }
}
// Registra os hooks para usuários logados e não logados
add_action('wp_ajax_nopriv_ph_check_email', 'ph_check_email_availability');
add_action('wp_ajax_ph_check_email', 'ph_check_email_availability');