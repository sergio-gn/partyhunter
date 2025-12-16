<?php
// Load WordPress
require_once(dirname(__FILE__) . '/../../../wp-load.php');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting permission debug script...\n";

// Create a 'hunter' user
$username = 'debug_hunter_' . time();
$password = 'password';
$email = $username . '@example.com';

$user_id = wp_create_user($username, $password, $email);
if (is_wp_error($user_id)) {
    die("Failed to create test user: " . $user_id->get_error_message() . "\n");
}

$user = get_user_by('id', $user_id);
$user->set_role('hunter');

echo "Testing with user: " . $user->user_login . " (ID: " . $user->ID . "), Role: " . implode(', ', $user->roles) . "\n";

// Set current user
wp_set_current_user($user->ID);

// Check capabilities
$caps_to_check = [
    'edit_partyhunter_groups',
    'publish_partyhunter_groups',
    'create_partyhunter_groups', // This might not be a standard primitive but let's check
    'read'
];

echo "Checking capabilities:\n";
foreach ($caps_to_check as $cap) {
    // Note: user_can() triggers the user_has_cap filter
    $has_cap = user_can($user, $cap);
    echo " - $cap: " . ($has_cap ? "YES" : "NO") . "\n";
}

// Attempt to create group
echo "Calling create_group...\n";
$group_id = create_group($user->ID);

if ($group_id) {
    echo "SUCCESS: Group created with ID: " . $group_id . "\n";
} else {
    echo "FAILURE: create_group returned false.\n";
}

// Clean up
wp_delete_user($user_id);
if ($group_id) {
    $group_post = get_group_post_by_id($group_id);
    if ($group_post) {
        wp_delete_post($group_post->ID, true);
    }
}
echo "Cleanup done.\n";
