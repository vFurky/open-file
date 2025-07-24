<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/security/login-check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/classes/FileManager.php';

header('Content-Type: application/json');

try {
	if (!isset($_SESSION['user'])) {
		throw new Exception('Oturum süreniz dolmuş.');
	}

	if (!isset($_GET['file_id'])) {
		throw new Exception('Dosya ID\'si belirtilmedi.');
	}

	$fileManager = new FileManager($db);
	$preview = $fileManager -> getFilePreview($_GET['file_id'], $_SESSION['user']['id']);

	echo json_encode([
		'success' => true,
		'path' => $preview['path'],
		'type' => $preview['type'],
		'name' => $preview['name']
	]);

} catch (Exception $e) {
	http_response_code(400);
	echo json_encode([
		'success' => false,
		'error' => $e->getMessage()
	]);
}