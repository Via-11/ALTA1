<?php
session_start();

include '../db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'] ?? null;
    $status = $_POST['status'] ?? null;

    if ($booking_id && in_array($status, ['approved', 'rejected'])) {
        try {
            $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
            $stmt->execute([$status, $booking_id]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }
}

header("Location: admin_bookings.php");
exit();
?>
