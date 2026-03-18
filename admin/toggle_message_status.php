<?php
include '../db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_id'])) {
    $messageId = intval($_POST['message_id']);
    $adminId = $_SESSION['user_id'];

    // current status
    $stmt = $conn->prepare("SELECT status FROM messages WHERE id = ? AND receiver_id = ?");
    $stmt->bind_param("ii", $messageId, $adminId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $msg = $result->fetch_assoc();
        $newStatus = ($msg['status'] === 'unread') ? 'read' : 'unread';

        // Update status
        $updateStmt = $conn->prepare("UPDATE messages SET status = ? WHERE id = ? AND receiver_id = ?");
        $updateStmt->bind_param("sii", $newStatus, $messageId, $adminId);
        $updateStmt->execute();
    }
}

header("Location: admin_messages.php");
exit();
