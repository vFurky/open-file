<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/open-file/files/security/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/open-file/files/security/all-values.php';

if (!isset($_SESSION['user'])) {
	header('Location: ' . $site_url . 'login');
	exit;
}

if (isset($_SESSION['user'])) {
	try {
		$user_id = $_SESSION['user']['id'];

		$getUserDetails = $db -> prepare("SELECT * FROM user_details WHERE user_id = :user_id");
		$getUserDetails -> execute(['user_id' => $user_id]);
		$user_details = $getUserDetails -> fetch(PDO::FETCH_ASSOC);

		$username = $_SESSION['user']['username'] ?? 'Kullanıcı';
		$user_email = $_SESSION['user']['email'] ?? '';
		$reg_date = $_SESSION['user']['created_at'] ?? '';

		if ($user_details) {
			$user_name = !empty($user_details['name']) ? htmlspecialchars($user_details['name']) : 'İsim';
			$user_surname = !empty($user_details['surname']) ? htmlspecialchars($user_details['surname']) : 'Soyisim';
			$user_dob = !empty($user_details['dob']) ? htmlspecialchars($user_details['dob']) : 'Girilmedi';
			$user_tel = !empty($user_details['telephone']) ? htmlspecialchars($user_details['telephone']) : 'Girilmedi';
		} else {
			$user_name = 'İsim';
			$user_surname = 'Soyisim';
			$user_dob = 'Girilmedi';
			$user_tel = 'Girilmedi';

			$createUserDetials = $db->prepare("INSERT INTO user_details (user_id, created_at) VALUES (:user_id, :created_at)");
			$createUserDetials -> execute(['user_id' => $user_id, 'created_at' => date('Y-m-d H:i:s')]);
		}

		$updateUserSession = $db->prepare("UPDATE user_sessions SET last_activity = :last_activity WHERE user_id = :user_id AND is_active = 1");
		$updateUserSession -> execute(['last_activity' => date('Y-m-d H:i:s'), 'user_id' => $user_id]);

	} catch (PDOException $e) {
		error_log("Login Check Error: " . $e->getMessage());
		header('Location: ' . $site_url . 'error');
		exit;
	}
}