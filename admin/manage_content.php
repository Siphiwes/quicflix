<?php
session_start();
require 'db.php'; // Database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch all entities and their associated videos
$query = $conn->query("
    SELECT e.id AS entity_id, e.name AS entity_name, e.thumbnail, e.preview, e.categoryId,
           v.id AS video_id, v.title, v.description, v.filePath, v.isMovie, v.releaseDate, v.duration, v.season, v.episode
    FROM entities e
    LEFT JOIN videos v ON e.id = v.entityId
    ORDER BY e.id, v.id
");
$content = $query->fetchAll(PDO::FETCH_ASSOC);

// Handle content deletion
if (isset($_GET['delete_entity'])) {
    $id = $_GET['delete_entity'];
    $stmt = $conn->prepare("DELETE FROM entities WHERE id = ?");
    $stmt->execute([$id]);
    $stmt = $conn->prepare("DELETE FROM videos WHERE entityId = ?");
    $stmt->execute([$id]);
    header("Location: manage_content.php");
    exit();
}

if (isset($_GET['delete_video'])) {
    $id = $_GET['delete_video'];
    $stmt = $conn->prepare("DELETE FROM videos WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: manage_content.php");
    exit();
}

// Handle content update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_entity'])) {
    $id = $_POST['entity_id'];
    $name = $_POST['name'];
    $categoryId = $_POST['categoryId'];
    
    $stmt = $conn->prepare("UPDATE entities SET name = ?, categoryId = ? WHERE id = ?");
    $stmt->execute([$name, $categoryId, $id]);
    
    header("Location: manage_content.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_video'])) {
    $id = $_POST['video_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $isMovie = isset($_POST['isMovie']) ? 1 : 0;
    $releaseDate = $_POST['releaseDate'];
    $duration = $_POST['duration'];
    $season = $_POST['season'] ?? null;
    $episode = $_POST['episode'] ?? null;
    
    $stmt = $conn->prepare("UPDATE videos SET title = ?, description = ?, isMovie = ?, releaseDate = ?, duration = ?, season = ?, episode = ? WHERE id = ?");
    $stmt->execute([$title, $description, $isMovie, $releaseDate, $duration, $season, $episode, $id]);
    
    header("Location: manage_content.php");
    exit();
}

// Fetch categories for dropdown
$categoryQuery = $conn->query("SELECT id, name FROM categories");
$categories = $categoryQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Manage Content</title>
</head>
<body>
    <div class="wrapper">
        <h1>Manage Content</h1>
        
        <?php foreach ($content as $item): ?>
            <?php if (!isset($currentEntity) || $currentEntity != $item['entity_id']): ?>
                <?php if (isset($currentEntity)): ?>
                    </div> <!-- Close previous entity div -->
                <?php endif; ?>
                <div class="entity">
                    <h2>Entity: <?= htmlspecialchars($item['entity_name']) ?></h2>
                    <form method="POST">
                        <input type="hidden" name="entity_id" value="<?= $item['entity_id'] ?>">
                        <input type="text" name="name" value="<?= htmlspecialchars($item['entity_name']) ?>" required>
                        <select name="categoryId">
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= $category['id'] == $item['categoryId'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="submit" name="update_entity" value="Update Entity">
                    </form>
                    <a href="manage_content.php?delete_entity=<?= $item['entity_id'] ?>" onclick="return confirm('Are you sure you want to delete this entity and all its videos?')">Delete Entity</a>
                <?php $currentEntity = $item['entity_id']; ?>
            <?php endif; ?>

            <?php if ($item['video_id']): ?>
                <div class="video">
                    <h3>Video: <?= htmlspecialchars($item['title']) ?></h3>
                    <form method="POST">
                        <input type="hidden" name="video_id" value="<?= $item['video_id'] ?>">
                        <input type="text" name="title" value="<?= htmlspecialchars($item['title']) ?>" required>
                        <textarea name="description"><?= htmlspecialchars($item['description']) ?></textarea>
                        <label>
                            <input type="checkbox" name="isMovie" <?= $item['isMovie'] ? 'checked' : '' ?>> Is Movie
                        </label>
                        <input type="text" name="releaseDate" value="<?= htmlspecialchars($item['releaseDate']) ?>" required>
                        <input type="number" name="duration" value="<?= htmlspecialchars($item['duration']) ?>" required>
                        <input type="number" name="season" value="<?= htmlspecialchars($item['season']) ?>">
                        <input type="number" name="episode" value="<?= htmlspecialchars($item['episode']) ?>">
                        <input type="submit" name="update_video" value="Update Video">
                    </form>
                    <a href="manage_content.php?delete_video=<?= $item['video_id'] ?>" onclick="return confirm('Are you sure you want to delete this video?')">Delete Video</a>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if (isset($currentEntity)): ?>
            </div> <!-- Close last entity div -->
        <?php endif; ?>

        <a href="index.php" class="adminButton">Back to Dashboard</a>
    </div>
</body>
</html>