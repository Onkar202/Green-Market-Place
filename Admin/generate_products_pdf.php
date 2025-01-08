<?php
require('../include/db_con.php');
require('../vendor/autoload.php');

use TCPDF as PDF;

// Create new PDF document
$pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Green Market Place');
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Products List');

// Remove header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 12);

// Title
$pdf->Cell(0, 10, 'Products List', 0, 1, 'C');
$pdf->Ln(10);

// Table header
$header = array('ID', 'Product Name', 'Quantity', 'Category', 'Selling Price', 'Buying Price', 'Seller ID', 'Status');
$pdf->SetFillColor(200, 220, 255);
$pdf->SetFont('helvetica', 'B', 10);

// Calculate column widths
$w = array(20, 40, 25, 30, 30, 30, 25, 25);

// Header
foreach($header as $i => $col) {
    $pdf->Cell($w[$i], 7, $col, 1, 0, 'C', true);
}
$pdf->Ln();

// Data
$pdf->SetFont('helvetica', '', 10);
$query = "SELECT * FROM products ORDER BY pr_id DESC";
$result = mysqli_query($con, $query);

while($row = mysqli_fetch_assoc($result)) {
    $pdf->Cell($w[0], 6, $row['pr_id'], 1);
    $pdf->Cell($w[1], 6, $row['pr_name'], 1);
    $pdf->Cell($w[2], 6, $row['pr_quantity'], 1);
    $pdf->Cell($w[3], 6, $row['pr_category'], 1);
    $pdf->Cell($w[4], 6, 'Rs. ' . $row['pr_price'], 1);
    $pdf->Cell($w[5], 6, 'Rs. ' . $row['buying_price'], 1);
    $pdf->Cell($w[6], 6, $row['seller_id'], 1);
    $pdf->Cell($w[7], 6, $row['active'] ? 'Active' : 'Inactive', 1);
    $pdf->Ln();
}

// Close and output PDF document
$pdf->Output('products_list.pdf', 'D');

// Close the database connection
mysqli_close($con);
?> 