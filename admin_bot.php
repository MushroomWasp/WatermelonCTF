<?php
// This script simulates an admin user browsing the site
// It should be run periodically (e.g., via cron)

// Create cookie file location if it doesn't exist
$cookie_file = '/tmp/admin_cookie.txt';
if (!file_exists(dirname($cookie_file))) {
    mkdir(dirname($cookie_file), 0777, true);
}
if (file_exists($cookie_file)) {
    // Clear old cookies after 30 minutes to prevent issues
    if (time() - filemtime($cookie_file) > 1800) {
        unlink($cookie_file);
    }
}

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_HEADER, true);

// Extra headers to better simulate a browser
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
    'Accept-Language: en-US,en;q=0.9',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1'
]);

// Admin credentials
$admin_username = 'admin';
$admin_password = 'adminpass';

// Function to log activity
function log_activity($message) {
    echo date('[Y-m-d H:i:s]') . " $message\n";
}

// Step 1: Login as admin
log_activity("Logging in as admin");
curl_setopt($ch, CURLOPT_URL, 'http://localhost/index.php');
$response = curl_exec($ch);

// Extract CSRF token if exists (not in current implementation but prepared for future)
$csrf_token = '';
if (preg_match('/<input[^>]*name=["\']csrf_token["\'][^>]*value=["\'](.*?)["\']/i', $response, $matches)) {
    $csrf_token = $matches[1];
}

// Now post the login form
curl_setopt($ch, CURLOPT_POST, true);
$post_data = [
    'username' => $admin_username,
    'password' => $admin_password
];
if ($csrf_token) {
    $post_data['csrf_token'] = $csrf_token;
}
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
$response = curl_exec($ch);

// Step 2: Visit the dashboard (which contains comments with potential XSS)
log_activity("Visiting dashboard to view comments");
curl_setopt($ch, CURLOPT_URL, 'http://localhost/dashboard.php');
curl_setopt($ch, CURLOPT_POST, false);
$response = curl_exec($ch);

if (strpos($response, 'Welcome, admin!') === false) {
    log_activity("WARNING: Not logged in as admin on dashboard!");
}

// Step 3: Visit the admin page
log_activity("Visiting admin page");
curl_setopt($ch, CURLOPT_URL, 'http://localhost/admin.php');
$response = curl_exec($ch);

if (strpos($response, 'Admin Panel') === false) {
    log_activity("WARNING: Could not access admin panel!");
}

// Close cURL session
curl_close($ch);

log_activity("Admin bot completed its rounds");
?> 