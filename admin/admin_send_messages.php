<?php
include '../db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$senderId = $_SESSION['user_id'];

$senderName = 'Admin'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipient = trim($_POST['recipient'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$recipient || !$subject || !$message) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: admin_messages.php");
        exit();
    }
//find by username
$stmt = $conn->prepare("SELECT user_id FROM users WHERE name = ? LIMIT 1");
$stmt->bind_param("s", $recipient);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Recipient not found.";
    header("Location: admin_messages.php");
    exit();
}

$recipientData = $result->fetch_assoc();
$recipientId = $recipientData['user_id'];  // Fix here

// Insert message
$stmtInsert = $conn->prepare("INSERT INTO messages (sender_id, sender_name, receiver_id, subject, message, status, created_at) VALUES (?, ?, ?, ?, ?, 'unread', NOW())");
$stmtInsert->bind_param("issss", $senderId, $senderName, $recipientId, $subject, $message);  

if ($stmtInsert->execute()) {
    $_SESSION['success'] = "Message sent successfully.";
} else {
    $_SESSION['error'] = "Failed to send message.";
}

header("Location: admin_messages.php");
exit();
}

