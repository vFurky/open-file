<?php
return [
	'host' => '', // Veritabanı Sunucusu
	'database' => '', // Veritabanı Adı
	'username' => '', // Kullanıcı Adı
	'password' => '', // Şifre
	'options' => [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Hata Modu
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Sonuç Modu
		PDO::ATTR_EMULATE_PREPARES => false, // Ön Hazırlama Modu
		PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4", // Karakter Seti
		PDO::ATTR_PERSISTENT => true // Kalıcı Bağlantı
	],
	'log_errors' => true, // Hata Loglama
	'timezone' => 'UTC', // Zaman Dilimi
	'charset' => 'utf8mb4', // Karakter Seti
	'collation' => 'utf8mb4_unicode_ci' // Karakter Seti
];