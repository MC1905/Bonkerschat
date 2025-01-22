<?php
include 'db.php';
$result = $conn->query("SELECT messages.*, users.username, users.profile_pic FROM messages JOIN users ON messages.user_id = users.id ORDER BY timestamp ASC");
while ($row = $result->fetch_assoc()) {
    echo "<div class='chat-message'><img src='{$row['profile_pic']}' alt='Profielfoto'><p><strong>{$row['username']}:</strong> {$row['message']} ({$row['timestamp']})</p></div>";
}
?>
