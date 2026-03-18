<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false]);
    exit();
}

if (!isset($_POST['message_id'])) {
    echo json_encode(["success" => false]);
    exit();
}

$user_id = $_SESSION['user_id'];
$message_id = intval($_POST['message_id']);

try {

    $stmt = $pdo->prepare("
        UPDATE messages
        SET status = 'unread'
        WHERE id = ? AND receiver_id = ?
    ");

    $stmt->execute([$message_id, $user_id]);

    echo json_encode([
        "success" => true
    ]);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);

}