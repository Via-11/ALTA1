<?php


//json heavy idk if can use others
include 'db.php'; 
header('Content-Type: application/json');

$data = [];
try {
    $stmt = $pdo->query("SELECT service_name, location, available_slots FROM service_slots");
    $slots = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($slots as $slot) {
        $key = $slot['service_name'] . ' at ' . $slot['location']; 
        $data[$key] = $slot['available_slots'];
    }

    echo json_encode($data);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
