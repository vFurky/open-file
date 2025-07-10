<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/open-file/files/security/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/open-file/files/security/all-values.php';

function getSiteSettings() {
	global $db;
	$query = $db -> prepare("SELECT * FROM site_settings WHERE id = :id");
	$query -> bindValue(':id', 1541, PDO::PARAM_INT);
	$query -> execute();
	$result = $query -> fetch(PDO::FETCH_ASSOC);
	return $result;
}