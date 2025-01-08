<?php
ob_start(); // Start output buffering
require('../include/db_con.php');
require('../vendor/autoload.php');

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Green Market Place');
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Sellers List');

// Remove header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 12);

// Title
$pdf->Cell(0, 10, 'Sellers List', 0, 1, 'C');
$pdf->Ln(10);

// Table header
$pdf->SetFont('helvetica', 'B', 12);
$w = array(20, 30, 50, 40, 25, 25);
$header = array('ID', 'Seller ID', 'Seller Name', 'City', 'State', 'ZIP');

foreach($header as $i => $h) {
    $pdf->Cell($w[$i], 10, $h, 1, 0, 'C');
}
$pdf->Ln();

// Table content
$pdf->SetFont('helvetica', '', 12);

// Fetch sellers from database
$sql = "SELECT id, seller_id, seller_name, city, state, zip FROM sellers";
$result = $con->query($sql);

while($row = $result->fetch_assoc()) {
    $pdf->Cell($w[0], 10, $row['id'], 1, 0, 'C');
    $pdf->Cell($w[1], 10, $row['seller_id'], 1, 0, 'C');
    $pdf->Cell($w[2], 10, $row['seller_name'], 1, 0, 'C');
    $pdf->Cell($w[3], 10, $row['city'], 1, 0, 'C');
    $pdf->Cell($w[4], 10, $row['state'], 1, 0, 'C');
    $pdf->Cell($w[5], 10, $row['zip'], 1, 0, 'C');
    $pdf->Ln();
}

// Clear any output buffers
ob_end_clean();

// Output PDF
$pdf->Output('sellers_list.pdf', 'D');
exit;
?> 