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
    //variável para guardar erro inline (caso apareça)
    $inline_error = '';

    // Processar o formulário
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_register'])) {
        // Preparar sessão debug se necessário
        if (isset($_GET['debug']) && $_GET['debug'] == '1') {
            error_reporting(E_ALL); ini_set('display_errors', 1);
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            $_SESSION['registration_debug_errors'] = [];
            function reg_debug($msg) {
                $_SESSION['registration_debug_errors'][] = $msg;
            }
        } else {
            if (!function_exists('reg_debug')) { function reg_debug($msg) {} }
        }

        // Resgatar dados do passo 1
        $step1_data = isset($_POST['step1_data']) ? json_decode(stripslashes($_POST['step1_data']), true) : [];
        $display_name = isset($step1_data['display_name']) ? sanitize_text_field($step1_data['display_name']) : '';
        $email = isset($step1_data['email']) ? sanitize_email($step1_data['email']) : '';

        // Dados do passo 2
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

        // Validar campos
        if ($password !== $password_confirm) {
            wp_redirect(add_query_arg('error', 'password_mismatch', $_SERVER['REQUEST_URI']));
            exit;
        }
        if (email_exists($email)) {
            wp_redirect(add_query_arg('error', 'email_exists', $_SERVER['REQUEST_URI']));
            exit;
        }

        $user_id = wp_create_user($email, $password, $email); // user_login = email
        if (is_wp_error($user_id)) {
            reg_debug('Erro wp_create_user: ' . $user_id->get_error_message());
            wp_redirect(add_query_arg('error', 'registration_failed', $_SERVER['REQUEST_URI']));
            exit;
        }

        // Ajustar dados extras
        wp_update_user(['ID'=>$user_id, 'display_name'=>$display_name, 'role'=>'hunter']);

        // Processar upload da foto de perfil, se houver
        if (
            isset($_FILES['profile_photo'])
            && is_array($_FILES['profile_photo'])
            && !empty($_FILES['profile_photo']['name'])
            && $_FILES['profile_photo']['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            $file = $_FILES['profile_photo'];
            reg_debug('Arquivo recebido: ' . print_r($file, true));

            // Adicionando checagem de erro antes do upload
            if ($file['error'] !== UPLOAD_ERR_OK) {
                reg_debug('Erro no upload do arquivo: ' . $file['error']);
            } else {
                // Load WordPress file functions
                if (!function_exists('wp_handle_upload')) {
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                }
                if (!function_exists('media_handle_upload')) {
                    require_once(ABSPATH . 'wp-admin/includes/media.php');
                }
                if (!function_exists('wp_generate_attachment_metadata')) {
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                }

                // Verify file exists and is valid
                if (!isset($file['tmp_name']) || !file_exists($file['tmp_name'])) {
                    reg_debug('Arquivo temporário não encontrado ou inválido.');
                } else {
                    // Media upload: user must have upload_files capability; temporarily add capability if not admin
                    $user = get_user_by('id', $user_id);
                    if (!$user) {
                        reg_debug('Usuário não encontrado após criação.');
                    } else {
                        // Temporarily set current user for media_handle_upload
                        $old_user_id = get_current_user_id();
                        wp_set_current_user($user_id);
                        
                        $old_caps = [];
                        if (!user_can($user, 'upload_files')) {
                            $user->add_cap('upload_files');
                            $old_caps[] = 'upload_files';
                        }

                        // Tentar upload com tratamento de erros robusto
                        try {
                            // Verify the file key exists in $_FILES before calling media_handle_upload
                            if (!isset($_FILES['profile_photo']) || empty($_FILES['profile_photo']['tmp_name'])) {
                                reg_debug('profile_photo não está disponível em $_FILES para upload.');
                            } else {
                                $file_return = media_handle_upload('profile_photo', 0, [], ['test_form' => false]);
                                
                                if (is_wp_error($file_return)) {
                                    reg_debug('Falha no upload da imagem: ' . $file_return->get_error_message());
                                    // Log the error but don't fail registration
                                } else {
                                    // Salva o ID/media URL do anexo na user_meta do usuário
                                    update_user_meta($user_id, 'profile_image_id', $file_return);
                                    $image_url = wp_get_attachment_url($file_return);
                                    if ($image_url) {
                                        // Save to both meta keys for compatibility
                                        update_user_meta($user_id, 'custom_profile_image', esc_url_raw($image_url));
                                        update_user_meta($user_id, 'custom_avatar', esc_url_raw($image_url));
                                        // Set timestamp for cache busting
                                        update_user_meta($user_id, 'avatar_updated', time());
                                    }
                                    reg_debug('Upload da imagem bem-sucedido. ID: ' . $file_return);
                                }
                            }
                        } catch (\Throwable $e) {
                            reg_debug('Exceção ao fazer upload do arquivo: ' . $e->getMessage());
                            reg_debug('Stack trace: ' . $e->getTraceAsString());
                            // Don't fail registration if image upload fails
                        } catch (\Exception $e) {
                            reg_debug('Exceção (Exception) ao fazer upload do arquivo: ' . $e->getMessage());
                            // Don't fail registration if image upload fails
                        }

                        // Remove temporary capability
                        if (!empty($old_caps)) {
                            foreach ($old_caps as $cap) {
                                $user->remove_cap($cap);
                            }
                        }
                        
                        // Restore previous user (or set to 0 if no previous user)
                        if ($old_user_id) {
                            wp_set_current_user($old_user_id);
                        } else {
                            wp_set_current_user(0);
                        }
                    }
                }
            }
        } else {
            reg_debug('Nenhuma imagem de perfil enviada.');
        }

        // Auto login após cadastro
        wp_set_auth_cookie($user_id, true);
        wp_redirect(get_role_redirect_url($user_id));
        exit;
    }

    ob_start();

    // Exibição de erros da querystring (erros comuns)
    if (isset($_GET['error'])) {
        if ($_GET['error'] === 'email_exists') {
            // Special handling for email_exists error with buttons
            echo '<div class="registration-error" style="background: #fee; color: #c33; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #fcc;">';
            echo '<p style="margin: 0 0 1rem 0; font-weight: 600;">Esse email já está cadastrado.</p>';
            echo '<div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">';
            echo '<a href="' . esc_url(wp_lostpassword_url()) . '" class="btn-secondary" style="flex: 1; min-width: 150px; text-align: center; text-decoration: none; display: inline-block; padding: 0.75rem 1rem; border-radius: 0.5rem; background: #f5f5f5; color: #666; font-weight: 500; transition: all 0.3s ease;">Recuperar senha</a>';
            echo '<a href="' . esc_url(home_url('/login/')) . '" class="btn-primary" style="flex: 1; min-width: 150px; text-align: center; text-decoration: none; display: inline-block; padding: 0.75rem 1rem; border-radius: 0.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">Tentar logar novamente</a>';
            echo '</div>';
            echo '</div>';
        } else {
            // Other errors
            $error_messages = [
                'password_mismatch' => 'As senhas não coincidem. Por favor, tente novamente.',
                'registration_failed' => 'Erro ao criar conta. Por favor, tente novamente.'
            ];
            $error_msg = isset($error_messages[$_GET['error']]) ? $error_messages[$_GET['error']] : 'Ocorreu um erro. Por favor, tente novamente.';
            echo '<div class="registration-error" style="background: #fee; color: #c33; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #fcc;">' . esc_html($error_msg) . '</div>';
        }
    }

    // Mostrar qualquer erro PHP com debug
    if (isset($_GET['debug']) && $_GET['debug'] == '1') {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        // Verifica se há algum erro armazenado na sessão, exibe e limpa
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!empty($_SESSION['registration_debug_errors'])) {
            echo '<pre style="background:#fdd; border:1px solid #c00; color:#900; font-size:0.93em; padding:1em; margin-bottom:1em; border-radius:4px;">';
            echo "Erros de Debug (PHP):\n";
            foreach ($_SESSION['registration_debug_errors'] as $debugMsg) {
                echo htmlspecialchars($debugMsg) . "\n";
            }
            echo "</pre>";
            unset($_SESSION['registration_debug_errors']);
        }
    }
    ?>
    <div class="multi-step-registration" id="registrationForm">
        <!-- Step 1: Name and Photo -->
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
                        <input type="text" id="display_name" name="display_name" required>
                        <span class="omrs-input-label">Nome completo</span>
                    </label>
                </div>
                
                <div class="input_label">
                    <label for="email" class="input_group">
                        <input type="email" id="email" name="email" required>
                        <span class="omrs-input-label">Email</span>
                    </label>
                </div>
                
                <button type="button" class="btn-primary" id="nextToStep2">Continuar</button>
            </form>
        </div>

        <!-- Step 2: Password -->
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
        const step2Form = document.getElementById('step2Form');
        const nextBtn = document.getElementById('nextToStep2');
        const backBtn = document.getElementById('backToStep1');
        const photoInput = document.getElementById('profile_photo');
        const photoPreview = document.getElementById('photoPreview');
        const photoUploadLabel = document.getElementById('photoUploadLabel');
        const step1Data = document.getElementById('step1_data');
        
        // Make the label in step 1 trigger the file input in step 2
        photoUploadLabel.addEventListener('click', function(e) {
            // Only trigger if we're on step 1
            const step1 = document.querySelector('[data-step="1"]');
            if (step1 && step1.classList.contains('active')) {
                e.preventDefault();
                photoInput.click();
            }
        });
        
        // Photo preview - when file is selected, show preview
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                    photoPreview.classList.add('has-image');
                };
                reader.onerror = function() {
                    console.error('Error reading file for preview');
                };
                reader.readAsDataURL(file);
            } else {
                // Reset preview if no file
                photoPreview.innerHTML = '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" stroke="currentColor" stroke-width="2"/><path d="M20.59 22C20.59 18.13 16.74 15 12 15C7.26 15 3.41 18.13 3.41 22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg><span class="photo-placeholder-text">Adicionar foto</span>';
                photoPreview.classList.remove('has-image');
            }
        });
        
        // Step 1 to Step 2
        nextBtn.addEventListener('click', function() {
            const displayName = document.getElementById('display_name').value;
            const email = document.getElementById('email').value;
            
            if (!displayName || !email) {
                alert('Por favor, preencha todos os campos.');
                return;
            }
            
            // Store step 1 data
            const step1DataObj = {
                display_name: displayName,
                email: email
            };
            step1Data.value = JSON.stringify(step1DataObj);
            
            // File is already in the form input, no need to transfer
            // Move to step 2
            document.querySelector('[data-step="1"]').classList.remove('active');
            document.querySelector('[data-step="2"]').classList.add('active');
        });
        
        // Step 2 back to Step 1
        backBtn.addEventListener('click', function() {
            document.querySelector('[data-step="2"]').classList.remove('active');
            document.querySelector('[data-step="1"]').classList.add('active');
        });
        
        // Password confirmation validation
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