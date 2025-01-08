<?php
session_start();
include('include/db_con.php');
require 'vendor/autoload.php'; // Ensure PHPMailer is properly included

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$host = 'localhost';
$db = 'gmp';
$user = 'root';
$pass = ''; // Empty password

// Establish connection to the database
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch states from the database
$stateQuery = "SELECT * FROM states";
$statesResult = $conn->query($stateQuery);

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $user_name = $_POST['user_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $address = $_POST['address'];
    $mobile = $_POST['mobile'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip = $_POST['zip'];

    // Check if user already exists
    $checkUserQuery = "SELECT * FROM users WHERE user_name = ? OR email = ? OR mobile = ?";
    $stmt = $conn->prepare($checkUserQuery);
    $stmt->bind_param("sss", $user_name, $email, $mobile);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $existingUser = $result->fetch_assoc();
        if ($existingUser['user_name'] == $user_name) {
            $error = 'Username already exists. Please choose a different username.';
        } elseif ($existingUser['email'] == $email) {
            $error = 'Email already registered. Please use a different email address.';
        } elseif ($existingUser['mobile'] == $mobile) {
            $error = 'Mobile number already registered. Please use a different mobile number.';
        }
    } else {
        // Password validation
        $passwordPattern = "/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/";
        
        if (!preg_match($passwordPattern, $password)) {
            $error = 'Password must be at least 6 characters long, contain one uppercase letter, one alphanumeric character, and one special character.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match.';
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Generate OTP
            $otp = rand(100000, 999999);

            // Save the data temporarily using sessions
            $_SESSION['user_data'] = [
                'name' => $name,
                'user_name' => $user_name,
                'email' => $email,
                'password' => $hashedPassword,
                'address' => $address,
                'mobile' => $mobile,
                'city' => $city,
                'state' => $state,
                'zip' => $zip,
                'otp' => $otp
            ];

            // Send OTP via email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; 
                $mail->SMTPAuth = true;
                $mail->Username = 'ronyk010201@gmail.com'; // Replace with your Gmail
                $mail->Password = 'lavy xcyv fisw gopr'; // Replace with your app-specific password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                // Recipient email and content
                $mail->setFrom('ronyk010201@gmail.com', 'green market place');
                $mail->addAddress($email); // Send OTP to the user's email

                $mail->isHTML(true);
                $mail->Subject = 'Email Verification OTP';
                $mail->Body    = "Dear $name, <br><br>Your OTP for email verification is: <strong>$otp</strong>";

                $mail->send();

                // Redirect to OTP verification page
                header('Location: verify.php');
                exit();
            } catch (Exception $e) {
                $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }
    }
    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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

        .register-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 700px;
            width: 100%;
        }

        .register-container h2 {
            color: #14c46c;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            justify-content: space-between;
        }

        .form-group {
            flex: 0 0 48%;
            margin-bottom: 15px;
        }

        .form-group.full-width {
            flex: 0 0 100%;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-size: 14px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .register-button {
            width: 100%;
            padding: 10px;
            background-color: #14c46c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .register-button:hover {
            background-color: #12a85c;
        }

        .error, .success {
            text-align: center;
            margin-bottom: 15px;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
            }

            .form-group {
                flex: 0 0 100%;
            }
        }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Register</h2>

    <?php if (!empty($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-row">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" value="<?php echo isset($name) ? $name : ''; ?>" required>
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="user_name" value="<?php echo isset($user_name) ? $user_name : ''; ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>" required>
            </div>

            <div class="form-group">
                <label>Mobile</label>
                <input type="text" name="mobile" value="<?php echo isset($mobile) ? $mobile : ''; ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>
        </div>

        <div class="form-group full-width">
            <label>Address</label>
            <input type="text" name="address" value="<?php echo isset($address) ? $address : ''; ?>" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>City</label>
                <input type="text" name="city" value="<?php echo isset($city) ? $city : ''; ?>" required>
            </div>

            <div class="form-group">
                <label>State</label>
                <select name="state" required>
                    <option value="">Select State</option>
                    <?php
                    if ($statesResult->num_rows > 0) {
                        while($row = $statesResult->fetch_assoc()) {
                            echo "<option value='" . $row['state_id'] . "'>" . $row['state_name'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Zip Code</label>
                <input type="text" name="zip" value="<?php echo isset($zip) ? $zip : ''; ?>" required>
            </div>
        </div>

        <button type="submit" class="register-button">Register</button>
    </form>
</div>

</body>
</html>
