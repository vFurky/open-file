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
        throw new Exception('Lütfen bir klasör ismi girin.');
    }

    $folderManager = new FolderManager($db, $_SESSION['user']['id']);
    $folderId = $folderManager -> createFolder(
        $input['name'],
        $input['parent_id'] ?? null,
        $input['description'] ?? null
    );

    Logger::info("KLASOR_OLUSTURULDU: " . $folderId);
    echo json_encode([
        'status' => 'success',
        'message' => 'Klasör başarıyla oluşturuldu.',
        'folder_id' => $folderId
    ]);

} catch (Exception $e) {
    http_response_code(400);
    Logger::error("KLASOR_OLUSTURMA_HATASI: " . $e -> getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Klasör oluştururken bir hata meydana geldi: ' . $e -> getMessage()
    ]);
}