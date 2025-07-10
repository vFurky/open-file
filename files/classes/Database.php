<?php
class Database {
	private static $instance = null;
	private $connection = null;
	private $config = [];

	private function __construct() {
		$this->loadConfig();
		$this->connect();
	}

	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function loadConfig() {
		$configFile = $_SERVER['DOCUMENT_ROOT'] . '/open-file/config/database.php';

		if (!file_exists($configFile)) {
			throw new Exception('Database configuration file not found');
		}

		$this->config = require $configFile;
	}

	private function connect() {
		try {
			$dsn = sprintf(
				"mysql:host=%s;dbname=%s;charset=utf8mb4",
				$this->config['host'],
				$this->config['database']
			);

			$this->connection = new PDO(
				$dsn,
				$this->config['username'],
				$this->config['password'],
				$this->config['options']
			);

			$this->connection->exec("SET SESSION sql_mode = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
			$this->connection->exec("SET time_zone = '+00:00'");

		} catch (PDOException $e) {
			$this->logError($e);
			throw new Exception('Database connection failed');
		}
	}

	public function getConnection() {
		return $this->connection;
	}

	private function logError($exception) {
		if (isset($this->config['log_errors']) && $this->config['log_errors']) {
			$logMessage = sprintf(
				"[%s] Database Error: %s in %s on line %d\n",
				date('Y-m-d H:i:s'),
				$exception->getMessage(),
				$exception->getFile(),
				$exception->getLine()
			);

			error_log($logMessage, 3, $_SERVER['DOCUMENT_ROOT'] . '/open-file/logs/database.log');
		}
	}

	public function __clone() {
		throw new Exception('Cloning of Database instance is not allowed');
	}

	public function __wakeup() {
		throw new Exception('Unserializing of Database instance is not allowed');
	}
}