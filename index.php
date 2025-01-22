<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Welkom, <?php echo htmlspecialchars($user['username']); ?></h2>
        <ul>
            <li><a href="index.php">Chat</a></li>
            <li><a href="profile.php">Mijn Account</a></li>
        </ul>
        <a href="logout.php" class="logout-button">Uitloggen</a>
    </div>

    <!-- Chat Container -->
    <div class="chat-container">
        <h2>Chat</h2>
        <div class="messages" id="messages">
            <?php
           $chatQuery = "SELECT users.username, users.profile_pic, messages.message 
           FROM messages 
           JOIN users ON messages.user_id = users.id 
           ORDER BY messages.id ASC"; // Use ASC to fetch oldest first
$chatResult = $conn->query($chatQuery);
            while ($chatRow = $chatResult->fetch_assoc()) {
                $profilePic = !empty($chatRow['profile_pic']) ? $chatRow['profile_pic'] : 'uploads/default.png';
                echo "<div class='chat-message'>";
                echo "<img src='" . htmlspecialchars($profilePic) . "' alt='Profielfoto' class='chat-avatar'>";
                echo "<p><strong>" . htmlspecialchars($chatRow['username']) . ":</strong> " . htmlspecialchars($chatRow['message']) . "</p>";
                echo "</div>";
            }
            ?>
        </div>
        <form action="send_message.php" method="POST" id="chat-form">
            <input type="text" name="message" id="message-input" placeholder="Typ een bericht..." required>
            <button type="submit">Versturen</button>
        </form>
    </div>

</body>
<script>
    const messagesContainer = document.getElementById('messages');


    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

  
    window.onload = scrollToBottom;

    document.getElementById('chat-form').addEventListener('submit', function () {
        setTimeout(scrollToBottom, 100); 
    });
</script>

</html>
