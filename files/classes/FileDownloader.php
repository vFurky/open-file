<?php
class FileDownloader {
	private $db;
	private $config;

	public function __construct($db) {
		$this->db = $db;
		$this->config = require $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
	}

	public function download($token) {
		$file = $this->getFileInfo($token);
		$filePath = $this->validateAndGetFilePath($file);
		$this->securityChecks($filePath);
		$this->streamFile($file, $filePath);
	}

	private function getFileInfo($token) {
		Logger::info("TOKEN_ICIN_DOSYA_BILGISI: " . $token);

		$fetchFileInfo = $this->db->prepare("SELECT f.*, u.username FROM files f JOIN users u ON f.user_id = u.id WHERE f.share_token = ? AND (f.expires_at > UTC_TIMESTAMP() OR f.expires_at IS NULL)");

		if (!$fetchFileInfo) {
			Logger::error("SQL_SORGU_HAZIRLAMA_HATASI");
			throw new Exception('SQL hazırlama hatası');
		}

		$fetchFileInfo -> execute([$token]);
		$file = $fetchFileInfo -> fetch(PDO::FETCH_ASSOC);

		if (!$file) {
			Logger::error("DOSYA_BULUNAMADI_VEYA_SURESI_DOLMUS");
			header('HTTP/1.0 404 Not Found');
			exit('Dosya artık bulunmuyor veya süresi dolmuş.');
		}

		return $file;
	}

	private function validateAndGetFilePath($file) {
		$filePath = $_SERVER['DOCUMENT_ROOT'] . '/' . $file['file_path'];

		if (!file_exists($filePath)) {
			Logger::error("DOSYA_KONUMDA_BULUNAMADI: " . $filePath);
			header('HTTP/1.0 404 Not Found');
			exit('Dosya artık bulunmuyor veya süresi dolmuş.');
		}

		return $filePath;
	}

	private function securityChecks($filePath) {
		if (!Security::isAllowedMimeType($filePath)) {
			Logger::error("GECERSIZ_MIME_TURU: " . $filePath);
			throw new Exception('Geçersiz dosya türü');
		}

		if (!Security::validateFileContent($filePath)) {
			Logger::error("GECERSIZ_DOSYA_TURU: " . $filePath);
			throw new Exception('Geçersiz dosya içeriği');
		}
	}

	private function streamFile($file, $filePath) {
		Logger::info("DOSYA_AKISI_BASLIYOR: " . $file['file_name']);

		$fileSize = filesize($filePath);

		while (ob_get_level()) {
			ob_end_clean();
		}

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mimeType = finfo_file($finfo, $filePath);
		finfo_close($finfo);

		header('Content-Type: ' . $mimeType);

		if ($mimeType === 'application/pdf') {
			header('Content-Disposition: inline; filename="' . rawurlencode($file['file_name']) . '"');
		} else {
			header('Content-Disposition: attachment; filename="' . rawurlencode($file['file_name']) . '"');
		}

		header('Content-Length: ' . $fileSize);
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Expires: 0');

		if ($fp = fopen($filePath, 'rb')) {
			while (!feof($fp) && connection_status() == 0) {
				echo fread($fp, 8192);
				flush();
			}
			fclose($fp);
			Logger::info("BASARILI_DOSYA_AKISI: " . $file['file_name']);
		} else {
			Logger::error("[FILEDOWNLOADER.PHP-98]-DOSYA_ACMA_HATASI: " . $filePath);
			throw new Exception('Dosya açılırken bir hata meydana geldi, lütfen daha sonra tekrar deneyin.');
		}

		exit;
	}
}