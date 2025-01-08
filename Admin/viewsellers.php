<?php
// Database connection details
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "gmp"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add this near the top, after database connection

// SQL query to get all sellers
$sql = "SELECT id, seller_id, seller_name, address, address2, city, state, zip FROM sellers";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Sellers</title>
    <link rel="icon" href="img/mainlogo.png" type="image/png" />
    <link rel="stylesheet" href="css/style1.css" />
    <link rel="stylesheet" href="css/bootstrap1.min.css" />
    <link rel="stylesheet" href="css/metisMenu.css" />
</head>

<body class="crm_body_bg">
    <?php include('sidebar.php'); ?>
    <section class="main_content dashboard_part large_header_bg">
        <div class="container-fluid">
            <h2 class="text-center">Sellers List</h2>
            <div class="text-right mb-3">
                <a href="download_sellers.php" class="btn btn-primary">Download PDF</a>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Seller ID</th>
                        <th>Seller Name</th>
                       
                        <th>City</th>
                        <th>State</th>
                        <th>ZIP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['seller_id']}</td>
                                    <td>{$row['seller_name']}</td>
                                   
                                    <td>{$row['city']}</td>
                                    <td>{$row['state']}</td>
                                    <td>{$row['zip']}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No sellers found</td></tr>";
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
    <script src="js/bootstrap1.min.js"></script> <!-- Fixed the path -->
    <script src="js/metisMenu.js"></script>
    <script src="js/custom.js"></script>
</body>

</html>


<?php
// Close the database connection
$conn->close();
?>
