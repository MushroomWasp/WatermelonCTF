<?php
session_start(['cookie_httponly' => true]);
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$upload_dir = 'uploads/';
if (!is_dir($upload_dir)) {
    if (!mkdir($upload_dir, 0777, true)) {
        $mkdir_error = "Failed to create upload directory";
    }
}

// Make sure uploads directory is writable
if (is_dir($upload_dir) && !is_writable($upload_dir)) {
    chmod($upload_dir, 0777);
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    // Check for errors during upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive",
            UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive",
            UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded",
            UPLOAD_ERR_NO_FILE => "No file was uploaded",
            UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder",
            UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
            UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload"
        ];
        
        $error_message = isset($error_messages[$file['error']]) 
            ? $error_messages[$file['error']] 
            : "Unknown upload error";
            
        $message = "<div class='error'>Upload failed: $error_message</div>";
    } else {
        $filename = basename($file['name']);
        $target = $upload_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $target)) {
            $message = "<div class='success'>File uploaded: $filename</div>";
            // Simulate temporary storage by scheduling deletion
            shell_exec("(sleep 10 && rm $target) > /dev/null 2>&1 &");
        } else {
            $message = "<div class='error'>Upload failed: Could not move file</div>";
            // Add more detailed error info
            if (!is_writable($upload_dir)) {
                $message .= " - Upload directory is not writable";
            }
        }
    }
}

// Debug info for admins only
$debug_info = "";
if (isset($mkdir_error)) {
    $debug_info .= "<p>Error: $mkdir_error</p>";
}
$debug_info .= "<p>Upload directory: " . realpath($upload_dir) . "</p>";
$debug_info .= "<p>Is writable: " . (is_writable($upload_dir) ? "Yes" : "No") . "</p>";
$debug_info .= "<p>PHP upload_max_filesize: " . ini_get('upload_max_filesize') . "</p>";
$debug_info .= "<p>PHP post_max_size: " . ini_get('post_max_size') . "</p>";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
        .upload-form {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ccc;
        }
        .debug-info {
            margin-top: 30px;
            padding: 10px;
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <h2>Admin Panel</h2>
    <p>Welcome, admin! This is your private section.</p>
    
    <div class="upload-form">
        <h3>Upload File</h3>
        <?php echo $message; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="file">
            <input type="submit" value="Upload">
        </form>
        <p><small>Note: Files will be automatically deleted after 10 seconds.</small></p>
    </div>
    
    <div class="debug-info">
        <h4>Debug Information</h4>
        <?php echo $debug_info; ?>
    </div>
    
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html> 