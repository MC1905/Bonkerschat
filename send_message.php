<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $message = $conn->real_escape_string($_POST['message']);

    $query = "INSERT INTO messages (user_id, message) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $user_id, $message);
    $stmt->execute();
    header("Location: index.php");
    exit();
}
?>
