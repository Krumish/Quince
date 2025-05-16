<?php
session_start();
include 'conn.php';

// Redirect if not logged in
if (!isset($_SESSION['employee_id']) || !isset($_SESSION['id'])) {
    header('Location: employee_login.php');
    exit();
}

$emp_id = $_SESSION['id'];

// Fetch employee schedule info
$sql = "SELECT e.firstname, e.lastname, e.employee_id, s.time_in, s.time_out 
        FROM employees e
        LEFT JOIN schedules s ON s.id = e.schedule_id
        WHERE e.id = '$emp_id'";
$res = $conn->query($sql);

if (!$res || $res->num_rows === 0) {
    die("Schedule not found for this employee.");
}

$emp = $res->fetch_assoc();

require_once('tcpdf/tcpdf.php');

$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('My Schedule');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(PDF_MARGIN_LEFT, 25, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->SetFont('helvetica', '', 11);
$pdf->AddPage();

$logoPath = 'images/company_logo.jpg'; 
if (file_exists($logoPath)) {
    $pdf->Image($logoPath, 10, 7, 35, 35);
}

$html = '
<h2 align="center">JVJ Interior Supplies Trading Company</h2>
<h4 align="center">My Schedule</h4><br><br><br>
<table border="1" cellspacing="3" cellpadding="3">  
    <tr>  
        <th width="40%" align="center"><b>Employee Name</b></th>
        <th width="30%" align="center"><b>Employee ID</b></th>
        <th width="30%" align="center"><b>Schedule</b></th> 
    </tr>  
    <tr>
        <td>' . htmlspecialchars($emp['lastname'] . ', ' . $emp['firstname']) . '</td>
        <td>' . htmlspecialchars($emp['employee_id']) . '</td>
        <td>' . date('h:i A', strtotime($emp['time_in'])) . ' - ' . date('h:i A', strtotime($emp['time_out'])) . '</td>
    </tr>
</table>';

$pdf->writeHTML($html);
$pdf->Output('my_schedule.pdf', 'I');
