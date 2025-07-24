<?php

class FileManager {
	private $db;
	private $config;
	private $logger;
	private $security;

	public function __construct($db, $config, $logger = null) {
		$this->db = $db;
		$this->config = $config;
		$this->logger = $logger;
		$this->security = new Security();
	}

	public function handleUpload($file, $userId) {
		try {
			$this->validateFile($file);

			if (!$this->security->isAllowedMimeType($file['tmp_name'])) {
				throw new Exception('Dosya türü güvenlik kontrolünden geçmedi.');
			}

			if (!$this->security->validateFileContent($file['tmp_name'])) {
				throw new Exception('Dosya içeriği güvenlik kontrolünden geçmedi.');
			}

			if (!$this->security->scanForMalware($file['tmp_name'])) {
				throw new Exception('Dosya güvenlik taramasından geçmedi.');
			}

			$filePath = $this->createFilePath($file);
			$mime = mime_content_type($file['tmp_name']);
			$fileHash = hash_file('sha256', $file['tmp_name']);

			$metadata = [
				'original_name' => $this->security->sanitizeFileName($file['name']),
				'file_size' => $file['size'],
				'mime_type' => $mime,
				'file_hash' => $fileHash,
				'upload_date' => date('Y-m-d H:i:s'),
				'share_token' => $this->createShareToken(),
				'expires_at' => date('Y-m-d H:i:s', strtotime("+{$this->config['upload']['expire_days']} days")),
				'is_public' => 0,
				'user_id' => $userId
			];

			if (!$this->moveFile($file['tmp_name'], $filePath)) {
				throw new Exception('Dosya yüklenemedi');
			}

			if (!$this->security->validateFileHash($filePath, $fileHash)) {
				unlink($filePath);
				throw new Exception('Dosya bütünlüğü kontrolü başarısız');
			}

			$fileId = $this->saveToDatabase($metadata, $filePath, $userId);
			$this->logActivity($userId, 'upload', $fileId, $metadata['original_name']);

			return $fileId;

		} catch (Exception $e) {
			$this->logError($userId, 'upload_error', $e->getMessage());
			throw $e;
		}
	}

	private function createFilePath($file) {
		$original_name = pathinfo($file['name'], PATHINFO_FILENAME);
		$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		$safe_filename = $this->security->sanitizeFileName($original_name);
		$unique_filename = sprintf('%s_%s.%s', $safe_filename, uniqid(), $extension);

		$date = new DateTime('now', new DateTimeZone('UTC'));
		$year_month = $date -> format('Y/m');
		$upload_dir = $this->config['paths']['upload_dir'] . $year_month;

		if (!file_exists($upload_dir)) {
			if (!mkdir($upload_dir, 0755, true)) {
				Logger::error("[FILEMANAGER.PHP-80]-DIZIN_OLUSTURULAMADI: " $upload_dir);
				throw new Exception('Dosya yükleme dizini oluşturulurken bir hata oluştu, lütfen daha sonra tekrar deneyin.');
			}
		}

		return $upload_dir . '/' . $unique_filename;
	}

	public function getFileStatistics($userId) {
		$stats = $this->db->prepare("
			SELECT COUNT(*) as total_files, 
			SUM(file_size) as total_size, 
			COUNT(CASE WHEN is_public = 1 THEN 1 END) as public_files, 
			COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today_uploads, 
			COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as week_uploads, 
			COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as month_uploads, 
			SUM(view_count) as total_views 
			FROM files 
			WHERE user_id = ? AND deleted_at IS NULL
			");
		$stats -> execute([$userId]);
		return $stats -> fetch(PDO::FETCH_ASSOC);
	}

	public function setFileVisibility($fileId, $userId, $isPublic) {
		$update = $this->db->prepare("UPDATE files SET is_public = ?, updated_at = UTC_TIMESTAMP() WHERE id = ? AND user_id = ? AND deleted_at IS NULL");
		$result = $update -> execute([$isPublic, $fileId, $userId]);

		if ($result) {
			$this->logActivity($userId, 'visibility_change', $fileId, $isPublic ? 'public' : 'private');
		}

		return $result;
	}

	public function updateFileMetadata($fileId, $userId, $metadata) {
		$allowedFields = ['title', 'description', 'expires_at', 'is_public'];
		$updates = array_intersect_key($metadata, array_flip($allowedFields));

		if (empty($updates)) {
			return false;
		}

		$cleanUpdates = [];
		foreach ($updates as $key => $value) {
			$cleanUpdates[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
		}

		$updateFileMetadata = $this->db->prepare("UPDATE files SET " . implode(', ', array_map(fn($k) => "$k = :$k", array_keys($cleanUpdates))) . ", updated_at = UTC_TIMESTAMP() WHERE id = :id AND user_id = :user_id AND deleted_at IS NULL");
		$result = $updateFileMetadata -> execute([
			...$cleanUpdates, 
			'id' => $fileId, 
			'user_id' => $userId
		]);

		if ($result) {
			$this->logActivity($userId, 'metadata_update', $fileId);
		}

		return $result;
	}

	public function getRecentFiles($userId, $limit = 5) {
		$getRecentFiles = $this->db->prepare("SELECT id, original_name, file_size, mime_type, created_at, view_count, is_public FROM files WHERE user_id = ? AND deleted_at IS NULL ORDER BY created_at DESC LIMIT ?");
		$getRecentFiles -> execute([$userId, $limit]);
		return $getRecentFiles -> fetchAll(PDO::FETCH_ASSOC);
	}

	public function getFilePreview($fileId, $userId) {
		$getFile = $this->db->prepare("SELECT f.*, LOWER(SUBSTRING_INDEX(original_name, '.', -1)) as extension FROM files f WHERE f.id = ? AND f.user_id = ? AND f.deleted_at IS NULL");
		$getFile -> execute([$fileId, $userId]);
		$file = $getFile -> fetch(PDO::FETCH_ASSOC);

		if (!$file) {
			throw new Exception('Dosya artık bulunmuyor veya süresi dolmuş.');
		}

		$previewableTypes = [
			'image/jpeg', 'image/png', 'image/gif', 'image/webp',
			'application/pdf',
			'text/plain', 'text/html', 'text/css', 'text/javascript',
			'video/mp4', 'video/webm', 'video/ogg',
			'audio/mpeg', 'audio/wav', 'audio/ogg'
		];

		if (!in_array($file['mime_type'], $previewableTypes)) {
			throw new Exception('Bu dosya türü için önizleme yapılamıyor');
		}

		$filePath = $this->config['paths']['upload_dir'] . $file['file_path'];
		if (!file_exists($filePath)) {
			throw new Exception('Dosya artık bulunmuyor veya süresi dolmuş.');
		}

		if (!$this->security->validateFileHash($filePath, $file['file_hash'])) {
			throw new Exception('Dosya bütünlüğü kontrolü başarısız');
		}

		$preview = [
			'id' => $file['id'],
			'name' => $file['original_name'],
			'type' => $file['mime_type'],
			'extension' => $file['extension'],
			'path' => $file['file_path'],
			'size' => $file['file_size']
		];

		$updateViews = $this->db->prepare("UPDATE files SET view_count = view_count + 1 WHERE id = ?");
		$updateViews -> execute([$fileId]);
		$this->logActivity($userId, 'preview', $fileId);

		return $preview;
	}

	public function deleteFile($fileId, $userId) {
		$this->db->beginTransaction();

		try {
			$getFile = $this->db->prepare("SELECT file_path FROM files WHERE id = ? AND user_id = ? AND deleted_at IS NULL");
			$getFile -> execute([$fileId, $userId]);
			$file = $getFile -> fetch(PDO::FETCH_ASSOC);

			if (!$file) {
				throw new Exception('Dosya bulunamadı.');
			}

			$deleteFile = $this->db->prepare("UPDATE files SET deleted_at = UTC_TIMESTAMP() WHERE id = ? AND user_id = ?");
			$deleteFile -> execute([$fileId, $userId]);

			$this->db->commit();
			$this -> logActivity($userId, 'delete', $fileId);

			return true;

		} catch (Exception $e) {
			$this->db->rollback();
			throw $e;
		}
	}

	private function validateFile($file) {
		if ($file['error'] !== UPLOAD_ERR_OK) {
			throw new Exception('Yükleme hatası: ' . $this->getUploadErrorMessage($file['error']));
		}

		if ($file['size'] > $this->config['upload']['max_size']) {
			throw new Exception('Dosya boyutu çok büyük (Max: ' . $this->formatBytes($this->config['upload']['max_size']) . ')');
		}

		if ($file['size'] == 0) {
			throw new Exception('Boş dosya yüklenemez.');
		}

		$mime = mime_content_type($file['tmp_name']);
		if (!in_array($mime, $this->config['security']['allowed_mime_types'])) {
			throw new Exception('Geçersiz dosya türü: ' . $mime);
		}
	}

	private function getUploadErrorMessage($error) {
		$messages = [
			UPLOAD_ERR_INI_SIZE => 'Dosya boyutu sunucu limitini aşıyor',
			UPLOAD_ERR_FORM_SIZE => 'Dosya boyutu form limitini aşıyor',
			UPLOAD_ERR_PARTIAL => 'Dosya kısmen yüklendi',
			UPLOAD_ERR_NO_FILE => 'Dosya yüklenmedi',
			UPLOAD_ERR_NO_TMP_DIR => 'Geçici dizin bulunamadı',
			UPLOAD_ERR_CANT_WRITE => 'Dosya yazılamadı',
			UPLOAD_ERR_EXTENSION => 'Dosya yükleme uzantı tarafından durduruldu'
		];
		return $messages[$error] ?? 'Bilinmeyen hata';
	}

	private function formatBytes($bytes) {
		$units = ['B', 'KB', 'MB', 'GB'];
		for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
			$bytes /= 1024;
		}
		return round($bytes, 2) . ' ' . $units[$i];
	}

	private function createShareToken() {
		return bin2hex(random_bytes(32));
	}

	private function moveFile($source, $destination) {
		return move_uploaded_file($source, $destination);
	}

	private function saveToDatabase($metadata, $filePath, $userId) {
		$saveToDatabaseSql = $this->db->prepare("INSERT INTO files (user_id, original_name, file_path, file_size, mime_type, file_hash, share_token, expires_at, is_public, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, UTC_TIMESTAMP(), UTC_TIMESTAMP())");

		$saveToDatabaseSql -> execute([
			$userId,
			$metadata['original_name'],
			$filePath,
			$metadata['file_size'],
			$metadata['mime_type'],
			$metadata['file_hash'],
			$metadata['share_token'],
			$metadata['expires_at'],
			$metadata['is_public']
		]);

		return $this->db->lastInsertId();
	}

	private function logActivity($userId, $action, $fileId = null, $details = null) {
		if (!$this->logger) return;

		$this->logger->info('FileManager activity', [
			'user_id' => $userId,
			'action' => $action,
			'file_id' => $fileId,
			'details' => $details,
			'timestamp' => date('Y-m-d H:i:s')
		]);
	}

	private function logError($userId, $action, $error) {
		if (!$this->logger) return;

		$this->logger->error('FileManager error', [
			'user_id' => $userId,
			'action' => $action,
			'error' => $error,
			'timestamp' => date('Y-m-d H:i:s')
		]);
	}
}