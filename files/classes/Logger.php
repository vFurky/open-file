<?php
class Logger {
	private static $logFile = '/logs/app.log';

	public static function log($message, $level = 'INFO') {
		try {
			$date = date('Y-m-d H:i:s');
			$logMessage = "[$date][$level] $message" . PHP_EOL;

			$logDir = $_SERVER['DOCUMENT_ROOT'] . '/logs';
			if (!file_exists($logDir)) {
				if (!mkdir($logDir, 0755, true)) {
					throw new Exception("Log dizini oluşturulamadı: $logDir");
				}
			}

			$logPath = $_SERVER['DOCUMENT_ROOT'] . self::$logFile;

			if (!error_log($logMessage, 3, $logPath)) {
				throw new Exception("Log dosyasına yazılamadı: $logPath");
			}
		} catch (Exception $e) {
			error_log("LOGGER_HATASI: " . $e->getMessage());
		}
	}

	public static function error($message) {
		self::log($message, 'ERROR');
	}

	public static function info($message) {
		self::log($message, 'INFO');
	}
}