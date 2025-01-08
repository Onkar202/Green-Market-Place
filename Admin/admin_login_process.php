<?php
session_start();
include('../include/db_con.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare a select statement
    $sql = "SELECT admin_id, username, password FROM admins WHERE username = ?";
    
    if ($stmt = $con->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("s", $param_username);
        
        // Set parameters
        $param_username = $username;
        
        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Store result
            $stmt->store_result();
            
            // Check if username exists, if yes then verify password
            if ($stmt->num_rows == 1) {                    
                // Bind result variables
                $stmt->bind_result($admin_id, $username, $hashed_password);
                if ($stmt->fetch()) {
                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, so start a new session
                        session_start();
                        
                        // Store data in session variables
                        $_SESSION["admin_id"] = $admin_id;
                        $_SESSION["admin_username"] = $username;
                        
                        // Redirect user to index.php
                        header("location: index.php");
                        exit();
                    } else {
                        // Password is not valid
                        $_SESSION['error'] = "Invalid username or password.";
                        header("location: admin_login.php");
                        exit();
                    }
                }
            } else {
                // Username doesn't exist
                $_SESSION['error'] = "Invalid username or password.";
                header("location: admin_login.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Oops! Something went wrong. Please try again later.";
            header("location: admin_login.php");
            exit();
        }

        // Close statement
        $stmt->close();
    }
    
    // Close connection
    $con->close();
}
?>
