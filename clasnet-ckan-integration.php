<?php
/*
 * Plugin Name: Clasnet CKAN Integration
 * Description: Endpoint khusus untuk mengintegrasikan CKAN ke WordPress menggunakan kata sandi aplikasi. Termasuk jenis posting E-Book dengan kategori dan tag.
 * Version: 1.0
 * Author: MOVZX (movzx@yahoo.com)
 * Author URI: https://github.com/MOVZX
 * Network: true
 * License: GPLv2
 */

add_action('init', 'register_ebook_post_type');
add_action('init', 'register_ebook_category_taxonomy');
add_action('init', 'register_ebook_tag_taxonomy');
add_action('admin_menu', 'clasnet_add_settings_page');
add_action('admin_enqueue_scripts', 'clasnet_enqueue_admin_scripts');
add_action('rest_api_init', 'clasnet_register_slider_api_routes');

/**
 * Daftarkan jenis pos baru: E-Book
 *
 * Digunakan untuk menambahkan jenis pos khusus E-Book yang memiliki
 * taksonomi khusus: ebook_category dan ebook_tag.
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
        'show_in_rest'          => true, // Enable REST API
        'rest_base'             => 'ebook', // REST route: /wp-json/wp/v2/ebook
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
 * Digunakan untuk menambahkan taksonomi khusus untuk tag E-Book.
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

/**
 * Tambahkan halaman pengaturan slider di menu admin
 *
 * Halaman ini digunakan untuk mengatur slider yang akan ditampilkan di halaman depan.
 *
 * @since 1.0
 */
function clasnet_add_settings_page()
{
    add_menu_page(
        'Pengaturan Slider',
        'Slider',
        'manage_options',
        'clasnet-slider-settings',
        'clasnet_render_slider_settings_page',
        'dashicons-images-alt2',
        6
    );
}

/**
 * Render halaman pengaturan slider di menu admin.
 *
 * Halaman ini digunakan untuk mengatur slider yang akan ditampilkan di halaman depan.
 * Pengguna dapat mengunggah gambar slider baru atau menghapus slider yang sudah ada.
 *
 * @since 1.0
 */
function clasnet_render_slider_settings_page()
{
    if (isset($_POST['clasnet_slider_submit']) && check_admin_referer('clasnet_slider_nonce', 'clasnet_slider_nonce_field'))
    {
        $media_id = isset($_POST['clasnet_slider_media_id']) ? intval($_POST['clasnet_slider_media_id']) : 0;

        error_log('Media ID received: ' . $media_id);

        if ($media_id > 0)
        {
            $sliders = get_option('clasnet_sliders', []);
            $sliders[] = $media_id;

            update_option('clasnet_sliders', $sliders);

            echo '<div class="notice notice-success"><p>Gambar slider berhasil diunggah.</p></div>';
        }
        else
        {
            echo '<div class="notice notice-error"><p>Gagal: Silakan pilih gambar.</p></div>';
            error_log('No valid media ID received. POST data: ' . print_r($_POST, true)); // Debug: Log POST data
        }
    }

    // Hapus gambar jika diminta
    if (isset($_GET['delete_slider']) && check_admin_referer('clasnet_delete_slider_nonce'))
    {
        $delete_id = intval($_GET['delete_slider']);
        $sliders = get_option('clasnet_sliders', []);

        if (isset($sliders[$delete_id]))
        {
            wp_delete_attachment($sliders[$delete_id]);

            unset($sliders[$delete_id]);

            $sliders = array_values($sliders);

            update_option('clasnet_sliders', $sliders);

            echo '<div class="notice notice-success"><p>Gambar slider berhasil dihapus.</p></div>';
        }
    }

    $sliders = get_option('clasnet_sliders', []);
?>
    <div class="wrap">
        <h1>Pengaturan Slider</h1>
        <form method="post">
            <?php wp_nonce_field('clasnet_slider_nonce', 'clasnet_slider_nonce_field'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="clasnet_slider_image">Unggah Gambar Slider</label></th>
                    <td>
                        <input type="hidden" name="clasnet_slider_media_id" id="clasnet_slider_media_id" value="">
                        <input type="button" id="clasnet_slider_image" class="button" value="Pilih Gambar">
                        <span id="clasnet_slider_image_name">Tidak ada gambar yang dipilih.</span>
                        <p class="description">Unggah gambar untuk digunakan sebagai slider.</p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="clasnet_slider_submit" class="button-primary" value="Unggah Gambar">
            </p>
        </form>

        <h2>Gambar Slider Saat Ini</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>ID</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($sliders)) : ?>
                    <tr>
                        <td colspan="3">Belum ada gambar slider.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($sliders as $index => $slider_id) : ?>
                        <tr>
                            <td><?php echo wp_get_attachment_image($slider_id, 'thumbnail'); ?></td>
                            <td><?php echo esc_html($slider_id); ?></td>
                            <td>
                                <a href="<?php echo wp_nonce_url(add_query_arg('delete_slider', $index), 'clasnet_delete_slider_nonce'); ?>" class="button button-secondary" onclick="return confirm('Apakah Anda yakin ingin menghapus slider ini?');">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php
}

/**
 * Menambahkan skrip administrasi slider.
 *
 * Skrip ini memungkinkan pengguna untuk mengunggah gambar slider
 * melalui tombol "Unggah Gambar" di halaman pengaturan slider
 * dan memperbarui daftar slider yang ditampilkan.
 *
 * Skrip ini dijalankan hanya jika pengguna berada di halaman
 * pengaturan slider.
 *
 * @since 1.0
 * @param string $hook Nama hook yang sedang dijalankan.
 */
function clasnet_enqueue_admin_scripts($hook)
{
    if ($hook !== 'toplevel_page_clasnet-slider-settings')
        return;

    wp_enqueue_media();
    wp_enqueue_script('clasnet-admin-script', plugin_dir_url(__FILE__) . 'admin-script.js', ['jquery', 'media-upload'], '1.1', true);
}

/**
 * Mendaftarkan rute API untuk mengatur slider.
 *
 * Rute yang didaftarkan:
 *
 * - `GET /sliders`: Mengembalikan daftar slider yang tersedia.
 * - `POST /sliders`: Menambahkan slider baru.
 * - `GET /sliders/:id`: Mengembalikan slider dengan ID yang diberikan.
 * - `PUT /sliders/:id`: Memperbarui slider dengan ID yang diberikan.
 * - `DELETE /sliders/:id`: Menghapus slider dengan ID yang diberikan.
 *
 * @since 1.0
 */
function clasnet_register_slider_api_routes()
{
    register_rest_route('clasnet/v1', '/sliders',
        [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => 'clasnet_get_sliders',
                'permission_callback' => '__return_true',
            ],
            [
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => 'clasnet_add_slider',
                'permission_callback' => function ()
                {
                    return current_user_can('manage_options');
                },
            ],
        ]
    );

    register_rest_route('clasnet/v1', '/sliders/(?P<id>\d+)',
        [
            [
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => 'clasnet_update_slider',
                'permission_callback' => function ()
                {
                    return current_user_can('manage_options');
                },
            ],
            [
                'methods' => WP_REST_Server::DELETABLE,
                'callback' => 'clasnet_delete_slider',
                'permission_callback' => function ()
                {
                    return current_user_can('manage_options');
                },
            ],
        ]
    );
}

/**
 * Mendapatkan daftar slider yang tersedia.
 *
 * Fungsi ini mengembalikan response dalam bentuk JSON
 * yang berisi daftar slider yang tersedia dalam format
 * berikut:
 *
 * [
 *     {
 *         "id": <integer>,
 *         "attachment_id": <integer>,
 *         "url": <string>
 *     },
 *     ...
 * ]
 *
 * @return array Daftar slider yang tersedia.
 */
function clasnet_get_sliders()
{
    $sliders = get_option('clasnet_sliders', []);
    $response = [];

    foreach ($sliders as $index => $slider_id)
    {
        $response[] =
            [
                'id' => $index,
                'attachment_id' => $slider_id,
                'url' => wp_get_attachment_url($slider_id),
            ];
    }

    return rest_ensure_response($response);
}

/**
 * Menambahkan slider baru.
 *
 * Fungsi ini menerima permintaan yang berisi file gambar untuk diunggah
 * sebagai slider baru. Menggunakan `media_handle_sideload` untuk
 * mengunggah gambar dan menambahkannya ke daftar slider yang disimpan
 * dalam opsi `clasnet_sliders`.
 *
 * @since 1.0
 *
 * @param WP_REST_Request $request Permintaan REST API yang berisi data file.
 *
 * @return WP_REST_Response|WP_Error Response REST API berisi detail slider
 *         yang baru ditambahkan, atau WP_Error jika terjadi kesalahan.
 */

function clasnet_add_slider($request)
{
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    $files = $request->get_file_params();

    if (empty($files['image']))
        return new WP_Error('no_image', 'Gambar diperlukan', ['status' => 400]);

    $image = $files['image'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 3 * 1024 * 1024; // 3MB

    if (!in_array($image['type'], $allowed_types))
        return new WP_Error('invalid_type', 'Format gambar tidak didukung. Gunakan JPEG, PNG, GIF, atau WebP.', ['status' => 400]);

    if ($image['size'] > $max_size)
        return new WP_Error('size_too_large', 'Ukuran gambar terlalu besar. Maksimum 5MB.', ['status' => 400]);

    if ($image['error'] !== UPLOAD_ERR_OK)
        return new WP_Error('upload_error', 'Gagal mengunggah gambar: ' . $image['error'], ['status' => 400]);

    $uploaded = media_handle_sideload($image, 0);

    if (is_wp_error($uploaded))
        return new WP_Error('upload_failed', $uploaded->get_error_message(), ['status' => 400]);

    $sliders = get_option('clasnet_sliders', []);
    $sliders[] = $uploaded;

    update_option('clasnet_sliders', $sliders);

    return rest_ensure_response(
        [
            'id' => count($sliders) - 1,
            'attachment_id' => $uploaded,
            'url' => wp_get_attachment_url($uploaded),
        ]
    );
}

/**
 * Memperbarui slider yang sudah ada.
 *
 * Fungsi ini menerima permintaan yang berisi data file gambar untuk
 * memperbarui slider yang sudah ada. Menggunakan `media_handle_sideload`
 * untuk mengunggah gambar baru dan menggantikan gambar lama yang
 * disimpan dalam opsi `clasnet_sliders`.
 *
 * @since 1.0
 *
 * @param WP_REST_Request $request Permintaan REST API yang berisi data file.
 *
 * @return WP_REST_Response|WP_Error Response REST API berisi detail slider
 *         yang diperbarui, atau WP_Error jika terjadi kesalahan.
 */
function clasnet_update_slider($request)
{
    $id = $request['id'];
    $sliders = get_option('clasnet_sliders', []);

    if (!isset($sliders[$id]))
        return new WP_Error('invalid_id', 'ID slider tidak valid', ['status' => 404]);

    $files = $request->get_file_params();

    if (empty($files['image']))
        return new WP_Error('no_image', 'Gambar diperlukan', ['status' => 400]);

    $uploaded = media_handle_sideload($files['image'], 0);

    if (is_wp_error($uploaded))
        return new WP_Error('upload_failed', $uploaded->get_error_message(), ['status' => 400]);

    wp_delete_attachment($sliders[$id]);

    $sliders[$id] = $uploaded;

    update_option('clasnet_sliders', $sliders);

    return rest_ensure_response(
        [
            'id' => $id,
            'attachment_id' => $uploaded,
            'url' => wp_get_attachment_url($uploaded),
        ]
    );
}

/**
 * Menghapus slider yang sudah ada.
 *
 * Fungsi ini menerima permintaan yang berisi ID slider yang ingin dihapus.
 * Menggunakan `wp_delete_attachment` untuk menghapus gambar slider yang
 * disimpan dalam opsi `clasnet_sliders`.
 *
 * @since 1.0
 *
 * @param WP_REST_Request $request Permintaan REST API yang berisi ID slider.
 *
 * @return WP_REST_Response|WP_Error Response REST API berisi pesan sukses
 *         jika slider dihapus, atau WP_Error jika terjadi kesalahan.
 */
function clasnet_delete_slider($request)
{
    $id = $request['id'];
    $sliders = get_option('clasnet_sliders', []);

    if (!isset($sliders[$id]))
        return new WP_Error('invalid_id', 'ID slider tidak valid', ['status' => 404]);

    wp_delete_attachment($sliders[$id]);

    unset($sliders[$id]);

    $sliders = array_values($sliders);

    update_option('clasnet_sliders', $sliders);

    return rest_ensure_response(['message' => 'Slider dihapus']);
}
?>