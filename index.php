<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT username, profile_pic FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$profile_pic = (!empty($user['profile_pic']) && file_exists($user['profile_pic'])) ? htmlspecialchars($user['profile_pic']) : 'uploads/default.png';
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BonkersChat</title>
    <style>
        /* Algemene styling */
        body {
            display: flex;
            height: 100vh;
            margin: 0;
            background: #36393F;
            font-family: 'Arial', sans-serif;
            color: white;
            overflow: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #2F3136;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: fixed;
            left: 0;
            top: 0;
        }

        .user-profile {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar-profile-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid #7289DA;
            margin-bottom: 10px;
            object-fit: cover;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            width: 100%;
        }

        .sidebar-menu li {
            text-align: center;
            margin: 10px 0;
        }

        .sidebar-menu li a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            display: block;
            padding: 10px;
            background: #5865F2;
            border-radius: 8px;
            font-weight: bold;
            transition: background 0.3s;
        }

        .sidebar-menu li a:hover {
            background: #4752C4;
        }

        /* Logout knop */
        .logout-button {
           
            display: block;
            text-align: center;
            color: white;
            padding: 10px;
            width: 67%;
            background: #ED4245;
            border-radius: 8px;
            font-weight: bold;
        }

        .logout-button:hover {
            background: #D43F42;
        }

        /* Chat-container gecentreerd */
        .main-content {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 20px;
            margin-left: 250px;
            width: calc(100% - 250px);
            height: 100vh;
        }

        .chat-container {
            width: 90%;
            max-width: 900px;
            background: #2F3136;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            height: 80vh;
        }

        /* Chat berichten */
        .messages {
            background: #2E3338;
            padding: 15px;
            flex-grow: 1;
            overflow-y: auto;
            border-radius: 8px;
            border: 1px solid #23272A;
        }

        .chat-message {
            display: flex;
            align-items: flex-start;
            padding: 10px;
            margin-bottom: 8px;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .chat-message:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .chat-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-right: 12px;
            object-fit: cover;
        }

        .chat-content {
            display: flex;
            flex-direction: column;
        }

        .chat-content strong {
            font-size: 15px;
            color: #F0F0F0;
        }

        .chat-content p {
            background: #40444B;
            padding: 12px;
            border-radius: 6px;
            margin: 4px 0;
            font-size: 15px;
            color: #DCDDDE;
        }

        /* Input en knoppen */
        #chat-form {
            display: flex;
            background: #40444B;
            border-radius: 6px;
            padding: 8px;
            margin-top: 10px;
        }

        #message-input {
            flex-grow: 1;
            padding: 14px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            background: #40444B;
            color: white;
            outline: none;
        }

        #chat-form button {
            padding: 14px;
            background: #5865F2;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.3s;
        }

        #chat-form button:hover {
            background: #4752C4;
        }
    </style>
</head>
<body>

   <div class="sidebar">
    <div class="sidebar-content">
        <div class="user-profile">
            <img src="<?php echo $profile_pic; ?>" alt="Profielfoto" class="sidebar-profile-img">
            <h2>Welkom, <?php echo htmlspecialchars($user['username']); ?></h2>
        </div>

        <ul class="sidebar-menu">
            <li><a href="index.php">💬 Chat</a></li>
            <li><a href="profile.php">👤 Mijn Account</a></li>
        </ul>
    </div>

    <!-- Uitlogknop correct gepositioneerd -->
    <a href="logout.php" class="logout-button">🚪 Uitloggen</a>
</div>


    <!-- Chat-container -->
    <div class="main-content">
        <div class="chat-container">
            <h2>Chat</h2>
            <div class="messages" id="messages">
                <?php
                $chatQuery = "SELECT users.username, users.profile_pic, messages.message 
                            FROM messages 
                            JOIN users ON messages.user_id = users.id 
                            ORDER BY messages.id ASC"; 
                $chatResult = $conn->query($chatQuery);
                while ($chatRow = $chatResult->fetch_assoc()) {
                    echo "<div class='chat-message'>";
                    echo "<img src='" . htmlspecialchars($chatRow['profile_pic']) . "' alt='Profielfoto' class='chat-avatar'>";
                    echo "<div class='chat-content'><strong>" . htmlspecialchars($chatRow['username']) . "</strong>";
                    echo "<p>" . htmlspecialchars($chatRow['message']) . "</p></div>";
                    echo "</div>";
                }
                ?>
            </div>
            <form action="send_message.php" method="POST" id="chat-form">
                <input type="text" name="message" id="message-input" placeholder="Typ een bericht..." required>
                <button type="submit">Versturen</button>
            </form>
        </div>
    </div>

    <script>
        // Scroll automatisch naar beneden bij laden
        var chatMessages = document.getElementById("messages");
        chatMessages.scrollTop = chatMessages.scrollHeight;
    </script>

</body>
</html>
