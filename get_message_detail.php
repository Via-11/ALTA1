<?php
require_once 'db.php';
session_start();

$message_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'] ?? 1;

// Fetch message and ensure it belongs to the logged-in user
$stmt = $conn->prepare("SELECT * FROM messages WHERE id = ? AND receiver_id = ?");
$stmt->bind_param("ii", $message_id, $user_id);
$stmt->execute();
$res = $stmt->get_result();
$msg = $res->fetch_assoc();

if ($msg) {
    echo json_encode([
        'success' => true,
        'subject' => htmlspecialchars($msg['subject']),
        'message' => htmlspecialchars($msg['message']),
        'status'  => $msg['status'],
        'date'    => date('F j, Y, g:i a', strtotime($msg['created_at']))
    ]);
} else {
    echo json_encode(['success' => false]);
}
?>