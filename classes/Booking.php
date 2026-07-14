<?php
require_once "Db.php";

class Booking extends Db {
    private $dbconn;

    public function __construct() {
        $this->dbconn = $this->connect();
    }

    public function createBooking($client_id, $provider_id, $booking_date, $booking_time, $notes = null) {
        try {
            $sql = "INSERT INTO bookings (client_id, provider_id, booking_date, booking_time, status, notes) 
                    VALUES (?, ?, ?, ?, 'pending', ?)";
            $stmt = $this->dbconn->prepare($sql);
            return $stmt->execute([$client_id, $provider_id, $booking_date, $booking_time, $notes]);
        } catch (PDOException $e) {
            $_SESSION['error'] = "Booking creation failed: " . $e->getMessage();
            return false;
        }
    }

    public function updateBookingStatus($booking_id, $status) {
        try {
            if (!in_array($status, ['pending', 'approved', 'cancelled'])) {
                return false;
            }
            $sql = "UPDATE bookings SET status = ? WHERE id = ?";
            $stmt = $this->dbconn->prepare($sql);
            return $stmt->execute([$status, $booking_id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getBookingsByClientId($client_id) {
        try {
            $sql = "SELECT b.*, p.category, p.price_per_hour, u.full_name AS provider_name, u.phone AS provider_phone, u.email AS provider_email
                    FROM bookings b
                    INNER JOIN providers p ON b.provider_id = p.id
                    INNER JOIN users u ON p.user_id = u.id
                    WHERE b.client_id = ?
                    ORDER BY b.booking_date DESC, b.booking_time DESC";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([$client_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getBookingsByProviderId($provider_id) {
        try {
            $sql = "SELECT b.*, u.full_name AS client_name, u.phone AS client_phone, u.email AS client_email
                    FROM bookings b
                    INNER JOIN users u ON b.client_id = u.id
                    WHERE b.provider_id = ?
                    ORDER BY b.booking_date DESC, b.booking_time DESC";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([$provider_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getAllBookings() {
        try {
            $sql = "SELECT b.*, p.category, u_client.full_name AS client_name, u_prov.full_name AS provider_name
                    FROM bookings b
                    INNER JOIN users u_client ON b.client_id = u_client.id
                    INNER JOIN providers p ON b.provider_id = p.id
                    INNER JOIN users u_prov ON p.user_id = u_prov.id
                    ORDER BY b.created_at DESC";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>
