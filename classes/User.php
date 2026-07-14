<?php
require_once "Db.php";

class User extends Db {
    private $dbconn;

    public function __construct() {
        $this->dbconn = $this->connect();
    }

    public function register($full_name, $email, $password, $role = 'client', $phone = null) {
        try {
            // Check if email already exists
            $checkSql = "SELECT id FROM users WHERE email = ?";
            $checkStmt = $this->dbconn->prepare($checkSql);
            $checkStmt->execute([$email]);
            if ($checkStmt->fetch()) {
                $_SESSION['error'] = "Email address is already registered.";
                return false;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (full_name, email, password, role, phone) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([$full_name, $email, $hashedPassword, $role, $phone]);
            return $this->dbconn->lastInsertId();
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
            return false;
        }
    }

    public function login($email, $password) {
        try {
            $sql = "SELECT * FROM users WHERE email = ?";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];
                return true;
            }
            $_SESSION['error'] = "Invalid email or password.";
            return false;
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
            return false;
        }
    }

    public function getUserById($id) {
        try {
            $sql = "SELECT id, full_name, email, role, phone, profile_image, created_at FROM users WHERE id = ?";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getAllUsers() {
        try {
            $sql = "SELECT id, full_name, email, role, phone, created_at FROM users ORDER BY created_at DESC";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function deleteUser($id) {
        try {
            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $this->dbconn->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
