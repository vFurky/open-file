<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/security/login-check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/classes/FolderManager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/classes/Logger.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user'])) {
        throw new Exception('Oturum süreniz dolmuş.');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['name'])) {
        throw new Exception('Klasör adı gereklidir.');
    }

    $folderManager = new FolderManager($db, $_SESSION['user']['id']);
    $folderId = $folderManager -> createFolder(
        $input['name'],
        $input['parent_id'] ?? null,
        $input['description'] ?? null
    );

    echo json_encode([
        'status' => 'success',
        'message' => 'Klasör başarıyla oluşturuldu.',
        'folder_id' => $folderId
    ]);

} catch (Exception $e) {
    Logger::error("KLASOR_OLUSTURMA_HATASI: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}