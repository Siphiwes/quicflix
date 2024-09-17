<?php
session_start();
require 'db.php'; // Database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch statistics for videos and subscribers
$videoQuery = $conn->query("
    SELECT 
        categories.name AS categoryName,
        COUNT(entities.id) AS total_videos,
        SUM(videos.views) AS total_views
    FROM entities
    JOIN categories ON entities.categoryId = categories.id
    LEFT JOIN videos ON entities.id = videos.entityId
    GROUP BY categories.name
    ORDER BY total_views DESC
");
$videos = $videoQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch total watch time and views from the videos table
$watchTimeQuery = $conn->query("
    SELECT 
        SUM(duration) AS total_watch_time,
        SUM(views) AS total_views 
    FROM videos
");
$watchTimeStats = $watchTimeQuery->fetch(PDO::FETCH_ASSOC);

// Fetch total number of subscribers from the users table
$subscribersQuery = $conn->query("SELECT COUNT(*) AS total_subscribers FROM users WHERE subscribed = 1");
$subscribers = $subscribersQuery->fetch(PDO::FETCH_ASSOC);

// Fetch categories for the dropdown
$categoryQuery = $conn->query("SELECT id, name FROM categories");
$categories = $categoryQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch the most and least viewed categories
$mostViewedCategoryQuery = $conn->query("
    SELECT categories.name AS categoryName, SUM(videos.views) AS total_views
    FROM entities
    JOIN categories ON entities.categoryId = categories.id
    LEFT JOIN videos ON entities.id = videos.entityId
    GROUP BY categories.name
    ORDER BY total_views DESC
    LIMIT 1
");
$mostViewedCategory = $mostViewedCategoryQuery->fetch(PDO::FETCH_ASSOC);

$leastViewedCategoryQuery = $conn->query("
    SELECT categories.name AS categoryName, SUM(videos.views) AS total_views
    FROM entities
    JOIN categories ON entities.categoryId = categories.id
    LEFT JOIN videos ON entities.id = videos.entityId
    GROUP BY categories.name
    ORDER BY total_views ASC
    LIMIT 1
");
$leastViewedCategory = $leastViewedCategoryQuery->fetch(PDO::FETCH_ASSOC);

// Handle video upload
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Increase post_max_size and upload_max_filesize limits
    ini_set('post_max_size', '8064M');
    ini_set('upload_max_filesize', '8064M');
    
    $name = $_POST['name'];
    $description = $_POST['description'];
    $categoryId = $_POST['category']; // This will be the ID from the dropdown
    $uploaded_by = $_SESSION['admin']; // Assuming admin ID is stored in the session
    $releaseDate = $_POST['releaseDate'];
    $duration = $_POST['duration'];

    // Define upload directories
    $base_dir = 'C:/xampp/htdocs/quicflix/entities';
    $video_dir = $base_dir . '/videos/';
    $thumbnail_dir = $base_dir . '/thumbnails/';
    $preview_dir = $base_dir . '/previews/';

    // Create file paths for uploading
    $video_path = $video_dir . basename($_FILES["video"]["name"]);
    $thumbnail_path = $thumbnail_dir . basename($_FILES["thumbnail"]["name"]);
    $preview_path = $preview_dir . basename($_FILES["preview"]["name"]);

    // Define relative paths for the database
    $relative_video_path = '/quicflix/entities/videos/' . basename($_FILES["video"]["name"]);
    $relative_thumbnail_path = '/quicflix/entities/thumbnails/' . basename($_FILES["thumbnail"]["name"]);
    $relative_preview_path = '/quicflix/entities/previews/' . basename($_FILES["preview"]["name"]);

    // Upload files
    if (move_uploaded_file($_FILES["video"]["tmp_name"], $video_path) &&
        move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $thumbnail_path) &&
        move_uploaded_file($_FILES["preview"]["tmp_name"], $preview_path)) {
        
        // Insert into entities table
        $stmt = $conn->prepare("INSERT INTO entities (name, thumbnail, preview, categoryId) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $relative_thumbnail_path, $relative_preview_path, $categoryId]);
        
        // Get the last inserted entity ID
        $entityId = $conn->lastInsertId();
        
        // Insert into videos table
        $isMovie = isset($_POST['isMovie']) ? 1 : 0;
        
        if ($isMovie) {
            // Movie: No season or episode
            $stmt = $conn->prepare("INSERT INTO videos (title, description, filePath, uploaded_by, isMovie, uploadDate, releaseDate, views, duration, season, episode, entityId) VALUES (?, ?, ?, ?, ?, NOW(), ?, 0, ?, 0, 0, ?)");
            $stmt->execute([$name, $description, $relative_video_path, $uploaded_by, $isMovie, $releaseDate, $duration, $entityId]);
        } else {
            // TV Show: Include season and episode
            $season = $_POST['season'];
            $episode = $_POST['episode'];
            $stmt = $conn->prepare("INSERT INTO videos (title, description, filePath, uploaded_by, isMovie, uploadDate, releaseDate, views, duration, season, episode, entityId) VALUES (?, ?, ?, ?, ?, NOW(), ?, 0, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $relative_video_path, $uploaded_by, $isMovie, $releaseDate, $duration, $season, $episode, $entityId]);
        }
        
        echo "Video, thumbnail, and preview uploaded successfully!";
    } else {
        echo "Error uploading files!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Admin Dashboard</title>
    <script>
        function toggleFields() {
            const isMovie = document.getElementById('isMovie').checked;
            const seasonField = document.getElementById('seasonField');
            const episodeField = document.getElementById('episodeField');
            
            if (isMovie) {
                seasonField.style.display = 'none';
                episodeField.style.display = 'none';
            } else {
                seasonField.style.display = 'block';
                episodeField.style.display = 'block';
            }
        }
    </script>
</head>
<body>
    
    <div class="wrapper">
        <h1>Welcome Quicflix Admin</h1>
        <div class="statsContainer">
            <?php foreach ($videos as $video): ?>
                <div class="statBox">
                    <h3>Category: <?= htmlspecialchars($video['categoryName']) ?></h3>
                    <p>Total Videos: <?= htmlspecialchars($video['total_videos']) ?></p>
                    <p>Total Views: <?= htmlspecialchars($video['total_views']) ?></p>
                </div>
            <?php endforeach; ?>

            <div class="statBox">
                <h3>Total Watch Time</h3>
                <p>Total Watch Time: <?= htmlspecialchars($watchTimeStats['total_watch_time']) ?> minutes</p>
                <p>Total Views: <?= htmlspecialchars($watchTimeStats['total_views']) ?></p>
            </div>

            <div class="statBox">
                <h3>Total Subscribers</h3>
                <p>Total Subscribers: <?= htmlspecialchars($subscribers['total_subscribers']) ?></p>
            </div>

            <div class="statBox">
                <h3>Most Viewed Category</h3>
                <p>Category: <?= htmlspecialchars($mostViewedCategory['categoryName']) ?></p>
                <p>Total Views: <?= htmlspecialchars($mostViewedCategory['total_views']) ?></p>
            </div>

            <div class="statBox">
                <h3>Least Viewed Category</h3>
                <p>Category: <?= htmlspecialchars($leastViewedCategory['categoryName']) ?></p>
                <p>Total Views: <?= htmlspecialchars($leastViewedCategory['total_views']) ?></p>
            </div>
        </div>

        <h2>Upload Video</h2>
        
        <label><input type="checkbox" id="isMovie" name="isMovie" onclick="toggleFields()"> Is this a Movie?</label>

        <form method="POST" enctype="multipart/form-data">
            <label for="name">Name (Movie/TV Show)</label>
            <input type="text" name="name" id="name" placeholder="Name" required>

            <label for="description">Description</label>
            <textarea name="description" id="description" placeholder="Description" required></textarea>

            <label for="category">Category</label>
            <select name="category" id="category" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="video">Upload Video</label>
            <input type="file" name="video" id="video" accept="video/*" required>

            <label for="thumbnail">Upload Thumbnail</label>
            <input type="file" name="thumbnail" id="thumbnail" accept="image/*" required>

            <label for="preview">Upload Preview</label>
            <input type="file" name="preview" id="preview" accept="video/*" required>

            <label for="releaseDate">Release Date (YYYY-MM-DD)</label>
            <input type="text" name="releaseDate" id="releaseDate" placeholder="Release Date" required>

            <label for="duration">Duration (in minutes)</label>
            <input type="text" name="duration" id="duration" placeholder="Duration" required>

            <div id="seasonField" style="display:none;">
                <label for="season">Season</label>
                <input type="text" name="season" id="season" placeholder="Season">
            </div>

            <div id="episodeField" style="display:none;">
                <label for="episode">Episode</label>
                <input type="text" name="episode" id="episode" placeholder="Episode">
            </div>

            <input type="submit" value="Upload">
        </form>

        <h2>Manage Users</h2>
        <a href="manage_users.php" class="adminButton">Manage Users</a>
        <a href="manage_content.php" class="adminButton">Manage Content</a>
        <a href="logout.php" class="adminButton">Logout</a>
    </div>
</body>
</html>

