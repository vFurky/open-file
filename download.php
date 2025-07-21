<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/security/login-check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/classes/FileDownloader.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/classes/Logger.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/classes/FileSecurity.php';

try {
    Logger::info("INDIRME_ISTEGI: " . ($_SESSION['user']['username'] ?? 'Guest'));

    $share_token = isset($_GET['token']) ? trim($_GET['token']) : '';
    
    if (empty($share_token)) {
        Logger::error("BOS_TOKEN_GONDERILDI");
        header('HTTP/1.0 404 Not Found');
        exit('Dosya artık bulunmuyor veya süresi dolmuş.');
    }

    if (!$db) {
        Logger::error("VERITABANI_BAGLANTI_HATASI");
        throw new Exception("Veritabanı bağlantısı kurulamadı");
    }

    $downloader = new FileDownloader($db);
    $downloader->download($share_token);

} catch (Exception $e) {
    Logger::error("[DOWNLOAD.PHP-27]-INDIRME_HATASI: " . $e->getMessage());
    header('HTTP/1.0 500 Internal Server Error');
    exit('Bir hata oluştu: ' . $e->getMessage());
}