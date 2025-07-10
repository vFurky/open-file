<?php

header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer-when-downgrade");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' https://cdn.jsdelivr.net;");
header("Content-Type: application/json; charset=utf-8");

ini_set('session.cookie_httponly', 1);
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/open-file/files/security/login-check.php';

if (!isset($_SESSION['user']['id']) || !is_numeric($_SESSION['user']['id'])) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => 'Yetkisiz erişim.'
    ]);
    exit;
}

$user_id = (int) $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Geçersiz CSRF token.'
    ]);
    exit;
}

unset($_SESSION['csrf_token']);
$now = time();
$reqs = &$_SESSION['profile_update_reqs'];

if (!isset($reqs)) {
    $reqs = [];
}

$reqs = array_filter($reqs, fn($t) => $t > $now - 60);

if (count($reqs) >= 5) {
    http_response_code(429);
    echo json_encode([
        'status' => 'error',
        'message' => 'Çok fazla istek geldi, lütfen bir süre bekleyin.'
    ]);
    exit;
}

$reqs[] = $now;
$username = filter_var(trim($_POST['username'] ?? ''), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$email        = trim($_POST['email'] ?? '');
$user_name = filter_var(trim($_POST['user_name'] ?? ''), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$user_surname = filter_var(trim($_POST['user_surname'] ?? ''), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$dob_raw      = trim($_POST['dob'] ?? '');
$phone_raw    = trim($_POST['phone'] ?? '');
$phone        = ($phone_raw !== '' && $phone_raw !== 'Girilmedi.') ? $phone_raw : 'Girilmedi.';
$securityNotifs = isset($_POST['securityNotifs']) ? 1 : 0;
$emailNotifs    = isset($_POST['emailNotifs']) ? 1 : 0;
$phoneNotifs    = isset($_POST['phoneNotifs']) ? 1 : 0;

if ($username === '' || $email === '' || $user_name === '' || $user_surname === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Zorunlu alanlar boş bırakılamaz.'
    ]);
    exit;
}

if (strlen($username) > 20 || strlen($user_name) > 50 || strlen($user_surname) > 50) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Kullanıcı adı maks. 20 karakter, isim ve soyisim maks. 50 karakterden oluşabilir.'
    ]);
    exit;
}

if (!preg_match('/^[a-zA-Z0-9._-]{3,20}$/', $username)) {
    echo json_encode([
        'status' => 'error',
        'field' => 'username',
        'message' => 'Lütfen geçerli bir kullanıcı adı girin!'
    ]);
    exit;
}

$usernameCheck = $db -> prepare("SELECT id FROM users WHERE username = :username AND id != :user_id");
$usernameCheck -> execute([':username' => $username, ':user_id' => $user_id]);
if ($usernameCheck -> fetch()) {
    echo json_encode([
        'status' => 'error',
        'field' => 'username',
        'message' => 'Bu kullanıcı adı zaten kullanılıyor!'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => 'error',
        'field' => 'email',
        'message' => 'Lütfen geçerli bir E-Posta adresi girin!'
    ]);
    exit;
}

$emailCheck = $db -> prepare("SELECT id FROM users WHERE email = :email AND id != :user_id");
$emailCheck -> execute([':email' => $email, ':user_id' => $user_id]);
if ($emailCheck -> fetch()) {
    echo json_encode([
        'status' => 'error',
        'field' => 'email',
        'message' => 'Bu E-Posta adresi zaten kullanılıyor!'
    ]);
    exit;
}

if ($phone !== 'Girilmedi.' && !preg_match('/^(\+90)?[5-9]\d{9}$/', $phone)) {
    $phone = preg_replace('/[^\d]/', '', $phone);
    if (strlen($phone) === 10 && in_array($phone[0], ['5', '6', '7', '8', '9'])) {
        $phone = '+90' . $phone;
    } elseif (strlen($phone) === 11 && $phone[0] === '0') {
        $phone = '+90' . substr($phone, 1);
    } elseif (strlen($phone) === 12 && substr($phone, 0, 2) === '90') {
        $phone = '+' . $phone;
    } else {
        echo json_encode([
            'status' => 'error',
            'field' => 'phone',
            'message' => 'Lütfen geçerli bir telefon numarası girin!'
        ]);
        exit;
    }
}

try {
    $db -> beginTransaction();

    $profileUpdate = $db->prepare("UPDATE users SET username = :username, email = :email WHERE id = :user_id");
    $detailUpdate  = $db->prepare("UPDATE user_details SET name = :user_name, surname = :user_surname, telephone = :phone, dob = :dob WHERE id = :user_id");
    $notifyUpdate  = $db->prepare("UPDATE notification_preferences SET security = :securityNotifs, email = :emailNotifs, telephone = :phoneNotifs WHERE user_id = :user_id");

    $result1 = $profileUpdate -> execute([
        ':username' => htmlspecialchars($username, ENT_QUOTES, 'UTF-8'),
        ':email'    => filter_var($email, FILTER_SANITIZE_EMAIL),
        ':user_id'  => $user_id
    ]);

    $result2 = $detailUpdate -> execute([
        ':user_name'    => htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8'),
        ':user_surname' => htmlspecialchars($user_surname, ENT_QUOTES, 'UTF-8'),
        ':phone'        => $phone,
        ':dob'          => $dob_raw,
        ':user_id'      => $user_id
    ]);

    $result3 = $notifyUpdate -> execute([
        ':securityNotifs' => $securityNotifs,
        ':emailNotifs'    => $emailNotifs,
        ':phoneNotifs'    => $phoneNotifs,
        ':user_id'        => $user_id
    ]);

    if (!$result1 || !$result2 || !$result3) {
        $db -> rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Veritabanı hatası oluştu, lütfen daha sonra tekrar deneyin.'
        ]);
        exit;
    }

    $db -> commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Profil bilgileriniz başarıyla güncellendi!'
    ]);
} catch (PDOException $e) {
    $db -> rollBack();
    error_log('Profile update error for user ' . $user_id . ': ' . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Beklenmedik bir hata oluştu.'
    ]);
}