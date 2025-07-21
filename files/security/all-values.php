<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/files/security/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/security/functions.php';

$config = require $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
$site_url = $config['paths']['site_url'];
$style_path = $config['paths']['style_path'];
$site_name = $config['paths']['site_name'];