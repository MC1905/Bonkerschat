<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $message = $conn->real_escape_string($_POST['message']);

    if (!empty($message)) {
        $sql = "INSERT INTO messages (user_id, message) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $message);
        $stmt->execute();
    }
}

// Redirect terug naar de chatpagina
header("Location: index.php");
exit();
?>
