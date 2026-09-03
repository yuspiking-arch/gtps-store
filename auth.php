<?php
// auth.php
require_once 'db.php';

class Auth {
    public static function login($username, $password) {
        $db = Database::getServerDb();
        // Asumsi password MD5 (sesuaikan dengan hash server-mu)
        $sql = "SELECT name, password, role FROM players WHERE LOWER(name) = LOWER(:username)";
        $user = Database::fetchOne($db, $sql, [':username' => $username]);

        if (!$user) {
            return ['success' => false, 'message' => 'Username tidak ditemukan'];
        }

        // Verifikasi password
        if (md5($password) !== $user['password']) {
            return ['success' => false, 'message' => 'Password salah'];
        }

        $_SESSION['user'] = [
            'username' => $user['name'],
            'role' => (int)$user['role'],
            'login_time' => time()
        ];

        return ['success' => true, 'user' => $_SESSION['user']];
    }

    public static function checkLogin() {
        if (!isset($_SESSION['user'])) {
            return false;
        }
        if (time() - $_SESSION['user']['login_time'] > SESSION_TIMEOUT) {
            self::logout();
            return false;
        }
        return true;
    }

    public static function logout() {
        unset($_SESSION['user']);
        session_destroy();
    }

    public static function getUser() {
        return $_SESSION['user'] ?? null;
    }

    public static function isAdmin() {
        $user = self::getUser();
        return $user && $user['role'] >= ADMIN_ROLE;
    }

    public static function requireLogin() {
        if (!self::checkLogin()) {
            header('Location: index.php');
            exit;
        }
    }

    public static function requireAdmin() {
        self::requireLogin();
        if (!self::isAdmin()) {
            die('Akses ditolak, Anda bukan admin.');
        }
    }
}
?>
