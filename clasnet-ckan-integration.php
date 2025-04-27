<?php
/*
 * Plugin Name: Clasnet CKAN Integration
 * Description: Custom endpoint to enable CKAN integration into WordPress using application passwords. Includes E-Book post type with categories and tags.
 * Version: 1.0
 * Author: MOVZX (movzx@yahoo.com)
 * Author URI: https://github.com/MOVZX
 * Network: true
 * License: GPL2
 */

// Register REST API endpoint for CKAN login
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

// Register E-Book custom post type
add_action('init', 'register_ebook_post_type');
function register_ebook_post_type() {
    $labels = array(
        'name'                  => _x('E-Books', 'Post Type General Name', 'text_domain'),
        'singular_name'         => _x('E-Book', 'Post Type Singular Name', 'text_domain'),
        'menu_name'             => __('E-Books', 'text_domain'),
        'name_admin_bar'        => __('E-Book', 'text_domain'),
        'archives'              => __('E-Book Archives', 'text_domain'),
        'attributes'            => __('E-Book Attributes', 'text_domain'),
        'parent_item_colon'     => __('Parent E-Book:', 'text_domain'),
        'all_items'             => __('All E-Books', 'text_domain'),
        'add_new_item'          => __('Add New E-Book', 'text_domain'),
        'add_new'               => __('Add New', 'text_domain'),
        'new_item'              => __('New E-Book', 'text_domain'),
        'edit_item'             => __('Edit E-Book', 'text_domain'),
        'update_item'           => __('Update E-Book', 'text_domain'),
        'view_item'             => __('View E-Book', 'text_domain'),
        'view_items'            => __('View E-Books', 'text_domain'),
        'search_items'          => __('Search E-Book', 'text_domain'),
        'not_found'             => __('Not found', 'text_domain'),
        'not_found_in_trash'    => __('Not found in Trash', 'text_domain'),
        'featured_image'        => __('Featured Image', 'text_domain'),
        'set_featured_image'    => __('Set featured image', 'text_domain'),
        'remove_featured_image' => __('Remove featured image', 'text_domain'),
        'use_featured_image'    => __('Use as featured image', 'text_domain'),
        'insert_into_item'      => __('Insert into E-Book', 'text_domain'),
        'uploaded_to_this_item' => __('Uploaded to this E-Book', 'text_domain'),
        'items_list'            => __('E-Books list', 'text_domain'),
        'items_list_navigation' => __('E-Books list navigation', 'text_domain'),
        'filter_items_list'     => __('Filter E-Books list', 'text_domain'),
    );

    $args = array(
        'label'                 => __('E-Book', 'text_domain'),
        'description'           => __('Custom post type for E-Books', 'text_domain'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'comments'),
        'taxonomies'            => array('ebook_category', 'ebook_tag'), // Custom taxonomies
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-book',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
    );

    register_post_type('ebook', $args);
}

// Register E-Book Category taxonomy
add_action('init', 'register_ebook_category_taxonomy');
function register_ebook_category_taxonomy() {
    $labels = array(
        'name'                       => _x('E-Book Categories', 'Taxonomy General Name', 'text_domain'),
        'singular_name'              => _x('E-Book Category', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name'                  => __('Categories', 'text_domain'),
        'all_items'                  => __('All Categories', 'text_domain'),
        'parent_item'                => __('Parent Category', 'text_domain'),
        'parent_item_colon'          => __('Parent Category:', 'text_domain'),
        'new_item_name'              => __('New Category Name', 'text_domain'),
        'add_new_item'               => __('Add New Category', 'text_domain'),
        'edit_item'                  => __('Edit Category', 'text_domain'),
        'update_item'                => __('Update Category', 'text_domain'),
        'view_item'                  => __('View Category', 'text_domain'),
        'separate_items_with_commas' => __('Separate categories with commas', 'text_domain'),
        'add_or_remove_items'        => __('Add or remove categories', 'text_domain'),
        'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
        'popular_items'              => __('Popular Categories', 'text_domain'),
        'search_items'               => __('Search Categories', 'text_domain'),
        'not_found'                  => __('Not Found', 'text_domain'),
        'no_terms'                   => __('No categories', 'text_domain'),
        'items_list'                 => __('Categories list', 'text_domain'),
        'items_list_navigation'      => __('Categories list navigation', 'text_domain'),
    );

    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );

    register_taxonomy('ebook_category', array('ebook'), $args);
}

// Register E-Book Tag taxonomy
add_action('init', 'register_ebook_tag_taxonomy');
function register_ebook_tag_taxonomy() {
    $labels = array(
        'name'                       => _x('E-Book Tags', 'Taxonomy General Name', 'text_domain'),
        'singular_name'              => _x('E-Book Tag', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name'                  => __('Tags', 'text_domain'),
        'all_items'                  => __('All Tags', 'text_domain'),
        'parent_item'                => __('Parent Tag', 'text_domain'),
        'parent_item_colon'          => __('Parent Tag:', 'text_domain'),
        'new_item_name'              => __('New Tag Name', 'text_domain'),
        'add_new_item'               => __('Add New Tag', 'text_domain'),
        'edit_item'                  => __('Edit Tag', 'text_domain'),
        'update_item'                => __('Update Tag', 'text_domain'),
        'view_item'                  => __('View Tag', 'text_domain'),
        'separate_items_with_commas' => __('Separate tags with commas', 'text_domain'),
        'add_or_remove_items'        => __('Add or remove tags', 'text_domain'),
        'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
        'popular_items'              => __('Popular Tags', 'text_domain'),
        'search_items'               => __('Search Tags', 'text_domain'),
        'not_found'                  => __('Not Found', 'text_domain'),
        'no_terms'                   => __('No tags', 'text_domain'),
        'items_list'                 => __('Tags list', 'text_domain'),
        'items_list_navigation'      => __('Tags list navigation', 'text_domain'),
    );

    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );

    register_taxonomy('ebook_tag', array('ebook'), $args);
}