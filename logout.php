<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/open-file/files/security/login-check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/open-file/files/classes/Logger.php';

try {
    $userInfo = isset($_SESSION['user']) ? [
        'id' => $_SESSION['user']['id'],
        'username' => $_SESSION['user']['username']
    ] : null;

    if ($userInfo) {

        Logger::info("CIKIS_ISLEMI_BASLADI: " . $userInfo['username']);
        if (isset($_COOKIE['remember_token'])) {
            try {
                $clearToken = $db -> prepare("UPDATE users SET remember_token = NULL, token_expiry = NULL, WHERE id = :id");
                $clearToken -> bindParam(':id', $userInfo['id']);
                
                if (!$clearToken -> execute()) {
                    throw new Exception("Token clear query failed");
                }

                setcookie('remember_token', '', time() - 3600, '/', '', true, true);
                Logger::info("Remember token cleared for user: " . $userInfo['username']);
            } catch (Exception $e) {
                Logger::error("Token clear error for user " . $userInfo['username'] . ": " . $e->getMessage());
            }
        }

        $_SESSION = array();
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/', '', true, true);
        }

        session_destroy();
        Logger::info("CIKIS_YAPTI: " . $userInfo['username']);
    }

    if (ob_get_length()) {
        ob_end_clean();
    }

    $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Başarıyla çıkış yaptınız.'];
    header("Location: " . $site_url . "login", true, 302);
    exit;

} catch (Exception $e) {
    Logger::error("[LOGOUT.PHP-100]-KRITIK_CIKIS_HATASI: " . $e->getMessage());
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Çıkış yaparken bir hata oluştu. Lütfen tekrar deneyin.'];
    header("Location: " . $site_url . "login", true, 302);
    exit;
}