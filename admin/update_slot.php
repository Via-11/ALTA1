<?php
session_start();
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $available = $_POST['available'];
    $date = $_POST['date'];
    $is_available = $_POST['is_available']; // Capture the toggle value
    $description = $_POST['description'] ?? '';

    try {
        $sql = "UPDATE service_slots 
                SET available_slots = ?, 
                    available_date = ?, 
                    is_available = ?, 
                    service_description = ? 
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$available, $date, $is_available, $description, $id]);

        header("Location: admin_services.php?status=success");
        exit();
    } catch (PDOException $e) {
        error_log("Update error: " . $e->getMessage());
        header("Location: admin_services.php?status=error");
    }
}
