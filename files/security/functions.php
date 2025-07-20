<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/open-file/files/security/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/open-file/files/security/all-values.php';

function getFileIcon($fileName) {
	$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
	$icons = [
		'pdf' => 'fa-file-pdf',
		'doc' => 'fa-file-word',
		'docx' => 'fa-file-word',
		'xls' => 'fa-file-excel',
		'xlsx' => 'fa-file-excel',
		'txt' => 'fa-file-alt',
		'jpg' => 'fa-file-image',
		'jpeg' => 'fa-file-image',
		'png' => 'fa-file-image',
		'gif' => 'fa-file-image',
		'zip' => 'fa-file-archive',
		'rar' => 'fa-file-archive'
	];

	return isset($icons[$extension]) ? $icons[$extension] : 'fa-file';
}

function formatFileSize($bytes) {
	$units = ['B', 'KB', 'MB', 'GB', 'TB'];
	$bytes = max($bytes, 0);
	$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
	$pow = min($pow, count($units) - 1);
	$bytes /= pow(1024, $pow);
	return round($bytes, 2) . ' ' . $units[$pow];
}