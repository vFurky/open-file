<?php
class Auth {
    private $db;
    private static $instance = null;
    private $maxLoginAttempts = 5;
    private $lockoutTime = 15;
    
    private function __construct($db) {
        $this->db = $db;
    }
    
    public static function getInstance($db) {
        if (self::$instance === null) {
            self::$instance = new self($db);
        }
        return self::$instance;
    }

    public function login($email, $password, $rememberMe = false) {
        try {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Lütfen geçerli bir E-Posta formatı girin!');
            }

            $this->checkBruteForce();
            $user = $this->getUserByEmail($email);

            if (!is_array($user) || !isset($user['password']) || !$this->verifyPassword($password, $user['password'])) {
                $this->logFailedAttempt($email);
                throw new Exception('E-Posta adresi veya parola hatalı!');
            }
            
            if (!$this->verifyPassword($password, $user['password'])) {
                $this->logFailedAttempt($email);
                throw new Exception('E-Posta adresi veya parola hatalı!');
            }

            if ($user['two_factor_enabled']) {
                return $this->initiate2FA($user);
            }

            $this->createSession($user);

            if ($rememberMe) {
                $this->setRememberMe($user['id']);
            }

            Logger::info("GIRIS_BASARILI: " . $user['username'] . " - IP: " . $this->getClientIp() . " - Tarih: " . date('Y-m-d H:i:s'));
            $this->clearLoginAttempts($email);

            return true;

        } catch (Exception $e) {
            Logger::error("LOGIN_HATASI: " . $e->getMessage() . " - IP: " . $this->getClientIp());
            throw $e;
        }
    }

    private function getUserByEmail($email) {
        $getUserByEmail = $this->db->prepare("SELECT * FROM users WHERE email = :email AND status = 1 AND deleted_at IS NULL");
        $getUserByEmail -> execute([':email' => $email]);
        $user = $getUserByEmail -> fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $this->logFailedAttempt($email);
            throw new Exception('E-Posta adresi veya parola hatalı!');
        }

        return $user;
    }

    private function verifyPassword($password, $hashedPassword) {
        // TODO: Gelecekte daha güvenli bir hash yöntemi kullanılmalı
        return md5(sha1($password)) === $hashedPassword;
    }

    private function checkBruteForce() {
        $ip = $this->getClientIp();
        
        $checkBruteForce = $this->db->prepare("SELECT COUNT(*) as attempt_count FROM login_attempts WHERE ip_address = ? AND attempt_time > DATE_SUB(UTC_TIMESTAMP(), INTERVAL ? MINUTE)");
        $checkBruteForce -> execute([$ip, $this->lockoutTime]);
        $attempts = $checkBruteForce -> fetch(PDO::FETCH_ASSOC);

        if ($attempts['attempt_count'] >= $this->maxLoginAttempts) {
            Logger::error("BRUTE_FORCE_TESPIT: IP: {$ip} - Tarih: " . date('Y-m-d H:i:s'));
            throw new Exception("Çok fazla başarısız deneme. Lütfen {$this->lockoutTime} dakika sonra tekrar deneyin.");
        }
    }

    private function createSession($user) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'username' => $user['username'],
            'name' => $user['name'],
            'role' => $user['role'],
            'created_at' => date('Y-m-d H:i:s', strtotime($user['created_at']))
        ];

        $createSession = $this->db->prepare("INSERT INTO user_sessions (user_id, session_id, ip_address, user_agent, logged_in, last_activity) VALUES (:user_id, :session_id, :ip_address, :user_agent, UTC_TIMESTAMP(), UTC_TIMESTAMP())");

        $createSession -> execute([
            ':user_id' => $user['id'],
            ':session_id' => session_id(),
            ':ip_address' => $this->getClientIp(),
            ':user_agent' => $_SERVER['HTTP_USER_AGENT']
        ]);

        $this->updateLastLogin($user['id']);
    }

    private function updateLastLogin($userId) {
        $updateLastLogin = $this->db->prepare("UPDATE users SET last_login = UTC_TIMESTAMP(), login_count = login_count + 1 WHERE id = :id");
        $updateLastLogin -> execute([':id' => $userId]);
    }

    private function setRememberMe($userId) {
        $token = bin2hex(random_bytes(32));

        $setRememberMe = $this->db->prepare("UPDATE users SET remember_token = :token, token_expiry = DATE_ADD(UTC_TIMESTAMP(), INTERVAL 30 DAY) WHERE id = :id");

        $setRememberMe -> execute([
            ':token' => $token,
            ':id' => $userId
        ]);

        setcookie('remember_token', $token, time() + (86400 * 30), '/', '', true, true);
    }

    private function logFailedAttempt($email) {
        $logFailedAttempt = $this->db->prepare("INSERT INTO login_attempts (ip_address, email, attempt_time, user_agent) VALUES (?, ?, UTC_TIMESTAMP(), ?)");
        $logFailedAttempt -> execute([$this->getClientIp(), $email, $_SERVER['HTTP_USER_AGENT']]);
    }

    private function clearLoginAttempts($email) {
        $clearLoginAttempts = $this->db->prepare("DELETE FROM login_attempts WHERE ip_address = ? AND email = ?");
        $clearLoginAttempts -> execute([$this->getClientIp(), $email]);
    }

    private function getClientIp() {
        $ipAddress = '';
        
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }
        
        return $ipAddress;
    }

    public function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time']) || time() - $_SESSION['csrf_token_time'] > 1800) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
        return $_SESSION['csrf_token'];
    }

    public function validateCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time']) || $token !== $_SESSION['csrf_token']) {
            return false;
        }

        if (time() - $_SESSION['csrf_token_time'] > 1800) {
            return false;
        }

        return true;
    }

    public function register($userData) {
        try {
            $this->validateRegistrationData($userData);
            $this->checkExistingUser($userData['username'], $userData['email']);
            $this->db->beginTransaction();
            $userId = $this->createUser($userData);
            $this->createUserDetails($userId);
            $this->createNotificationPreferences($userId);
            $this->db->commit();
            Logger::info("KAYIT_BASARILI: {$userData['username']} - IP: " . $this->getClientIp() . " - Tarih: 2025-07-02 23:26:15");

            return $userId;

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            Logger::error("KAYIT_HATASI: " . $e->getMessage() . " - IP: " . $this->getClientIp());
            throw $e;
        }
    }

    private function validatePassword($password) {
        $uppercase = preg_match('/[A-Z]/', $password);
        $lowercase = preg_match('/[a-z]/', $password);
        $number = preg_match('/[0-9]/', $password);
        $specialChar = preg_match('/[^a-zA-Z0-9]/', $password);

        if (!$uppercase || !$lowercase || !$number || !$specialChar || strlen($password) < 8) {
            return false;
        }

        return true;
    }

    private function validatePasswordStrength($password) {
        $requirements = [
            'length' => strlen($password) >= 8,
            'uppercase' => preg_match('/[A-Z]/', $password),
            'lowercase' => preg_match('/[a-z]/', $password),
            'number' => preg_match('/[0-9]/', $password),
            'special' => preg_match('/[^a-zA-Z0-9]/', $password)
        ];

        $errors = [];
        if (!$requirements['length']) $errors[] = "en az 8 karakter";
        if (!$requirements['uppercase']) $errors[] = "büyük harf";
        if (!$requirements['lowercase']) $errors[] = "küçük harf";
        if (!$requirements['number']) $errors[] = "rakam";
        if (!$requirements['special']) $errors[] = "özel karakter";

        if (!empty($errors)) {
            throw new Exception('Parola şunları içermelidir: ' . implode(', ', $errors));
        }

        return true;
    }

    private function validateRegistrationData($data) {
        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $data['username'])) {
            throw new Exception('Kullanıcı adı yalnızca harf, rakam ve alt çizgi içerebilir; 3-20 karakter uzunluğunda olmalıdır.');
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Geçersiz e-posta formatı.');
        }

        $this->validatePasswordStrength($data['password']);

        if ($data['password'] !== $data['confirmPassword']) {
            throw new Exception('Parolalar eşleşmiyor.');
        }

        if (!isset($data['termsCheck']) || $data['termsCheck'] !== 'on') {
            throw new Exception('Kayıt olmak için kullanım şartlarını ve gizlilik politikasını kabul etmelisiniz!');
        }
    }

    private function checkExistingUser($username, $email) {
        $checkExistingUser1 = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND deleted_at IS NULL");
        $checkExistingUser1 -> execute([$username]);
        if ($checkExistingUser1 -> fetchColumn() > 0) {
            throw new Exception('Bu kullanıcı adı zaten kullanılıyor!');
        }

        $checkExistingUser2 = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND deleted_at IS NULL");
        $checkExistingUser2 -> execute([$email]);
        if ($checkExistingUser2 -> fetchColumn() > 0) {
            throw new Exception('Bu E-Posta adresi zaten kullanılıyor!');
        }
    }

    private function createUser($userData) {
        $createUser = $this->db->prepare("INSERT INTO users (username, email, password, subs, created_at, status, role, remember_token, token_expiry, two_factor_enabled, last_login, login_count) VALUES (:username, :email, :password, 'free', UTC_TIMESTAMP(), 1, 'user', NULL, NULL, 0, NULL, 0)");

        $hashedPassword = md5(sha1($userData['password'])); // TODO: Daha güvenli hash yöntemi kullanılmalı

        $createUser->execute([
            ':username' => $userData['username'],
            ':email' => $userData['email'],
            ':password' => $hashedPassword
        ]);

        return $this->db->lastInsertId();
    }

    private function createUserDetails($userId) {
        $createUserDetails = $this->db->prepare("INSERT INTO user_details (user_id, created_at) VALUES (:user_id, NOW())");

        return $createUserDetails -> execute([
            ':user_id' => $userId
        ]);
    }

    private function createNotificationPreferences($userId) {
        $createNotificationPreferences = $this->db->prepare("INSERT INTO notification_preferences (user_id, security, email, telephone) VALUES (:user_id, 1, 1, 1)");

        return $createNotificationPreferences->execute([
            ':user_id' => $userId
        ]);
    }
}