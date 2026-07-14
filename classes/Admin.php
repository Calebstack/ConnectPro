<?php
require_once "Db.php";

class Admin extends Db {
    private $dbconn;

    public function __construct() {
        $this->dbconn = $this->connect();
    }

    public function login($email, $password) {
        try {
            $sql = "SELECT * FROM admins WHERE email = ?";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([$email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['user_id'] = $admin['id']; // Map to user_id for shared headers
                $_SESSION['full_name'] = $admin['full_name'];
                $_SESSION['role'] = 'admin';
                $_SESSION['email'] = $admin['email'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
