<?php
	include 'includes/session.php';

	function generateRow($from, $to, $conn, $deduction){
		$contents = '';
	 	
	$sql = "SELECT *, SUM(num_hr) AS total_hr, attendance.employee_id AS empid, employees.employee_id AS employee 
        FROM attendance 
        LEFT JOIN employees ON employees.id = attendance.employee_id 
        LEFT JOIN position ON position.id = employees.position_id 
        WHERE date BETWEEN '$from' AND '$to' 
        GROUP BY attendance.employee_id 
        ORDER BY employees.lastname ASC, employees.firstname ASC";

		$query = $conn->query($sql);
		$total = 0;
		while($row = $query->fetch_assoc()){
			$empid = $row['empid'];
                      
	      	$casql = "SELECT *, SUM(amount) AS cashamount FROM cashadvance WHERE employee_id='$empid' AND date_advance BETWEEN '$from' AND '$to'";
	      
	      	$caquery = $conn->query($casql);
	      	$carow = $caquery->fetch_assoc();
	      	$cashadvance = $carow['cashamount'];

			$gross = $row['rate'] * $row['total_hr'];
			$total_deduction = $deduction + $cashadvance;
      		$net = $gross - $total_deduction;

			$total += $net;
			$contents .= '
			<tr>
				<td>'.$row['lastname'].', '.$row['firstname'].' '.$row['middlename'].'</td>
				<td>'.$row['employee_id'].'</td>
				<td align="right">'.number_format($net, 2).'</td>
			</tr>
			';
		}

		$contents .= '
			<tr>
				<td colspan="2" align="right"><b>Total</b></td>
				<td align="right"><b>'.number_format($total, 2).'</b></td>
			</tr>
		';
		return $contents;
	}
		
	$range = $_POST['date_range'];
	$ex = explode(' - ', $range);
	$from = date('Y-m-d', strtotime($ex[0]));
	$to = date('Y-m-d', strtotime($ex[1]));

	$sql = "SELECT *, SUM(amount) as total_amount FROM deductions";
    $query = $conn->query($sql);
   	$drow = $query->fetch_assoc();
    $deduction = $drow['total_amount'];

	$from_title = date('M d, Y', strtotime($ex[0]));
	$to_title = date('M d, Y', strtotime($ex[1]));

	require_once('../tcpdf/tcpdf.php');  
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  
$pdf->SetCreator(PDF_CREATOR);  
$pdf->SetTitle('Payroll: '.$from_title.' - '.$to_title);  

// Set margins
$pdf->SetMargins(15, 20, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->SetFont('helvetica', '', 11);  

// Add a page
$pdf->AddPage();  

// Add logo (adjust path and dimensions as needed)
$logoPath = '../images/company_logo.jpg'; // change to your actual path
if (file_exists($logoPath)) {
    $pdf->Image($logoPath, 15, 3, 35, 35); // X, Y, Width, Height
}

// Extract months and year for dynamic payroll title
$fromMonth = date('F', strtotime($from));  // e.g., April
$toMonth = date('F', strtotime($to));      // e.g., May
$year = date('Y', strtotime($to));         // Final year

if ($fromMonth === $toMonth) {
    $monthRange = "$toMonth $year";
} else {
    $monthRange = "$fromMonth to $toMonth, $year";
}

$summaryTitle = "Payroll Summary of the Month $monthRange";
// Header content
$pdf->SetY(10); // set vertical position for text
$content = '
	<style>
		.table-header {
			background-color: #f2f2f2;
			font-weight: bold;
		}
	</style>
	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td width="30%"></td>
			<td width="70%" align="center">
				<h2 style="margin:0;">JVJ Interior Supplies Trading Company</h2>
				<small>'.$from_title.' - '.$to_title.'</small>
			</td>
		</tr>
	</table>
	<br><br><br>

	<h4 align="center">'.$summaryTitle.'</h4>
	<br>

	<table border="1" cellspacing="0" cellpadding="4">
		<tr style="background-color:#f2f2f2;">
			<th width="40%" align="center"><b>Employee Name</b></th>
			<th width="30%" align="center"><b>Employee ID</b></th>
			<th width="30%" align="center"><b>Net Pay</b></th> 
		</tr>
';


$content .= generateRow($from, $to, $conn, $deduction);  
$content .= '</table>';



// Output the PDF
$pdf->writeHTML($content, true, false, true, false, '');
$pdf->Output('payroll.pdf', 'I');

?>