<?php
/*
 * Plugin Name: Clasnet CKAN Integration
 * Description: Endpoint khusus untuk mengintegrasikan WordPress ke CKAN menggunakan REST API.
 * Mendukung jenis posting E-Book, Infografis, dan Videografis dengan kategori/tag khusus,
 * sistem tiket dataset, dan manajemen slider visual.
 * Version: 1.2
 * Author: MOVZX (movzx@yahoo.com)
 * Author URI: https://github.com/MOVZX
 * Network: true
 * License: GPLv2
 */

// Registrasi Custom Post Types
add_action('init', 'register_ebook_post_type', 10);
add_action('init', 'register_infografis_post_type', 10);
add_action('init', 'register_videografis_post_type', 10);

// Registrasi Taksonomi
add_action('init', 'register_ebook_category_taxonomy', 20);
add_action('init', 'register_ebook_tag_taxonomy', 20);
add_action('init', 'register_infografis_category_taxonomy', 20);
add_action('init', 'register_infografis_tag_taxonomy', 20);
add_action('init', 'register_videografis_category_taxonomy', 20);
add_action('init', 'register_videografis_tag_taxonomy', 20);

// Menu Admin
add_action('admin_menu', 'clasnet_add_ticket_menu');
add_action('admin_menu', 'clasnet_add_website_config_menu');
add_action('admin_enqueue_scripts', 'clasnet_enqueue_admin_scripts');

// REST API Endpoints
add_action('rest_api_init', 'clasnet_register_ticket_api_routes');
add_action('rest_api_init', 'clasnet_register_website_config_api_routes');

/* ------------------------------------------------- E-Book: Mulai ------------------------------------------------- */
/**
 * Daftarkan jenis pos baru: E-Book
 *
 * Fitur:
 * - Dukungan: Judul, Editor, Gambar Unggulan, Ringkasan, Bidang Kustom, Komentar
 * - Taksonomi: ebook_category (hirarkis), ebook_tag (non-hirarkis)
 * - REST API: Tersedia di `/wp-json/wp/v2/ebook`
 *
 * @since 1.0
 */
function register_ebook_post_type()
{
    $labels = array(
        'name'                  => _x('E-Book', 'Nama Umum Jenis Posting', 'text_domain'),
        'singular_name'         => _x('E-Book', 'Nama Tunggal Jenis Posting', 'text_domain'),
        'menu_name'             => __('E-Book', 'text_domain'),
        'name_admin_bar'        => __('E-Book', 'text_domain'),
        'archives'              => __('Arsip E-Book', 'text_domain'),
        'attributes'            => __('Atribut E-Book', 'text_domain'),
        'parent_item_colon'     => __('E-Book Induk:', 'text_domain'),
        'all_items'             => __('Semua E-Book', 'text_domain'),
        'add_new_item'          => __('Tambah E-Book Baru', 'text_domain'),
        'add_new'               => __('Tambah Baru', 'text_domain'),
        'new_item'              => __('E-Book Baru', 'text_domain'),
        'edit_item'             => __('Edit E-Book', 'text_domain'),
        'update_item'           => __('Perbarui E-Book', 'text_domain'),
        'view_item'             => __('Lihat E-Book', 'text_domain'),
        'view_items'            => __('Lihat E-Book', 'text_domain'),
        'search_items'          => __('Cari E-Book', 'text_domain'),
        'not_found'             => __('Tidak ditemukan', 'text_domain'),
        'not_found_in_trash'    => __('Tidak ditemukan di Sampah', 'text_domain'),
        'featured_image'        => __('Gambar Unggulan', 'text_domain'),
        'set_featured_image'    => __('Atur Gambar Unggulan', 'text_domain'),
        'remove_featured_image' => __('Hapus Gambar Unggulan', 'text_domain'),
        'use_featured_image'    => __('Gunakan sebagai Gambar Unggulan', 'text_domain'),
        'insert_into_item'      => __('Masukkan ke E-Book', 'text_domain'),
        'uploaded_to_this_item' => __('Diunggah ke E-Book ini', 'text_domain'),
        'items_list'            => __('Daftar E-Book', 'text_domain'),
        'items_list_navigation' => __('Navigasi Daftar E-Book', 'text_domain'),
        'filter_items_list'     => __('Filter Daftar E-Book', 'text_domain'),
    );

    $args = array(
        'label'                 => __('E-Book', 'text_domain'),
        'description'           => __('Jenis posting khusus untuk E-Book', 'text_domain'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'comments', 'author'),
        'taxonomies'            => array('ebook_category', 'ebook_tag'), // Taksonomi khusus
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
        'show_in_rest'          => true, // Enable REST API
        'rest_base'             => 'ebook', // REST route: /wp-json/wp/v2/ebook
    );

    register_post_type('ebook', $args);
}

/**
 * Daftarkan taksonomi khusus untuk kategori E-Book
 *
 * Taksonomi hirarkis untuk kategori E-Book dengan dukungan REST API.
 *
 * @since 1.0
 */
function register_ebook_category_taxonomy()
{
    $labels = array(
        'name'                       => _x('Kategori E-Book', 'Nama Umum Taksonomi', 'text_domain'),
        'singular_name'              => _x('Kategori E-Book', 'Nama Tunggal Taksonomi', 'text_domain'),
        'menu_name'                  => __('Kategori', 'text_domain'),
        'all_items'                  => __('Semua Kategori', 'text_domain'),
        'parent_item'                => __('Kategori Induk', 'text_domain'),
        'parent_item_colon'          => __('Kategori Induk:', 'text_domain'),
        'new_item_name'              => __('Nama Kategori Baru', 'text_domain'),
        'add_new_item'               => __('Tambah Kategori Baru', 'text_domain'),
        'edit_item'                  => __('Edit Kategori', 'text_domain'),
        'update_item'                => __('Perbarui Kategori', 'text_domain'),
        'view_item'                  => __('Lihat Kategori', 'text_domain'),
        'separate_items_with_commas' => __('Pisahkan kategori dengan koma', 'text_domain'),
        'add_or_remove_items'        => __('Tambah atau hapus kategori', 'text_domain'),
        'choose_from_most_used'      => __('Pilih dari yang paling sering digunakan', 'text_domain'),
        'popular_items'              => __('Kategori Populer', 'text_domain'),
        'search_items'               => __('Cari Kategori', 'text_domain'),
        'not_found'                  => __('Tidak ditemukan', 'text_domain'),
        'no_terms'                   => __('Tidak ada kategori', 'text_domain'),
        'items_list'                 => __('Daftar Kategori', 'text_domain'),
        'items_list_navigation'      => __('Navigasi Daftar Kategori', 'text_domain'),
    );

    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'show_in_rest'               => true, // Enable REST API
        'rest_base'                  => 'ebook_category', // REST route: /wp-json/wp/v2/ebook_category
    );

    register_taxonomy('ebook_category', array('ebook'), $args);
}

/**
 * Daftarkan taksonomi khusus untuk tag E-Book
 *
 * Taksonomi non-hirarkis untuk tag E-Book dengan dukungan REST API.
 *
 * @since 1.0
 */
function register_ebook_tag_taxonomy()
{
    $labels = array(
        'name'                       => _x('Tag E-Book', 'Nama Umum Taksonomi', 'text_domain'),
        'singular_name'              => _x('Tag E-Book', 'Nama Tunggal Taksonomi', 'text_domain'),
        'menu_name'                  => __('Tag', 'text_domain'),
        'all_items'                  => __('Semua Tag', 'text_domain'),
        'parent_item'                => __('Tag Induk', 'text_domain'),
        'parent_item_colon'          => __('Tag Induk:', 'text_domain'),
        'new_item_name'              => __('Nama Tag Baru', 'text_domain'),
        'add_new_item'               => __('Tambah Tag Baru', 'text_domain'),
        'edit_item'                  => __('Edit Tag', 'text_domain'),
        'update_item'                => __('Perbarui Tag', 'text_domain'),
        'view_item'                  => __('Lihat Tag', 'text_domain'),
        'separate_items_with_commas' => __('Pisahkan tag dengan koma', 'text_domain'),
        'add_or_remove_items'        => __('Tambah atau hapus tag', 'text_domain'),
        'choose_from_most_used'      => __('Pilih dari yang paling sering digunakan', 'text_domain'),
        'popular_items'              => __('Tag Populer', 'text_domain'),
        'search_items'               => __('Cari Tag', 'text_domain'),
        'not_found'                  => __('Tidak ditemukan', 'text_domain'),
        'no_terms'                   => __('Tidak ada tag', 'text_domain'),
        'items_list'                 => __('Daftar Tag', 'text_domain'),
        'items_list_navigation'      => __('Navigasi Daftar Tag', 'text_domain'),
    );

    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'show_in_rest'               => true, // Enable REST API
        'rest_base'                  => 'ebook_tag', // REST route: /wp-json/wp/v2/ebook_tag
    );

    register_taxonomy('ebook_tag', array('ebook'), $args);
}

/* ------------------------------------------------ E-Book: Selesai ------------------------------------------------ */



/* ----------------------------------------------- Infografis: Mulai ----------------------------------------------- */
/**
 * Daftarkan jenis pos baru: Infografis
 *
 * Fitur:
 * - Dukungan: Judul, Editor, Gambar Unggulan, Ringkasan, Bidang Kustom, Komentar
 * - Taksonomi: infografis_category (hirarkis), infografis_tag (non-hirarkis)
 * - REST API: Tersedia di `/wp-json/wp/v2/infografis`
 *
 * @since 1.0
 */
function register_infografis_post_type()
{
    $labels = array(
        'name'                  => _x('Infografis', 'Nama Umum Jenis Posting', 'text_domain'),
        'singular_name'         => _x('Infografis', 'Nama Tunggal Jenis Posting', 'text_domain'),
        'menu_name'             => __('Infografis', 'text_domain'),
        'name_admin_bar'        => __('Infografis', 'text_domain'),
        'archives'              => __('Arsip Infografis', 'text_domain'),
        'attributes'            => __('Atribut Infografis', 'text_domain'),
        'parent_item_colon'     => __('Infografis Induk:', 'text_domain'),
        'all_items'             => __('Semua Infografis', 'text_domain'),
        'add_new_item'          => __('Tambah Infografis Baru', 'text_domain'),
        'add_new'               => __('Tambah Baru', 'text_domain'),
        'new_item'              => __('Infografis Baru', 'text_domain'),
        'edit_item'             => __('Edit Infografis', 'text_domain'),
        'update_item'           => __('Perbarui Infografis', 'text_domain'),
        'view_item'             => __('Lihat Infografis', 'text_domain'),
        'view_items'            => __('Lihat Infografis', 'text_domain'),
        'search_items'          => __('Cari Infografis', 'text_domain'),
        'not_found'             => __('Tidak ditemukan', 'text_domain'),
        'not_found_in_trash'    => __('Tidak ditemukan di Sampah', 'text_domain'),
        'featured_image'        => __('Gambar Unggulan', 'text_domain'),
        'set_featured_image'    => __('Atur Gambar Unggulan', 'text_domain'),
        'remove_featured_image' => __('Hapus Gambar Unggulan', 'text_domain'),
        'use_featured_image'    => __('Gunakan sebagai Gambar Unggulan', 'text_domain'),
        'insert_into_item'      => __('Masukkan ke Infografis', 'text_domain'),
        'uploaded_to_this_item' => __('Diunggah ke Infografis ini', 'text_domain'),
        'items_list'            => __('Daftar Infografis', 'text_domain'),
        'items_list_navigation' => __('Navigasi Daftar Infografis', 'text_domain'),
        'filter_items_list'     => __('Filter Daftar Infografis', 'text_domain'),
    );

    $args = array(
        'label'                 => __('Infografis', 'text_domain'),
        'description'           => __('Jenis posting khusus untuk Infografis', 'text_domain'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'comments', 'author'),
        'taxonomies'            => array('infografis_category', 'infografis_tag'), // Taksonomi khusus
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 6,
        'menu_icon'             => 'dashicons-format-image',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true, // Enable REST API
        'rest_base'             => 'infografis', // REST route: /wp-json/wp/v2/infografis
    );

    register_post_type('infografis', $args);
}

/**
 * Daftarkan taksonomi khusus untuk kategori Infografis
 *
 * Taksonomi hirarkis untuk kategori Infografis dengan dukungan REST API.
 *
 * @since 1.0
 */
function register_infografis_category_taxonomy()
{
    $labels = array(
        'name'                       => _x('Kategori Infografis', 'Nama Umum Taksonomi', 'text_domain'),
        'singular_name'              => _x('Kategori Infografis', 'Nama Tunggal Taksonomi', 'text_domain'),
        'menu_name'                  => __('Kategori', 'text_domain'),
        'all_items'                  => __('Semua Kategori', 'text_domain'),
        'parent_item'                => __('Kategori Induk', 'text_domain'),
        'parent_item_colon'          => __('Kategori Induk:', 'text_domain'),
        'new_item_name'              => __('Nama Kategori Baru', 'text_domain'),
        'add_new_item'               => __('Tambah Kategori Baru', 'text_domain'),
        'edit_item'                  => __('Edit Kategori', 'text_domain'),
        'update_item'                => __('Perbarui Kategori', 'text_domain'),
        'view_item'                  => __('Lihat Kategori', 'text_domain'),
        'separate_items_with_commas' => __('Pisahkan kategori dengan koma', 'text_domain'),
        'add_or_remove_items'        => __('Tambah atau hapus kategori', 'text_domain'),
        'choose_from_most_used'      => __('Pilih dari yang paling sering digunakan', 'text_domain'),
        'popular_items'              => __('Kategori Populer', 'text_domain'),
        'search_items'               => __('Cari Kategori', 'text_domain'),
        'not_found'                  => __('Tidak ditemukan', 'text_domain'),
        'no_terms'                   => __('Tidak ada kategori', 'text_domain'),
        'items_list'                 => __('Daftar Kategori', 'text_domain'),
        'items_list_navigation'      => __('Navigasi Daftar Kategori', 'text_domain'),
    );

    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'show_in_rest'               => true, // Enable REST API
        'rest_base'                  => 'infografis_category', // REST route: /wp-json/wp/v2/infografis_category
    );

    register_taxonomy('infografis_category', array('infografis'), $args);
}

/**
 * Daftarkan taksonomi khusus untuk tag Infografis
 *
 * Taksonomi non-hirarkis untuk tag Infografis dengan dukungan REST API.
 *
 * @since 1.0
 */
function register_infografis_tag_taxonomy()
{
    $labels = array(
        'name'                       => _x('Tag Infografis', 'Nama Umum Taksonomi', 'text_domain'),
        'singular_name'              => _x('Tag Infografis', 'Nama Tunggal Taksonomi', 'text_domain'),
        'menu_name'                  => __('Tag', 'text_domain'),
        'all_items'                  => __('Semua Tag', 'text_domain'),
        'parent_item'                => __('Tag Induk', 'text_domain'),
        'parent_item_colon'          => __('Tag Induk:', 'text_domain'),
        'new_item_name'              => __('Nama Tag Baru', 'text_domain'),
        'add_new_item'               => __('Tambah Tag Baru', 'text_domain'),
        'edit_item'                  => __('Edit Tag', 'text_domain'),
        'update_item'                => __('Perbarui Tag', 'text_domain'),
        'view_item'                  => __('Lihat Tag', 'text_domain'),
        'separate_items_with_commas' => __('Pisahkan tag dengan koma', 'text_domain'),
        'add_or_remove_items'        => __('Tambah atau hapus tag', 'text_domain'),
        'choose_from_most_used'      => __('Pilih dari yang paling sering digunakan', 'text_domain'),
        'popular_items'              => __('Tag Populer', 'text_domain'),
        'search_items'               => __('Cari Tag', 'text_domain'),
        'not_found'                  => __('Tidak ditemukan', 'text_domain'),
        'no_terms'                   => __('Tidak ada tag', 'text_domain'),
        'items_list'                 => __('Daftar Tag', 'text_domain'),
        'items_list_navigation'      => __('Navigasi Daftar Tag', 'text_domain'),
    );

    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'show_in_rest'               => true, // Enable REST API
        'rest_base'                  => 'infografis_tag', // REST route: /wp-json/wp/v2/infografis_tag
    );

    register_taxonomy('infografis_tag', array('infografis'), $args);
}

/* ---------------------------------------------- Infografis: Selesai ---------------------------------------------- */



/* ----------------------------------------------- Videografis: Mulai ----------------------------------------------- */
/**
 * Daftarkan jenis pos baru: Videografis
 *
 * Fitur:
 * - Dukungan: Judul, Editor, Gambar Unggulan, Ringkasan, Bidang Kustom, Komentar
 * - Taksonomi: videografis_category (hirarkis), videografis_tag (non-hirarkis)
 * - REST API: Tersedia di `/wp-json/wp/v2/videografis`
 *
 * @since 1.0
 */
function register_videografis_post_type()
{
    $labels = array(
        'name'                  => _x('Videografis', 'Nama Umum Jenis Posting', 'text_domain'),
        'singular_name'         => _x('Videografis', 'Nama Tunggal Jenis Posting', 'text_domain'),
        'menu_name'             => __('Videografis', 'text_domain'),
        'name_admin_bar'        => __('Videografis', 'text_domain'),
        'archives'              => __('Arsip Videografis', 'text_domain'),
        'attributes'            => __('Atribut Videografis', 'text_domain'),
        'parent_item_colon'     => __('Videografis Induk:', 'text_domain'),
        'all_items'             => __('Semua Videografis', 'text_domain'),
        'add_new_item'          => __('Tambah Videografis Baru', 'text_domain'),
        'add_new'               => __('Tambah Baru', 'text_domain'),
        'new_item'              => __('Videografis Baru', 'text_domain'),
        'edit_item'             => __('Edit Videografis', 'text_domain'),
        'update_item'           => __('Perbarui Videografis', 'text_domain'),
        'view_item'             => __('Lihat Videografis', 'text_domain'),
        'view_items'            => __('Lihat Videografis', 'text_domain'),
        'search_items'          => __('Cari Videografis', 'text_domain'),
        'not_found'             => __('Tidak ditemukan', 'text_domain'),
        'not_found_in_trash'    => __('Tidak ditemukan di Sampah', 'text_domain'),
        'featured_image'        => __('Gambar Unggulan', 'text_domain'),
        'set_featured_image'    => __('Atur Gambar Unggulan', 'text_domain'),
        'remove_featured_image' => __('Hapus Gambar Unggulan', 'text_domain'),
        'use_featured_image'    => __('Gunakan sebagai Gambar Unggulan', 'text_domain'),
        'insert_into_item'      => __('Masukkan ke Videografis', 'text_domain'),
        'uploaded_to_this_item' => __('Diunggah ke Videografis ini', 'text_domain'),
        'items_list'            => __('Daftar Videografis', 'text_domain'),
        'items_list_navigation' => __('Navigasi Daftar Videografis', 'text_domain'),
        'filter_items_list'     => __('Filter Daftar Videografis', 'text_domain'),
    );

    $args = array(
        'label'                 => __('Videografis', 'text_domain'),
        'description'           => __('Jenis posting khusus untuk Videografis', 'text_domain'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'comments', 'author'),
        'taxonomies'            => array('videografis_category', 'videografis_tag'), // Taksonomi khusus
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 7,
        'menu_icon'             => 'dashicons-format-video',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true, // Enable REST API
        'rest_base'             => 'videografis', // REST route: /wp-json/wp/v2/videografis
    );

    register_post_type('videografis', $args);
}

/**
 * Daftarkan taksonomi khusus untuk kategori Videografis
 *
 * Taksonomi hirarkis untuk kategori Videografis dengan dukungan REST API.
 *
 * @since 1.0
 */
function register_videografis_category_taxonomy()
{
    $labels = array(
        'name'                       => _x('Kategori Videografis', 'Nama Umum Taksonomi', 'text_domain'),
        'singular_name'              => _x('Kategori Videografis', 'Nama Tunggal Taksonomi', 'text_domain'),
        'menu_name'                  => __('Kategori', 'text_domain'),
        'all_items'                  => __('Semua Kategori', 'text_domain'),
        'parent_item'                => __('Kategori Induk', 'text_domain'),
        'parent_item_colon'          => __('Kategori Induk:', 'text_domain'),
        'new_item_name'              => __('Nama Kategori Baru', 'text_domain'),
        'add_new_item'               => __('Tambah Kategori Baru', 'text_domain'),
        'edit_item'                  => __('Edit Kategori', 'text_domain'),
        'update_item'                => __('Perbarui Kategori', 'text_domain'),
        'view_item'                  => __('Lihat Kategori', 'text_domain'),
        'separate_items_with_commas' => __('Pisahkan kategori dengan koma', 'text_domain'),
        'add_or_remove_items'        => __('Tambah atau hapus kategori', 'text_domain'),
        'choose_from_most_used'      => __('Pilih dari yang paling sering digunakan', 'text_domain'),
        'popular_items'              => __('Kategori Populer', 'text_domain'),
        'search_items'               => __('Cari Kategori', 'text_domain'),
        'not_found'                  => __('Tidak ditemukan', 'text_domain'),
        'no_terms'                   => __('Tidak ada kategori', 'text_domain'),
        'items_list'                 => __('Daftar Kategori', 'text_domain'),
        'items_list_navigation'      => __('Navigasi Daftar Kategori', 'text_domain'),
    );

    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'show_in_rest'               => true, // Enable REST API
        'rest_base'                  => 'videografis_category', // REST route: /wp-json/wp/v2/videografis_category
    );

    register_taxonomy('videografis_category', array('videografis'), $args);
}

/**
 * Daftarkan taksonomi khusus untuk tag Videografis
 *
 * Taksonomi non-hirarkis untuk tag Videografis dengan dukungan REST API.
 *
 * @since 1.0
 */
function register_videografis_tag_taxonomy()
{
    $labels = array(
        'name'                       => _x('Tag Videografis', 'Nama Umum Taksonomi', 'text_domain'),
        'singular_name'              => _x('Tag Videografis', 'Nama Tunggal Taksonomi', 'text_domain'),
        'menu_name'                  => __('Tag', 'text_domain'),
        'all_items'                  => __('Semua Tag', 'text_domain'),
        'parent_item'                => __('Tag Induk', 'text_domain'),
        'parent_item_colon'          => __('Tag Induk:', 'text_domain'),
        'new_item_name'              => __('Nama Tag Baru', 'text_domain'),
        'add_new_item'               => __('Tambah Tag Baru', 'text_domain'),
        'edit_item'                  => __('Edit Tag', 'text_domain'),
        'update_item'                => __('Perbarui Tag', 'text_domain'),
        'view_item'                  => __('Lihat Tag', 'text_domain'),
        'separate_items_with_commas' => __('Pisahkan tag dengan koma', 'text_domain'),
        'add_or_remove_items'        => __('Tambah atau hapus tag', 'text_domain'),
        'choose_from_most_used'      => __('Pilih dari yang paling sering digunakan', 'text_domain'),
        'popular_items'              => __('Tag Populer', 'text_domain'),
        'search_items'               => __('Cari Tag', 'text_domain'),
        'not_found'                  => __('Tidak ditemukan', 'text_domain'),
        'no_terms'                   => __('Tidak ada tag', 'text_domain'),
        'items_list'                 => __('Daftar Tag', 'text_domain'),
        'items_list_navigation'      => __('Navigasi Daftar Tag', 'text_domain'),
    );

    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'show_in_rest'               => true, // Enable REST API
        'rest_base'                  => 'videografis_tag', // REST route: /wp-json/wp/v2/videografis_tag
    );

    register_taxonomy('videografis_tag', array('videografis'), $args);
}

/* ---------------------------------------------- Videografis: Selesai ---------------------------------------------- */


/* -------------------------------------------------- Tiket: Mulai -------------------------------------------------- */

/**
 * Tambahkan menu tiket di admin
 *
 * Menambahkan menu top-level untuk Tiket, serupa dengan Slider.
 * Halaman ini memungkinkan Admin mengelola tiket permintaan dataset dari CKAN.
 *
 * @since 1.0
 */
function clasnet_add_ticket_menu()
{
    add_menu_page(
        'Pengaturan Tiket Permintaan Dataset',
        'Tiket Permintaan Dataset',
        'manage_options',
        'clasnet-ticket-settings',
        'clasnet_render_ticket_settings_page',
        'dashicons-tickets',
        8
    );
}

/**
 * Render halaman pengaturan tiket di menu admin
 *
 * Fitur:
 * - Formulir tambah/ubah tiket
 * - Tabel daftar tiket dengan filter pekerjaan, keperluan, dan status
 * - Sistem sorting berdasarkan kolom tertentu
 *
 * @since 1.0
 */
function clasnet_render_ticket_settings_page()
{
    // Buat Tiket
    if (isset($_POST['clasnet_ticket_submit'])
    && check_admin_referer('clasnet_ticket_nonce', 'clasnet_ticket_nonce_field'))
    {
        $tickets = get_option('clasnet_tickets', []);
        $ticket_id = isset($_POST['clasnet_ticket_id']) ? sanitize_text_field($_POST['clasnet_ticket_id']) : '';

        $ticket_data = array(
            'nama_dataset' => sanitize_text_field($_POST['clasnet_nama_dataset']),
            'nama' => sanitize_text_field($_POST['clasnet_nama']),
            'tanggal' => current_time('Y-m-d H:i:s'),
            'no_hp' => sanitize_text_field($_POST['clasnet_no_hp']),
            'email' => sanitize_email($_POST['clasnet_email']),
            'instansi' => sanitize_text_field($_POST['clasnet_instansi']),
            'pekerjaan' => sanitize_text_field($_POST['clasnet_pekerjaan']),
            'keperluan' => sanitize_text_field($_POST['clasnet_keperluan']),
            'status' => sanitize_text_field($_POST['clasnet_status']),
            'ticket_id' => $ticket_id ?: uniqid('ticket_'),
        );

        if ($ticket_id)
        {
            $tickets[$ticket_id] = $ticket_data;
        }
        else
        {
            $ticket_id = $ticket_data['ticket_id'];
            $tickets[$ticket_id] = $ticket_data;
        }

        update_option('clasnet_tickets', $tickets);

        echo '<div class="notice notice-success"><p>Tiket berhasil disimpan.</p></div>';
    }

    // Hapus Tiket
    if (isset($_GET['action'])
    && $_GET['action'] === 'delete_ticket'
    && isset($_GET['ticket_id'])
    && check_admin_referer('clasnet_delete_ticket_nonce', '_wpnonce'))
    {
        $delete_id = sanitize_text_field($_GET['ticket_id']);
        $tickets = get_option('clasnet_tickets', []);

        if (isset($tickets[$delete_id]))
        {
            unset($tickets[$delete_id]);

            update_option('clasnet_tickets', $tickets);

            echo '<div class="notice notice-success"><p>Tiket berhasil dihapus.</p></div>';
        }
        else
        {
            echo '<div class="notice notice-error"><p>Tiket tidak ditemukan.</p></div>';
        }
    }

    // Ubah Tiket
    $edit_ticket = null;
    $edit_ticket_id = '';
    $show_form = false;

    if (isset($_GET['action'])
    && $_GET['action'] === 'edit_ticket'
    && isset($_GET['ticket_id'])
    && check_admin_referer('clasnet_edit_ticket_nonce', '_wpnonce'))
    {
        $edit_id = sanitize_text_field($_GET['ticket_id']);
        $tickets = get_option('clasnet_tickets', []);

        if (isset($tickets[$edit_id]))
        {
            $edit_ticket = $tickets[$edit_id];
            $edit_ticket_id = $edit_id;
            $show_form = true;
        }
        else
        {
            echo '<div class="notice notice-error"><p>Tiket tidak ditemukan.</p></div>';
        }
    }
    elseif (isset($_GET['action']) && $_GET['action'] === 'add_ticket')
    {
        $show_form = true;
    }

    // Sort/Filter
    $tickets = get_option('clasnet_tickets', []);
    $filtered_tickets = $tickets;
    $pekerjaan_filter = isset($_GET['pekerjaan']) ? sanitize_text_field($_GET['pekerjaan']) : '';
    $keperluan_filter = isset($_GET['keperluan']) ? sanitize_text_field($_GET['keperluan']) : '';
    $status_filter = isset($_GET['ticket_status']) ? sanitize_text_field($_GET['ticket_status']) : '';

    if ($pekerjaan_filter)
    {
        $filtered_tickets = array_filter($filtered_tickets, function ($ticket) use ($pekerjaan_filter)
        {
            return $ticket['pekerjaan'] === $pekerjaan_filter;
        });
    }

    if ($keperluan_filter)
    {
        $filtered_tickets = array_filter($filtered_tickets, function ($ticket) use ($keperluan_filter)
        {
            return $ticket['keperluan'] === $keperluan_filter;
        });
    }

    if ($status_filter)
    {
        $filtered_tickets = array_filter($filtered_tickets, function ($ticket) use ($status_filter)
        {
            return $ticket['status'] === $status_filter;
        });
    }

    $sort_by = isset($_GET['sort_by']) ? sanitize_text_field($_GET['sort_by']) : 'nama';
    $sort_order = isset($_GET['sort_order']) ? sanitize_text_field($_GET['sort_order']) : 'asc';

    usort($filtered_tickets, function ($a, $b) use ($sort_by, $sort_order)
    {
        $value_a = $a[$sort_by] ?? '';
        $value_b = $b[$sort_by] ?? '';

        if ($sort_by === 'ticket_id')
        {
            $value_a = $a['ticket_id'];
            $value_b = $b['ticket_id'];
        }

        $cmp = strcmp($value_a, $value_b);

        return $sort_order === 'asc' ? $cmp : -$cmp;
    });

    $sort_order_opposite = $sort_order === 'asc' ? 'desc' : 'asc';
    $base_url = remove_query_arg(array('sort_by', 'sort_order', 'action', 'ticket_id', '_wpnonce'));
?>
    <div class="wrap">
        <h1>Pengaturan Tiket Permintaan Dataset</h1>
        <?php if ($show_form) : ?>
            <h2><?php echo $edit_ticket ? 'Ubah Permintaan Dataset' : 'Tambah Permintaan Dataset Baru'; ?></h2>
            <form method="post">
                <?php wp_nonce_field('clasnet_ticket_nonce', 'clasnet_ticket_nonce_field'); ?>
                <input type="hidden" name="clasnet_ticket_id" value="<?php echo esc_attr($edit_ticket_id); ?>">
                <table class="form-table">
                    <tr>
                        <th><label for="clasnet_ticket_id_display">ID Tiket</label></th>
                        <td>
                            <input type="text" id="clasnet_ticket_id_display"
                                value="<?php echo esc_attr($edit_ticket['ticket_id'] ?? 'Akan dibuat otomatis'); ?>"
                                style="width:100%;" readonly
                            >
                        </td>
                    </tr>
                    <tr>
                        <th><label for="clasnet_nama_dataset">Nama Dataset</label></th>
                        <td>
                            <input type="text" name="clasnet_nama_dataset" id="clasnet_nama_dataset"
                                value="<?php echo esc_attr($edit_ticket['nama_dataset'] ?? ''); ?>"
                                style="width:100%;" <?php echo $edit_ticket ? 'readonly' : ''; ?>
                            >
                        </td>
                    </tr>
                    <tr>
                        <th><label for="clasnet_nama">Nama Peminta</label></th>
                        <td>
                            <input type="text" name="clasnet_nama" id="clasnet_nama"
                                value="<?php echo esc_attr($edit_ticket['nama'] ?? ''); ?>"
                                style="width:100%;" <?php echo $edit_ticket ? 'readonly' : ''; ?>
                            >
                        </td>
                    </tr>
                    <tr>
                        <th><label for="clasnet_tanggal">Tanggal</label></th>
                        <td>
                            <input type="date" name="clasnet_tanggal" id="clasnet_tanggal"
                                value="<?php
                                    $tanggal = $edit_ticket['tanggal'] ?? '';
                                    if (!empty($tanggal)) {
                                        $tanggal = str_replace('/', '-', $tanggal);
                                        $date = DateTime::createFromFormat('Y-m-d H:i:s', $tanggal) ?: DateTime::createFromFormat('Y-m-d', $tanggal);
                                        if ($date) {
                                            echo esc_attr($date->format('Y-m-d'));
                                        }
                                    }
                                ?>"
                                style="width:100%;" <?php echo $edit_ticket ? 'readonly' : ''; ?>
                            >
                        </td>
                    </tr>
                    <tr>
                        <th><label for="clasnet_no_hp">No. HP</label></th>
                        <td>
                            <input type="text" name="clasnet_no_hp" id="clasnet_no_hp"
                                value="<?php echo esc_attr($edit_ticket['no_hp'] ?? ''); ?>"
                                style="width:100%;" <?php echo $edit_ticket ? 'readonly' : ''; ?>
                            >
                        </td>
                    </tr>
                    <tr>
                        <th><label for="clasnet_email">Email</label></th>
                        <td>
                            <input type="email" name="clasnet_email" id="clasnet_email"
                                value="<?php echo esc_attr($edit_ticket['email'] ?? ''); ?>"
                                style="width:100%;" <?php echo $edit_ticket ? 'readonly' : ''; ?>
                            >
                        </td>
                    </tr>
                    <tr>
                        <th><label for="clasnet_instansi">Instansi/Organisasi/Perusahaan</label></th>
                        <td>
                            <input type="text" name="clasnet_instansi" id="clasnet_instansi"
                                value="<?php echo esc_attr($edit_ticket['instansi'] ?? ''); ?>"
                                style="width:100%;" <?php echo $edit_ticket ? 'readonly' : ''; ?>
                            >
                        </td>
                    </tr>
                    <tr>
                        <th><label for="clasnet_pekerjaan">Pekerjaan</label></th>
                        <td>
                            <input type="text" name="clasnet_pekerjaan" id="clasnet_pekerjaan"
                                value="<?php echo esc_attr($edit_ticket['pekerjaan'] ?? ''); ?>"
                                style="width:100%;" <?php echo $edit_ticket ? 'readonly' : ''; ?>
                            >
                        </td>
                    </tr>
                    <tr>
                        <th><label for="clasnet_keperluan">Keperluan</label></th>
                        <td>
                            <input type="text" name="clasnet_keperluan" id="clasnet_keperluan"
                                value="<?php echo esc_attr($edit_ticket['keperluan'] ?? ''); ?>"
                                style="width:100%;" <?php echo $edit_ticket ? 'readonly' : ''; ?>
                            >
                        </td>
                    </tr>
                    <tr>
                        <th><label for="clasnet_status">Status</label></th>
                        <td>
                            <select name="clasnet_status" id="clasnet_status">
                                <?php
                                $status = $edit_ticket['status'] ?? 'Menunggu Persetujuan';
                                ?>
                                <option value="Menunggu Persetujuan"
                                    <?php selected($status, 'Menunggu Persetujuan'); ?>>Menunggu Persetujuan</option>
                                <option value="Disetujui" <?php selected($status, 'Disetujui'); ?>>Disetujui</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="clasnet_ticket_submit" class="button-primary" value="Simpan Tiket">
                </p>
            </form>
        <?php else : ?>
            <p>
                <a href="<?php echo esc_url(add_query_arg('action', 'add_ticket')); ?>"
                class="button button-primary">Tambah Tiket Permintaan Dataset Baru</a>
            </p>

            <h2>Daftar Tiket</h2>
            <form method="get">
                <input type="hidden" name="page" value="clasnet-ticket-settings">
                <?php
                // Filter Pekerjaan
                $pekerjaan_options = array('Pelajar/Mahasiswa', 'Akademisi/Peneliti', 'Swasta', 'ASN', 'Lainnya');
                ?>
                <select name="pekerjaan">
                    <option value=""><?php _e('Seluruh Pekerjaan', 'text_domain'); ?></option>
                    <?php foreach ($pekerjaan_options as $option) : ?>
                        <option value="<?php echo esc_attr($option); ?>"
                            <?php selected($pekerjaan_filter, $option); ?>><?php echo esc_html($option); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php
                // Filter Keperluan
                $keperluan_options = array('Penelitian', 'Analisa Bisnis', 'Kebijakan/Perencanaan', 'Lainnya');
                ?>
                <select name="keperluan">
                    <option value=""><?php _e('Seluruh Keperluan', 'text_domain'); ?></option>
                    <?php foreach ($keperluan_options as $option) : ?>
                        <option value="<?php echo esc_attr($option); ?>"
                            <?php selected($keperluan_filter, $option); ?>><?php echo esc_html($option); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php
                // Filter Status
                $status_options = array('Menunggu Persetujuan', 'Disetujui');
                ?>
                <select name="ticket_status">
                    <option value=""><?php _e('Seluruh Status', 'text_domain'); ?></option>
                    <?php foreach ($status_options as $option) : ?>
                        <option value="<?php echo esc_attr($option); ?>"
                            <?php selected($status_filter, $option); ?>><?php echo esc_html($option); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="submit" value="Filter" class="button">
            </form>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 2%">No.</th>
                        <th style="width: 5%">
                            <a href="
                                <?php
                                    echo esc_url(add_query_arg(
                                        array(
                                            'sort_by' => 'tanggal',
                                            'sort_order' => $sort_by === 'tanggal' ? $sort_order_opposite : 'asc'
                                        ),
                                        $base_url
                                    ));
                                ?>
                            ">
                                Tanggal
                                <?php if ($sort_by === 'tanggal') echo $sort_order === 'asc' ? '↑' : '↓'; ?>
                            </a>
                        </th>
                        <th>
                            <a href="
                                <?php
                                    echo esc_url(add_query_arg(
                                        array(
                                            'sort_by' => 'nama_dataset',
                                            'sort_order' => $sort_by === 'nama_dataset' ? $sort_order_opposite : 'asc'
                                        ),
                                        $base_url
                                    ));
                                ?>
                            ">
                                Nama Dataset
                                <?php if ($sort_by === 'nama_dataset') echo $sort_order === 'asc' ? '↑' : '↓'; ?>
                            </a>
                        </th>
                        <th>
                            <a href="
                                <?php
                                    echo esc_url(add_query_arg(
                                        array('sort_by' => 'nama',
                                            'sort_order' => $sort_by === 'nama' ? $sort_order_opposite : 'asc'
                                        ),
                                        $base_url
                                    ));
                                ?>
                            ">
                                Nama Peminta
                                <?php if ($sort_by === 'nama') echo $sort_order === 'asc' ? '↑' : '↓'; ?>
                            </a>
                        </th>
                        <th>
                            <a href="
                                <?php
                                    echo esc_url(add_query_arg(
                                        array(
                                            'sort_by' => 'email',
                                            'sort_order' => $sort_by === 'email' ? $sort_order_opposite : 'asc'
                                        ),
                                        $base_url
                                    ));
                                ?>
                            ">
                                Email
                                <?php if ($sort_by === 'email') echo $sort_order === 'asc' ? '↑' : '↓'; ?>
                            </a>
                        </th>
                        <th>
                            <a href="
                                <?php
                                    echo esc_url(add_query_arg(
                                        array(
                                            'sort_by' => 'instansi',
                                            'sort_order' => $sort_by === 'instansi' ? $sort_order_opposite : 'asc'
                                        ),
                                        $base_url
                                    ));
                                ?>
                            ">
                                Instansi/Organisasi/Perusahaan
                                <?php if ($sort_by === 'instansi') echo $sort_order === 'asc' ? '↑' : '↓'; ?>
                            </a>
                        </th>
                        <th>
                            <a href="
                                <?php
                                    echo esc_url(add_query_arg(
                                        array(
                                            'sort_by' => 'pekerjaan',
                                            'sort_order' => $sort_by === 'pekerjaan' ? $sort_order_opposite : 'asc'
                                        ),
                                        $base_url
                                    ));
                                ?>
                            ">
                                Pekerjaan
                                <?php if ($sort_by === 'pekerjaan') echo $sort_order === 'asc' ? '↑' : '↓'; ?>
                            </a>
                        </th>
                        <th>
                            <a href="
                                <?php
                                    echo esc_url(add_query_arg(
                                        array(
                                            'sort_by' => 'keperluan',
                                            'sort_order' => $sort_by === 'keperluan' ? $sort_order_opposite : 'asc'
                                        ),
                                        $base_url
                                    ));
                                ?>
                            ">
                                Keperluan
                                <?php if ($sort_by === 'keperluan') echo $sort_order === 'asc' ? '↑' : '↓'; ?>
                            </a>
                        </th>
                        <th>
                            <a href="
                                <?php
                                    echo esc_url(add_query_arg(
                                        array(
                                            'sort_by' => 'status',
                                            'sort_order' => $sort_by === 'status' ? $sort_order_opposite : 'asc'
                                        ),
                                        $base_url
                                    ));
                                ?>
                            ">
                                Status
                                <?php if ($sort_by === 'status') echo $sort_order === 'asc' ? '↑' : '↓'; ?>
                            </a>
                        </th>
                        <th>
                            <a href="
                                <?php
                                    echo esc_url(add_query_arg(
                                        array(
                                            'sort_by' => 'ticket_id',
                                            'sort_order' => $sort_by === 'ticket_id' ? $sort_order_opposite : 'asc'
                                        ),
                                        $base_url
                                    ));
                                ?>
                            ">
                                ID Tiket
                                <?php if ($sort_by === 'ticket_id') echo $sort_order === 'asc' ? '↑' : '↓'; ?>
                            </a>
                        </th>
                        <th style="width: 5%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($filtered_tickets)) : ?>
                        <tr>
                            <td colspan="10">Belum ada tiket.</td>
                        </tr>
                    <?php else : ?>
                        <?php $row_number = 1; ?>
                        <?php foreach ($filtered_tickets as $ticket_id => $ticket) : ?>
                            <tr>
                                <td style="place-content: center"><?php echo $row_number++; ?></td>
                                <td style="place-content: center">
                                    <?php echo esc_html($ticket['tanggal'] ?? '-'); ?>
                                </td>
                                <td style="place-content: center">
                                    <?php echo esc_html($ticket['nama_dataset'] ?? '-'); ?>
                                </td>
                                <td style="place-content: center"><?php echo esc_html($ticket['nama']); ?></td>
                                <td style="place-content: center"><?php echo esc_html($ticket['email']); ?></td>
                                <td style="place-content: center"><?php echo esc_html($ticket['instansi']); ?></td>
                                <td style="place-content: center"><?php echo esc_html($ticket['pekerjaan']); ?></td>
                                <td style="place-content: center"><?php echo esc_html($ticket['keperluan']); ?></td>
                                <td style="place-content: center">
                                    <?php if ($ticket['status'] === 'Disetujui') : ?>
                                        <span class="dashicons dashicons-yes-alt ticket-status ticket-status-approved">
                                        </span> Disetujui
                                    <?php elseif ($ticket['status'] === 'Menunggu Persetujuan') : ?>
                                        <span class="dashicons dashicons-info ticket-status ticket-status-pending">
                                        </span> Menunggu Persetujuan
                                    <?php endif; ?>
                                </td>
                                <td style="place-content: center"><?php echo esc_html($ticket['ticket_id']); ?></td>
                                <td style="place-content: center">
                                    <a href="
                                        <?php
                                            echo esc_url(wp_nonce_url(add_query_arg(
                                                array(
                                                    'action' => 'edit_ticket',
                                                    'ticket_id' => $ticket['ticket_id']
                                                ),
                                                admin_url('admin.php?page=clasnet-ticket-settings')),
                                                'clasnet_edit_ticket_nonce', '_wpnonce'
                                            ));
                                        ?>
                                    "
                                        class="button button-secondary">Ubah
                                    </a>
                                    <a href="
                                        <?php
                                            echo esc_url(wp_nonce_url(add_query_arg(
                                                array(
                                                    'action' => 'delete_ticket',
                                                    'ticket_id' => $ticket['ticket_id']
                                                ),
                                                admin_url('admin.php?page=clasnet-ticket-settings')),
                                                'clasnet_delete_ticket_nonce', '_wpnonce'
                                            ));
                                        ?>
                                    "
                                        class="button button-secondary"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus tiket ini?');">
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php
}

/**
 * Mendaftarkan rute API untuk tiket
 *
 * Rute yang didaftarkan:
 * - POST /clasnet/v1/tiket: Membuat tiket baru
 * - GET /clasnet/v1/tiket/pending: Jumlah tiket menunggu persetujuan
 * - GET /clasnet/v1/tiket/status/{ticket_id}: Cek status tiket
 *
 * @since 1.0
 */
function clasnet_register_ticket_api_routes()
{
    register_rest_route('clasnet/v1', '/tiket',
        [
            [
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => 'clasnet_create_ticket',
                'permission_callback' => function ()
                {
                    return current_user_can('manage_options');
                },
            ]
        ]
    );

    register_rest_route('clasnet/v1', '/tiket/pending',
        [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => 'clasnet_get_pending_tickets_count',
                'permission_callback' => '__return_true',
            ]
        ]
    );

    register_rest_route('clasnet/v1', '/tiket/status/(?P<ticket_id>[a-zA-Z0-9_-]+)',
        [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => 'clasnet_get_ticket_status',
                'permission_callback' => '__return_true',
            ]
        ]
    );

    register_rest_route('clasnet/v1', '/tiket/all',
        [
            'methods' => 'GET',
            'callback' => 'clasnet_get_all_tickets',
            'args' =>
            [
                'per_page' =>
                [
                    'validate_callback' => function($value)
                    {
                        return is_numeric($value) && $value > 0;
                    },
                ],
                'page' =>
                [
                    'validate_callback' => function($value)
                    {
                        return is_numeric($value) && $value > 0;
                    },
                ]
            ]
        ]
    );

    register_rest_route('clasnet/v1', '/tiket/update',
        [
            [
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => 'clasnet_update_ticket_status',
                'permission_callback' => function ()
                {
                    return current_user_can('manage_options');
                },
            ]
        ]
    );
}

/**
 * Membuat tiket baru melalui REST API
 *
 * Validasi:
 * - Email valid
 * - Pekerjaan harus salah satu dari: Pelajar/Mahasiswa, Akademisi/Peneliti, Swasta, ASN, Lainnya
 * - Keperluan harus salah satu dari: Penelitian, Analisa Bisnis, Kebijakan/Perencanaan, Lainnya
 *
 * @param WP_REST_Request $request Data tiket dalam format JSON
 * @return WP_REST_Response|WP_Error Respons JSON atau pesan kesalahan
 *
 * @since 1.0
 */
function clasnet_create_ticket($request)
{
    $params = $request->get_params();

    $required_fields = array(
        'nama_dataset',
        'nama',
        'no_hp',
        'email',
        'pekerjaan',
        'keperluan',
    );

    foreach ($required_fields as $field)
    {
        if (!isset($params[$field]) || empty($params[$field]))
            return new WP_Error('missing_field', "Field '$field' diperlukan", array('status' => 400));
    }

    if (!is_email($params['email']))
        return new WP_Error('invalid_email', 'Email tidak valid', array('status' => 400));

    $valid_pekerjaan = array('Pelajar/Mahasiswa', 'Akademisi/Peneliti', 'Swasta', 'ASN', 'Lainnya');

    if (!in_array($params['pekerjaan'], $valid_pekerjaan))
        return new WP_Error('invalid_pekerjaan', 'Pekerjaan tidak valid', array('status' => 400));

    $valid_keperluan = array('Penelitian', 'Analisa Bisnis', 'Kebijakan/Perencanaan', 'Lainnya');

    if (!in_array($params['keperluan'], $valid_keperluan))
        return new WP_Error('invalid_keperluan', 'Keperluan tidak valid', array('status' => 400));

    $tickets = get_option('clasnet_tickets', []);
    $ticket_id = uniqid('ticket_');

    $ticket_data = array(
        'nama_dataset' => sanitize_text_field($params['nama_dataset']),
        'nama' => sanitize_text_field($params['nama']),
        'tanggal' => current_time('Y-m-d H:i:s'),
        'no_hp' => sanitize_text_field($params['no_hp']),
        'email' => sanitize_email($params['email']),
        'instansi' => sanitize_text_field($params['instansi']),
        'pekerjaan' => sanitize_text_field($params['pekerjaan']),
        'keperluan' => sanitize_text_field($params['keperluan']),
        'status' => 'Menunggu Persetujuan',
        'ticket_id' => $ticket_id,
    );

    $tickets[$ticket_id] = $ticket_data;

    update_option('clasnet_tickets', $tickets);

    return rest_ensure_response(array(
        'ticket_id' => $ticket_id,
        'status' => 'Menunggu Persetujuan',
        'message' => 'Tiket berhasil dibuat',
    ));
}

/**
 * Mendapatkan jumlah tiket yang menunggu persetujuan
 *
 * @return WP_REST_Response JSON dengan jumlah tiket pending
 *
 * @since 1.0
 */
function clasnet_get_pending_tickets_count()
{
    $tickets = get_option('clasnet_tickets', []);
    $count = 0;

    foreach ($tickets as $ticket)
    {
        if ($ticket['status'] === 'Menunggu Persetujuan')
            $count++;
    }

    return rest_ensure_response(array(
        'pending_tickets' => $count,
    ));
}

/**
 * Mendapatkan status tiket berdasarkan ID
 *
 * @param WP_REST_Request $request Permintaan REST API
 * @return WP_REST_Response|WP_Error Respons JSON atau pesan kesalahan
 *
 * @since 1.0
 */
function clasnet_get_ticket_status($request)
{
    $ticket_id = $request['ticket_id'];
    $tickets = get_option('clasnet_tickets', []);

    if (!isset($tickets[$ticket_id]))
        return new WP_Error('not_found', 'Tiket tidak ditemukan', array('status' => 404));

    $ticket = $tickets[$ticket_id];

    return rest_ensure_response(array(
        'ticket_id' => $ticket_id,
        'status' => $ticket['status'],
    ));
}

/**
 * Mendapatkan daftar semua tiket dengan pagination
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 *
 * @since 1.2
 */
function clasnet_get_all_tickets($request)
{
    $tickets = get_option('clasnet_tickets', []);

    // Parse query parameters
    $per_page = intval($request->get_param('per_page')) ?: 10;
    $page = intval($request->get_param('page')) ?: 1;

    foreach ($tickets as &$ticket)
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $ticket['tanggal']))
            $ticket['tanggal'] = date('Y-m-d H:i:s', strtotime($ticket['tanggal']));
    }

    unset($ticket);

    usort($tickets, function($a, $b) {
        return strtotime($b['tanggal']) - strtotime($a['tanggal']);
    });

    // Hitung data berdasarkan halaman
    $total = count($tickets);
    $pages = ceil($total / $per_page);
    $paginated_tickets = array_slice($tickets, ($page - 1) * $per_page, $per_page);

    // Set header X-WP-Total
    $response = new WP_REST_Response(array_values($paginated_tickets));
    $response->header('X-WP-Total', $total);
    $response->header('X-WP-TotalPages', $pages);

    return $response;
}

/**
 * Memperbarui status tiket melalui REST API
 *
 * @param WP_REST_Request $request Data tiket dalam format JSON
 * @return WP_REST_Response|WP_Error Respons JSON atau pesan kesalahan
 *
 * @since 1.1
 */
function clasnet_update_ticket_status($request)
{
    $params = $request->get_params();
    $ticket_id = sanitize_text_field($params['ticket_id']);
    $action = sanitize_text_field($params['action'] ?? '');

    if (!$ticket_id)
        return new WP_Error('missing_field', 'Field ticket_id diperlukan', array('status' => 400));

    $tickets = get_option('clasnet_tickets', []);

    if (!isset($tickets[$ticket_id]))
        return new WP_Error('not_found', 'Tiket tidak ditemukan', array('status' => 404));

    if ($action === 'delete')
    {
        unset($tickets[$ticket_id]);

        update_option('clasnet_tickets', $tickets);

        return rest_ensure_response(array(
            'ticket_id' => $ticket_id,
            'message' => 'Tiket berhasil dihapus',
        ));
    }

    $new_status = sanitize_text_field($params['status']);

    if (!$new_status)
        return new WP_Error('missing_field', 'Field status diperlukan untuk pembaruan status', array('status' => 400));

    $valid_statuses = array('Menunggu Persetujuan', 'Disetujui');

    if (!in_array($new_status, $valid_statuses))
        return new WP_Error('invalid_status', 'Status tidak valid', array('status' => 400));

    $tickets[$ticket_id]['status'] = $new_status;

    update_option('clasnet_tickets', $tickets);

    return rest_ensure_response(array(
        'ticket_id' => $ticket_id,
        'status' => $new_status,
        'message' => 'Status tiket berhasil diperbarui',
    ));
}

/**
 * Menambahkan gaya administrasi untuk halaman pengaturan tiket
 *
 * Fungsi ini menambahkan file CSS khusus (`admin-style.css`)
 * ke halaman pengaturan tiket di area admin WordPress.
 * Gaya ini digunakan untuk menyesuaikan tampilan ikon status tiket
 * (seperti ⚠️ untuk "Menunggu Persetujuan" dan ✅ untuk "Disetujui").
 *
 * @since 1.0
 * @param string $hook Nama hook halaman admin saat ini (contoh: 'toplevel_page_clasnet-ticket-settings').
 */
function clasnet_enqueue_admin_scripts($hook)
{
    if ($hook === 'toplevel_page_clasnet-ticket-settings')
        wp_enqueue_style('clasnet-admin-style', plugin_dir_url(__FILE__) . 'admin-style.css', [], '1.0');
}

/* ------------------------------------------------- Tiket: Selesai ------------------------------------------------- */


/* ------------------------------------------- Konfigurasi Website: Mulai ------------------------------------------- */

// Inisialisasi data default saat plugin diaktifkan
register_activation_hook(__FILE__, 'clasnet_init_website_configs');

function clasnet_init_website_configs()
{
    if (false === get_option('clasnet_kecamatan_api_urls'))
    {
        update_option('clasnet_kecamatan_api_urls',
        [
            "banjarmangu" => "https://api-banjarmangu.smartdesa.net/",
            "punggelan" => "https://api-punggelan.smartdesa.net/",
            "wanayasa" => "https://api-wanayasa.smartdesa.net/",
            "madukara" => "https://api-madukara.smartdesa.net/",
            "pandanarum" => "https://api-pandanarum.smartdesa.net/"
        ]);
    }

    if (false === get_option('clasnet_website_opendk'))
    {
        update_option('clasnet_website_opendk',
        [
            ["id" => "Kecamatan Banjarmangu", "url" => "https://kecamatan-banjarmangu.smartdesa.net/public/filter-berita-desa?page=1&desa=Semua&tanggal=Terlama&cari="],
            ["id" => "Kecamatan Madukara", "url" => "https://kecamatan-madukara.smartdesa.net/public/filter-berita-desa?page=1&desa=Semua&tanggal=Terlama&cari="],
            ["id" => "Kecamatan Punggelan", "url" => "https://kecamatan-punggelan.smartdesa.net/filter-berita-desa?page=1&desa=Semua&tanggal=Terlama&cari="],
            ["id" => "Kecamatan Wanayasa", "url" => "https://kecamatan-wanayasa.smartdesa.net/public/filter-berita-desa?page=1&desa=Semua&tanggal=Terlama&cari="],
        ]);
    }

    if (false === get_option('clasnet_website_opd'))
    {
        update_option('clasnet_website_opd',
        [
            ["id" => "banjarnegarakab.go.id", "url" => "https://banjarnegarakab.go.id/wp-json/wp/v2/posts", "auth" => false],
            ["id" => "dinkominfo.banjarnegarakab.go.id", "url" => "https://dinkominfo.banjarnegarakab.go.id/wp-json/wp/v2/posts", "auth" => false],
            ["id" => "setda.banjarnegarakab.go.id", "url" => "https://setda.banjarnegarakab.go.id/wp-json/wp/v2/posts", "auth" => false],
            ["id" => "dprd.banjarnegarakab.go.id", "url" => "https://dprd.banjarnegarakab.go.id/wp-json/wp/v2/posts", "auth" => false],
            ["id" => "dindikpora.banjarnegarakab.go.id", "url" => "https://dindikpora.banjarnegarakab.go.id/wp-json/wp/v2/posts", "auth" => false],
            ["id" => "dinkes.banjarnegarakab.go.id", "url" => "https://dinkes.banjarnegarakab.go.id/wp-json/wp/v2/posts", "auth" => false],
            ["id" => "dpupr.banjarnegarakab.go.id", "url" => "https://dpupr.banjarnegarakab.go.id/wp-json/wp/v2/posts", "auth" => false],
            ["id" => "dinhub.banjarnegarakab.go.id", "url" => "https://dinhub.banjarnegarakab.go.id/wp-json/wp/v2/posts", "auth" => false],
            ["id" => "disarpus.banjarnegarakab.go.id", "url" => "https://disarpus.banjarnegarakab.go.id/wp-json/wp/v2/posts", "auth" => false],
            ["id" => "wisata.banjarnegarakab.go.id", "url" => "https://wisata.banjarnegarakab.go.id/wp-json/wp/v2/posts", "auth" => false],
            ["id" => "www.humaspolresbanjarnegara.com", "url" => "https://www.humaspolresbanjarnegara.com/wp-json/wp/v2/posts", "auth" => false],
            ["id" => "rsud.banjarnegarakab.go.id", "url" => "https://rsud.banjarnegarakab.go.id/wp-json/wp/v2/posts", "auth" => false],
            ["id" => "distankan.banjarnegarakab.go.id", "url" => "https://distankan.banjarnegarakab.go.id/wp-json/wp/v2/posts", "auth" => false],
            ["id" => "satpolpp.banjarnegarakab.go.id", "url" => "https://satpolpp.banjarnegarakab.go.id/wp-json/wp/v2/posts", "auth" => false],
            ["id" => "bpbd.banjarnegarakab.go.id", "url" => "https://bpbd.banjarnegarakab.go.id/wp-json/wp/v2/posts", "auth" => false],
            ["id" => "bppkad.banjarnegarakab.go.id", "url" => "https://bppkad.banjarnegarakab.go.id/wp-json/wp/v2/posts", "auth" => false],
            ["id" => "baperlitbang.banjarnegarakab.go.id", "url" => "https://baperlitbang.banjarnegarakab.go.id/wp-json/wp/v2/posts", "auth" => false],
            ["id" => "bkd.banjarnegarakab.go.id", "url" => "https://bkd.banjarnegarakab.go.id/wp-json/wp/v2/posts", "auth" => false],
        ]);
    }
}

function clasnet_add_website_config_menu()
{
    add_menu_page(
        'Konfigurasi OpenData',
        'Konfigurasi OpenData',
        'manage_options',
        'clasnet-website-config-settings',
        'clasnet_render_website_config_settings_page',
        'dashicons-welcome-widgets-menus',
        9
    );
}

/**
 * Render halaman pengaturan konfigurasi website
 *
 * Fitur:
 * - Formulir tambah/ubah konfigurasi per kategori (Kecamatan API, Opendk, Opd)
 * - Tabel daftar konfigurasi per kategori
 *
 * @since 1.2
 */
function clasnet_render_website_config_settings_page()
{
    $kecamatan_api_urls = get_option('clasnet_kecamatan_api_urls', []);
    $website_opendk = get_option('clasnet_website_opendk', []);
    $website_opd = get_option('clasnet_website_opd', []);

    if (isset($_POST['clasnet_config_submit']) && check_admin_referer('clasnet_config_nonce', 'clasnet_config_nonce_field'))
    {
        $type = sanitize_text_field($_POST['clasnet_config_type']);
        $website_id = sanitize_text_field($_POST['clasnet_website_id']);
        $url = esc_url_raw($_POST['clasnet_url']);
        $auth = isset($_POST['clasnet_auth']) ? true : false;

        if ($type === 'kecamatan_api')
        {
            $kecamatan_api_urls[$website_id] = $url;

            update_option('clasnet_kecamatan_api_urls', $kecamatan_api_urls);
        }
        elseif ($type === 'website_opendk')
        {
            $website_opendk[] = ['id' => $website_id, 'url' => $url];

            update_option('clasnet_website_opendk', $website_opendk);
        }
        elseif ($type === 'website_opd')
        {
            $website_opd[] = ['id' => $website_id, 'url' => $url, 'auth' => $auth];

            update_option('clasnet_website_opd', $website_opd);
        }

        echo '<div class="notice notice-success"><p>Konfigurasi berhasil disimpan.</p></div>';
    }

    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['index']) && isset($_GET['type'])
    && check_admin_referer('clasnet_delete_config_nonce', '_wpnonce'))
    {
        $type = sanitize_text_field($_GET['type']);
        $index = intval($_GET['index']);

        if ($type === 'kecamatan_api' && isset($kecamatan_api_urls))
        {
            $keys = array_keys($kecamatan_api_urls);

            if (isset($keys[$index]))
            {
                unset($kecamatan_api_urls[$keys[$index]]);

                $kecamatan_api_urls = array_filter($kecamatan_api_urls);

                update_option('clasnet_kecamatan_api_urls', $kecamatan_api_urls);
            }
        }
        elseif ($type === 'website_opendk' && isset($website_opendk[$index]))
        {
            unset($website_opendk[$index]);

            $website_opendk = array_values($website_opendk);

            update_option('clasnet_website_opendk', $website_opendk);
        }
        elseif ($type === 'website_opd' && isset($website_opd[$index]))
        {
            unset($website_opd[$index]);

            $website_opd = array_values($website_opd);

            update_option('clasnet_website_opd', $website_opd);
        }

        echo '<div class="notice notice-success"><p>Konfigurasi berhasil dihapus.</p></div>';
    }
?>
    <div class="wrap">
        <h1>Pengaturan Konfigurasi Website</h1>

        <h2>Tambah Konfigurasi Baru</h2>
        <form method="post">
            <?php wp_nonce_field('clasnet_config_nonce', 'clasnet_config_nonce_field'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="clasnet_config_type">Tipe</label></th>
                    <td>
                        <select name="clasnet_config_type" id="clasnet_config_type" required>
                            <option value="kecamatan_api">Kecamatan API</option>
                            <option value="website_opendk">Website OpenDK</option>
                            <option value="website_opd">Website OPD</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="clasnet_website_id">ID Website</label></th>
                    <td><input type="text" name="clasnet_website_id" id="clasnet_website_id" style="width:100%;" required></td>
                </tr>
                <tr>
                    <th><label for="clasnet_url">URL</label></th>
                    <td><input type="url" name="clasnet_url" id="clasnet_url" style="width:100%;" required></td>
                </tr>
                <tr>
                    <th><label for="clasnet_auth">Autentikasi</label></th>
                    <td><input type="checkbox" name="clasnet_auth" id="clasnet_auth" value="1"></td>
                </tr>
            </table>
            <p class="submit"><input type="submit" name="clasnet_config_submit" class="button-primary" value="Simpan Konfigurasi"></p>
        </form>

        <h2>Daftar Konfigurasi</h2>
        <?php
        // Kecamatan API URLs
        if (!empty($kecamatan_api_urls))
        {
            echo '<h3>Kecamatan API URLs</h3>';
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>ID</th><th>URL</th><th>Aksi</th></tr></thead><tbody>';
            $i = 0;
            foreach ($kecamatan_api_urls as $id => $url)
            {
                echo "<tr><td>$id</td><td>$url</td><td><a href='" . wp_nonce_url(add_query_arg(['action' => 'delete', 'type' => 'kecamatan_api', 'index' => $i++]), 'clasnet_delete_config_nonce', '_wpnonce') . "' class='button button-secondary' onclick='return confirm(\"Apakah Anda yakin ingin menghapus?\");'>Hapus</a></td></tr>";
            }
            echo '</tbody></table>';
        }

        // Website OpenDK
        if (!empty($website_opendk))
        {
            echo '<h3>Website OpenDK</h3>';
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>ID</th><th>URL</th><th>Aksi</th></tr></thead><tbody>';
            $i = 0;
            foreach ($website_opendk as $config)
            {
                echo "<tr><td>{$config['id']}</td><td>{$config['url']}</td><td><a href='" . wp_nonce_url(add_query_arg(['action' => 'delete', 'type' => 'website_opendk', 'index' => $i++]), 'clasnet_delete_config_nonce', '_wpnonce') . "' class='button button-secondary' onclick='return confirm(\"Apakah Anda yakin ingin menghapus?\");'>Hapus</a></td></tr>";
            }
            echo '</tbody></table>';
        }

        // Website OPD
        if (!empty($website_opd))
        {
            echo '<h3>Website OPD</h3>';
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>ID</th><th>URL</th><th>Auth</th><th>Aksi</th></tr></thead><tbody>';
            $i = 0;
            foreach ($website_opd as $config)
            {
                echo "<tr><td>{$config['id']}</td><td>{$config['url']}</td><td>" . ($config['auth'] ? 'True' : 'False') . "</td><td><a href='" . wp_nonce_url(add_query_arg(['action' => 'delete', 'type' => 'website_opd', 'index' => $i++]), 'clasnet_delete_config_nonce', '_wpnonce') . "' class='button button-secondary' onclick='return confirm(\"Apakah Anda yakin ingin menghapus?\");'>Hapus</a></td></tr>";
            }
            echo '</tbody></table>';
        }
        ?>
    </div>
<?php
}

/**
 * Mendaftarkan rute API untuk konfigurasi website
 *
 * Rute yang didaftarkan:
 * - GET /clasnet/v1/website-config: Mengambil semua konfigurasi website
 *
 * @since 1.2
 */
function clasnet_register_website_config_api_routes()
{
    register_rest_route('clasnet/v1', '/website-config',
        [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => 'clasnet_get_website_configs',
                'permission_callback' => '__return_true',
            ]
        ]
    );
}

/**
 * Mendapatkan semua konfigurasi website dari opsi
 *
 * @return WP_REST_Response
 *
 * @since 1.2
 */
function clasnet_get_website_configs()
{
    return rest_ensure_response([
        'kecamatan_api_urls' => get_option('clasnet_kecamatan_api_urls', []),
        'website_opendk' => get_option('clasnet_website_opendk', []),
        'website_opd' => get_option('clasnet_website_opd', []),
    ]);
}
?>