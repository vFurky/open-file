<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/files/security/auth-check.php';

$err = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

}

require realpath('.') . '/view-forgot-password.php';