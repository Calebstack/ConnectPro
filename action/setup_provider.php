<?php
session_start();
require_once "../classes/ProviderService.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'provider') {
    $_SESSION['error'] = "Unauthorized access.";
    header("Location: ../index.php");
    exit();
}

if (isset($_POST['setup_profile'])) {
    $category = $_POST['category'];
    $bio = trim($_POST['bio']);
    $price_per_hour = (float)$_POST['price_per_hour'];
    $experience_years = (int)$_POST['experience_years'];
    $location = trim($_POST['location']);
    $user_id = $_SESSION['user_id'];

    if (empty($category) || empty($bio) || $price_per_hour <= 0 || empty($location)) {
        $_SESSION['error'] = "All profile fields are required.";
        header("Location: ../users/dashboard.php");
        exit();
    }

    $providerService = new ProviderService();
    $result = $providerService->setupProviderProfile($user_id, $category, $bio, $price_per_hour, $experience_years, $location);

    if ($result) {
        $_SESSION['success'] = "Provider profile updated successfully!";
    }
    
    header("Location: ../users/dashboard.php");
    exit();
} else {
    header("Location: ../users/dashboard.php");
    exit();
}
?>
