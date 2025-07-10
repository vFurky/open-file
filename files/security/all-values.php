<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/open-file/files/security/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/open-file/files/security/functions.php';

$config = require $_SERVER['DOCUMENT_ROOT'] . '/open-file/config/config.php';
$site_url = $config['paths']['site_url'];
$style_path = $config['paths']['style_path'];
$site_name = $config['paths']['site_name'];