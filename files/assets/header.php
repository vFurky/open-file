<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/files/security/login-check.php'; ?>
<?php header("Set-Cookie: cross-site-cookie=whatever; SameSite=None; Secure"); ?>