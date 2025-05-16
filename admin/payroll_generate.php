<?php
include 'includes/session.php';

// Tax computation function
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

function generateRow($from, $to, $conn, $deduction) {
    $contents = '';

    $sql = "SELECT attendance.*, employees.firstname, employees.middlename, employees.lastname, employees.employee_id, position.rate, position.description, SUM(num_hr) AS total_hr, attendance.employee_id AS empid 
            FROM attendance 
            LEFT JOIN employees ON employees.id = attendance.employee_id 
            LEFT JOIN position ON position.id = employees.position_id 
            WHERE date BETWEEN '$from' AND '$to' 
            GROUP BY attendance.employee_id 
            ORDER BY employees.lastname ASC, employees.firstname ASC";

    $query = $conn->query($sql);
    $total = 0;
    
    while ($row = $query->fetch_assoc()) {
        $empid = $row['empid'];

        // Get cash advance
        $casql = "SELECT SUM(amount) AS cashamount FROM cashadvance WHERE employee_id='$empid' AND date_advance BETWEEN '$from' AND '$to'";
        $caquery = $conn->query($casql);
        $carow = $caquery->fetch_assoc();
        $cashadvance = $carow['cashamount'] ? $carow['cashamount'] : 0;

        // Get overtime pay
        $otsql = "SELECT SUM(hours * rate) AS total_overtime FROM overtime WHERE employee_id = '$empid' AND date_overtime BETWEEN '$from' AND '$to'";
        $otquery = $conn->query($otsql);
        $otrow = $otquery->fetch_assoc();
        $overtime_pay = $otrow['total_overtime'] ? $otrow['total_overtime'] : 0;

        // Calculate gross pay including overtime
        $gross = ($row['rate'] * $row['total_hr']) + $overtime_pay;

        // Approximate monthly salary (22 working days x 8 hours)
        $monthly_salary = $row['rate'] * 22 * 8;

        // Calculate tax based on monthly salary
        $tax = computePhilippinesTax($monthly_salary);

        // Total deductions: fixed deductions + cash advance + tax
        $total_deduction = $deduction + $cashadvance + $tax;

        // Net pay
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

$sql = "SELECT SUM(amount) as total_amount FROM deductions";
$query = $conn->query($sql);
$drow = $query->fetch_assoc();
$deduction = $drow['total_amount'] ? $drow['total_amount'] : 0;

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
$logoPath = '../images/company_logo.jpg';
if (file_exists($logoPath)) {
    $pdf->Image($logoPath, 15, 3, 35, 35);
}

// Extract months and year for dynamic payroll title
$fromMonth = date('F', strtotime($from));
$toMonth = date('F', strtotime($to));
$year = date('Y', strtotime($to));

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
