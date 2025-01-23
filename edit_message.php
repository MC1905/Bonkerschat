<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $message_id = intval($_POST['message_id']);
    $new_message = $conn->real_escape_string($_POST['message']);

    $query = "SELECT user_id FROM messages WHERE id = ? AND deleted_at IS NULL";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $message = $result->fetch_assoc();

    if ($message && $message['user_id'] == $user_id) {
        $updateQuery = "UPDATE messages SET message = ?, edited_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $new_message, $message_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to edit message.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Permission denied.']);
    }
}
?>
