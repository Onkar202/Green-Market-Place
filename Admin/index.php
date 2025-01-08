<?php
// Add error handling for includes
function safeRequire($path) {
    if (file_exists($path)) {
        require_once($path);
        return true;
    }
    return false;
}

// Basic includes that don't require Composer
include('admin_session_check.php');
include('../include/db_con.php');

// Only require TCPDF directly - remove composer autoload requirement
$tcpdfPath = '../vendor/tecnickcom/tcpdf/tcpdf.php';

if (!safeRequire($tcpdfPath)) {
    die("Error: TCPDF library not found. Please ensure it's properly installed in the vendor directory.");
}

// Function to safely execute a query and return count
function safeQueryCount($con, $query) {
    $result = $con->query($query);
    if ($result === false) {
        error_log("Query failed: " . $con->error);
        return 0;
    }
    return $result->fetch_row()[0] ?? 0;
}

// Function to safely fetch all rows
function safeQueryFetchAll($con, $query) {
    $result = $con->query($query);
    if ($result === false) {
        error_log("Query failed: " . $con->error);
        return [];
    }
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Enhanced function to fetch profit and loss data, including product-level details
function fetchProfitLossData($con, $period) {
    // Define the date range based on the period
    switch ($period) {
        case 'daily':
            $dateCondition = "DATE(o.created_at) = CURDATE()";
            break;
        case 'weekly':
            $dateCondition = "WEEK(o.created_at) = WEEK(CURDATE())";
            break;
        case 'monthly':
            $dateCondition = "MONTH(o.created_at) = MONTH(CURDATE()) AND YEAR(o.created_at) = YEAR(CURDATE())";
            break;
        case 'total':
            $dateCondition = "1"; // No date condition for total
            break;
        default:
            return [];
    }

    // Query to calculate total sales, total costs, and profit for each product
    $query = "
        SELECT 
            p.pr_name,
            oi.quantity,
            p.pr_price,
            p.buying_price,
            (oi.quantity * p.pr_price) AS total_sales,
            (oi.quantity * p.buying_price) AS total_costs,
            ((oi.quantity * p.pr_price) - (oi.quantity * p.buying_price)) AS profit
        FROM 
            orders o
        JOIN 
            order_items oi ON o.order_id = oi.order_id
        JOIN 
            products p ON oi.product_id = p.pr_id
        WHERE 
            $dateCondition
    ";

    return safeQueryFetchAll($con, $query);
}

// Enhanced function to generate PDF report for profit and loss with item-level data
function generateProfitLossPDFReport($con, $period) {
    $reportData = fetchProfitLossData($con, $period);

    // Create new PDF document
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->Cell(0, 10, ucfirst($period) . ' Profit and Loss Report', 0, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 12);

    if (empty($reportData)) {
        $pdf->Cell(0, 10, 'No data available for this period.', 0, 1, 'C');
    } else {
        $total_sales = 0;
        $total_costs = 0;
        $total_profit = 0;
        foreach ($reportData as $row) {
            $sales = number_format($row['total_sales'], 2);
            $costs = number_format($row['total_costs'], 2);
            $profit = number_format($row['profit'], 2);
            
            $pdf->Cell(0, 10, 'Product: ' . $row['pr_name'] . ', Quantity: ' . $row['quantity'] . ', Sales: ' . $sales . ', Costs: ' . $costs . ', Profit: ' . $profit, 0, 1, 'L');
            
            // Accumulate total sales, costs, and profit
            $total_sales += $row['total_sales'];
            $total_costs += $row['total_costs'];
            $total_profit += $row['profit'];
        }

        // Show total sales, costs, and profit summary
        $pdf->Cell(0, 10, 'Total Sales: ' . number_format($total_sales, 2), 0, 1, 'L');
        $pdf->Cell(0, 10, 'Total Costs: ' . number_format($total_costs, 2), 0, 1, 'L');
        $pdf->Cell(0, 10, 'Total Profit: ' . number_format($total_profit, 2), 0, 1, 'L');
    }

    // Output PDF
    $pdf->Output(ucfirst($period) . '_Profit_Loss_Report.pdf', 'D'); // 'D' for download
}

// Handle profit and loss report generation requests
if (isset($_GET['report']) && $_GET['report'] === 'profit_loss') {
    $period = $_GET['period'] ?? 'total'; // Default to total if not specified
    generateProfitLossPDFReport($con, $period);
    exit; // Stop further execution
}

// Fetch statistics
$stats = [
    'orders' => safeQueryCount($con, "SELECT COUNT(*) FROM orders"),
    'users' => safeQueryCount($con, "SELECT COUNT(*) FROM users"),
    'sellers' => safeQueryCount($con, "SELECT COUNT(*) FROM sellers"),
    'products' => safeQueryCount($con, "SELECT COUNT(*) FROM products"),
    'admins' => safeQueryCount($con, "SELECT COUNT(*) FROM admins"),
];

// Fetch admin logs
$admin_logs = [];
$result = $con->query("SELECT * FROM admin_logs ORDER BY action_time DESC LIMIT 10");
if ($result !== false) {
    while ($row = $result->fetch_assoc()) {
        $admin_logs[] = $row;
    }
}

// Handle report generation requests
if (isset($_GET['report']) && in_array($_GET['report'], ['daily', 'weekly', 'monthly', 'total'])) {
    generateProfitLossPDFReport($con, $_GET['report']);
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Admin Dashboard</title>
    <link rel="icon" href="img/mainlogo.png" type="image/png">
    <link rel="stylesheet" href="css/style1.css" />
    <link rel="stylesheet" href="css/bootstrap1.min.css" />
    <link rel="stylesheet" href="css/metisMenu.css">
</head>

<body class="crm_body_bg">

    <?php include 'sidebar.php'; ?>

    <section class="main_content dashboard_part large_header_bg">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="mb-4">Dashboard Statistics</h2>
                </div>
            </div>
            <div class="row">
                <?php
                $stat_items = [
                    'orders' => 'Total Orders',
                    'users' => 'Total Users',
                    'sellers' => 'Total Sellers',
                    'products' => 'Total Products',
                    'admins' => 'Total Admins',
                ];
                foreach ($stat_items as $key => $label) {
                    echo "<div class='col-lg-4 col-md-6 mb-4'>
                            <div class='card'>
                                <div class='card-body'>
                                    <h5 class='card-title'>$label</h5>
                                    <p class='card-text display-4'>{$stats[$key]}</p>
                                </div>
                            </div>
                          </div>";
                }
                ?>
            </div>
            
            <div class="row mt-5">
                <div class="col-lg-12">
                    <h3>Reports</h3>
                    <div class="btn-group" role="group" aria-label="Report Buttons">
                        <a href="?report=daily" class="btn btn-primary">Download Daily Report</a>
                        <a href="?report=weekly" class="btn btn-primary">Download Weekly Report</a>
                        <a href="?report=monthly" class="btn btn-primary">Download Monthly Report</a>
                        <a href="?report=profit_loss" class="btn btn-primary">Download Profit and Loss Report</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer_part">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="footer_iner text-center">
                            <p>2024 © All Rights are Reserved by Green Market Place</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div id="back-top" style="display: none;">
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
