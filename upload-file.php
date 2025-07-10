<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/security/login-check.php';

$config = require $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user'])) throw new Exception('Oturum süreniz dolmuş.');
    if (!isset($_FILES['file'])) throw new Exception('Dosya bulunamadı.');

    $file = $_FILES['file'];
    $user_id = $_SESSION['user']['id'];

    if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception('Dosya yükleme hatası: ' . $file['error']);
    if ($file['size'] > $config['upload']['max_size']) throw new Exception('Maksimum dosya boyutunu aştınız.');

    $original_name = pathinfo($file['name'], PATHINFO_FILENAME);
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $safe_filename = preg_replace('/[^a-z0-9]+/', '-', strtolower($original_name));
    $unique_filename = sprintf('%s_%s.%s', $safe_filename, uniqid(), $extension);

    $date = new DateTime('now', new DateTimeZone('UTC'));
    $year_month = $date->format('Y/m');
    $upload_dir = $config['paths']['upload_dir'] . $year_month;

    if (!file_exists($upload_dir) && !mkdir($upload_dir, 0755, true)) {
        throw new Exception('Klasör oluşturulamadı.');
    }

    $file_path = $upload_dir . '/' . $unique_filename;
    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        throw new Exception('Dosya yüklenirken bir hata oluştu.');
    }

    $relative_path = 'uploaded-files/' . $year_month . '/' . $unique_filename;
    $share_token = bin2hex(random_bytes(32));
    $expires_at = $date->modify("+{$config['upload']['expire_days']} days")->format('Y-m-d H:i:s');
    $mime_type = mime_content_type($file_path);

    if (!in_array($mime_type, $config['security']['allowed_mime_types'])) {
        unlink($file_path);
        throw new Exception('Dosya türü geçersiz.');
    }

    $uploadFile = $db->prepare("INSERT INTO files (user_id, file_name, file_path, file_size, original_name, mime_type, is_public, share_token, expires_at, created_at) VALUES (:user_id, :file_name, :file_path, :file_size, :original_name, :mime_type, :is_public, :share_token, :expires_at, UTC_TIMESTAMP())");

    $result = $uploadFile->execute([
        ':user_id' => $user_id,
        ':file_name' => $file['name'],
        ':file_path' => $relative_path,
        ':file_size' => $file['size'],
        ':original_name' => $file['name'],
        ':mime_type' => $mime_type,
        ':is_public' => 0,
        ':share_token' => $share_token,
        ':expires_at' => $expires_at
    ]);

    if (!$result) {
        unlink($file_path);
        throw new Exception('Veritabanı kaydı başarısız.');
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Dosya başarıyla yüklendi!',
        'file' => [
            'name' => $file['name'],
            'size' => $file['size'],
            'share_token' => $share_token
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}