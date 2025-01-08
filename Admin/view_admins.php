<?php
include('admin_session_check.php');
// Check if a session is already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../include/db_con.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// SQL query to get all admin users
$sql = "SELECT admin_id, username, email, full_name, created_at, last_login, is_active FROM admins";
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Admin Users</title>
    <link rel="icon" href="img/mainlogo.png" type="image/png" />
    <link rel="stylesheet" href="css/style1.css" />
    <link rel="stylesheet" href="css/bootstrap1.min.css" />
    <link rel="stylesheet" href="css/metisMenu.css" />
</head>

<body class="crm_body_bg">
    <?php include 'sidebar.php'; ?>
    <section class="main_content dashboard_part large_header_bg">
        <div class="container-fluid">
            <h2 class="text-center">Admin Users List</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Full Name</th>
                        <th>Created At</th>
                        <th>Last Login</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['admin_id']}</td>
                                    <td>{$row['username']}</td>
                                    <td>{$row['email']}</td>
                                    <td>{$row['full_name']}</td>
                                    <td>{$row['created_at']}</td>
                                    <td>" . ($row['last_login'] ? $row['last_login'] : 'Never') . "</td>
                                    <td>" . ($row['is_active'] ? 'Active' : 'Inactive') . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No admin users found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="footer_part">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="footer_iner text-center text-align-center">
                            <p class="fs-5">2024 © All Rights Reserved by Green Market Place</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="back-top" style="display: none">
        <a title="Go to Top" href="#">
            <i class="ti-angle-up"></i>
        </a>
    </div>

    <script src="js/jquery1-3.4.1.min.js"></script>
    <script src="js/popper1.min.js"></script>
    <script src="js/bootstrap1.min.js"></script>
    <script src="js/metisMenu.js"></script>
    <script src="js/custom.js"></script>
</body>

</html>

<?php
// Close the database connection
$con->close();
?>
