<?php
// manage_users.php
session_start();
require 'db.php'; // Database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch all users
$query = $conn->query("SELECT * FROM users");
$users = $query->fetchAll(PDO::FETCH_ASSOC);

// Handle user addition or update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['user_id'] ?? null;
    $fn = $_POST['firstName'];
    $ln = $_POST['lastName'];
    $un = $_POST['username'];
    $em = $_POST['email'];
    $role = $_POST['role'];
    $subscribed = isset($_POST['subscribed']) ? 1 : 0;

    if ($id) {
        // Update user
        $stmt = $conn->prepare("UPDATE users SET firstName=?, lastName=?, username=?, email=?, role=?, subscribed=? WHERE user_id=?");
        $stmt->execute([$fn, $ln, $un, $em, $role, $subscribed, $id]);
    } else {
        // Add new user
        $pw = hash("sha512", $_POST['password']);
        $stmt = $conn->prepare("INSERT INTO users (firstName, lastName, username, email, password, role, subscribed) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$fn, $ln, $un, $em, $pw, $role, $subscribed]);
    }
    header("Location: manage_users.php");
}

// Handle user deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
    $stmt->execute([$id]);
    header("Location: manage_users.php");
}

// Check if we are editing a user
$editUser = [];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id=?");
    $stmt->execute([$id]);
    $editUser = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Manage Users</title>
</head>
<body>
    <div class="wrapper">
        <h1>Manage Users</h1>
        <table>
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Subscribed</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['firstName'] ?></td>
                        <td><?= $user['lastName'] ?></td>
                        <td><?= $user['username'] ?></td>
                        <td><?= $user['email'] ?></td>
                        <td><?= $user['role'] ?></td>
                        <td><?= $user['subscribed'] ? 'Yes' : 'No' ?></td>
                        <td>
                            <a href="manage_users.php?edit=<?= $user['user_id'] ?>">Edit</a>
                            <a href="manage_users.php?delete=<?= $user['user_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Add / Edit User</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $_GET['edit'] ?? '' ?>">
            <label for="firstName">First Name:</label>
            <input type="text" name="firstName" placeholder="First Name" value="<?= $editUser['firstName'] ?? '' ?>" required>
            <label for="lastName">Last Name:</label>
            <input type="text" name="lastName" placeholder="Last Name" value="<?= $editUser['lastName'] ?? '' ?>" required>
            <label for="username">Username:</label>
            <input type="text" name="username" placeholder="Username" value="<?= $editUser['username'] ?? '' ?>" required>
            <label for="email">Email:</label>
            <input type="email" name="email" placeholder="Email" value="<?= $editUser['email'] ?? '' ?>" required>
            <label for="password">Password:</label>
            <input type="password" name="password" placeholder="Password" <?= isset($_GET['edit']) ? '' : 'required' ?>>
            <label for="role">Role:</label>
            <select name="role">
                <option value="user" <?= (isset($editUser['role']) && $editUser['role'] == 'user') ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= (isset($editUser['role']) && $editUser['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
            </select>
            <label>
                <input type="checkbox" name="subscribed" <?= (isset($editUser['subscribed']) && $editUser['subscribed']) ? 'checked' : '' ?>> Subscribed
            </label>
            <input type="submit" value="Save">
        </form>

        <!-- Back Button -->
        <a href="index.php" class="adminButton">Back</a>
    </div>
</body>
</html>
