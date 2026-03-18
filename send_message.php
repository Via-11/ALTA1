<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

try {

    $sender_id = $_SESSION['user_id'];
    $sender_name = $_SESSION['user_name'] ?? 'User';
    $receiver_id = trim($_POST['receiver_id'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validation
    if (empty($receiver_id) || empty($subject) || empty($message)) {
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        exit;
    }

    // Check receiver
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE user_id = ?");
    $stmt->execute([$receiver_id]);

    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Recipient not found']);
        exit;
    }

    // Conversation ID
    $stmt = $pdo->prepare("SELECT MAX(conversation_id) as last_id FROM messages");
    $stmt->execute();
    $row = $stmt->fetch();
    $conversation_id = ($row['last_id'] ?? 0) + 1;

    // Insert
    $stmt = $pdo->prepare("
        INSERT INTO messages (conversation_id, sender_id, sender_name, receiver_id, subject, message, status)
        VALUES (?, ?, ?, ?, ?, ?, 'unread')
    ");
    $stmt->execute([$conversation_id, $sender_id, $sender_name, $receiver_id, $subject, $message]);

    // ✅ SUCCESS RESPONSE
    echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
    exit;

} catch (Exception $e) {

    // ❌ ONLY ONE RESPONSE HERE
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}

?>