<?php
	include 'includes/session.php';

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
	$pdf->SetTitle('Payslip: '.$from_title.' - '.$to_title);  
	$pdf->setPrintHeader(false);  
	$pdf->setPrintFooter(false);  
	$pdf->SetMargins(15, 10, 15);  
	$pdf->SetAutoPageBreak(TRUE, 10);  
	$pdf->SetFont('helvetica', '', 10);  

	$sql = "SELECT *, SUM(num_hr) AS total_hr, attendance.employee_id AS empid, employees.employee_id AS employee FROM attendance 
			LEFT JOIN employees ON employees.id=attendance.employee_id 
			LEFT JOIN position ON position.id=employees.position_id 
			WHERE date BETWEEN '$from' AND '$to' 
			GROUP BY attendance.employee_id 
			ORDER BY employees.lastname ASC, employees.firstname ASC";

	$query = $conn->query($sql);
	while($row = $query->fetch_assoc()){
		$empid = $row['empid'];

		$casql = "SELECT *, SUM(amount) AS cashamount FROM cashadvance WHERE employee_id='$empid' AND date_advance BETWEEN '$from' AND '$to'";
		$caquery = $conn->query($casql);
		$carow = $caquery->fetch_assoc();
		$cashadvance = $carow['cashamount'];

		$gross = $row['rate'] * $row['total_hr'];
		$total_deduction = $deduction + $cashadvance;
		$net = $gross - $total_deduction;

		// New page for each employee
		$pdf->AddPage(); 

		// Company logo
		$logoPath = '../images/company_logo.jpg'; // Update this to actual path
		if (file_exists($logoPath)) {
			$pdf->Image($logoPath, 15, 12, 35, 35); // X, Y, Width, Height
		}

		$contents = '
		<br><br><br>
		<h2 style="text-align:center;">JVJ Interior Supplies Trading Company</h2>
		<p style="text-align:center;"><strong>Pay Period:</strong> '.$from_title.' – '.$to_title.'<br>
		<strong>Pay Date:</strong> '.date("M d, Y").'</p>

<p><strong>Employee Name:</strong> '.$row['lastname'].', '.$row['firstname'].' '.$row['middlename'].'<br>
		<strong>Employee ID:</strong> '.$row['employee'].'</p>

		<table border="1" cellpadding="5" cellspacing="0">
			<tr bgcolor="#dddddd">
				<th colspan="2">Earnings</th>
			</tr>
			<tr>
				<td>Base Salary</td>
				<td align="right">'.number_format($gross, 2).'</td>
			</tr>
			<tr>
				<td>Overtime Pay</td>
				<td align="right">0.00</td> 
			</tr>
			<tr>
				<td>Hourly Rate</td>
				<td align="right">'.number_format($row['rate'], 2).'</td>
			</tr>
			<tr>
				<td>Gross Salary</td>
				<td align="right">'.number_format($gross, 2).'</td>
			</tr>
		</table>
		
		<br><br><br>

		<table border="1" cellpadding="5" cellspacing="0">
			<tr bgcolor="#dddddd">
				<th colspan="2">Deductions</th>
			</tr>
			<tr>
				<td>Taxes</td>
				<td align="right">'.number_format($deduction, 2).'</td>
			</tr>
			<tr>
				<td>Cash Advance</td>
				<td align="right">'.number_format($cashadvance, 2).'</td>
			</tr>
		</table>
		
		<br><br><br>

		<table border="1" cellpadding="5" cellspacing="0">
			<tr bgcolor="#dddddd">
				<th colspan="2">Net Pay</th>
			</tr>
			<tr>
				<td>Net Pay</td>
				<td align="right"><strong>'.number_format($net, 2).'</strong></td>
			</tr>
		</table><br><br><hr>
		';

		$pdf->writeHTML($contents, true, false, true, false, '');
	}

	$pdf->Output('payslip.pdf', 'I');
?>
