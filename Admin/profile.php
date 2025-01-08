<?php
include('admin_session_check.php');
include('../include/db_con.php');

$admin_id = $_SESSION['admin_id'];
$success_message = '';
$error_message = '';

// Fetch admin details
$stmt = $con->prepare("SELECT username, email, full_name FROM admins WHERE admin_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
    $new_email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error_message = "New password and confirm password do not match.";
    } else {
        $update_stmt = $con->prepare("UPDATE admins SET full_name = ?, email = ? WHERE admin_id = ?");
        $update_stmt->bind_param("ssi", $new_full_name, $new_email, $admin_id);
        
        if ($update_stmt->execute()) {
            $success_message = "Profile updated successfully.";
            $admin['full_name'] = $new_full_name;
            $admin['email'] = $new_email;

            // Update password if provided
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $password_stmt = $con->prepare("UPDATE admins SET password = ? WHERE admin_id = ?");
                $password_stmt->bind_param("si", $hashed_password, $admin_id);
                $password_stmt->execute();
                $password_stmt->close();
                $success_message .= " Password updated.";
            }
        } else {
            $error_message = "Error updating profile: " . $con->error;
        }
        $update_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="icon" href="img/mainlogo.png" type="image/png">
    <link rel="stylesheet" href="css/style1.css" />
    <link rel="stylesheet" href="css/bootstrap1.min.css" />
    <link rel="stylesheet" href="css/metisMenu.css">
   <style>
        .profile-container {
            height: calc(100vh - 60px); /* Adjust 60px according to your header/footer height */
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .profile-form {
            width: 100%;
            max-width: 500px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .btn_1 {
            margin-top: 10px;
        }
    </style>
</head>
<body class="crm_body_bg">
    <?php include 'sidebar.php'; ?>
    <section class="main_content dashboard_part large_header_bg">
        <div class="main_content_iner">
            <div class="container-fluid p-0">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="profile-container">
                            <div class="profile-form">
                                <h3 class="text-center mb-4">Admin Profile</h3>
                                <?php if ($success_message): ?>
                                    <div class="alert alert-success" role="alert">
                                        <?php echo $success_message; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($error_message): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $error_message; ?>
                                    </div>
                                <?php endif; ?>
                                <form action="profile.php" method="POST">
                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($admin['username']); ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="full_name">Full Name</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($admin['full_name']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="new_password">New Password (leave blank to keep current)</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password">
                                    </div>
                                    <div class="form-group">
                                        <label for="confirm_password">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                    </div>
                                    <button type="submit" class="btn_1 full_width text-center">Update Profile</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer_part">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="footer_iner text-center">
                            <p>2024 © All Rights Reserved by Green Market Place</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="js/jquery1-3.4.1.min.js"></script>
    <script src="js/popper1.min.js"></script>
    <script src="js/bootstrap1.min.js"></script>
    <script src="js/metisMenu.js"></script>
    <script src="js/custom.js"></script>
</body>
</html>
