<?php
session_start();
require_once "../classes/Admin.php";
require_once "../classes/User.php";

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Please fill in all credentials.";
        header("Location: ../users/dashboard.php");
        exit();
    }

    // Try Admin login first
    $admin = new Admin();
    if ($admin->login($email, $password)) {
        $_SESSION['success'] = "Welcome back, Administrator!";
        header("Location: ../admin/index.php");
        exit();
    }

// Try regular Client/Provider login
     $user = new User();
     if ($user->login($email, $password)) {
         $_SESSION['success'] = "Welcome back, " . $_SESSION['full_name'] . "!";
         // Redirect providers to their dedicated dashboard
         if ($_SESSION['role'] === 'provider') {
             header("Location: ../users/provider_dashboard.php");
         } else {
             header("Location: ../users/dashboard.php");
         }
         exit();
     } else {
         header("Location: ../users/dashboard.php");
         exit();
     }
} else {
    header("Location: ../users/dashboard.php");
    exit();
}
?>
