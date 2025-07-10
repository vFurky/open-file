<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/classes/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/classes/Logger.php';

if (!isset($_SESSION)) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true
    ]);
}

try {
    $database = Database::getInstance();
    $db = $database -> getConnection();
    $db -> query('SELECT 1');
    
} catch (Exception $e) {
    Logger::error("VERITABANI_BAGLANTI_HATASI: " . $e->getMessage());
    
    if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
        die("Database Error: " . $e->getMessage());
    } else {
        die("Sistemde geçici bir sorun oluştu. Lütfen daha sonra tekrar deneyiniz.");
    }
}

function cleanupDatabase() {
    global $db;
    $db = null;
}

register_shutdown_function('cleanupDatabase');