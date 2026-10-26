<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Aturan unggah berkas pendaftaran
    |--------------------------------------------------------------------------
    |
    | Valid format dan ukuran maksimal berkas yang diterima untuk berkas
    | rapendaftaran (rapor, KK, dan berkas lainnya).
    |
    */

    'allowed_extensions' => ['pdf', 'jpg', 'jpeg', 'png'],

    'max_file_size_mb' => 5,

    'max_total_size_mb' => 15,

    'types' => [

        'rapor' => [
            'label' => 'Berkas Rapor',
            'required' => true,
            'description' => 'Format PDF, JPG, atau PNG (Maks. 5MB). Pastikan halaman biodata rapor dan tabel nilai semester tampak jelas serta terbaca.',
        ],

        'kk_ktp' => [
            'label' => 'Berkas KK/ KTP',
            'required' => false,
            'description' => 'Format PDF, JPG, atau PNG (Maks. 5MB).',
        ],

        'surat_komitmen' => [
            'label' => 'Surat Komitmen',
            'required' => false,
            'description' => 'Format PDF, JPG, atau PNG (Maks. 5MB).',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Catatan untuk developer
    |--------------------------------------------------------------------------
    |
    | Catatan: konfigurasi di bawah ini masih menunggu konfirmasi dari klien
    | mengenai format dan ukuran berkas yang diinginkan. Silakan sesuaikan
    | dengan spesifikasi yang disepakati.
    |
    */

    'TODO' => 'menunggu konfirmasi klien',

];