<?php
session_start();
require_once "../classes/User.php";

if (isset($_POST['register'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role']; // 'client' or 'provider'
    $phone = trim($_POST['phone']);

    if (empty($full_name) || empty($email) || empty($password) || empty($role)) {
        $_SESSION['error'] = "All required fields must be filled.";
        header("Location: ../users/dashboard.php?register=1");
        exit();
    }

    $user = new User();
    $userId = $user->register($full_name, $email, $password, $role, $phone);

    if ($userId) {
        // Log them in immediately
        $_SESSION['user_id'] = $userId;
        $_SESSION['full_name'] = $full_name;
        $_SESSION['role'] = $role;
        $_SESSION['email'] = $email;

        $_SESSION['success'] = "Registration successful! Welcome to Connect Pro.";
        header("Location: ../users/dashboard.php");
        exit();
    } else {
        header("Location: ../users/dashboard.php?register=1");
        exit();
    }
} else {
    header("Location: ../users/dashboard.php");
    exit();
}
?>
