<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputOtp = $_POST['otp'];

    // Retrieve the OTP and user data stored in session
    if (isset($_SESSION['user_data'])) {
        $storedOtp = $_SESSION['user_data']['otp'];

        if ($inputOtp == $storedOtp) {
            // If OTP matches, check for duplicate users in the database
            include('include/db_con.php');

            $name = $_SESSION['user_data']['name'];
            $user_name = $_SESSION['user_data']['user_name'];
            $email = $_SESSION['user_data']['email'];
            $password = $_SESSION['user_data']['password'];
            $address = $_SESSION['user_data']['address'];
            $mobile = $_SESSION['user_data']['mobile'];
            $city = $_SESSION['user_data']['city'];
            $state = $_SESSION['user_data']['state'];
            $zip = $_SESSION['user_data']['zip'];

            // Check if user already exists with the same username, email, or mobile number
            $duplicateCheckStmt = $con->prepare("SELECT * FROM users WHERE user_name = ? OR email = ? OR mobile = ?");
            $duplicateCheckStmt->bind_param('sss', $user_name, $email, $mobile);
            $duplicateCheckStmt->execute();
            $duplicateResult = $duplicateCheckStmt->get_result();

            if ($duplicateResult->num_rows > 0) {
                echo "<p class='error'>User with the same username, email, or mobile number already exists.</p>";
            } else {
                // No duplicates found, insert the user data into the database
                $stmt = $con->prepare("INSERT INTO users (name, user_name, email, password, address, mobile, city, state, zip, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->bind_param('sssssssss', $name, $user_name, $email, $password, $address, $mobile, $city, $state, $zip);

                if ($stmt->execute()) {
                    echo "<p class='success'>Registration successful! Redirecting to login...</p>";
                    // Clear session after successful registration
                    session_unset();
                    session_destroy();
                    // Redirect to login page after 3 seconds
                    header("refresh:3;url=login.php");
                } else {
                    echo "<p class='error'>Error: " . $con->error . "</p>";
                }

                $stmt->close();
            }

            $duplicateCheckStmt->close();
            $con->close();
        } else {
            echo "<p class='error'>Invalid OTP. Please try again.</p>";
        }
    } else {
        echo "<p class='error'>No OTP found. Please register first.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .otp-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        h2 {
            color: #14c46c;
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            color: #333;
            font-size: 14px;
        }

        input[type="text"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        button {
            padding: 10px;
            background-color: #14c46c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #12a85c;
        }

        .error {
            color: red;
            text-align: center;
        }

        .success {
            color: green;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="otp-container">
    <h2>Verify Your Email</h2>
    <form method="POST" action="">
        <label for="otp">Enter OTP:</label>
        <input type="text" name="otp" required>
        <button type="submit">Verify</button>
    </form>
</div>

</body>
</html>
