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

// Profielfoto uploaden met unieke naam en mapcontrole
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_pic'])) {
    $target_dir = "uploads/";

    // Controleer of de uploads-map bestaat, anders maak deze aan
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Maakt de map met juiste rechten
    }

    $imageFileType = strtolower(pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION));
    $newFileName = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $newFileName;

    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
        $updateQuery = "UPDATE users SET profile_pic = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $target_file, $user_id);
        $stmt->execute();
        header("Location: profile.php");
        exit();
    } else {
        echo "<p style='color: red;'>Fout bij uploaden van bestand!</p>";
    }
}

// Gebruikersnaam wijzigen
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_username'])) {
    $new_username = $conn->real_escape_string($_POST['new_username']);
    $updateUsernameQuery = "UPDATE users SET username = ? WHERE id = ?";
    $stmt = $conn->prepare($updateUsernameQuery);
    $stmt->bind_param("si", $new_username, $user_id);
    $stmt->execute();
    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Mijn Profiel</title>
    <style>
        .container {
            max-width: 400px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin: auto;
            margin-top: 50px;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 15px;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background: #007bff;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Mijn Profiel</h2>
        <img src="<?php echo (!empty($user['profile_pic']) && file_exists($user['profile_pic'])) ? htmlspecialchars($user['profile_pic']) : 'uploads/default.png'; ?>" alt="Profielfoto" class="profile-img">
        <p><strong>Gebruikersnaam:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        
        <form action="profile.php" method="POST">
            <input type="text" name="new_username" placeholder="Nieuwe gebruikersnaam" required>
            <button type="submit">Gebruikersnaam wijzigen</button>
        </form>
        
        <form action="profile.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="profile_pic" required>
            <button type="submit">Profielfoto wijzigen</button>
        </form>
        
        <a href="index.php" class="profile-button">Terug naar Chat</a>
    </div>
    <script>
        // Scroll automatisch naar het laatste bericht
        function scrollToBottom() {
            var messageContainer = document.getElementById("messages");
            if (messageContainer) {
                messageContainer.scrollTop = messageContainer.scrollHeight;
            }
        }

        // Sorteer berichten oplopend en scroll automatisch naar onder
        window.onload = function() {
            scrollToBottom();
        };

        document.addEventListener("DOMContentLoaded", function() {
            var chatForm = document.getElementById("chat-form");
            if (chatForm) {
                chatForm.addEventListener("submit", function() {
                    setTimeout(scrollToBottom, 100);
                });
            }
        });
    </script>
</body>
</html>
