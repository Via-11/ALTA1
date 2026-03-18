<?php
include '../db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['slot_id'];
    $name = $_POST['service_name'];
    $total = $_POST['total_slots'];
    $available = $_POST['available_slots'];
    $date = $_POST['available_date'];

    try {
        $sql = "UPDATE service_slots 
                SET service_name = ?, total_slots = ?, available_slots = ?, available_date = ? 
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $total, $available, $date, $id]);

        header("Location: admin_services.php?msg=Success");
    } catch (Exception $e) {
        die("Error updating: " . $e->getMessage());
    }
}
?>
