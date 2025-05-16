<?php
	include 'includes/session.php';

	$range = $_POST['date_range'];
	$ex = explode(' - ', $range);
	$from = date('Y-m-d', strtotime($ex[0]));
	$to = date('Y-m-d', strtotime($ex[1]));

	$sql = "SELECT *, SUM(amount) as total_amount FROM deductions";
	$query = $conn->query($sql);
	$drow = $query->fetch_assoc();
	$fixed_deduction = $drow['total_amount']; // fixed deductions excluding tax and cash advance

	$from_title = date('M d, Y', strtotime($ex[0]));
	$to_title = date('M d, Y', strtotime($ex[1]));

	// Tax computation function same as in payroll.php
	function computePhilippinesTax($monthly_salary) {
	    if ($monthly_salary <= 20833) {
	        return 0;
	    } elseif ($monthly_salary <= 33332) {
	        return ($monthly_salary - 20833) * 0.20;
	    } elseif ($monthly_salary <= 66666) {
	        return 2500 + ($monthly_salary - 33332) * 0.25;
	    } elseif ($monthly_salary <= 166666) {
	        return 10833 + ($monthly_salary - 66666) * 0.30;
	    } elseif ($monthly_salary <= 666666) {
	        return 40833 + ($monthly_salary - 166666) * 0.32;
	    } else {
	        return 200833 + ($monthly_salary - 666666) * 0.35;
	    }
	}

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

		// Cash advance in period
		$casql = "SELECT *, SUM(amount) AS cashamount FROM cashadvance WHERE employee_id='$empid' AND date_advance BETWEEN '$from' AND '$to'";
		$caquery = $conn->query($casql);
		$carow = $caquery->fetch_assoc();
		$cashadvance = $carow['cashamount'] ? $carow['cashamount'] : 0;

		// Overtime pay calculation
		$otsql = "SELECT SUM(hours * rate) AS total_overtime 
          FROM overtime 
          WHERE employee_id = '$empid' 
          AND date_overtime BETWEEN '$from' AND '$to'";
		$otquery = $conn->query($otsql);
		$otrow = $otquery->fetch_assoc();
		$overtime_pay = $otrow['total_overtime'] ? $otrow['total_overtime'] : 0;

		// Gross pay = (rate * total hours) + overtime pay
		$gross = ($row['rate'] * $row['total_hr']) + $overtime_pay;

		// Approximate monthly salary (22 working days × 8 hours)
		$monthly_salary = $row['rate'] * 22 * 8;

		// Calculate tax
		$tax = computePhilippinesTax($monthly_salary);

		// Total deduction = fixed deductions + cash advance + tax
		$total_deduction = $fixed_deduction + $cashadvance + $tax;

		// Net pay
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
				<td align="right">'.number_format($row['rate'] * $row['total_hr'], 2).'</td>
			</tr>
			<tr>
    <td>Overtime Pay</td>
    <td align="right">'.number_format($overtime_pay, 2).'</td> 
</tr>
			<tr>
				<td>Hourly Rate</td>
				<td align="right">'.number_format($row['rate'], 2).'</td>
			</tr>
			<tr>
				<td><b>Gross Salary</b></td>
				<td align="right"><b>'.number_format($gross, 2).'</b></td>
			</tr>
		</table>
		
		<br><br><br>

		<table border="1" cellpadding="5" cellspacing="0">
			<tr bgcolor="#dddddd">
				<th colspan="2">Deductions</th>
			</tr>
			<tr>
				<td>Fixed Deductions</td>
				<td align="right">'.number_format($fixed_deduction, 2).'</td>
			</tr>
			<tr>
				<td>Tax</td>
				<td align="right">'.number_format($tax, 2).'</td>
			</tr>
			<tr>
				<td>Cash Advance</td>
				<td align="right">'.number_format($cashadvance, 2).'</td>
			</tr>
			<tr>
				<td><b>Total Deductions</b></td>
				<td align="right"><b>'.number_format($total_deduction, 2).'</b></td>
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
