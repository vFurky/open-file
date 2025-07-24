<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/files/security/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/security/all-values.php';

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

function getFileIconClass($extension) {
	$icons = [
		'pdf' => 'fas fa-file-pdf fa-4x text-danger',
		'doc' => 'fas fa-file-word fa-4x text-primary',
		'docx' => 'fas fa-file-word fa-4x text-primary',
		'xls' => 'fas fa-file-excel fa-4x text-success',
		'xlsx' => 'fas fa-file-excel fa-4x text-success',
		'txt' => 'fas fa-file-alt fa-4x text-secondary',
		'jpg' => 'fas fa-file-image fa-4x text-info',
		'jpeg' => 'fas fa-file-image fa-4x text-info',
		'png' => 'fas fa-file-image fa-4x text-info',
		'gif' => 'fas fa-file-image fa-4x text-info',
		'zip' => 'fas fa-file-archive fa-4x text-warning',
		'rar' => 'fas fa-file-archive fa-4x text-warning'
	];

	return $icons[$extension] ?? 'fas fa-file fa-4x text-secondary';
}