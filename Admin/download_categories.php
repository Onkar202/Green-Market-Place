<?php
ob_start(); // Start output buffering
require('../include/db_con.php');
require('../vendor/autoload.php');

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Green Market Place');
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Categories List');

// Remove header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 12);

// Title
$pdf->Cell(0, 10, 'Categories List', 0, 1, 'C');
$pdf->Ln(10);

// Table header
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(95, 10, 'Category ID', 1, 0, 'C');
$pdf->Cell(95, 10, 'Category Name', 1, 1, 'C');

// Table content
$pdf->SetFont('helvetica', '', 12);

// Fetch categories from database
$sql = "SELECT * FROM categories";
$result = $con->query($sql);

while ($row = $result->fetch_assoc()) {
    $pdf->Cell(95, 10, $row['cat_id'], 1, 0, 'C');
    $pdf->Cell(95, 10, $row['cat_name'], 1, 1, 'C');
}

// Clear any output buffers
ob_end_clean();

// Output PDF
$pdf->Output('categories_list.pdf', 'D');
exit; 