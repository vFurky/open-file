<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/security/login-check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/classes/FolderManager.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user'])) {
        throw new Exception('Oturum süreniz dolmuş.');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['folder_id'])) {
        throw new Exception('Klasör ID gerekli.');
    }

    $folderManager = new FolderManager($db, $_SESSION['user']['id']);
    $folderManager->deleteFolder($input['folder_id']);

    echo json_encode([
        'status' => 'success',
        'message' => 'Klasör ve içeriği başarıyla silindi.'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}