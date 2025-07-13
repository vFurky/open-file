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
	$success = '';

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (!isset($_POST['csrf_token']) || !$auth->validateCSRFToken($_POST['csrf_token'])) {
			throw new Exception('Güvenlik doğrulaması başarısız!');
		}

		$userId = $auth->register(['username' => trim($_POST['username'] ?? ''), 'email' => trim($_POST['email'] ?? ''), 'password' => $_POST['password'] ?? '', 'confirmPassword' => $_POST['confirmPassword'] ?? '', 'termsCheck' => $_POST['termsCheck'] ?? '']);
		$auth -> login(trim($_POST['email']), $_POST['password'], false);

		header("Location: " . $site_url . "home");
		exit;
	}

} catch (Exception $e) {
	Logger::error("KAYIT_FORM_HATASI: " . $e->getMessage());
    $err = 'Hata!<br>' . $e->getMessage();
}

require realpath('.') . '/view-register.php';