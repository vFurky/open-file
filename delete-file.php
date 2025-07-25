<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/security/login-check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/classes/Logger.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
	http_response_code(401);
	echo json_encode(['status' => 'error', 'message' => 'Oturum süreniz dolmuş.']);
	exit;
}

try {
	$input = json_decode(file_get_contents('php://input'), true);
	
	if (!isset($input['file_id'])) {
		throw new Exception('Geçersiz istek.');
	}

	$getFile = $db -> prepare("SELECT * FROM files WHERE id = ? AND user_id = ?");
	$getFile -> execute([$input['file_id'], $_SESSION['user']['id']]);
	$file = $getFile -> fetch(PDO::FETCH_ASSOC);

	if (!$file) {
		throw new Exception('Dosya bulunamadı.');
	}

	$file_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $file['file_path'];
	if (file_exists($file_path)) {
		if (!unlink($file_path)) {
			Logger::error("DOSYA_SISTEMDEN_SILINEMEDI: " . $file_path);
			throw new Exception('Dosya sistemden silinemedi.');
		}
	}

	$deleteFile = $db -> prepare("UPDATE files SET deleted_at = UTC_TIMESTAMP() WHERE id = ? AND user_id = ? AND deleted_at IS NULL");
	if (!$deleteFile -> execute([$input['file_id'], $_SESSION['user']['id']])) {
		Logger::error("DOSYA_VERITABANINDAN_SILINEMEDI: " . $input['file_id']);
		throw new Exception('Dosya veritabanından silinemedi.');
	}

	Logger::info("DOSYA_SILINDI: " . $file['file_name'] . " - " . $_SESSION['user']['username']);
	
	echo json_encode([
		'status' => 'success',
		'message' => 'Dosya başarıyla silindi.'
	]);

} catch (Exception $e) {
	http_response_code(400);
	Logger::error("[DELETE-FILE.PHP-48]-DOSYA_SILME_HATASI: " . $e->getMessage());
	echo json_encode([
		'status' => 'error',
		'message' => $e->getMessage()
	]);
}