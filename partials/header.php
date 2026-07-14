<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Dynamically detect the application root path
$script_name = $_SERVER['SCRIPT_NAME'];
$script_path = str_replace('\\', '/', dirname($script_name));
// Get the first directory level (e.g., /booking_service from /booking_service/users)
$path_parts = explode('/', trim($script_path, '/'));
$app_root = !empty($path_parts[0]) ? '/' . $path_parts[0] : '';
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $app_root . '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connect Pro - Service Booking Marketplace</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <!-- Local Offline Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/bootstrap/css/bootstrap.min.css">
    <!-- Local Offline FontAwesome Icons -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/fa/css/all.min.css">
    <!-- Custom Theme Stylesheet -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/theme.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand navbar-brand-custom" href="<?php echo $base_url; ?>index.php">
                <i class="fa-solid fa-circle-nodes"></i>
                Connect<span>Pro</span>
            </a>
            <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="<?php echo $base_url; ?>index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="<?php echo $base_url; ?>users/search.php">Browse Services</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <span class="text-white d-none d-lg-inline">
                            Hello, <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong> 
                            <span class="badge bg-secondary ms-1 text-capitalize"><?php echo $_SESSION['role']; ?></span>
                        </span>
                        
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <a href="<?php echo $base_url; ?>admin/index.php" class="btn btn-secondary-custom py-2 px-3">
                                <i class="fa-solid fa-gauge"></i> Admin Dashboard
                            </a>
                        <?php elseif ($_SESSION['role'] === 'provider'): ?>
                            <a href="<?php echo $base_url; ?>users/provider_dashboard.php" class="btn btn-secondary-custom py-2 px-3">
                                <i class="fa-solid fa-briefcase"></i> Provider Dashboard
                            </a>
                        <?php else: ?>
                            <a href="<?php echo $base_url; ?>users/dashboard.php" class="btn btn-secondary-custom py-2 px-3">
                                <i class="fa-solid fa-circle-user"></i> Dashboard
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?php echo $base_url; ?>action/logout.php" class="btn btn-primary-custom py-2 px-3">
                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                        </a>
                    <?php else: ?>
                        <a href="<?php echo $base_url; ?>users/dashboard.php" class="btn btn-secondary-custom py-2 px-3">Sign In</a>
                        <a href="<?php echo $base_url; ?>users/dashboard.php?register=1" class="btn btn-primary-custom py-2 px-3">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
