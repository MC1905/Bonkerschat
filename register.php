<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $profilePic = 'uploads/default.png';

    if (!empty($_FILES['profile_pic']['name'])) {
        $targetDir = "uploads/";

        // Controleer of de uploads-map bestaat, anders maak deze aan
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $imageFileType = strtolower(pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION));
        $newFileName = uniqid() . '.' . $imageFileType;
        $profilePic = $targetDir . $newFileName;

        if (!move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $profilePic)) {
            $error = "Fout bij uploaden van de profielfoto!";
        }
    }

    if (!isset($error)) {
        $sql = "INSERT INTO users (username, password, profile_pic) VALUES ('$username', '$password', '$profilePic')";
        if ($conn->query($sql)) {
            header("Location: login.php");
            exit();
        } else {
            $error = "Fout bij registreren, probeer opnieuw.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registreren</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <h1>Registreer</h1>
            <p>Maak een account om deel te nemen aan de chat.</p>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="username" placeholder="Gebruikersnaam" required>
                <input type="password" name="password" placeholder="Wachtwoord" required>
                <label>Profielfoto (optioneel):</label>
                <input type="file" name="profile_pic">
                <button type="submit">Registreren</button>
            </form>
            <p>Al een account? <a href="login.php">Log hier in</a></p>
        </div>
    </div>
</body>
</html>
