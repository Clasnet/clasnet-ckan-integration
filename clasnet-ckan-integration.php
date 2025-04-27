<?php
/*
 * Plugin Name: Clasnet CKAN Integration
 * Description: Endpoint khusus untuk mengintegrasikan CKAN ke WordPress menggunakan kata sandi aplikasi. Termasuk jenis posting E-Book dengan kategori dan tag.
 * Version: 1.0
 * Author: MOVZX (movzx@yahoo.com)
 * Author URI: https://github.com/MOVZX
 * Network: true
 * License: GPL2
 */

add_action('init', 'register_ebook_post_type');
add_action('init', 'register_ebook_category_taxonomy');
add_action('init', 'register_ebook_tag_taxonomy');

/**
 * Daftarkan jenis pos baru: E-Book
 *
 * Digunakan untuk menambahkan jenis pos khusus E-Book yang memiliki
 * taksonomi khusus: ebook_category dan ebook_tag.
 *
 * @since 1.0
 */
function register_ebook_post_type() {
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
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'comments'),
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
    );

    register_post_type('ebook', $args);
}

/**
 * Daftarkan taksonomi khusus untuk kategori E-Book
 *
 * Digunakan untuk menambahkan taksonomi khusus untuk kategori E-Book.
 *
 * @since 1.0
 */
function register_ebook_category_taxonomy() {
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
    );

    register_taxonomy('ebook_category', array('ebook'), $args);
}

/**
 * Daftarkan taksonomi khusus untuk tag E-Book
 *
 * Digunakan untuk menambahkan taksonomi khusus untuk tag E-Book.
 *
 * @since 1.0
 */
function register_ebook_tag_taxonomy() {
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
    );

    register_taxonomy('ebook_tag', array('ebook'), $args);
}
