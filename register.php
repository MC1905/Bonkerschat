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
            $error = "❌ Fout bij uploaden van de profielfoto!";
        }
    }

    if (!isset($error)) {
        $sql = "INSERT INTO users (username, password, profile_pic) VALUES ('$username', '$password', '$profilePic')";
        if ($conn->query($sql)) {
            header("Location: login.php");
            exit();
        } else {
            $error = "❌ Fout bij registreren, probeer opnieuw.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registreren - BonkersChat</title>
    <style>
        /* Algemene body styling */
        body {
            background: #36393F;
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: white;
        }

        /* Registratie container */
        .register-container {
            width: 100%;
            max-width: 400px;
            background: #2F3136;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .register-box h1 {
            color: #ffffff;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .register-box p {
            color: #B9BBBE;
            font-size: 14px;
            margin-bottom: 20px;
        }

        /* Error message */
        .error {
            color: #ff5c5c;
            font-size: 14px;
            margin-bottom: 10px;
        }

        /* Input velden */
        input {
            width: 90%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            background: #40444B;
            color: white;
            text-align: center;
            transition: border 0.3s ease-in-out;
        }

        input:focus {
            border: 2px solid #5865F2;
            outline: none;
            background: #3A3E45;
        }

        /* File input */
        label {
            display: block;
            margin-top: 10px;
            color: #B9BBBE;
            font-size: 14px;
        }

        input[type="file"] {
            width: 100%;
            background: none;
            border: none;
            padding: 5px;
            color: white;
            font-size: 14px;
        }

        /* Registratie knop */
        button {
            width: 95%;
            padding: 12px;
            margin-top: 10px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            background: #5865F2;
            color: white;
            transition: background 0.3s ease-in-out;
        }

        button:hover {
            background: #4752C4;
        }

        /* Inlog-link */
        .login-link {
            display: block;
            margin-top: 15px;
            font-size: 14px;
            color: #B9BBBE;
            text-decoration: none;
            transition: color 0.3s;
        }

        .login-link:hover {
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <h1>BonkersChat</h1>
            <p>Maak een account om deel te nemen aan de chat.</p>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="username" placeholder="Gebruikersnaam" required>
                <input type="password" name="password" placeholder="Wachtwoord" required>
                <label>Profielfoto (optioneel):</label>
                <input type="file" name="profile_pic">
                <button type="submit">Registreren</button>
            </form>
            <a href="login.php" class="login-link">Al een account? Log hier in</a>
        </div>
    </div>
</body>
</html>
