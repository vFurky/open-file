<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/files/security/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/files/security/all-values.php';

if (isset($_SESSION['user'])) {
    Header('Location: ' . $site_url . 'home');
    exit;
}