<?php
class Security {
	private static $instance = null;
	private $config;
	private $logger;

	private static $maliciousSignatures = [
		'eval(',
		'system(',
		'exec(',
		'shell_exec(',
		'passthru(',
		'base64_decode(',
		'file_get_contents(',
		'file_put_contents(',
		'fopen(',
		'fwrite(',
		'curl_exec(',
		'proc_open(',
		'popen(',
		'assert(',
		'preg_replace(',
		'create_function(',
		'ReflectionFunction',
		'${',
		'php://',
		'data://',
		'expect://',
		'input://',
		'phar://',
		'zip://',
		'bzip2://',
		'zlib://',
		'glob://',
		'ssh2.exec://',
		'rar://',
		'ogg://',
		'expect://'
	];

	private static $maliciousHeaders = [
		'<?php',
		'<?=',
		'<%',
		'<script',
		'<iframe',
		'<object',
		'<embed',
		'<applet',
		'<form',
		'<meta',
		'<link',
		'<style',
		'javascript:',
		'vbscript:',
		'onload=',
		'onerror=',
		'onclick=',
		'onmouseover=',
		'MZ', 
		'PK', 
		'\x7fELF', 
		'\xcf\xfa\xed\xfe',
		'\xfe\xed\xfa\xce',
		'\xfe\xed\xfa\xcf',
		'\xcf\xfa\xed\xfe',
	];

	private static $allowedMimeTypes = [
		'image/jpeg',
		'image/png',
		'image/gif',
		'image/webp',
		'image/bmp',
		'image/tiff',
		'image/svg+xml',
		'application/pdf',
		'application/msword',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'application/vnd.ms-excel',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'application/vnd.ms-powerpoint',
		'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'application/vnd.oasis.opendocument.text',
		'application/vnd.oasis.opendocument.spreadsheet',
		'application/vnd.oasis.opendocument.presentation',
		'application/rtf',
		'text/plain',
		'text/csv',
		'text/html',
		'text/css',
		'text/javascript',
		'text/xml',
		'application/json',
		'application/xml',
		'application/zip',
		'application/x-rar-compressed',
		'application/x-7z-compressed',
		'application/x-tar',
		'application/gzip',
		'audio/mpeg',
		'audio/wav',
		'audio/ogg',
		'audio/mp4',
		'video/mp4',
		'video/webm',
		'video/ogg',
		'video/avi',
		'video/quicktime',
		'video/x-msvideo'
	];

	private static $allowedExtensions = [
		'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tiff', 'svg',
		'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
		'odt', 'ods', 'odp', 'rtf',
		'txt', 'csv', 'html', 'css', 'js', 'xml', 'json',
		'zip', 'rar', '7z', 'tar', 'gz',
		'mp3', 'wav', 'ogg', 'mp4', 'webm', 'avi', 'mov'
	];

	private static $maxFileSizes = [
		'image' => 10 * 1024 * 1024,
		'document' => 50 * 1024 * 1024,
		'archive' => 100 * 1024 * 1024,
		'video' => 500 * 1024 * 1024,
		'audio' => 50 * 1024 * 1024,
		'default' => 25 * 1024 * 1024
	];

	public function __construct($config = null, $logger = null) {
		$this->config = $config;
		$this->logger = $logger;
	}

	public static function getInstance($config = null, $logger = null) {
		if (self::$instance === null) {
			self::$instance = new self($config, $logger);
		}
		return self::$instance;
	}

	public static function isAllowedMimeType($filePath) {
		if (!file_exists($filePath)) {
			return false;
		}

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mimeType = finfo_file($finfo, $filePath);
		finfo_close($finfo);
		$mimeType2 = mime_content_type($filePath);

		if ($mimeType !== $mimeType2) {
			return false;
		}

		return in_array($mimeType, self::$allowedMimeTypes);
	}

	public static function sanitizeFileName($fileName) {
		$fileName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
		$fileName = preg_replace('/\.+/', '.', $fileName);
		$fileName = trim($fileName, '.');

		if (strlen($fileName) > 255) {
			$fileName = substr($fileName, 0, 255);
		}

		if (empty($fileName)) {
			$fileName = 'file_' . uniqid();
		}

		return $fileName;
	}

	public static function validateFileHash($filePath, $originalHash) {
		if (!file_exists($filePath)) {
			return false;
		}

		return hash_file('sha256', $filePath) === $originalHash;
	}

	public static function scanForMalware($filePath) {
		if (!file_exists($filePath)) {
			return false;
		}

		$fileSize = filesize($filePath);
		if ($fileSize > 100 * 1024 * 1024) { 
			return self::scanLargeFile($filePath);
		}

		$content = file_get_contents($filePath);
		if ($content === false) {
			return false;
		}

		foreach (self::$maliciousSignatures as $signature) {
			if (stripos($content, $signature) !== false) {
				return false;
			}
		}

		$extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
		if (!in_array($extension, self::$allowedExtensions)) {
			return false;
		}

		if (substr_count(basename($filePath), '.') > 1) {
			return false;
		}

		return true;
	}

	private static function scanLargeFile($filePath) {
		$handle = fopen($filePath, 'rb');
		if (!$handle) {
			return false;
		}

		$chunk = fread($handle, 1024 * 1024);

		foreach (self::$maliciousSignatures as $signature) {
			if (stripos($chunk, $signature) !== false) {
				fclose($handle);
				return false;
			}
		}

		fseek($handle, -1024 * 1024, SEEK_END);
		$chunk = fread($handle, 1024 * 1024);

		foreach (self::$maliciousSignatures as $signature) {
			if (stripos($chunk, $signature) !== false) {
				fclose($handle);
				return false;
			}
		}

		fclose($handle);
		return true;
	}

	public static function validateFileContent($filePath) {
		if (!file_exists($filePath)) {
			return false;
		}

		$handle = fopen($filePath, 'rb');
		if (!$handle) {
			return false;
		}

		$header = fread($handle, 1024);
		fclose($handle);

		foreach (self::$maliciousHeaders as $maliciousHeader) {
			if (stripos($header, $maliciousHeader) !== false) {
				return false;
			}
		}

		$mimeType = mime_content_type($filePath);
		if (!self::validateMimeHeader($mimeType, $header)) {
			return false;
		}

		return true;
	}

	private static function validateMimeHeader($mimeType, $header) {
		$mimeSignatures = [
			'image/jpeg' => ["\xFF\xD8\xFF"],
			'image/png' => ["\x89PNG\r\n\x1A\n"],
			'image/gif' => ["GIF87a", "GIF89a"],
			'application/pdf' => ["%PDF-"],
			'application/zip' => ["PK\x03\x04", "PK\x05\x06", "PK\x07\x08"],
			'image/webp' => ["RIFF", "WEBP"],
			'video/mp4' => ["\x00\x00\x00\x18ftypmp4", "\x00\x00\x00\x20ftypmp4"],
			'audio/mpeg' => ["\xFF\xFB", "\xFF\xF3", "\xFF\xF2", "ID3"]
		];

		if (isset($mimeSignatures[$mimeType])) {
			foreach ($mimeSignatures[$mimeType] as $signature) {
				if (strpos($header, $signature) === 0) {
					return true;
				}
			}
			return false;
		}

		return true;
	}

	public static function validateFileSize($filePath, $mimeType = null) {
		if (!file_exists($filePath)) {
			return false;
		}

		$fileSize = filesize($filePath);
		$mimeType = $mimeType ?? mime_content_type($filePath);

		if (strpos($mimeType, 'image/') === 0) {
			$maxSize = self::$maxFileSizes['image'];
		} elseif (strpos($mimeType, 'video/') === 0) {
			$maxSize = self::$maxFileSizes['video'];
		} elseif (strpos($mimeType, 'audio/') === 0) {
			$maxSize = self::$maxFileSizes['audio'];
		} elseif (in_array($mimeType, ['application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed'])) {
			$maxSize = self::$maxFileSizes['archive'];
		} elseif (strpos($mimeType, 'application/') === 0 || strpos($mimeType, 'text/') === 0) {
			$maxSize = self::$maxFileSizes['document'];
		} else {
			$maxSize = self::$maxFileSizes['default'];
		}

		return $fileSize <= $maxSize;
	}

	public static function validateFileExtension($fileName) {
		$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
		return in_array($extension, self::$allowedExtensions);
	}

	public static function comprehensiveSecurityCheck($filePath, $originalName = null) {
		$originalName = $originalName ?? basename($filePath);

		$checks = [
			'file_exists' => file_exists($filePath),
			'mime_type' => self::isAllowedMimeType($filePath),
			'file_extension' => self::validateFileExtension($originalName),
			'file_content' => self::validateFileContent($filePath),
			'malware_scan' => self::scanForMalware($filePath),
			'file_size' => self::validateFileSize($filePath)
		];

		foreach ($checks as $check => $result) {
			if (!$result) {
				return [
					'success' => false,
					'failed_check' => $check,
					'checks' => $checks
				];
			}
		}

		return [
			'success' => true,
			'checks' => $checks
		];
	}

	public static function quarantineFile($filePath, $reason = 'security_violation') {
		if (!file_exists($filePath)) {
			return false;
		}

		$quarantineDir = dirname($filePath) . '/quarantine/';
		if (!is_dir($quarantineDir)) {
			mkdir($quarantineDir, 0700, true);
		}

		$quarantineFile = $quarantineDir . basename($filePath) . '_' . time() . '.quarantine';

		if (rename($filePath, $quarantineFile)) {
			$logFile = $quarantineDir . 'quarantine.log';
			$logEntry = date('Y-m-d H:i:s') . " - " . basename($filePath) . " - " . $reason . "\n";
			file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

			return true;
		}

		return false;
	}

	public static function validateArchiveFile($filePath) {
		$mimeType = mime_content_type($filePath);

		if (!in_array($mimeType, ['application/zip', 'application/x-rar-compressed'])) {
			return false;
		}

		try {
			if ($mimeType === 'application/zip') {
				$zip = new ZipArchive();
				if ($zip -> open($filePath) !== TRUE) {
					return false;
				}

				for ($i = 0; $i < $zip->numFiles; $i++) {
					$fileInfo = $zip -> statIndex($i);
					$fileName = $fileInfo['name'];

					if (self::isDangerousFileName($fileName)) {
						$zip -> close();
						return false;
					}

					if ($fileInfo['size'] > 100 * 1024 * 1024) {
						$zip -> close();
						return false;
					}
				}

				$zip -> close();
				return true;
			}

			if ($mimeType === 'application/x-rar-compressed') {
				return filesize($filePath) <= self::$maxFileSizes['archive'];
			}

		} catch (Exception $e) {
			return false;
		}

		return false;
	}

	private static function isDangerousFileName($fileName) {
		$dangerousPatterns = [
			'/\.(php|phtml|php3|php4|php5|phar|exe|bat|cmd|com|scr|vbs|js|jar|py|pl|sh|bin)$/i',
			'/\.\w+\.(php|phtml|exe|bat|cmd|com|scr|vbs|js)$/i',
			'/^(con|prn|aux|nul|com[1-9]|lpt[1-9])$/i',
			'/\.\./i',
			'/[<>:"|?*]/i'
		];

		foreach ($dangerousPatterns as $pattern) {
			if (preg_match($pattern, $fileName)) {
				return true;
			}
		}

		return false;
	}

	public static function cleanImageExifData($filePath) {
		$mimeType = mime_content_type($filePath);

		if (!in_array($mimeType, ['image/jpeg', 'image/tiff'])) {
			return true;
		}

		try {
			$exifData = exif_read_data($filePath);

			if ($exifData) {
				$sensitiveKeys = ['GPS', 'Make', 'Model', 'Software', 'DateTime', 'DateTimeOriginal'];

				foreach ($sensitiveKeys as $key) {
					if (isset($exifData[$key])) {
						return self::stripExifData($filePath);
					}
				}
			}

			if (strpos($mime_type, 'image/') === 0) {
				$exifCleaned = Security::cleanImageExifData($file_path);
				Logger::info("EXIF_TEMIZLEME", ['dosya' => $file['name'], 'temizlendi' => $exifCleaned]);
			}

			return true;

		} catch (Exception $e) {
			return false;
		}
	}

	private static function stripExifData($filePath) {
		try {
			$image = imagecreatefromjpeg($filePath);
			if ($image) {
				$result = imagejpeg($image, $filePath, 90);
				imagedestroy($image);
				return $result;
			}
		} catch (Exception $e) {
			return false;
		}

		return false;
	}

	public static function generateSecurityReport($filePath, $originalName = null) {
		$originalName = $originalName ?? basename($filePath);
		$report = [
			'file_path' => $filePath,
			'original_name' => $originalName,
			'scan_time' => date('Y-m-d H:i:s'),
			'file_size' => filesize($filePath),
			'mime_type' => mime_content_type($filePath),
			'file_hash' => hash_file('sha256', $filePath),
			'security_checks' => []
		];

		$securityResult = self::comprehensiveSecurityCheck($filePath, $originalName);
		$report['security_checks'] = $securityResult['checks'];
		$report['security_status'] = $securityResult['success'] ? 'SAFE' : 'UNSAFE';

		if (!$securityResult['success']) {
			$report['failed_check'] = $securityResult['failed_check'];
		}

		if (in_array($report['mime_type'], ['application/zip', 'application/x-rar-compressed'])) {
			$report['archive_validation'] = self::validateArchiveFile($filePath);
		}

		if (strpos($report['mime_type'], 'image/') === 0) {
			$report['exif_cleaned'] = self::cleanImageExifData($filePath);
		}

		return $report;
	}

	public static function validateClientIP($ip) {
		$privateRanges = [
			'10.0.0.0/8',
			'172.16.0.0/12',
			'192.168.0.0/16',
			'127.0.0.0/8'
		];

		if (!filter_var($ip, FILTER_VALIDATE_IP)) {
			return false;
		}

		if ($ip === '127.0.0.1' || $ip === '::1') {
			return false;
		}

		return true;
	}

	public static function checkRateLimit($identifier, $maxRequests = 100, $timeWindow = 3600) {
		$cacheFile = sys_get_temp_dir() . '/rate_limit_' . md5($identifier) . '.cache';

		$requests = [];
		if (file_exists($cacheFile)) {
			$requests = json_decode(file_get_contents($cacheFile), true) ?: [];
		}

		$currentTime = time();
		$windowStart = $currentTime - $timeWindow;

		$requests = array_filter($requests, function($timestamp) use ($windowStart) {
			return $timestamp > $windowStart;
		});

		if (count($requests) >= $maxRequests) {
			return false;
		}

		$requests[] = $currentTime;
		file_put_contents($cacheFile, json_encode($requests), LOCK_EX);

		return true;
	}

	public static function validateSession() {
		if (session_status() === PHP_SESSION_NONE) {
			return false;
		}

		$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
		$remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';

		$sessionFingerprint = md5($userAgent . $remoteAddr);

		if (!isset($_SESSION['fingerprint'])) {
			$_SESSION['fingerprint'] = $sessionFingerprint;
		} elseif ($_SESSION['fingerprint'] !== $sessionFingerprint) {
			return false;
		}

		$sessionTimeout = 3600;
		if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $sessionTimeout) {
			return false;
		}

		$_SESSION['last_activity'] = time();
		return true;
	}

	public static function validateCSRFToken($token) {
		if (!isset($_SESSION['csrf_token'])) {
			return false;
		}

		return hash_equals($_SESSION['csrf_token'], $token);
	}

	public static function generateCSRFToken() {
		$token = bin2hex(random_bytes(32));
		$_SESSION['csrf_token'] = $token;
		return $token;
	}

	private function logSecurityEvent($event, $details = []) {
		if (!$this->logger) {
			return;
		}

		$this->logger->warning('Security event', [
			'event' => $event,
			'details' => $details,
			'timestamp' => date('Y-m-d H:i:s'),
			'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
			'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
		]);
	}
}