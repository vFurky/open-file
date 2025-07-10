<?php

define('DEVELOPMENT_MODE', true); // Geliştirme Modu

if (DEVELOPMENT_MODE) {
	error_reporting(E_ALL); // Hata Raporlama
	ini_set('display_errors', 1); // Hata Görüntüleme
	ini_set('display_startup_errors', 1); // Başlangıç Hata Görüntüleme
} else {
	error_reporting(0); // Hata Raporlama
	ini_set('display_errors', 0); // Hata Görüntüleme
	ini_set('display_startup_errors', 0); // Başlangıç Hata Görüntüleme
}

date_default_timezone_set('UTC'); // Zaman Dilimi
ini_set('default_charset', 'UTF-8'); // Karakter Seti
ini_set('session.cookie_httponly', 1); // Cookie HTTP Yalnızca
ini_set('session.use_only_cookies', 1); // Cookie Kullanımı
ini_set('session.cookie_secure', 1); // Cookie Güvenliği
ini_set('session.cookie_samesite', 'Lax'); // Cookie Aynı Site
ini_set('session.use_strict_mode', 1); // Strict Mod