<?php
/*
 * Plugin Name: Clasnet CKAN Integration
 * Description: Custom endpoint to enable CKAN integration into WordPress using application passwords.
 * Version: 1.0
 * Author: MOVZX (movzx@yahoo.com)
 * Author URI: https://github.com/MOVZX
 * Network: true
 * License: GPL2
 */

add_action('rest_api_init', function () {
    register_rest_route('ckan/v1', '/login', array(
        'methods' => 'POST',
        'callback' => 'ckan_autologin',
        'permission_callback' => '__return_true'
    ));
});

function ckan_autologin($request) {
    $username = $request->get_param('username');
    $app_password = $request->get_param('app_password');

    // Verify the application password
    $user = wp_authenticate_application_password(null, $username, $app_password);

    if (is_wp_error($user)) {
        return new WP_Error('invalid_credentials', 'Password aplikasi salah!', array(
            'status' => 403
        ));
    }

    // Log the user in and set session cookies
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true, true);

    return array(
        'status' => 'success',
        'message' => 'Logged in successfully'
    );
}
