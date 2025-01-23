<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$usernameQuery = $conn->prepare("SELECT username, profile_pic FROM users WHERE id = ?");
$usernameQuery->bind_param("i", $user_id);
$usernameQuery->execute();
$userResult = $usernameQuery->get_result()->fetch_assoc();
$username = $userResult['username'];
$profilePic = $userResult['profile_pic'] ?: 'uploads/default.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        /* Navbar styles */
        .navbar {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar h1 {
            margin: 0;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }
        .navbar a:hover {
            text-decoration: underline;
        }

        /* Chat container styles */
        .chat-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .messages {
            height: 300px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 20px;
        }
        .chat-message {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 5px;
            background: #f9f9f9;
            border-radius: 4px;
        }
        .chat-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }
        .chat-message-content {
            flex-grow: 1;
        }
        .delete-button, .edit-button {
            background: red;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 5px;
        }
        .delete-button:hover, .edit-button:hover {
            background: darkred;
        }
        form {
            display: flex;
        }
        input[type="text"] {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            margin-left: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <h1>BonkersChat</h1>
        <div>
            <a href="profile.php">My Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Chat Container -->
    <div class="chat-container">
        <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture" class="chat-avatar" style="margin-bottom: 20px;">

        <div class="messages" id="messages">
            <?php
            $query = "SELECT messages.id, messages.message, messages.timestamp, users.username, users.profile_pic 
                      FROM messages 
                      JOIN users ON messages.user_id = users.id 
                      WHERE messages.deleted_at IS NULL 
                      ORDER BY messages.timestamp ASC";
            $result = $conn->query($query);

            while ($row = $result->fetch_assoc()) {
                $avatar = $row['profile_pic'] ?: 'uploads/default.png';
                echo "<div class='chat-message' data-message-id='{$row['id']}'>
                        <img src='" . htmlspecialchars($avatar) . "' alt='Profile Picture' class='chat-avatar'>
                        <div class='chat-message-content'>
                            <p class='message-text'><strong>" . htmlspecialchars($row['username']) . ":</strong> <span>" . htmlspecialchars($row['message']) . "</span></p>
                            <small>" . htmlspecialchars($row['timestamp']) . "</small>
                        </div>";
                if ($row['username'] == $username) {
                    echo "<button class='edit-button'>Edit</button>";
                    echo "<button class='delete-button'>Delete</button>";
                }
                echo "</div>";
            }
            ?>
        </div>
        <form action="send_message.php" method="POST">
            <input type="text" name="message" placeholder="Type a message..." required>
            <button type="submit">Send</button>
        </form>
    </div>

    <script>
        document.addEventListener("click", function (e) {
            if (e.target.classList.contains("edit-button")) {
                const chatMessage = e.target.closest(".chat-message");
                const messageId = chatMessage.dataset.messageId;
                const messageTextElement = chatMessage.querySelector(".message-text span");

                const originalText = messageTextElement.textContent;

                const input = document.createElement("input");
                input.type = "text";
                input.value = originalText;
                messageTextElement.replaceWith(input);

                const saveButton = document.createElement("button");
                saveButton.textContent = "Save";
                saveButton.classList.add("save-button");

                const cancelButton = document.createElement("button");
                cancelButton.textContent = "Cancel";
                cancelButton.classList.add("cancel-button");

                e.target.replaceWith(saveButton);
                chatMessage.appendChild(cancelButton);

                saveButton.addEventListener("click", function () {
                    const updatedMessage = input.value;

                    fetch("edit_message.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `message_id=${messageId}&message=${encodeURIComponent(updatedMessage)}`,
                    })
                        .then((res) => res.json())
                        .then((data) => {
                            if (data.success) {
                                const span = document.createElement("span");
                                span.textContent = updatedMessage;
                                input.replaceWith(span);
                                saveButton.replaceWith(e.target);
                                cancelButton.remove();
                            } else {
                                alert(data.error || "Failed to edit the message.");
                            }
                        })
                        .catch((err) => {
                            alert("An error occurred while editing the message.");
                            console.error(err);
                        });
                });

                cancelButton.addEventListener("click", function () {
                    const span = document.createElement("span");
                    span.textContent = originalText;
                    input.replaceWith(span);
                    saveButton.replaceWith(e.target);
                    cancelButton.remove();
                });
            }

            if (e.target.classList.contains("delete-button")) {
                const chatMessage = e.target.closest(".chat-message");
                const messageId = chatMessage.dataset.messageId;

                if (confirm("Are you sure you want to delete this message?")) {
                    fetch("delete_message.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `message_id=${messageId}`,
                    })
                        .then((res) => res.json())
                        .then((data) => {
                            if (data.success) {
                                chatMessage.remove();
                            } else {
                                alert(data.error || "Failed to delete the message.");
                            }
                        })
                        .catch((err) => {
                            alert("An error occurred.");
                            console.error(err);
                        });
                }
            }
        });
    </script>
    <script>
    // Smooth scroll to the bottom of the messages container
    function scrollToBottom() {
        const messagesContainer = document.getElementById('messages');
        messagesContainer.scrollTo({
            top: messagesContainer.scrollHeight,
            behavior: 'smooth', // Enables smooth scrolling
        });
    }

    // Scroll to bottom on page load
    window.onload = scrollToBottom;

    // Scroll to bottom when a message is sent
    document.querySelector('form').addEventListener('submit', function (e) {
        setTimeout(scrollToBottom, 100); // Delay to ensure the message is added
    });
</script>

</body>
</html>
