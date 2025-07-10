<?php

class Security {
	public static function isAllowedMimeType($filePath) {
		$allowedMimes = [
			'image/jpeg', 'image/png', 'image/gif', 
			'application/pdf', 'application/msword',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'application/vnd.ms-excel',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'text/plain',
			'application/zip', 'application/x-rar-compressed'
		];

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mimeType = finfo_file($finfo, $filePath);
		finfo_close($finfo);

		return in_array($mimeType, $allowedMimes);
	}

	public static function sanitizeFileName($fileName) {
		$fileName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
		$fileName = preg_replace('/\.+/', '.', $fileName);
		return trim($fileName, '.');
	}

	public static function validateFileHash($filePath, $originalHash) {
		return hash_file('sha256', $filePath) === $originalHash;
	}

	public static function scanForMalware($filePath) {
		$suspicious = ['eval(', 'system(', 'exec(', 'base64_decode('];
		$content = file_get_contents($filePath);

		foreach ($suspicious as $pattern) {
			if (stripos($content, $pattern) !== false) {
				return false;
			}
		}
		return true;
	}

	public static function validateFileContent($filePath) {
		$handle = fopen($filePath, 'rb');
		$content = fread($handle, 100);
		fclose($handle);

		$dangerous = ['<?php', '<?=', '<%', '<script'];
		foreach ($dangerous as $pattern) {
			if (stripos($content, $pattern) !== false) {
				return false;
			}
		}
		return true;
	}
}