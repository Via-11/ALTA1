<?php
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
    $original_message_id = trim($_POST['message_id'] ?? '');
    $reply_message = trim($_POST['reply_message'] ?? '');

    // Validation
    if (empty($original_message_id) || empty($reply_message)) {
        echo json_encode(['success' => false, 'error' => 'Reply message cannot be empty']);
        exit;
    }

    // Get original message to find receiver and conversation
    $stmt = $pdo->prepare("
        SELECT sender_id, sender_name, subject, conversation_id
        FROM messages
        WHERE id = ? AND receiver_id = ?
    ");
    $stmt->execute([$original_message_id, $sender_id]);
    $originalMsg = $stmt->fetch();

    if (!$originalMsg) {
        echo json_encode(['success' => false, 'error' => 'Original message not found']);
        exit;
    }

    $receiver_id = $originalMsg['sender_id'];
    $conversation_id = $originalMsg['conversation_id'] ?? null;
    $original_subject = $originalMsg['subject'];
    $reply_subject = (strpos($original_subject, 'Re:') === 0) ? $original_subject : 'Re: ' . $original_subject;

    // Insert reply message with same conversation_id
    $stmt = $pdo->prepare("
        INSERT INTO messages 
        (conversation_id, sender_id, sender_name, receiver_id, subject, message, status, parent_message_id)
        VALUES (?, ?, ?, ?, ?, ?, 'unread', ?)
    ");
    $stmt->execute([$conversation_id, $sender_id, $sender_name, $receiver_id, $reply_subject, $reply_message, $original_message_id]);

    echo json_encode(['success' => true, 'message' => 'Reply sent successfully']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>