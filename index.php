<?php

if (!isset($_SESSION)) {
    session_start();
}

if (isset($_SESSION['user'])) {
    Header('Location: ' . $site_url . 'home');
    exit;
} else {
	Header('Location: ' . $site_url . 'login');
	exit;
}