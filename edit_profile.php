<?php
session_start(); // Start the session
include('include/db_con.php');

// Function to check if user is logged in
function isUserLoggedIn() {
    return isset($_SESSION['user_id']); // Assuming 'user_id' is set when the user logs in
}

// Redirect to login if not logged in
if (!isUserLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Fetch user details from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT Name, user_name, email, address, Mobile, city, state, zip FROM users WHERE id = ?";
$stmt = $con->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $con->error); // Display error if preparation fails
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Update user details if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $user_name = $_POST['user_name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $mobile = $_POST['mobile'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip = $_POST['zip'];

    $update_sql = "UPDATE users SET Name = ?, user_name = ?, email = ?, address = ?, Mobile = ?, city = ?, state = ?, zip = ? WHERE id = ?";
    $update_stmt = $con->prepare($update_sql);

    if ($update_stmt === false) {
        die("Error preparing update statement: " . $con->error);
    }

    $update_stmt->bind_param("ssssssssi", $name, $user_name, $email, $address, $mobile, $city, $state, $zip, $user_id);
    $update_stmt->execute();
    $update_stmt->close();

    // Handle password update
    if (!empty($_POST['current_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Fetch the current password from the database
        $sql_password = "SELECT password FROM users WHERE id = ?";
        $stmt_password = $con->prepare($sql_password);
        $stmt_password->bind_param("i", $user_id);
        $stmt_password->execute();
        $result_password = $stmt_password->get_result();
        $user_data = $result_password->fetch_assoc();
        $stmt_password->close();

        // Verify the current password
        if (password_verify($current_password, $user_data['password'])) {
            // Check if new password and confirm password match
            if ($new_password === $confirm_password) {
                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update the password in the database
                $update_password_sql = "UPDATE users SET password = ? WHERE id = ?";
                $update_password_stmt = $con->prepare($update_password_sql);
                $update_password_stmt->bind_param("si", $hashed_password, $user_id);
                $update_password_stmt->execute();
                $update_password_stmt->close();
            } else {
                echo "New password and confirmation do not match.";
            }
        } else {
            echo "Current password is incorrect.";
        }
    }

    // Redirect to profile page after update
    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Profile</h2>
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['Name']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="user_name" class="form-label">Username</label>
                    <input type="text" class="form-control" id="user_name" name="user_name" value="<?php echo htmlspecialchars($user['user_name']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="mobile" class="form-label">Mobile</label>
                    <input type="text" class="form-control" id="mobile" name="mobile" value="<?php echo htmlspecialchars($user['Mobile']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="city" class="form-label">City</label>
                    <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($user['city']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="state" class="form-label">State</label>
                    <input type="text" class="form-control" id="state" name="state" value="<?php echo htmlspecialchars($user['state']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="zip" class="form-label">Zip Code</label>
                    <input type="text" class="form-control" id="zip" name="zip" value="<?php echo htmlspecialchars($user['zip']); ?>" required>
                </div>
            </div>

            <!-- Update Password Section -->
            <h3 class="mt-4">Update Password</h3>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Profile</button>
            <a href="profile.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>