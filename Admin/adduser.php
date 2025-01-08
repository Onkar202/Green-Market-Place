<?php
// Database connection (adjust your credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gmp"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
$message = "";  // Variable to store success or error message
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare SQL query using prepared statements for security
    $stmt = $conn->prepare("INSERT INTO admins (username, password, email, full_name, created_at, is_active) 
            VALUES (?, ?, ?, ?, NOW(), 1)");
    $stmt->bind_param("ssss", $username, $hashed_password, $email, $full_name);

    // Sanitize and assign inputs
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Execute the query
    if ($stmt->execute()) {
        $message = "User added successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Add Admin</title>
    <link rel="icon" href="img/mainlogo.png" type="image/png" />
    <link rel="stylesheet" href="css/style1.css" />
    <link rel="stylesheet" href="css/bootstrap1.min.css" />
    <link rel="stylesheet" href="css/metisMenu.css" />
</head>

<body class="crm_body_bg">

    <?php include 'sidebar.php'; ?>

    <section class="main_content dashboard_part large_header_bg">
        <div class="main_content_iner">
            <form action="" method="POST">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="urId">User Name</label>
                        <input type="text" class="form-control" id="urId" name="username" placeholder="User Name" required />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="urName">Full Name</label>
                        <input type="text" class="form-control" id="urName" name="full_name" placeholder="Full Name" required />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required />
                    </div>
                </div>
                <button type="submit" class="btn btn-success">Add Admin</button>
            </form>
        </div>

        <div class="footer_part">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="footer_iner text-center">
                            <p class="fs-5">2024 © All Rights are Reserved by Green Market Place</p>
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
    <script src="js/bootstrap.min.js"></script>
    <script src="js/metisMenu.js"></script>
    <script src="js/custom.js"></script>

    <!-- JavaScript for displaying alerts -->
    <script>
        <?php if ($message): ?>
            alert("<?php echo $message; ?>");
        <?php endif; ?>
    </script>

</body>

</html>
