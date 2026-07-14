<?php
session_start();
require_once "../classes/Booking.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    $_SESSION['error'] = "You must be signed in as a client to book a service.";
    header("Location: ../index.php");
    exit();
}

if (isset($_POST['book_service'])) {
    $provider_id = (int)$_POST['provider_id'];
    $booking_date = trim($_POST['booking_date']);
    $booking_time = trim($_POST['booking_time']);
    $notes = trim($_POST['notes']);
    $client_id = $_SESSION['user_id'];

    if (empty($booking_date) || empty($booking_time) || $provider_id <= 0) {
        $_SESSION['error'] = "Preferred date and time are required.";
        header("Location: ../users/provider_details.php?id=" . $provider_id);
        exit();
    }

    $booking = new Booking();
    $result = $booking->createBooking($client_id, $provider_id, $booking_date, $booking_time, $notes);

    if ($result) {
        $_SESSION['success'] = "Your booking request was submitted successfully! The provider will review it shortly.";
        header("Location: ../users/dashboard.php");
        exit();
    } else {
        header("Location: ../users/provider_details.php?id=" . $provider_id);
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>
