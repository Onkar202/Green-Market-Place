<?php
session_start();
include('../include/db_con.php');

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    // Prepare SQL statement
    $stmt = $con->prepare("SELECT admin_id, username, password, is_active FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            if ($admin['is_active'] == 1) {
                // Login successful
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_username'] = $admin['username'];

                // Update last login time
                $update_stmt = $con->prepare("UPDATE admins SET last_login = NOW() WHERE admin_id = ?");
                $update_stmt->bind_param("i", $admin['admin_id']);
                $update_stmt->execute();
                $update_stmt->close();

                // Redirect to the requested page or index.php if not set
                $redirect_to = isset($_SESSION['requested_page']) ? $_SESSION['requested_page'] : 'index.php';
                unset($_SESSION['requested_page']); // Clear the stored URL
                header("Location: " . $redirect_to);
                exit();
            } else {
                $error = "Your account is not active. Please contact the system administrator.";
            }
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }

    $stmt->close();
}

// If admin is already logged in, redirect to index.php
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        /* Additional styling for the form */
        body {
            background-color: #f7f7f7;
        }

        .crm_body_bg {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .white_box_50px {
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 30px rgba(0, 0, 0, 0.1);
        }

        .modal-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
        }

        .form-control {
            height: 45px;
            border: 1px solid #e6e6e6;
            border-radius: 5px;
            padding: 10px 15px;
            font-size: 16px;
        }

        .btn_1 {
            background-color: #28a745;
            color: white;
            border: none;
            height: 45px;
            width: 100%;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn_1:hover {
            background-color: #218838;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            margin-bottom: 20px;
            border-radius: 5px;
            padding: 15px;
        }

        .footer_iner {
            padding: 20px 0;
        }

        .footer_iner p {
            color: #999;
        }
    </style>
</head>
<body class="crm_body_bg">
    <section class="main_content dashboard_part large_header_bg">
        <div class="main_content_iner">
            <div class="container-fluid p-0">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6">
                        <div class="white_box_50px mb_30">
                            <div class="modal-content cs_modal">
                                <div class="modal-header justify-content-center">
                                    <h5 class="modal-title">Admin Login</h5>
                                </div>
                                <div class="modal-body">
                                    <?php if ($error): ?>
                                        <div class="alert alert-danger" role="alert">
                                            <?php echo $error; ?>
                                        </div>
                                    <?php endif; ?>
                                    <form action="" method="POST">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="username" placeholder="Username" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control" name="password" placeholder="Password" required>
                                        </div>
                                        <button type="submit" class="btn_1 full_width text-center">Login</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

       
    </section>

   
</body>
</html>
