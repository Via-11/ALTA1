<?php
include 'db.php';
header('Content-Type: application/json');

$service = $_GET['service'] ?? '';
$location = $_GET['location'] ?? '';

if ($service && $location) {
    $stmt = $pdo->prepare("SELECT available_slots, is_available FROM service_slots WHERE service_name = ? AND location = ?");
    $stmt->execute([$service, $location]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        if ($data['available_slots'] <= 0 || $data['is_available'] == 0) {
            echo json_encode([
                'status' => 'error', 
                'message' => "🔴 Sorry, this service is currently FULL or UNAVAILABLE at $location."
            ]);
        } else {
            echo json_encode([
                'status' => 'success', 
                'message' => "🟢 {$data['available_slots']} slots available at $location. Book now!"
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error', 
            'message' => "🟡 Service not found for this campus."
        ]);
    }
}
exit;
