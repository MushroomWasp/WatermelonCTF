<?php
session_start(['cookie_httponly' => true]);
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$comments_file = 'comments.txt';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comment = $_POST['comment'];
    // Store username with comment
    $comment_with_user = "<b>" . $_SESSION['user'] . "</b>: " . $comment;
    file_put_contents($comments_file, $comment_with_user . "\n", FILE_APPEND);
}
$comments = file_exists($comments_file) ? file_get_contents($comments_file) : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .comment-form {
            margin: 20px 0;
        }
        .comment-input {
            width: 80%;
            padding: 8px;
        }
        .comments {
            border: 1px solid #ccc;
            padding: 10px;
            margin-top: 10px;
            min-height: 100px;
        }
    </style>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?>!</h2>
    
    <div class="comment-form">
        <h3>Leave a comment:</h3>
        <form method="POST">
            <input type="text" name="comment" class="comment-input" placeholder="Your comment here...">
            <input type="submit" value="Post">
        </form>
    </div>
    
    <h3>Comments:</h3>
    <div class="comments">
        <?php echo $comments; // XSS vulnerability: no sanitization ?>
    </div>
    
    <?php if ($_SESSION['user'] === 'admin') { ?>
        <p><a href="admin.php">Admin Section</a></p>
    <?php } ?>
    <p><a href="logout.php">Logout</a></p>
</body>
</html> 