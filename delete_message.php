<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $message_id = intval($_POST['message_id']);

    $query = "SELECT user_id FROM messages WHERE id = ? AND deleted_at IS NULL";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if (!$result || $result['user_id'] != $user_id) {
        echo json_encode(['success' => false, 'error' => 'Permission denied.']);
        exit();
    }

    $deleteQuery = "UPDATE messages SET deleted_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $message_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete the message.']);
    }
}
?>
