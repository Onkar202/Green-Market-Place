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

// Add this near the top of the file
require_once('../vendor/autoload.php');

// Define the SQL query first
$sql = "SELECT id, Name, user_name, Email, address, Mobile, city, state, zip, created_at FROM users";
$result = $conn->query($sql);

// Then add the PDF generation function
function generatePDF($result) {
    ob_start(); // Start output buffering
    
    // Create new PDF document
    $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator('Green Market Place');
    $pdf->SetAuthor('Admin');
    $pdf->SetTitle('Users List');

    // Remove header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Add a page
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('helvetica', '', 12);

    // Title
    $pdf->Cell(0, 10, 'Users List', 0, 1, 'C');
    $pdf->Ln(5);

    // Table header
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(25, 8, 'ID', 1, 0, 'C');
    $pdf->Cell(40, 8, 'Name', 1, 0, 'C');
    $pdf->Cell(35, 8, 'Username', 1, 0, 'C');
    $pdf->Cell(60, 8, 'Email', 1, 0, 'C');
    $pdf->Cell(50, 8, 'Address', 1, 0, 'C');
    $pdf->Cell(30, 8, 'Mobile', 1, 0, 'C');
    $pdf->Cell(25, 8, 'City', 1, 0, 'C');
    $pdf->Cell(25, 8, 'State', 1, 0, 'C');
    $pdf->Cell(20, 8, 'ZIP', 1, 1, 'C');

    // Table content
    $pdf->SetFont('helvetica', '', 8);

    while ($row = $result->fetch_assoc()) {
        // Ensure data doesn't exceed cell length
        $id = substr($row['id'], 0, 10);
        $name = substr($row['Name'], 0, 25);
        $username = substr($row['user_name'], 0, 20);
        $email = substr($row['Email'], 0, 35);
        $address = substr($row['address'], 0, 30);
        $mobile = substr($row['Mobile'], 0, 15);
        $city = substr($row['city'], 0, 15);
        $state = substr($row['state'], 0, 15);
        $zip = substr($row['zip'], 0, 10);

        $pdf->Cell(25, 8, $id, 1, 0, 'C');
        $pdf->Cell(40, 8, $name, 1, 0, 'L');
        $pdf->Cell(35, 8, $username, 1, 0, 'L');
        $pdf->Cell(60, 8, $email, 1, 0, 'L');
        $pdf->Cell(50, 8, $address, 1, 0, 'L');
        $pdf->Cell(30, 8, $mobile, 1, 0, 'C');
        $pdf->Cell(25, 8, $city, 1, 0, 'C');
        $pdf->Cell(25, 8, $state, 1, 0, 'C');
        $pdf->Cell(20, 8, $zip, 1, 1, 'C');
    }

    // Clear any output buffers
    ob_end_clean();

    // Output PDF
    $pdf->Output('users_list.pdf', 'D');
    exit;
}

// Then handle the PDF download
if (isset($_POST['download_pdf'])) {
    // Re-run the query to reset the result pointer
    global $sql, $conn;
    $result = $conn->query($sql);
    generatePDF($result);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Users</title>
    <link rel="icon" href="img/mainlogo.png" type="image/png" />
    <link rel="stylesheet" href="css/style1.css" />
    <link rel="stylesheet" href="css/bootstrap1.min.css" />
    <link rel="stylesheet" href="css/metisMenu.css" />
</head>

<body class="crm_body_bg">
    <?php include 'sidebar.php'; ?>
    <section class="main_content dashboard_part large_header_bg">
        <div class="container-fluid">
            <h2 class="text-center">Users List</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Mobile</th>
                        <th>City</th>
                        <th>State</th>
                        <th>ZIP</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['Name']}</td>
                                    <td>{$row['user_name']}</td>
                                    <td>{$row['Email']}</td>
                                    <td>{$row['address']}</td>
                                    <td>{$row['Mobile']}</td>
                                    <td>{$row['city']}</td>
                                    <td>{$row['state']}</td>
                                    <td>{$row['zip']}</td>
                                    <td>{$row['created_at']}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10'>No users found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <form method="post" class="text-center mt-3 mb-4">
                <button type="submit" name="download_pdf" class="btn btn-primary">
                    <i class="fas fa-download"></i> Download PDF
                </button>
            </form>
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
$conn->close();
?>
