<?php
function custom_user_roles() {
    // Remove roles if they already exist to reset permissions
    remove_role('produtor');
    remove_role('hunter');

    // Add Produtor Role
    add_role(
        'produtor',
        'Produtor',
        [
            'read'          => true,
            'edit_posts'    => true, // Allow creating posts
            'edit_pages'    => true, // Allow creating pages
            'publish_posts' => true, // Allow publishing posts
            'publish_pages' => true, // Allow publishing pages
            'delete_posts'  => true,
        ]
    );

    // Add Hunter Role
    add_role(
        'hunter',
        'Hunter',
        [
            'read' => true, // Only profile access
        ]
    );
}
add_action('init', 'custom_user_roles');

// DISABLED: This function conflicts with custom_registration_form() in login_functions.php
// Both were processing the same form submission, causing critical errors
// The registration is now handled entirely by custom_registration_form() shortcode
/*
function process_custom_registration() {
    if (isset($_POST['custom_register'])) {
        // Get step 1 data
        $step1_data = json_decode(stripslashes($_POST['step1_data']), true);
        $display_name = sanitize_text_field($step1_data['display_name']);
        $email = sanitize_email($step1_data['email']);
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];
        $role = 'hunter'; // Default role

        // Validate passwords match
        if ($password !== $password_confirm) {
            wp_redirect(home_url('/registrar/?error=password_mismatch'));
            exit;
        }

        // Check if email already exists
        if (email_exists($email)) {
            wp_redirect(home_url('/registrar/?error=email_exists'));
            exit;
        }

        // Generate username from email
        $username = sanitize_user(substr($email, 0, strpos($email, '@')));
        $original_username = $username;
        $counter = 1;
        while (username_exists($username)) {
            $username = $original_username . $counter;
            $counter++;
        }

        // Create user
        $user_id = wp_create_user($username, $password, $email);
        if (is_wp_error($user_id)) {
            wp_redirect(home_url('/registrar/?error=registration_failed'));
            exit;
        }

        // Update user display name
        wp_update_user([
            'ID' => $user_id,
            'display_name' => $display_name,
            'role' => $role
        ]);

        // Handle profile photo upload
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_photo'];
            
            // Validate file type
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = wp_check_filetype($file['name']);
            
            if (in_array($file['type'], $allowed_types) || in_array($file_type['type'], $allowed_types)) {
                // Compress if needed (skip for GIFs)
                if (function_exists('compress_image') && $file['type'] !== 'image/gif' && $file_type['type'] !== 'image/gif') {
                    compress_image($file['tmp_name'], $file['tmp_name'], 75);
                }
                
                $upload = wp_handle_upload($file, ['test_form' => false]);
                
                if (!empty($upload['url']) && !isset($upload['error'])) {
                    update_user_meta($user_id, 'custom_avatar', $upload['url']);
                }
            }
        }

        // Auto-login the user
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
        
        // Redirect to user's author page
        wp_redirect(get_author_posts_url($user_id));
        exit;
    }
}
add_action('init', 'process_custom_registration');
*/

function restrict_page_access_by_role() {
    if (is_page('hunter') && !current_user_can('hunter') && !current_user_can('administrator')) {
        wp_redirect(home_url());
        exit;
    }

    if (is_page('produtor') && !current_user_can('produtor') && !current_user_can('administrator')) {
        wp_redirect(home_url());
        exit;
    }

    if (is_page('adicionar-evento') && !current_user_can('produtor') && !current_user_can('administrator')) {
        wp_redirect(home_url());
        exit;
    }
}

add_action('template_redirect', 'restrict_page_access_by_role');

function hide_admin_bar_for_non_admins() {
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'hide_admin_bar_for_non_admins');

function get_role_redirect_url() {
    $user = wp_get_current_user();
    
    // Check if the user has a specific role and return the corresponding URL
    if (in_array('hunter', $user->roles)) {
        return home_url($user->user_nicename);
    } elseif (in_array('produtor', $user->roles)) {
        return home_url('/produtor/');
    } else {
        return home_url('/login/'); // Default redirection if the user doesn't have the expected role
    }
}
/**
 * Get the correct redirect URL for user profile pages
 * Uses author page URL instead of /hunter/
 */
function ph_get_user_profile_redirect_url($user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    
    // First try to use the referer
    if (isset($_SERVER['HTTP_REFERER'])) {
        return remove_query_arg(['avatar_updated', 'avatar_error', 'cover_updated', 'cover_error'], $_SERVER['HTTP_REFERER']);
    }
    
    // Fallback to author page URL
    if ($user_id) {
        return get_author_posts_url($user_id);
    }
    
    // Last resort: home page
    return home_url();
}

/**
 * Save debug message to be displayed on page
 */
function ph_save_debug_message($message, $type = 'error') {
    $user_id = get_current_user_id();
    if (!$user_id) return;
    
    $debug_key = 'ph_upload_debug_' . $user_id;
    $existing = get_option($debug_key, []);
    if (!is_array($existing)) {
        $existing = [];
    }
    
    $existing[] = [
        'message' => $message,
        'type' => $type,
        'time' => time()
    ];
    
    // Keep only last 5 messages
    if (count($existing) > 5) {
        $existing = array_slice($existing, -5);
    }
    
    update_option($debug_key, $existing);
    return $debug_key;
}

/**
 * Get and clear debug messages
 */
function ph_get_debug_messages() {
    $user_id = get_current_user_id();
    if (!$user_id) return [];
    
    $debug_key = 'ph_upload_debug_' . $user_id;
    $messages = get_option($debug_key, []);
    
    // Clear messages after reading
    if (!empty($messages)) {
        delete_option($debug_key);
    }
    
    return is_array($messages) ? $messages : [];
}

function custom_user_avatar_upload() {
    // Include WordPress file upload functions
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    
    error_log('=== AVATAR UPLOAD DEBUG: Function called ===');
    error_log('AVATAR UPLOAD DEBUG: REQUEST_METHOD: ' . (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'NOT SET'));
    error_log('AVATAR UPLOAD DEBUG: POST data: ' . print_r($_POST, true));
    error_log('AVATAR UPLOAD DEBUG: FILES data: ' . print_r($_FILES, true));
    
    if (!is_user_logged_in()) {
        error_log('AVATAR UPLOAD DEBUG: User not logged in');
        return;
    }
    
    // Check if the form was submitted with the upload_avatar button
    if (!isset($_POST['upload_avatar']) || !isset($_FILES['user_avatar']) || empty($_FILES['user_avatar']['name'])) {
        error_log('AVATAR UPLOAD DEBUG: Form not submitted or file missing. POST upload_avatar: ' . (isset($_POST['upload_avatar']) ? 'yes' : 'no') . ', FILES user_avatar: ' . (isset($_FILES['user_avatar']) ? 'yes' : 'no'));
        if (isset($_FILES['user_avatar'])) {
            error_log('AVATAR UPLOAD DEBUG: FILES[user_avatar] details: ' . print_r($_FILES['user_avatar'], true));
        }
        return;
    }

    error_log('AVATAR UPLOAD DEBUG: Form submitted, starting upload process');

    // Prevent any output before redirect
    if (ob_get_level()) {
        ob_clean();
        error_log('AVATAR UPLOAD DEBUG: Output buffer cleaned');
    }

    try {
        $user_id = get_current_user_id();
        $file = $_FILES['user_avatar'];
        
        // Log PHP upload limits
        $upload_max = ini_get('upload_max_filesize');
        $post_max = ini_get('post_max_size');
        $memory_limit = ini_get('memory_limit');
        error_log('AVATAR UPLOAD DEBUG: PHP Limits - upload_max_filesize: ' . $upload_max . ', post_max_size: ' . $post_max . ', memory_limit: ' . $memory_limit);
        
        error_log('AVATAR UPLOAD DEBUG: User ID: ' . $user_id);
        error_log('AVATAR UPLOAD DEBUG: File info - Name: ' . $file['name'] . ', Size: ' . $file['size'] . ', Type: ' . $file['type'] . ', Error: ' . $file['error']);
        error_log('AVATAR UPLOAD DEBUG: File tmp_name: ' . (isset($file['tmp_name']) ? $file['tmp_name'] : 'NOT SET'));
        error_log('AVATAR UPLOAD DEBUG: File tmp_name exists: ' . (isset($file['tmp_name']) && file_exists($file['tmp_name']) ? 'YES' : 'NO'));

        if ($file['error'] !== UPLOAD_ERR_OK) {
            error_log('AVATAR UPLOAD DEBUG: File upload error code: ' . $file['error']);
            $redirect_url = ph_get_user_profile_redirect_url($user_id);
            if (!headers_sent()) {
                wp_safe_redirect($redirect_url . '?avatar_error=1');
                exit;
            } else {
                echo '<script>window.location.href="' . esc_js($redirect_url . '?avatar_error=1') . '";</script>';
                exit;
            }
        }

        error_log('AVATAR UPLOAD DEBUG: File upload error check passed');

        // Check file size (max 5MB)
        $max_size = 5 * 1024 * 1024; // 5MB in bytes
        if ($file['size'] > $max_size) {
            error_log('AVATAR UPLOAD DEBUG: File too large - ' . $file['size'] . ' bytes (max: ' . $max_size . ')');
            $redirect_url = ph_get_user_profile_redirect_url($user_id);
            if (!headers_sent()) {
                wp_safe_redirect($redirect_url . '?avatar_error=1');
                exit;
            } else {
                echo '<script>window.location.href="' . esc_js($redirect_url . '?avatar_error=1') . '";</script>';
                exit;
            }
        }

        error_log('AVATAR UPLOAD DEBUG: File size check passed - ' . $file['size'] . ' bytes');

        // Validate file type
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = wp_check_filetype($file['name']);
        
        error_log('AVATAR UPLOAD DEBUG: File type check - MIME: ' . $file['type'] . ', wp_check_filetype: ' . print_r($file_type, true));
        
        if (!in_array($file['type'], $allowed_types) && !in_array($file_type['type'], $allowed_types)) {
            error_log('AVATAR UPLOAD DEBUG: Invalid file type - MIME: ' . $file['type'] . ', wp_checkfiletype: ' . $file_type['type']);
            $redirect_url = ph_get_user_profile_redirect_url($user_id);
            if (!headers_sent()) {
                wp_safe_redirect($redirect_url . '?avatar_error=1');
                exit;
            } else {
                echo '<script>window.location.href="' . esc_js($redirect_url . '?avatar_error=1') . '";</script>';
                exit;
            }
        }

        error_log('AVATAR UPLOAD DEBUG: File type validation passed');

        // Delete old avatar file if exists
        $old_avatar = get_user_meta($user_id, 'custom_avatar', true);
        if ($old_avatar) {
            error_log('AVATAR UPLOAD DEBUG: Old avatar found: ' . $old_avatar);
            // Try multiple methods to get the file path
            $old_avatar_path = str_replace(site_url(), ABSPATH, $old_avatar);
            if (!file_exists($old_avatar_path)) {
                // Try with wp_upload_dir
                $upload_dir = wp_upload_dir();
                if (!$upload_dir['error']) {
                    $old_avatar_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $old_avatar);
                }
            }
            if (file_exists($old_avatar_path)) {
                $unlink_result = @unlink($old_avatar_path);
                error_log('AVATAR UPLOAD DEBUG: Old avatar deletion - Path: ' . $old_avatar_path . ', Result: ' . ($unlink_result ? 'success' : 'failed'));
            } else {
                error_log('AVATAR UPLOAD DEBUG: Old avatar file not found at: ' . $old_avatar_path);
            }
        } else {
            error_log('AVATAR UPLOAD DEBUG: No old avatar to delete');
        }

        // Compress if needed (skip for GIFs)
        if (function_exists('compress_image') && $file['type'] !== 'image/gif' && $file_type['type'] !== 'image/gif') {
            error_log('AVATAR UPLOAD DEBUG: Attempting image compression');
            $compress_result = compress_image($file['tmp_name'], $file['tmp_name'], 75);
            if ($compress_result === false) {
                error_log('AVATAR UPLOAD DEBUG: Image compression failed, continuing with original image');
            } else {
                error_log('AVATAR UPLOAD DEBUG: Image compression successful');
            }
        } else {
            error_log('AVATAR UPLOAD DEBUG: Skipping compression (GIF or function not available)');
        }

        // Handle upload with unique filename
        error_log('AVATAR UPLOAD DEBUG: Getting upload directory');
        $upload_dir = wp_upload_dir();
        error_log('AVATAR UPLOAD DEBUG: Upload directory - ' . print_r($upload_dir, true));
        
        if ($upload_dir['error']) {
            error_log('AVATAR UPLOAD DEBUG: Upload directory error: ' . $upload_dir['error']);
            throw new Exception('Upload directory error: ' . $upload_dir['error']);
        }

        // Check if upload directory is writable
        if (!is_writable($upload_dir['path'])) {
            error_log('AVATAR UPLOAD DEBUG: Upload directory not writable: ' . $upload_dir['path']);
            throw new Exception('Upload directory is not writable: ' . $upload_dir['path']);
        }
        
        error_log('AVATAR UPLOAD DEBUG: Upload directory is writable');
        
        $filename = 'avatar-' . $user_id . '-' . time() . '.' . $file_type['ext'];
        error_log('AVATAR UPLOAD DEBUG: Generated filename: ' . $filename);
        
        // Verify file still exists before upload
        if (!isset($file['tmp_name']) || !file_exists($file['tmp_name'])) {
            $debug_msg = 'AVATAR UPLOAD FAILED - Temporary file not found or missing. tmp_name: ' . (isset($file['tmp_name']) ? $file['tmp_name'] : 'NOT SET');
            error_log('AVATAR UPLOAD DEBUG: ' . $debug_msg);
            ph_save_debug_message($debug_msg, 'error');
            $redirect_url = ph_get_user_profile_redirect_url($user_id);
            if (!headers_sent()) {
                wp_safe_redirect($redirect_url . '?avatar_error=1&debug=1');
                exit;
            } else {
                echo '<script>window.location.href="' . esc_js($redirect_url . '?avatar_error=1&debug=1') . '";</script>';
                exit;
            }
        }
        
        error_log('AVATAR UPLOAD DEBUG: File verified, tmp_name exists: ' . $file['tmp_name']);
        
        // Verify wp_handle_upload function exists
        if (!function_exists('wp_handle_upload')) {
            $debug_msg = 'AVATAR UPLOAD FAILED - wp_handle_upload() function not available. WordPress file functions not loaded.';
            error_log('AVATAR UPLOAD DEBUG: ' . $debug_msg);
            ph_save_debug_message($debug_msg, 'error');
            $redirect_url = ph_get_user_profile_redirect_url($user_id);
            if (!headers_sent()) {
                wp_safe_redirect($redirect_url . '?avatar_error=1&debug=1');
                exit;
            } else {
                echo '<script>window.location.href="' . esc_js($redirect_url . '?avatar_error=1&debug=1') . '";</script>';
                exit;
            }
        }
        
        error_log('AVATAR UPLOAD DEBUG: wp_handle_upload function verified, calling with file: ' . print_r($file, true));
        
        $upload = wp_handle_upload($file, [
            'test_form' => false,
            'unique_filename_callback' => function($dir, $name, $ext) use ($filename) {
                error_log('AVATAR UPLOAD DEBUG: unique_filename_callback called - returning: ' . $filename);
                return $filename;
            }
        ]);
        
        error_log('AVATAR UPLOAD DEBUG: wp_handle_upload result: ' . print_r($upload, true));

        if (!empty($upload['url']) && !isset($upload['error'])) {
            error_log('AVATAR UPLOAD DEBUG: Upload successful, processing URL');
            // Ensure URL is absolute
            $avatar_url = $upload['url'];
            error_log('AVATAR UPLOAD DEBUG: Original URL: ' . $avatar_url);
            if (strpos($avatar_url, 'http') !== 0) {
                $avatar_url = site_url($avatar_url);
                error_log('AVATAR UPLOAD DEBUG: Converted to absolute URL: ' . $avatar_url);
            }
            
            // Update user meta with the new avatar URL
            error_log('AVATAR UPLOAD DEBUG: Updating user meta with URL: ' . $avatar_url);
            $updated = update_user_meta($user_id, 'custom_avatar', $avatar_url);
            error_log('AVATAR UPLOAD DEBUG: User meta update result: ' . ($updated ? 'updated' : 'not updated (same value)'));
            
            // Also clear any WordPress avatar cache
            delete_transient('avatar_' . md5($user_id));
            error_log('AVATAR UPLOAD DEBUG: Avatar cache cleared');
            
            // Force refresh by updating a timestamp in user meta
            update_user_meta($user_id, 'avatar_updated', time());
            error_log('AVATAR UPLOAD DEBUG: Avatar timestamp updated');
            
            // Redirect to refresh the page and show the new avatar
            $redirect_url = ph_get_user_profile_redirect_url($user_id);
            error_log('AVATAR UPLOAD DEBUG: Redirect URL: ' . $redirect_url);
            error_log('AVATAR UPLOAD DEBUG: Headers sent: ' . (headers_sent() ? 'yes' : 'no'));
            
            if (!headers_sent()) {
                wp_safe_redirect($redirect_url . '?avatar_updated=1&t=' . time());
                error_log('AVATAR UPLOAD DEBUG: Redirecting via wp_safe_redirect');
                exit;
            } else {
                // Fallback if headers already sent
                error_log('AVATAR UPLOAD DEBUG: Headers already sent, using JavaScript redirect');
                echo '<script>window.location.href="' . esc_js($redirect_url . '?avatar_updated=1&t=' . time()) . '";</script>';
                exit;
            }
        } else {
            // Handle upload error
            $error_msg = isset($upload['error']) ? $upload['error'] : 'Erro desconhecido';
            $upload_max = ini_get('upload_max_filesize');
            $post_max = ini_get('post_max_size');
            
            $debug_msg = 'AVATAR UPLOAD FAILED - ';
            $debug_msg .= 'Error: ' . $error_msg . ' | ';
            $debug_msg .= 'File: ' . $file['name'] . ' | ';
            $debug_msg .= 'Size: ' . $file['size'] . ' bytes | ';
            $debug_msg .= 'Type: ' . $file['type'] . ' | ';
            $debug_msg .= 'PHP upload_max: ' . $upload_max . ' | ';
            $debug_msg .= 'PHP post_max: ' . $post_max . ' | ';
            $debug_msg .= 'Upload result: ' . json_encode($upload);
            
            error_log('AVATAR UPLOAD DEBUG: Upload failed');
            error_log('AVATAR UPLOAD DEBUG: Error message: ' . $error_msg);
            error_log('AVATAR UPLOAD DEBUG: Upload array: ' . print_r($upload, true));
            error_log('AVATAR UPLOAD DEBUG: File array: ' . print_r($file, true));
            
            // Save debug message
            ph_save_debug_message($debug_msg, 'error');
            
            $redirect_url = ph_get_user_profile_redirect_url($user_id);
            if (!headers_sent()) {
                wp_safe_redirect($redirect_url . '?avatar_error=1&debug=1');
                exit;
            } else {
                echo '<script>window.location.href="' . esc_js($redirect_url . '?avatar_error=1&debug=1') . '";</script>';
                exit;
            }
        }
    } catch (Exception $e) {
        $debug_msg = 'AVATAR UPLOAD EXCEPTION: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine();
        error_log('Avatar upload exception: ' . $e->getMessage());
        error_log('Exception trace: ' . $e->getTraceAsString());
        ph_save_debug_message($debug_msg, 'error');
        
        $redirect_url = ph_get_user_profile_redirect_url($user_id);
        if (!headers_sent()) {
            wp_safe_redirect($redirect_url . '?avatar_error=1&debug=1');
            exit;
        } else {
            echo '<script>window.location.href="' . esc_js($redirect_url . '?avatar_error=1&debug=1') . '";</script>';
            exit;
        }
    } catch (Error $e) {
        $debug_msg = 'AVATAR UPLOAD FATAL ERROR: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine();
        error_log('Avatar upload fatal error: ' . $e->getMessage());
        error_log('Error trace: ' . $e->getTraceAsString());
        ph_save_debug_message($debug_msg, 'error');
        
        $redirect_url = ph_get_user_profile_redirect_url($user_id);
        if (!headers_sent()) {
            wp_safe_redirect($redirect_url . '?avatar_error=1&debug=1');
            exit;
        } else {
            echo '<script>window.location.href="' . esc_js($redirect_url . '?avatar_error=1&debug=1') . '";</script>';
            exit;
        }
    }
}
add_action('init', 'custom_user_avatar_upload');

function custom_user_cover_upload() {
    // Include WordPress file upload functions
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    
    error_log('=== COVER UPLOAD DEBUG: Function called ===');
    error_log('COVER UPLOAD DEBUG: REQUEST_METHOD: ' . (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'NOT SET'));
    error_log('COVER UPLOAD DEBUG: POST data: ' . print_r($_POST, true));
    error_log('COVER UPLOAD DEBUG: FILES data: ' . print_r($_FILES, true));
    
    if (!is_user_logged_in()) {
        error_log('COVER UPLOAD DEBUG: User not logged in');
        return;
    }
    
    if (!isset($_POST['upload_cover']) || !isset($_FILES['cover_image']) || empty($_FILES['cover_image']['name'])) {
        error_log('COVER UPLOAD DEBUG: Form not submitted or file missing. POST upload_cover: ' . (isset($_POST['upload_cover']) ? 'yes' : 'no') . ', FILES cover_image: ' . (isset($_FILES['cover_image']) ? 'yes' : 'no'));
        if (isset($_FILES['cover_image'])) {
            error_log('COVER UPLOAD DEBUG: FILES[cover_image] details: ' . print_r($_FILES['cover_image'], true));
        }
        return;
    }

    error_log('COVER UPLOAD DEBUG: Form submitted, starting upload process');

    // Prevent any output before redirect
    if (ob_get_level()) {
        ob_clean();
        error_log('COVER UPLOAD DEBUG: Output buffer cleaned');
    }

    try {
        $user_id = get_current_user_id();
        $file = $_FILES['cover_image'];
        
        // Log PHP upload limits
        $upload_max = ini_get('upload_max_filesize');
        $post_max = ini_get('post_max_size');
        $memory_limit = ini_get('memory_limit');
        error_log('COVER UPLOAD DEBUG: PHP Limits - upload_max_filesize: ' . $upload_max . ', post_max_size: ' . $post_max . ', memory_limit: ' . $memory_limit);
        
        error_log('COVER UPLOAD DEBUG: User ID: ' . $user_id);
        error_log('COVER UPLOAD DEBUG: File info - Name: ' . $file['name'] . ', Size: ' . $file['size'] . ', Type: ' . $file['type'] . ', Error: ' . $file['error']);
        error_log('COVER UPLOAD DEBUG: File tmp_name: ' . (isset($file['tmp_name']) ? $file['tmp_name'] : 'NOT SET'));
        error_log('COVER UPLOAD DEBUG: File tmp_name exists: ' . (isset($file['tmp_name']) && file_exists($file['tmp_name']) ? 'YES' : 'NO'));

        if ($file['error'] !== UPLOAD_ERR_OK) {
            error_log('COVER UPLOAD DEBUG: File upload error code: ' . $file['error']);
            $redirect_url = ph_get_user_profile_redirect_url($user_id);
            if (!headers_sent()) {
                wp_safe_redirect($redirect_url . '?cover_error=1');
                exit;
            } else {
                echo '<script>window.location.href="' . esc_js($redirect_url . '?cover_error=1') . '";</script>';
                exit;
            }
        }

        error_log('COVER UPLOAD DEBUG: File upload error check passed');

        // Check file size (max 10MB for cover images)
        $max_size = 10 * 1024 * 1024; // 10MB in bytes
        if ($file['size'] > $max_size) {
            error_log('COVER UPLOAD DEBUG: File too large - ' . $file['size'] . ' bytes (max: ' . $max_size . ')');
            $redirect_url = ph_get_user_profile_redirect_url($user_id);
            if (!headers_sent()) {
                wp_safe_redirect($redirect_url . '?cover_error=1');
                exit;
            } else {
                echo '<script>window.location.href="' . esc_js($redirect_url . '?cover_error=1') . '";</script>';
                exit;
            }
        }

        error_log('COVER UPLOAD DEBUG: File size check passed - ' . $file['size'] . ' bytes');

        // Validate file type
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = wp_check_filetype($file['name']);
        
        error_log('COVER UPLOAD DEBUG: File type check - MIME: ' . $file['type'] . ', wp_check_filetype: ' . print_r($file_type, true));
        
        if (!in_array($file['type'], $allowed_types) && !in_array($file_type['type'], $allowed_types)) {
            error_log('COVER UPLOAD DEBUG: Invalid file type - MIME: ' . $file['type'] . ', wp_checkfiletype: ' . $file_type['type']);
            $redirect_url = ph_get_user_profile_redirect_url($user_id);
            if (!headers_sent()) {
                wp_safe_redirect($redirect_url . '?cover_error=1');
                exit;
            } else {
                echo '<script>window.location.href="' . esc_js($redirect_url . '?cover_error=1') . '";</script>';
                exit;
            }
        }

        error_log('COVER UPLOAD DEBUG: File type validation passed');

        // Delete old cover image if exists
        $old_cover = get_user_meta($user_id, 'cover_image', true);
        if ($old_cover) {
            error_log('COVER UPLOAD DEBUG: Old cover found: ' . $old_cover);
            $old_cover_path = str_replace(site_url(), ABSPATH, $old_cover);
            if (!file_exists($old_cover_path)) {
                // Try with wp_upload_dir
                $upload_dir_check = wp_upload_dir();
                if (!$upload_dir_check['error']) {
                    $old_cover_path = str_replace($upload_dir_check['baseurl'], $upload_dir_check['basedir'], $old_cover);
                }
            }
            if (file_exists($old_cover_path)) {
                $unlink_result = @unlink($old_cover_path);
                error_log('COVER UPLOAD DEBUG: Old cover deletion - Path: ' . $old_cover_path . ', Result: ' . ($unlink_result ? 'success' : 'failed'));
            } else {
                error_log('COVER UPLOAD DEBUG: Old cover file not found at: ' . $old_cover_path);
            }
        } else {
            error_log('COVER UPLOAD DEBUG: No old cover to delete');
        }

        // Compress if needed (skip for GIFs)
        if (function_exists('compress_image') && $file['type'] !== 'image/gif' && $file_type['type'] !== 'image/gif') {
            error_log('COVER UPLOAD DEBUG: Attempting image compression');
            $compress_result = compress_image($file['tmp_name'], $file['tmp_name'], 75);
            if ($compress_result === false) {
                error_log('COVER UPLOAD DEBUG: Image compression failed, continuing with original image');
            } else {
                error_log('COVER UPLOAD DEBUG: Image compression successful');
            }
        } else {
            error_log('COVER UPLOAD DEBUG: Skipping compression (GIF or function not available)');
        }

        error_log('COVER UPLOAD DEBUG: Getting upload directory');
        $upload_dir = wp_upload_dir();
        error_log('COVER UPLOAD DEBUG: Upload directory - ' . print_r($upload_dir, true));
        
        if ($upload_dir['error']) {
            error_log('COVER UPLOAD DEBUG: Upload directory error: ' . $upload_dir['error']);
            throw new Exception('Upload directory error: ' . $upload_dir['error']);
        }

        // Check if upload directory is writable
        if (!is_writable($upload_dir['path'])) {
            error_log('COVER UPLOAD DEBUG: Upload directory not writable: ' . $upload_dir['path']);
            throw new Exception('Upload directory is not writable: ' . $upload_dir['path']);
        }

        error_log('COVER UPLOAD DEBUG: Upload directory is writable');
        
        // Verify file still exists before upload
        if (!isset($file['tmp_name']) || !file_exists($file['tmp_name'])) {
            $debug_msg = 'COVER UPLOAD FAILED - Temporary file not found or missing. tmp_name: ' . (isset($file['tmp_name']) ? $file['tmp_name'] : 'NOT SET');
            error_log('COVER UPLOAD DEBUG: ' . $debug_msg);
            ph_save_debug_message($debug_msg, 'error');
            $redirect_url = ph_get_user_profile_redirect_url($user_id);
            if (!headers_sent()) {
                wp_safe_redirect($redirect_url . '?cover_error=1&debug=1');
                exit;
            } else {
                echo '<script>window.location.href="' . esc_js($redirect_url . '?cover_error=1&debug=1') . '";</script>';
                exit;
            }
        }
        
        error_log('COVER UPLOAD DEBUG: File verified, tmp_name exists: ' . $file['tmp_name']);
        
        // Verify wp_handle_upload function exists
        if (!function_exists('wp_handle_upload')) {
            $debug_msg = 'COVER UPLOAD FAILED - wp_handle_upload() function not available. WordPress file functions not loaded.';
            error_log('COVER UPLOAD DEBUG: ' . $debug_msg);
            ph_save_debug_message($debug_msg, 'error');
            $redirect_url = ph_get_user_profile_redirect_url($user_id);
            if (!headers_sent()) {
                wp_safe_redirect($redirect_url . '?cover_error=1&debug=1');
                exit;
            } else {
                echo '<script>window.location.href="' . esc_js($redirect_url . '?cover_error=1&debug=1') . '";</script>';
                exit;
            }
        }
        
        error_log('COVER UPLOAD DEBUG: wp_handle_upload function verified, calling with file: ' . print_r($file, true));

        $upload = wp_handle_upload($file, [
            'test_form' => false,
            'mimes' => [
                'jpg|jpeg|jpe' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp'
            ]
        ]);
        
        error_log('COVER UPLOAD DEBUG: wp_handle_upload result: ' . print_r($upload, true));

        if (!empty($upload['url']) && !isset($upload['error'])) {
            error_log('COVER UPLOAD DEBUG: Upload successful, processing URL');
            $cover_url = $upload['url'];
            error_log('COVER UPLOAD DEBUG: Original URL: ' . $cover_url);
            // Ensure URL is absolute
            if (strpos($cover_url, 'http') !== 0) {
                $cover_url = site_url($cover_url);
                error_log('COVER UPLOAD DEBUG: Converted to absolute URL: ' . $cover_url);
            }
            
            error_log('COVER UPLOAD DEBUG: Updating user meta with URL: ' . $cover_url);
            $meta_updated = update_user_meta($user_id, 'cover_image', $cover_url);
            error_log('COVER UPLOAD DEBUG: User meta update result: ' . ($meta_updated ? 'updated' : 'not updated (same value)'));
            
            // Redirect to refresh the page and show the new cover
            $redirect_url = ph_get_user_profile_redirect_url($user_id);
            error_log('COVER UPLOAD DEBUG: Redirect URL: ' . $redirect_url);
            error_log('COVER UPLOAD DEBUG: Headers sent: ' . (headers_sent() ? 'yes' : 'no'));
            
            if (!headers_sent()) {
                error_log('COVER UPLOAD DEBUG: Redirecting via wp_safe_redirect');
                wp_safe_redirect($redirect_url . '?cover_updated=1');
                exit;
            } else {
                error_log('COVER UPLOAD DEBUG: Headers already sent, using JavaScript redirect');
                echo '<script>window.location.href="' . esc_js($redirect_url . '?cover_updated=1') . '";</script>';
                exit;
            }
        } else {
            // Handle upload error
            $error_msg = isset($upload['error']) ? $upload['error'] : 'Erro desconhecido';
            $upload_max = ini_get('upload_max_filesize');
            $post_max = ini_get('post_max_size');
            
            $debug_msg = 'COVER UPLOAD FAILED - ';
            $debug_msg .= 'Error: ' . $error_msg . ' | ';
            $debug_msg .= 'File: ' . $file['name'] . ' | ';
            $debug_msg .= 'Size: ' . $file['size'] . ' bytes | ';
            $debug_msg .= 'Type: ' . $file['type'] . ' | ';
            $debug_msg .= 'PHP upload_max: ' . $upload_max . ' | ';
            $debug_msg .= 'PHP post_max: ' . $post_max . ' | ';
            $debug_msg .= 'Upload result: ' . json_encode($upload);
            
            error_log('COVER UPLOAD DEBUG: Upload failed');
            error_log('COVER UPLOAD DEBUG: Error message: ' . $error_msg);
            error_log('COVER UPLOAD DEBUG: Upload array: ' . print_r($upload, true));
            error_log('COVER UPLOAD DEBUG: File array: ' . print_r($file, true));
            
            // Save debug message
            ph_save_debug_message($debug_msg, 'error');
            
            $redirect_url = ph_get_user_profile_redirect_url($user_id);
            if (!headers_sent()) {
                wp_safe_redirect($redirect_url . '?cover_error=1&debug=1');
                exit;
            } else {
                echo '<script>window.location.href="' . esc_js($redirect_url . '?cover_error=1&debug=1') . '";</script>';
                exit;
            }
        }
    } catch (Exception $e) {
        $debug_msg = 'COVER UPLOAD EXCEPTION: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine();
        error_log('Cover upload exception: ' . $e->getMessage());
        error_log('Exception trace: ' . $e->getTraceAsString());
        ph_save_debug_message($debug_msg, 'error');
        
        $redirect_url = ph_get_user_profile_redirect_url($user_id);
        if (!headers_sent()) {
            wp_safe_redirect($redirect_url . '?cover_error=1&debug=1');
            exit;
        } else {
            echo '<script>window.location.href="' . esc_js($redirect_url . '?cover_error=1&debug=1') . '";</script>';
            exit;
        }
    } catch (Error $e) {
        $debug_msg = 'COVER UPLOAD FATAL ERROR: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine();
        error_log('Cover upload fatal error: ' . $e->getMessage());
        error_log('Error trace: ' . $e->getTraceAsString());
        ph_save_debug_message($debug_msg, 'error');
        
        $redirect_url = ph_get_user_profile_redirect_url($user_id);
        if (!headers_sent()) {
            wp_safe_redirect($redirect_url . '?cover_error=1&debug=1');
            exit;
        } else {
            echo '<script>window.location.href="' . esc_js($redirect_url . '?cover_error=1&debug=1') . '";</script>';
            exit;
        }
    }
}
add_action('init', 'custom_user_cover_upload');

function compress_image($source, $destination, $quality) {
    // Check if GD library is available
    if (!function_exists('imagecreatefromjpeg') || !function_exists('imagejpeg')) {
        return false; // GD library not available
    }

    $info = getimagesize($source);

    if ($info === false) {
        return false; // Invalid image
    }

    try {
        $image = false;
        $save_function = null;

        if ($info['mime'] == 'image/jpeg') {
            $image = @imagecreatefromjpeg($source);
            $save_function = function($img, $dest, $qual) {
                return @imagejpeg($img, $dest, $qual);
            };
        } elseif ($info['mime'] == 'image/png') {
            $image = @imagecreatefrompng($source);
            $save_function = function($img, $dest, $qual) {
                return @imagepng($img, $dest, floor($qual / 10));
            };
        } elseif ($info['mime'] == 'image/webp' && function_exists('imagecreatefromwebp') && function_exists('imagewebp')) {
            $image = @imagecreatefromwebp($source);
            $save_function = function($img, $dest, $qual) {
                return @imagewebp($img, $dest, $qual);
            };
        } else {
            return false; // Unsupported format
        }

        if ($image === false || $save_function === null) {
            return false; // Failed to create image resource
        }

        $result = $save_function($image, $destination, $quality);
        if ($image) {
            @imagedestroy($image);
        }

        return $result ? $destination : false;
    } catch (Exception $e) {
        error_log('Image compression error: ' . $e->getMessage());
        if (isset($image) && $image) {
            @imagedestroy($image);
        }
        return false;
    } catch (Error $e) {
        error_log('Image compression fatal error: ' . $e->getMessage());
        if (isset($image) && $image) {
            @imagedestroy($image);
        }
        return false;
    }
}
function get_custom_user_avatar($avatar, $id_or_email, $size, $default, $alt) {
    $user = false;

    if (is_numeric($id_or_email)) {
        $user = get_user_by('id', $id_or_email);
    } elseif (is_object($id_or_email) && !empty($id_or_email->user_id)) {
        $user = get_user_by('id', $id_or_email->user_id);
    } elseif (is_string($id_or_email) && is_email($id_or_email)) {
        $user = get_user_by('email', $id_or_email);
    }

    if ($user) {
        $custom_avatar = get_user_meta($user->ID, 'custom_avatar', true);
        if ($custom_avatar && !empty($custom_avatar)) {
            // Get avatar update timestamp for cache busting
            $avatar_updated = get_user_meta($user->ID, 'avatar_updated', true);
            $avatar_url = esc_url($custom_avatar);
            
            // Add cache busting parameter if avatar was recently updated
            if ($avatar_updated && (time() - $avatar_updated) < 3600) {
                $separator = (strpos($avatar_url, '?') !== false) ? '&' : '?';
                $avatar_url .= $separator . 'v=' . $avatar_updated;
            }
            
            return '<img src="' . $avatar_url . '" width="' . $size . '" height="' . $size . '" alt="' . esc_attr($alt) . '" class="avatar avatar-' . $size . '">';
        }
    }

    return $avatar;
}
add_filter('get_avatar', 'get_custom_user_avatar', 10, 5);

/**
 * Handle AJAX request to save avatar shape
 */
function ajax_save_avatar_shape() {
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Você precisa estar logado.']);
        return;
    }
    
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'avatar_shape_nonce')) {
        wp_send_json_error(['message' => 'Erro de segurança. Por favor, recarregue a página e tente novamente.']);
        return;
    }
    
    $user_id = get_current_user_id();
    $shape = isset($_POST['shape']) ? sanitize_text_field($_POST['shape']) : '';
    
    if (empty($shape)) {
        wp_send_json_error(['message' => 'Forma não especificada.']);
        return;
    }
    
    // Validate shape
    $allowed_shapes = ['circle', 'square', 'star', 'heart', 'diamond', 'hexagon', 'octagon', 'triangle', 'pentagon', 'squircle', 'blob', 'badge', 'wavy'];
    if (!in_array($shape, $allowed_shapes)) {
        wp_send_json_error(['message' => 'Forma inválida.']);
        return;
    }
    
    // Save shape preference
    update_user_meta($user_id, 'avatar_shape', $shape);
    
    wp_send_json_success(['message' => 'Forma do avatar atualizada com sucesso!', 'shape' => $shape]);
}
add_action('wp_ajax_save_avatar_shape', 'ajax_save_avatar_shape');

/**
 * Get fresh nonce for avatar shape
 */
function ajax_get_avatar_shape_nonce() {
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Você precisa estar logado.']);
        return;
    }
    
    $nonce = wp_create_nonce('avatar_shape_nonce');
    wp_send_json_success(['nonce' => $nonce]);
}
add_action('wp_ajax_get_avatar_shape_nonce', 'ajax_get_avatar_shape_nonce');

/**
 * Handle admin-post requests for avatar upload
 */
function handle_admin_post_avatar_upload() {
    custom_user_avatar_upload();
}
add_action('admin_post_upload_avatar_image', 'handle_admin_post_avatar_upload');
add_action('admin_post_nopriv_upload_avatar_image', 'handle_admin_post_avatar_upload');

/**
 * Handle admin-post requests for cover upload
 */
function handle_admin_post_cover_upload() {
    custom_user_cover_upload();
}
add_action('admin_post_upload_cover_image', 'handle_admin_post_cover_upload');
add_action('admin_post_nopriv_upload_cover_image', 'handle_admin_post_cover_upload');

/**
 * Handle AJAX request to update avatar shape (alias for save_avatar_shape)
 */
function ajax_update_avatar_shape() {
    ajax_save_avatar_shape();
}
add_action('wp_ajax_update_avatar_shape', 'ajax_update_avatar_shape');







/**
 * Handle clean author URLs without /author/ base (e.g., /username/)
 * 
 * This uses template_redirect instead of rewrite rules, which means:
 * - No flushing required - works immediately for new users
 * - More reliable and efficient
 * - Only processes when WordPress hasn't matched the URL to a page/post/author
 */
function ph_handle_clean_author_urls() {
    // Don't process in admin area
    if (is_admin()) {
        return;
    }
    
    // If WordPress already found a page or post, don't override it
    if (is_page() || is_single()) {
        return;
    }
    
    // If it's already an author page via /author/ URL, don't interfere
    if (is_author() && isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/author/') !== false) {
        return;
    }
    
    // If it's matched something else (and not a 404), don't interfere
    if ((is_archive() || is_search() || is_home()) && !is_404()) {
        return;
    }
    
    // Get the current request path
    $request_uri = isset($_SERVER['REQUEST_URI']) ? trim($_SERVER['REQUEST_URI'], '/') : '';
    
    if (empty($request_uri)) {
        return;
    }
    
    // Remove query string if present
    $path_parts = explode('?', $request_uri);
    $clean_path = trim($path_parts[0], '/');
    
    // Skip if empty or contains slashes (we only handle top-level usernames like /username/)
    if (empty($clean_path) || strpos($clean_path, '/') !== false) {
        return;
    }
    
    // Skip common WordPress paths and reserved paths
    $skip_paths = ['wp-admin', 'wp-content', 'wp-includes', 'wp-login.php', 'wp-cron.php', 'xmlrpc.php'];
    if (in_array($clean_path, $skip_paths)) {
        return;
    }
    
    // IMPORTANT: Check if a page exists with this slug first
    // If it does, let WordPress handle it normally (don't override)
    $page = get_page_by_path($clean_path);
    
    if ($page) {
        // A page exists with this slug, don't override
        return;
    }
    
    // Check if a post exists with this slug (using get_page_by_path with post type)
    // This is a safeguard in case is_single() didn't catch it
    $post = get_page_by_path($clean_path, OBJECT, 'post');
    
    if ($post) {
        // A post exists with this slug, don't override
        return;
    }
    
    // Now check if this path matches a username (user_nicename)
    $user = get_user_by('slug', $clean_path);
    
    if ($user) {
        // Found a user - set up the author query
        global $wp_query, $wp;
        
        // Set query vars to make WordPress think this is an author page
        $wp->query_vars['author_name'] = $user->user_nicename;
        $wp_query->query_vars['author_name'] = $user->user_nicename;
        
        // Override the queried object
        $wp_query->queried_object = $user;
        $wp_query->queried_object_id = $user->ID;
        
        // Set query flags
        $wp_query->is_404 = false;
        $wp_query->is_author = true;
        $wp_query->is_archive = true;
        $wp_query->is_home = false;
        
        // Prevent 404 status
        status_header(200);
    }
}
add_action( 'template_redirect', 'ph_handle_clean_author_urls', 1 );
