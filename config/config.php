<?php
return [
    'upload' => [
        'max_size' => 104857600,  // Maksimum Dosya Boyutu - 100MB
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip', 'rar'],  // İzin Verilen Dosya Uzantıları
        'expire_days' => 30  // Geçerlilik Süresi
    ],
    'security' => [
        'allowed_mime_types' => [
            'image/jpeg', 'image/png', 'image/gif', 'application/pdf'  // İzin Verilen MIME Tipleri
        ]
    ],
    'paths' => [
        'upload_dir' => $_SERVER['DOCUMENT_ROOT'] . '/open-file/uploaded-files/',  // Yükleme Yolu
        'log_dir' => $_SERVER['DOCUMENT_ROOT'] . '/open-file/logs/',  // Log Yolu
        'site_url' => 'http://localhost/open-file/', // Site URL
        'site_name' => 'OpenFile', // Site Adı
        'style_path' => $_SERVER['DOCUMENT_ROOT'] . '/open-file/style/' // Stil Yolu
    ]
];
