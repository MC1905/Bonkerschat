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
        mkdir($target_dir, 0777, true);
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
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Profiel</title>
    <style>
        body {
            background: #1b2838;
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: white;
        }

        .container {
            max-width: 380px;
            background: #22303f;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        h2 {
            margin-bottom: 15px;
            color: #f8f9fa;
        }

        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid #4a90e2;
            margin-bottom: 15px;
            object-fit: cover;
        }

        p {
            font-size: 16px;
            color: #f8f9fa;
            margin-bottom: 20px;
        }

        input {
            width: 90%;
            padding: 10px;
            margin: 8px 0;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            background: #2e3d4e;
            color: white;
            text-align: center;
            transition: border 0.3s ease-in-out;
        }

        input:focus {
            border: 2px solid #4a90e2;
            outline: none;
            background: #34495e;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        button {
            width: 95%;
            padding: 12px;
            margin-top: 10px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease-in-out;
        }

        .btn-blue {
            background: #4a90e2;
            color: white;
        }

        .btn-blue:hover {
            background: #357abd;
        }

        .btn-green {
            background: #1abc9c;
            color: white;
        }

        .btn-green:hover {
            background: #16a085;
        }

        .btn-red {
            background: #e74c3c;
            color: white;
        }

        .btn-red:hover {
            background: #c0392b;
        }

        .profile-button {
            display: inline-block;
            margin-top: 15px;
            padding: 12px;
            width: 95%;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Mijn Profiel</h2>
        <img src="<?php echo (!empty($user['profile_pic']) && file_exists($user['profile_pic'])) ? htmlspecialchars($user['profile_pic']) : 'uploads/default.png'; ?>" alt="Profielfoto" class="profile-img">
        <p><strong>Gebruikersnaam:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        
        <form action="profile.php" method="POST" class="form-group">
            <input type="text" name="new_username" placeholder="Nieuwe gebruikersnaam" required>
            <button type="submit" class="btn-blue">Gebruikersnaam wijzigen</button>
        </form>
        
        <form action="profile.php" method="POST" enctype="multipart/form-data" class="form-group">
            <input type="file" name="profile_pic" required>
            <button type="submit" class="btn-blue">Profielfoto wijzigen</button>
        </form>
        
        <a href="index.php" class="profile-button btn-green">Terug naar Chat</a>
    </div>
</body>
</html>
