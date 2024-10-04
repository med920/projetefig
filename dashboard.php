<?php
session_start();
// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "username", "password", "database_name");
$user_id = $_SESSION['user_id'];

// Fetch user-specific data
// Last searched book
$search_stmt = $conn->prepare("SELECT search_query FROM search_history WHERE user_id = ? ORDER BY search_time DESC LIMIT 1");
$search_stmt->bind_param("i", $user_id);
$search_stmt->execute();
$search_stmt->bind_result($last_search);
$search_stmt->fetch();
$search_stmt->close();

// Files uploaded
$upload_stmt = $conn->prepare("SELECT file_name FROM files WHERE user_id = ?");
$upload_stmt->bind_param("i", $user_id);
$upload_stmt->execute();
$upload_result = $upload_stmt->get_result();

// Files downloaded
$download_stmt = $conn->prepare("SELECT files.file_name FROM downloads JOIN files ON downloads.file_id = files.id WHERE downloads.user_id = ?");
$download_stmt->bind_param("i", $user_id);
$download_stmt->execute();
$download_result = $download_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>

        <h3>Last Searched Book:</h3>
        <p><?php echo $last_search ? $last_search : "No searches yet."; ?></p>

        <h3>Your Uploaded Files:</h3>
        <ul>
            <?php while ($row = $upload_result->fetch_assoc()) { ?>
                <li><?php echo $row['file_name']; ?></li>
            <?php } ?>
        </ul>

        <h3>Your Downloaded Files:</h3>
        <ul>
            <?php while ($row = $download_result->fetch_assoc()) { ?>
                <li><?php echo $row['file_name']; ?></li>
            <?php } ?>
        </ul>
    </div>
</body>
</html>

<?php
$upload_stmt->close();
$download_stmt->close();
$conn->close();
?>
