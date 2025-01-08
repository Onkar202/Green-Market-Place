<?php
session_start();

// Check if admin is not logged in
if (!isset($_SESSION['admin_id'])) {
    // Store the requested URL in the session
    $_SESSION['requested_page'] = $_SERVER['REQUEST_URI'];
    
    // Redirect to the login page
    header("Location: login.php");
    exit();
}
?>
