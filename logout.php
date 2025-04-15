<?php
session_start(['cookie_httponly' => true]);
session_destroy();
header("Location: index.php");
exit;
?> 