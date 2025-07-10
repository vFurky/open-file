<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/open-file/files/security/login-check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/open-file/files/classes/FolderManager.php';

header('Content-Type: application/json');

try {
	if (!isset($_SESSION['user'])) {
		throw new Exception('Oturum süreniz dolmuş.');
	}

	$input = json_decode(file_get_contents('php://input'), true);

	if (!isset($input['folder_id']) || !isset($input['new_name'])) {
		throw new Exception('Gerekli parametreler eksik.');
	}

	$folderManager = new FolderManager($db, $_SESSION['user']['id']);
	$folderManager->renameFolder($input['folder_id'], $input['new_name']);

	echo json_encode([
		'status' => 'success',
		'message' => 'Klasör adı başarıyla değiştirildi.'
	]);

} catch (Exception $e) {
	http_response_code(400);
	echo json_encode([
		'status' => 'error',
		'message' => $e->getMessage()
	]);
}