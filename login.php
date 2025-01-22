<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: index.php");
            exit;
        } else {
            $error = "❌ Verkeerd wachtwoord!";
        }
    } else {
        $error = "❌ Gebruiker niet gevonden!";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BonkersChat</title>
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

        /* Login container */
        .login-container {
            width: 100%;
            max-width: 380px;
            background: #2F3136;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .login-box h1 {
            color: #ffffff;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .login-box p {
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

        /* Login knop */
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

        /* Registratie link */
        .register-link {
            display: block;
            margin-top: 15px;
            font-size: 14px;
            color: #B9BBBE;
            text-decoration: none;
            transition: color 0.3s;
        }

        .register-link:hover {
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>BonkersChat</h1>
            <p>Maak contact met vrienden en chat live.</p>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Gebruikersnaam" required>
                <input type="password" name="password" placeholder="Wachtwoord" required>
                <button type="submit">Inloggen</button>
            </form>
            <a href="register.php" class="register-link">Nieuw? Maak een account aan</a>
        </div>
    </div>
</body>
</html>
