<?php
header('Content-Type: application/json; charset=utf-8');
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");

ini_set('session.cookie_httponly', 1);
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/files/security/login-check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	exit;
}

if (!isset($_SESSION['user']['id']) || !is_numeric($_SESSION['user']['id'])) {
	http_response_code(403);
	echo json_encode([
		'status' => 'error',
		'message' => 'Yetkisiz erişim.'
	]);
	exit;
}

$user_id = (int) $_SESSION['user']['id'];

if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
	echo json_encode([
		'status' => 'error',
		'message' => 'Geçersiz CSRF token.'
	]);
	exit;
}

$currentPassword = trim($_POST['currentPassword'] ?? '');
$newPassword = trim($_POST['newPassword'] ?? '');
$confirmPassword = trim($_POST['confirmPassword'] ?? '');

if ($newPassword !== $confirmPassword) {
	echo json_encode([
		'status' => 'error',
		'field' => 'confirmPassword',
		'message' => 'Girdiğiniz yeni parolalar eşleşmiyor, lütfen kontrol edin.'
	]);
	exit;
}

if (strlen($newPassword) < 8) {
	echo json_encode([
		'status' => 'error',
		'field' => 'newPassword',
		'message' => 'Yeni parola en az 8 karakter uzunluğunda olmalı.'
	]);
	exit;
}

$complexityPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/';
if (!preg_match($complexityPattern, $newPassword)) {
	echo json_encode([
		'status' => 'error',
		'field'  => 'newPassword',
		'message'=> 'Yeni parola en az 8 karakter, 1 büyük harf, 1 küçük harf, 1 rakam ve 1 özel karakter içermelidir.'
	]);
	exit;
}

try {
	$changePw = $db -> prepare("SELECT password FROM users WHERE id = :id");
	$changePw -> execute([':id' => $user_id]);
	$row = $changePw -> fetch(PDO::FETCH_ASSOC);
	$storedHash	 = $row['password'];
	$inputHash = md5(sha1($currentPassword));

	if (!$row || $inputHash !== $storedHash) {
		echo json_encode([
			'status' => 'error',
			'field'  => 'currentPassword',
			'message'=> 'Mevcut parolanızı yanlış girdiniz, lütfen tekrar deneyin!'
		]);
		exit;
	}

	$hashedPw = md5(sha1($newPassword));

	if ($hashedPw === $storedHash) {
		echo json_encode([
			'status' => 'error',
			'field'  => 'newPassword',
			'message'=> 'Yeni parola, mevcut parolanızla aynı olamaz. Lütfen farklı bir parola girin.'
		]);
		exit;
	}
	
	$updatePw = $db -> prepare("UPDATE users SET password = :pass WHERE id = :id");
	$updatePw -> execute([':pass' => $hashedPw, ':id' => $user_id]);

	session_unset();
	session_destroy();

	Logger::info("PAROLA_DEGISTIRILDI: " . $user_id);
	echo json_encode([
		'status' => 'success',
		'message' => 'Parolanız başarıyla güncellendi, güvenlik gereği otomatik olarak çıkış yaptınız. Lütfen tekrar giriş yapın.'
	]);

} catch (PDOException $e) {
	Logger::error('[CHANGE-PASSWORD.PHP-107]-PAROLA_GUNCELLEME_HATASI: ' . $e -> getMessage());
	echo json_encode([
		'status' => 'error',
		'message' => 'Bir veritabanı hatası oluştu, lütfen daha sonra tekrar deneyin.'
	]);
}