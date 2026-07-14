<?php
session_start();
require_once "../classes/Booking.php";

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be signed in to perform this action.";
    header("Location: ../users/dashboard.php");
    exit();
}

$booking_id = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : (isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0);
$status = isset($_POST['status']) ? trim($_POST['status']) : (isset($_GET['status']) ? trim($_GET['status']) : '');
$redirect = isset($_POST['redirect']) ? trim($_POST['redirect']) : 'users/dashboard.php';

if ($booking_id <= 0 || empty($status)) {
    $_SESSION['error'] = "Invalid booking details requested.";
    header("Location: ../users/dashboard.php");
    exit();
}

$booking = new Booking();

// Update status
$result = $booking->updateBookingStatus($booking_id, $status);

if ($result) {
    $_SESSION['success'] = "Booking status successfully updated to " . ucfirst($status) . ".";
} else {
    $_SESSION['error'] = "Failed to update booking status.";
}

header("Location: ../" . $redirect);
exit();
?>
