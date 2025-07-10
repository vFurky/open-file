<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/open-file/files/security/auth-check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/open-file/files/classes/Auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/open-file/files/classes/Logger.php';

try {
    $auth = Auth::getInstance($db);
    
    if (isset($_SESSION['user'])) {
        header("Location: " . $site_url . "home");
        exit;
    }

    $err = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!isset($_POST['csrf_token']) || !$auth->validateCSRFToken($_POST['csrf_token'])) {
            throw new Exception('Güvenlik doğrulaması başarısız!');
        }

        if (empty($_POST['emailInput']) || empty($_POST['passwordInput'])) {
            throw new Exception('Lütfen tüm alanları doldurun!');
        }

        $auth -> login(filter_var($_POST['emailInput'], FILTER_SANITIZE_EMAIL), $_POST['passwordInput'], isset($_POST['rememberMe']) && $_POST['rememberMe'] == 'on');

        header("Location: " . $site_url . "home");
        exit;
    }

} catch (Exception $e) {
    Logger::error("LOGIN_FORM_HATASI: " . $e->getMessage());
    $err = 'Hata!<br>' . $e->getMessage();
}

require realpath('.') . '/view-login.php';