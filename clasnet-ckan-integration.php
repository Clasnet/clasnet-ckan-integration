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
add_action('admin_menu', 'clasnet_add_ticket_menu');
add_action('admin_enqueue_scripts', 'clasnet_enqueue_admin_scripts');
add_action('rest_api_init', 'clasnet_register_slider_api_routes');
add_action('rest_api_init', 'clasnet_register_ticket_api_routes');

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
        'Pengaturan Slider Infografis',
        'Infografis',
        'manage_options',
        'clasnet-slider-settings',
        'clasnet_render_slider_settings_page',
        'dashicons-images-alt2',
        6
    );
}

/**
 * Tambahkan menu tiket di admin
 *
 * Menambahkan menu top-level untuk Tiket, serupa dengan Slider.
 *
 * @since 1.0
 */
function clasnet_add_ticket_menu()
{
    add_menu_page(
        'Pengaturan Tiket',
        'Tiket',
        'manage_options',
        'clasnet-ticket-settings',
        'clasnet_render_ticket_settings_page',
        'dashicons-tickets',
        7
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
        <h1>Pengaturan Slider Infografis</h1>
        <form method="post">
            <?php wp_nonce_field('clasnet_slider_nonce', 'clasnet_slider_nonce_field'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="clasnet_slider_image">Unggah Gambar Slider Infografis</label></th>
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

        <h2>Gambar Slider Infografis Saat Ini</h2>
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
 * Menambahkan skrip dan gaya administrasi
 *
 * Skrip ini memungkinkan pengguna untuk mengunggah gambar slider
 * melalui tombol "Unggah Gambar" di halaman pengaturan slider.
 * Gaya digunakan untuk menyesuaikan tampilan ikon status tiket.
 *
 * @since 1.0
 * @param string $hook Nama hook yang sedang dijalankan.
 */
function clasnet_enqueue_admin_scripts($hook)
{
    if ($hook === 'toplevel_page_clasnet-slider-settings')
    {
        wp_enqueue_media();
        wp_enqueue_script('clasnet-admin-script', plugin_dir_url(__FILE__) . 'admin-script.js', ['jquery', 'media-upload'], '1.1', true);
    }

    if ($hook === 'toplevel_page_clasnet-ticket-settings')
        wp_enqueue_style('clasnet-admin-style', plugin_dir_url(__FILE__) . 'admin-style.css', [], '1.0');
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

    return rest_ensure_response(['message' => 'Slider Infografis dihapus']);
}

/**
 * Render halaman pengaturan tiket di menu admin
 *
 * Halaman ini digunakan untuk mengelola tiket yang diajukan.
 *
 * @since 1.0
 */
function clasnet_render_ticket_settings_page()
{
    // Buat Tiket
    if (isset($_POST['clasnet_ticket_submit']) && check_admin_referer('clasnet_ticket_nonce', 'clasnet_ticket_nonce_field'))
    {
        $tickets = get_option('clasnet_tickets', []);
        $ticket_id = isset($_POST['clasnet_ticket_id']) ? sanitize_text_field($_POST['clasnet_ticket_id']) : '';

        $ticket_data = array(
            'nama_dataset' => sanitize_text_field($_POST['clasnet_nama_dataset']),
            'nama' => sanitize_text_field($_POST['clasnet_nama']),
            'tanggal' => sanitize_text_field($_POST['clasnet_tanggal']),
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
    if (isset($_GET['action']) && $_GET['action'] === 'delete_ticket' && isset($_GET['ticket_id']) && check_admin_referer('clasnet_delete_ticket_nonce', '_wpnonce'))
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

    if (isset($_GET['action']) && $_GET['action'] === 'edit_ticket' && isset($_GET['ticket_id']) && check_admin_referer('clasnet_edit_ticket_nonce', '_wpnonce'))
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
        <h1>Pengaturan Tiket</h1>
        <?php if ($show_form) : ?>
            <h2><?php echo $edit_ticket ? 'Ubah Tiket' : 'Tambah Tiket Baru'; ?></h2>
            <form method="post">
                <?php wp_nonce_field('clasnet_ticket_nonce', 'clasnet_ticket_nonce_field'); ?>
                <input type="hidden" name="clasnet_ticket_id" value="<?php echo esc_attr($edit_ticket_id); ?>">
                <table class="form-table">
                    <tr>
                        <th><label for="clasnet_ticket_id_display">ID Tiket</label></th>
                        <td>
                            <input type="text" id="clasnet_ticket_id_display" value="<?php echo esc_attr($edit_ticket['ticket_id'] ?? 'Akan dibuat otomatis'); ?>" style="width:100%;" readonly>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="clasnet_nama_dataset">Nama Dataset</label></th>
                        <td>
                            <input type="text" name="clasnet_nama_dataset" id="clasnet_nama_dataset" value="<?php echo esc_attr($edit_ticket['nama_dataset'] ?? ''); ?>" style="width:100%;" <?php echo $edit_ticket ? 'readonly' : ''; ?>>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="clasnet_nama">Nama Peminta</label></th>
                        <td>
                            <input type="text" name="clasnet_nama" id="clasnet_nama" value="<?php echo esc_attr($edit_ticket['nama'] ?? ''); ?>" style="width:100%;" <?php echo $edit_ticket ? 'readonly' : ''; ?>>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="clasnet_tanggal">Tanggal</label></th>
                        <td>
                            <input type="date" name="clasnet_tanggal" id="clasnet_tanggal" value="<?php echo esc_attr($edit_ticket['tanggal'] ?? date('Y-m-d')); ?>" style="width:100%;" <?php echo $edit_ticket ? 'readonly' : ''; ?>>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="clasnet_no_hp">No. HP</label></th>
                        <td>
                            <input type="text" name="clasnet_no_hp" id="clasnet_no_hp" value="<?php echo esc_attr($edit_ticket['no_hp'] ?? ''); ?>" style="width:100%;" <?php echo $edit_ticket ? 'readonly' : ''; ?>>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="clasnet_email">Email</label></th>
                        <td>
                            <input type="email" name="clasnet_email" id="clasnet_email" value="<?php echo esc_attr($edit_ticket['email'] ?? ''); ?>" style="width:100%;" <?php echo $edit_ticket ? 'readonly' : ''; ?>>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="clasnet_instansi">Instansi/Organisasi/Perusahaan</label></th>
                        <td>
                            <input type="text" name="clasnet_instansi" id="clasnet_instansi" value="<?php echo esc_attr($edit_ticket['instansi'] ?? ''); ?>" style="width:100%;" <?php echo $edit_ticket ? 'readonly' : ''; ?>>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="clasnet_pekerjaan">Pekerjaan</label></th>
                        <td>
                            <input type="text" name="clasnet_pekerjaan" id="clasnet_pekerjaan" value="<?php echo esc_attr($edit_ticket['pekerjaan'] ?? ''); ?>" style="width:100%;" <?php echo $edit_ticket ? 'readonly' : ''; ?>>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="clasnet_keperluan">Keperluan</label></th>
                        <td>
                        <input type="text" name="clasnet_keperluan" id="clasnet_keperluan" value="<?php echo esc_attr($edit_ticket['keperluan'] ?? ''); ?>" style="width:100%;" <?php echo $edit_ticket ? 'readonly' : ''; ?>>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="clasnet_status">Status</label></th>
                        <td>
                            <select name="clasnet_status" id="clasnet_status">
                                <?php
                                $status = $edit_ticket['status'] ?? 'Menunggu Persetujuan';
                                ?>
                                <option value="Menunggu Persetujuan" <?php selected($status, 'Menunggu Persetujuan'); ?>>Menunggu Persetujuan</option>
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
                <a href="<?php echo esc_url(add_query_arg('action', 'add_ticket')); ?>" class="button button-primary">Tambah Tiket Baru</a>
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
                        <option value="<?php echo esc_attr($option); ?>" <?php selected($pekerjaan_filter, $option); ?>><?php echo esc_html($option); ?></option>
                    <?php endforeach; ?>
                </select>

                <?php
                // Filter Keperluan
                $keperluan_options = array('Penelitian', 'Analisa Bisnis', 'Kebijakan/Perencanaan', 'Lainnya');
                ?>
                <select name="keperluan">
                    <option value=""><?php _e('Seluruh Keperluan', 'text_domain'); ?></option>
                    <?php foreach ($keperluan_options as $option) : ?>
                        <option value="<?php echo esc_attr($option); ?>" <?php selected($keperluan_filter, $option); ?>><?php echo esc_html($option); ?></option>
                    <?php endforeach; ?>
                </select>

                <?php
                // Filter Status
                $status_options = array('Menunggu Persetujuan', 'Disetujui');
                ?>
                <select name="ticket_status">
                    <option value=""><?php _e('Seluruh Status', 'text_domain'); ?></option>
                    <?php foreach ($status_options as $option) : ?>
                        <option value="<?php echo esc_attr($option); ?>" <?php selected($status_filter, $option); ?>><?php echo esc_html($option); ?></option>
                    <?php endforeach; ?>
                </select>

                <input type="submit" value="Filter" class="button">
            </form>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 2%">No.</th>
                        <th style="width: 5%">
                            <a href="<?php echo esc_url(add_query_arg(array('sort_by' => 'tanggal', 'sort_order' => $sort_by === 'tanggal' ? $sort_order_opposite : 'asc'), $base_url)); ?>">
                                Tanggal
                                <?php if ($sort_by === 'tanggal') echo $sort_order === 'asc' ? '↑' : '↓'; ?>
                            </a>
                        </th>
                        <th>
                            <a href="<?php echo esc_url(add_query_arg(array('sort_by' => 'nama_dataset', 'sort_order' => $sort_by === 'nama_dataset' ? $sort_order_opposite : 'asc'), $base_url)); ?>">
                                Nama Dataset
                                <?php if ($sort_by === 'nama_dataset') echo $sort_order === 'asc' ? '↑' : '↓'; ?>
                            </a>
                        </th>
                        <th>
                            <a href="<?php echo esc_url(add_query_arg(array('sort_by' => 'nama', 'sort_order' => $sort_by === 'nama' ? $sort_order_opposite : 'asc'), $base_url)); ?>">
                                Nama Peminta
                                <?php if ($sort_by === 'nama') echo $sort_order === 'asc' ? '↑' : '↓'; ?>
                            </a>
                        </th>
                        <th>
                            <a href="<?php echo esc_url(add_query_arg(array('sort_by' => 'email', 'sort_order' => $sort_by === 'email' ? $sort_order_opposite : 'asc'), $base_url)); ?>">
                                Email
                                <?php if ($sort_by === 'email') echo $sort_order === 'asc' ? '↑' : '↓'; ?>
                            </a>
                        </th>
                        <th>
                            <a href="<?php echo esc_url(add_query_arg(array('sort_by' => 'instansi', 'sort_order' => $sort_by === 'instansi' ? $sort_order_opposite : 'asc'), $base_url)); ?>">
                                Instansi/Organisasi/Perusahaan
                                <?php if ($sort_by === 'instansi') echo $sort_order === 'asc' ? '↑' : '↓'; ?>
                            </a>
                        </th>
                        <th>
                            <a href="<?php echo esc_url(add_query_arg(array('sort_by' => 'pekerjaan', 'sort_order' => $sort_by === 'pekerjaan' ? $sort_order_opposite : 'asc'), $base_url)); ?>">
                                Pekerjaan
                                <?php if ($sort_by === 'pekerjaan') echo $sort_order === 'asc' ? '↑' : '↓'; ?>
                            </a>
                        </th>
                        <th>
                            <a href="<?php echo esc_url(add_query_arg(array('sort_by' => 'keperluan', 'sort_order' => $sort_by === 'keperluan' ? $sort_order_opposite : 'asc'), $base_url)); ?>">
                                Keperluan
                                <?php if ($sort_by === 'keperluan') echo $sort_order === 'asc' ? '↑' : '↓'; ?>
                            </a>
                        </th>
                        <th>
                            <a href="<?php echo esc_url(add_query_arg(array('sort_by' => 'status', 'sort_order' => $sort_by === 'status' ? $sort_order_opposite : 'asc'), $base_url)); ?>">
                                Status
                                <?php if ($sort_by === 'status') echo $sort_order === 'asc' ? '↑' : '↓'; ?>
                            </a>
                        </th>
                        <th>
                            <a href="<?php echo esc_url(add_query_arg(array('sort_by' => 'ticket_id', 'sort_order' => $sort_by === 'ticket_id' ? $sort_order_opposite : 'asc'), $base_url)); ?>">
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
                                <td style="place-content: center"><?php echo esc_html($ticket['tanggal'] ?? '-'); ?></td>
                                <td style="place-content: center"><?php echo esc_html($ticket['nama_dataset'] ?? '-'); ?></td>
                                <td style="place-content: center"><?php echo esc_html($ticket['nama']); ?></td>
                                <td style="place-content: center"><?php echo esc_html($ticket['email']); ?></td>
                                <td style="place-content: center"><?php echo esc_html($ticket['instansi']); ?></td>
                                <td style="place-content: center"><?php echo esc_html($ticket['pekerjaan']); ?></td>
                                <td style="place-content: center"><?php echo esc_html($ticket['keperluan']); ?></td>
                                <td style="place-content: center">
                                    <?php if ($ticket['status'] === 'Disetujui') : ?>
                                        <span class="dashicons dashicons-yes-alt ticket-status ticket-status-approved"></span> Disetujui
                                    <?php elseif ($ticket['status'] === 'Menunggu Persetujuan') : ?>
                                        <span class="dashicons dashicons-info ticket-status ticket-status-pending"></span> Menunggu Persetujuan
                                    <?php endif; ?>
                                </td>
                                <td style="place-content: center"><?php echo esc_html($ticket['ticket_id']); ?></td>
                                <td style="place-content: center">
                                    <a href="<?php echo esc_url(wp_nonce_url(add_query_arg(array('action' => 'edit_ticket', 'ticket_id' => $ticket['ticket_id']), admin_url('admin.php?page=clasnet-ticket-settings')), 'clasnet_edit_ticket_nonce', '_wpnonce')); ?>" class="button button-secondary">Ubah</a>
                                    <a href="<?php echo esc_url(wp_nonce_url(add_query_arg(array('action' => 'delete_ticket', 'ticket_id' => $ticket['ticket_id']), admin_url('admin.php?page=clasnet-ticket-settings')), 'clasnet_delete_ticket_nonce', '_wpnonce')); ?>" class="button button-secondary" onclick="return confirm('Apakah Anda yakin ingin menghapus tiket ini?');">Hapus</a>
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
 * - POST /tiket: Membuat tiket baru dari CKAN.
 * - GET /tiket/pending: Mengembalikan jumlah tiket yang menunggu persetujuan.
 * - GET /tiket/status/:ticket_id: Mengembalikan status tiket berdasarkan ticket_id.
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
}

/**
 * Membuat tiket baru melalui API
 *
 * Menerima data tiket dari CKAN dan membuat tiket di WordPress.
 *
 * @since 1.0
 * @param WP_REST_Request $request Permintaan REST API.
 * @return WP_REST_Response|WP_Error Response atau error.
 */
function clasnet_create_ticket($request)
{
    $params = $request->get_params();

    $required_fields = array(
        'nama_dataset',
        'nama',
        'tanggal',
        'no_hp',
        'email',
        'instansi',
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
        'tanggal' => sanitize_text_field($params['tanggal']),
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
 * Mengembalikan jumlah tiket dengan status "Menunggu Persetujuan".
 *
 * @since 1.0
 * @return WP_REST_Response Response dengan jumlah tiket pending.
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
 * Mendapatkan status tiket berdasarkan ticket_id
 *
 * Mengembalikan status tiket berdasarkan ticket_id yang diberikan.
 *
 * @since 1.0
 * @param WP_REST_Request $request Permintaan REST API.
 * @return WP_REST_Response|WP_Error Response atau error.
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
?>