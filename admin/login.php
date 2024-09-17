<?php
// login.php
session_start();
require 'db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = hash("sha512", $_POST['password']);

    $query = $conn->prepare("SELECT * FROM users WHERE username=:username AND password=:password AND role='admin'");
    $query->bindValue(":username", $username);
    $query->bindValue(":password", $password);
    $query->execute();

    if ($query->rowCount() == 1) {
        $_SESSION['admin'] = $username;
        header("Location: index.php");
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Admin Login</title>
</head>
<body>
<div class="signInContainer">
    <div class="column">
        <div class="header">
            <h3>Admin Login</h3>
        </div>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>
        <?php if (isset($error)) echo "<div class='errorMessage'>$error</div>"; ?>
    </div>
</div>
</body>
</html>
