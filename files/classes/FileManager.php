<?php
class FileManager {
	private $db;
	private $config;

	public function __construct($db, $config) {
		$this->db = $db;
		$this->config = $config;
	}

	public function handleUpload($file, $userId) {
		$this->validateFile($file);

		$filePath = $this->createFilePath($file);
		$mime = mime_content_type($file['tmp_name']);

		$metadata = [
			'original_name' => $file['name'],
			'file_size' => $file['size'],
			'mime_type' => $mime,
			'upload_date' => date('Y-m-d H:i:s'),
			'share_token' => $this->createShareToken(),
			'expires_at' => date('Y-m-d H:i:s', strtotime("+{$this->config['upload']['expire_days']} days")),
			'is_public' => 0
		];

		if (!$this->moveFile($file['tmp_name'], $filePath)) {
			throw new Exception('Dosya yüklenemedi');
		}

		return $this->saveToDatabase($metadata, $filePath, $userId);
	}

	private function createFilePath($file) {
		$original_name = pathinfo($file['name'], PATHINFO_FILENAME);
		$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		$safe_filename = preg_replace('/[^a-z0-9]+/', '-', strtolower($original_name));
		$unique_filename = sprintf('%s_%s.%s', $safe_filename, uniqid(), $extension);

		$date = new DateTime('now', new DateTimeZone('UTC'));
		$year_month = $date->format('Y/m');
		$upload_dir = $this->config['paths']['upload_dir'] . $year_month;

		if (!file_exists($upload_dir)) {
			mkdir($upload_dir, 0755, true);
		}

		return $upload_dir . '/' . $unique_filename;
	}

	public function getFileStatistics($userId) {
		$stats = $this->db->prepare("SELECT COUNT(*) as total_files, SUM(file_size) as total_size, COUNT(CASE WHEN is_public = 1 THEN 1 END) as public_files, COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today_uploads FROM files WHERE user_id = ? AND deleted_at IS NULL");
		$stats -> execute([$userId]);
		return $stats -> fetch(PDO::FETCH_ASSOC);
	}

	public function setFileVisibility($fileId, $userId, $isPublic) {
		$update = $this->db->prepare("UPDATE files SET is_public = ?, updated_at = UTC_TIMESTAMP() WHERE id = ? AND user_id = ?");
		return $update -> execute([$isPublic, $fileId, $userId]);
	}

	public function updateFileMetadata($fileId, $userId, $metadata) {
		$allowedFields = ['title', 'description', 'expires_at', 'is_public'];
		$updates = array_intersect_key($metadata, array_flip($allowedFields));

		if (empty($updates)) {
			return false;
		}

		$updateFileMetadata = $this->db->prepare("UPDATE files SET " . implode(', ', array_map(fn($k) => "$k = :$k", array_keys($updates))) . ", updated_at = UTC_TIMESTAMP() WHERE id = :id AND user_id = :user_id");
		return $updateFileMetadata -> execute([
			...array_map(fn($v) => htmlspecialchars($v), $updates),
			'id' => $fileId,
			'user_id' => $userId
		]);
	}

	public function getRecentFiles($userId, $limit = 5) {
		$getRecentFiles = $this->db->prepare("SELECT * FROM files WHERE user_id = ? AND deleted_at IS NULL ORDER BY created_at DESC LIMIT ?");
		$getRecentFiles -> execute([$userId, $limit]);
		return $getRecentFiles -> fetchAll(PDO::FETCH_ASSOC);
	}

	private function validateFile($file) {
		if ($file['error'] !== UPLOAD_ERR_OK) {
			throw new Exception('Yükleme hatası: ' . $file['error']);
		}

		if ($file['size'] > $this->config['upload']['max_size']) {
			throw new Exception('Dosya boyutu çok büyük');
		}

		$mime = mime_content_type($file['tmp_name']);
		if (!in_array($mime, $this->config['security']['allowed_mime_types'])) {
			throw new Exception('Geçersiz dosya türü');
		}
	}

	private function createShareToken() {
		return bin2hex(random_bytes(32));
	}

	public function getFilePreview($fileId, $userId) {
		$getFile = $this->db->prepare("SELECT f.*, LOWER(SUBSTRING_INDEX(file_name, '.', -1)) as extension, mime_type FROM files f WHERE f.id = ? AND f.user_id = ? AND f.deleted_at IS NULL");
		$getFile -> execute([$fileId, $userId]);
		$file = $getFile -> fetch(PDO::FETCH_ASSOC);

		if (!$file) {
			throw new Exception('Dosya bulunamadı');
		}

		$previewableTypes = [
			'image/jpeg', 'image/png', 'image/gif', 'image/webp',
			'application/pdf',
			'text/plain', 'text/html', 'text/css', 'text/javascript',
			'video/mp4', 'video/webm',
			'audio/mpeg', 'audio/wav', 'audio/ogg'
		];

		if (!in_array($file['mime_type'], $previewableTypes)) {
			throw new Exception('Bu dosya türü için önizleme yapılamıyor');
		}

		$filePath = $_SERVER['DOCUMENT_ROOT'] . '/open-file/' . $file['file_path'];
		if (!file_exists($filePath)) {
			throw new Exception('Dosya bulunamadı');
		}

		$preview = [
			'id' => $file['id'],
			'name' => $file['file_name'],
			'type' => $file['mime_type'],
			'extension' => $file['extension'],
			'path' => $file['file_path'],
			'size' => $file['file_size']
		];

		$updateViews = $this->db->prepare("UPDATE files SET view_count = view_count + 1 WHERE id = ?");
		$updateViews -> execute([$fileId]);

		return $preview;
	}
}