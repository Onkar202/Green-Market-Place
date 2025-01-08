<?php
// Start the session
session_start();
include('../include/db_con.php');

// Log the logout action
if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
    
    // Check if the admin_logs table exists
    $table_check = $con->query("SHOW TABLES LIKE 'admin_logs'");
    if($table_check->num_rows == 0) {
        // Table doesn't exist, so we'll create it
        $create_table = "CREATE TABLE admin_logs (
            log_id INT(11) AUTO_INCREMENT PRIMARY KEY,
            admin_id INT(11) NOT NULL,
            action VARCHAR(255) NOT NULL,
            action_time DATETIME NOT NULL
        )";
        $con->query($create_table);
    }

    $stmt = $con->prepare("INSERT INTO admin_logs (admin_id, action, action_time) VALUES (?, 'logout', NOW())");
    if ($stmt === false) {
        die("Prepare failed: " . $con->error);
    }
    
    $stmt->bind_param("i", $admin_id);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    $stmt->close();
}

// Unset all of the session variables
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: login.php");
exit();
?>
