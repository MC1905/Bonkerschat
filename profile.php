<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $targetDir = "uploads/";
    $imageFileType = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
    $newFileName = uniqid() . '.' . $imageFileType;
    $targetFile = $targetDir . $newFileName;

    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFile)) {
        $updateQuery = "UPDATE users SET profile_pic = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $targetFile, $user_id);
        $stmt->execute();
        header("Location: profile.php");
        exit();
    } else {
        $error = "Failed to upload the profile picture.";
    }
}

$query = "SELECT username, profile_pic FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
</head>
<body>
    <h2>Profile</h2>
    <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture" width="100">
    <p>Username: <?php echo htmlspecialchars($user['username']); ?></p>

    <form action="profile.php" method="POST" enctype="multipart/form-data">
        <label for="profile_pic">Change Profile Picture:</label>
        <input type="file" name="profile_pic" id="profile_pic" required>
        <button type="submit">Upload</button>
    </form>
    <a href="index.php">Back to Chat</a>
</body>
</html>
