<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/security/login-check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/classes/FileDownloader.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/classes/Logger.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/classes/FileSecurity.php';

try {
    Logger::info("INDIRME_ISTEGI: " . ($_SESSION['user']['username'] ?? 'Guest'));

    $share_token = isset($_GET['token']) ? trim($_GET['token']) : '';
    
    if (empty($share_token)) {
        Logger::error("BOS_PAYLASIM_TOKENI_GONDERILDI: " . $_SERVER['REMOTE_ADDR']);
        header('HTTP/1.0 404 Not Found');
        exit('Dosya bulunamadı.');
    }

    if (!$db) {
        Logger::error("[DOWNLOAD.PHP-19]-VERITABANI_BAGLANTI_HATASI");
        throw new Exception("Bir hata oluştu, lütfen daha sonra tekrar deneyin.");
    }

    $downloader = new FileDownloader($db);
    $downloader -> download($share_token);

} catch (Exception $e) {
    header('HTTP/1.0 500 Internal Server Error');
    Logger::error("[DOWNLOAD.PHP-28]-INDIRME_HATASI: " . $e -> getMessage());
    exit('Bir hata oluştu, lütfen daha sonra tekrar deneyin.');
}