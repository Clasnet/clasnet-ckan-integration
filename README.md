# Clasnet CKAN Integration dengan Manajemen E-Book

## Deskripsi:
**Clasnet CKAN Integration** adalah plugin WordPress yang dirancang untuk mengintegrasikan **CKAN** *(Comprehensive Knowledge Archive Network)* ke situs WordPress Anda menggunakan kata sandi aplikasi.

Plugin ini menyediakan endpoint REST API yang aman untuk otentikasi pengguna dan memperluas fungsi WordPress dengan menambahkan jenis posting khusus untuk mengelola E-Book.

## Fitur Utama:
- **Integrasi CKAN**
   - Endpoint REST API aman (`/ckan/v1/login`) untuk otentikasi pengguna melalui kata sandi aplikasi.
   - Mempermudah integrasi dengan platform CKAN untuk berbagi dan mengelola data.

- **Manajemen E-Book**
   - Menambahkan jenis posting khusus bernama **E-Book** (`ebook`) untuk mengatur publikasi digital.
   - Dilengkapi taksonomi khusus untuk kategorisasi:
     - **Kategori E-Book** (kategori hierarkis).
     - **Tag E-Book** (tag non-hierarkis).
   - Mendukung judul, editor, gambar unggulan, kutipan, bidang kustom, dan komentar.

- **Antarmuka Ramah Pengguna**
   - Seluruh teks diterjemahkan ke dalam bahasa Indonesia untuk kemudahan pengguna lokal.
   - Antarmuka admin intuitif untuk mengelola E-Book, kategori, dan tag.

- **Ekstensibel dan Modular**
   - Dibangun sesuai standar pengkodean WordPress untuk kemudahan penyesuaian dan skalabilitas.
   - Siap untuk kontribusi dan peningkatan fitur.

#### Kasus Penggunaan
- Mengelola perpustakaan digital atau koleksi E-Book di situs WordPress Anda.
- Mengintegrasikan dataset CKAN ke WordPress untuk menerbitkan dan berbagi sumber daya pengetahuan.
- Mengatur E-Book dengan kategori dan tag untuk navigasi dan pencarian yang lebih baik.

---

## Prasyarat
- WordPress 6.8 atau lebih tinggi.
- PHP 7.4 atau lebih tinggi.

## Instalasi
1. Clone atau unduh repositori ini.
2. Unggah folder plugin ke direktori `wp-content/plugins` WordPress Anda.
3. Aktifkan plugin melalui dasbor admin WordPress.
4. Mulai gunakan endpoint integrasi CKAN dan kelola E-Book dari menu **E-Book** baru.

---


## Lisensi

Ekstensi ini dilisensikan di bawah **GPLv2 License**. Lihat file ![LICENSE](LICENSE) untuk detailnya.