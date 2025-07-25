<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/security/login-check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/classes/FolderManager.php';

header('Content-Type: application/json');

try {
	if (!isset($_SESSION['user'])) {
		throw new Exception('Oturum süreniz dolmuş.');
	}

	$input = json_decode(file_get_contents('php://input'), true);

	if (!isset($input['file_id'])) {
		throw new Exception('Dosya ID\'si gereklidir.');
	}

	$folderManager = new FolderManager($db, $_SESSION['user']['id']);
	$result = $folderManager -> moveFile($input['file_id'], $input['folder_id'] ?? null);

	Logger::info("DOSYA_TASINDI: " . $input['file_id'] . " - " . $input['folder_id'] ?? null)
	echo json_encode([
		'status' => 'success',
		'message' => 'Dosya başarıyla taşındı!'
	]);

} catch (Exception $e) {
	http_response_code(400);
	Logger::error("[MOVE-FILE.PHP-38]-DOSYA_TASIMA_HATASI: " . $e -> getMessage());
	echo json_encode([
		'status' => 'error',
		'message' => 'Dosya taşınırken bir hata oluştu: ' . $e -> getMessage()
	]);
}