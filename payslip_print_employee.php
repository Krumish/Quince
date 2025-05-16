<?php
session_start();

include __DIR__ . '/conn.php';
require_once('tcpdf/tcpdf.php');

// Redirect if not logged in
if (!isset($_SESSION['employee_id']) || !isset($_SESSION['id'])) {
    header('Location: employee_login.php');
    exit();
}

$emp_id = $_SESSION['id'];
$employee_code = $_SESSION['employee_id'];

// Fetch employee details and position
$sql = "SELECT e.*, p.description AS position_name, p.rate FROM employees e
        LEFT JOIN position p ON p.id = e.position_id
        WHERE e.id = '$emp_id'";
$query = $conn->query($sql);
$emp = $query->fetch_assoc();

// Get cash advance for the current employee
$cashadvance_sql = "SELECT SUM(amount) AS total_advance FROM cashadvance WHERE employee_id = '$emp_id'";
$cashadvance_query = $conn->query($cashadvance_sql);
$cashadvance = $cashadvance_query->fetch_assoc()['total_advance'] ?? 0;

// Get overtime pay for the current employee
$overtime_sql = "SELECT SUM(hours * rate) AS total_overtime FROM overtime WHERE employee_id = '$emp_id'";
$overtime_query = $conn->query($overtime_sql);
$overtime_pay = $overtime_query->fetch_assoc()['total_overtime'] ?? 0;

// Calculate monthly salary (22 working days × 8 hours)
$monthly_salary = $emp['rate'] * 22 * 8;
$gross_salary = $monthly_salary + $overtime_pay;

// Tax computation
function computePhilippinesTax($monthly_salary) {
    if ($monthly_salary <= 20833) return 0;
    if ($monthly_salary <= 33332) return ($monthly_salary - 20833) * 0.20;
    if ($monthly_salary <= 66666) return 2500 + ($monthly_salary - 33332) * 0.25;
    if ($monthly_salary <= 166666) return 10833 + ($monthly_salary - 66666) * 0.30;
    if ($monthly_salary <= 666666) return 40833 + ($monthly_salary - 166666) * 0.32;
    return 200833 + ($monthly_salary - 666666) * 0.35;
}

$tax = computePhilippinesTax($monthly_salary);
$fixed_deductions = 1000;

// Total deduction = fixed deductions + cash advance + tax
$total_deduction = $fixed_deductions + $cashadvance + $tax;
$net_pay = $gross_salary - $total_deduction;

// Generate PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Employee Payslip');
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

// Company logo
$logoPath = './images/company_logo.jpg'; 
if (file_exists($logoPath)) {
    $pdf->Image($logoPath, 8, 21, 35, 35); // X, Y, Width, Height
}

$html = '
<br><br><br>
<h2 align="center">JVJ Interior Supplies Trading Company</h2>
<p align="center"><strong>Pay Period:</strong> '.date("F Y").'</p>

<p><strong>Employee Name:</strong> '.$emp['firstname'].' '.$emp['lastname'].'<br>
<strong>Employee ID:</strong> '.$employee_code.'<br>
<strong>Position:</strong> '.$emp['position_name'].'</p>

<table border="1" cellpadding="5" cellspacing="0">
    <tr bgcolor="#dddddd">
        <th colspan="2">Earnings</th>
    </tr>
    <tr>
        <td>Base Salary</td>
        <td align="right">'.number_format($monthly_salary, 2).'</td>
    </tr>
    <tr>
        <td>Overtime Pay</td>
        <td align="right">'.number_format($overtime_pay, 2).'</td>
    </tr>
    <tr>
        <td>Hourly Rate</td>
        <td align="right">'.number_format($emp['rate'], 2).'</td>
    </tr>
    <tr>
        <td><b>Gross Salary</b></td>
        <td align="right"><b>'.number_format($gross_salary, 2).'</b></td>
    </tr>
</table>

<br><br><br>

<table border="1" cellpadding="5" cellspacing="0">
    <tr bgcolor="#dddddd">
        <th colspan="2">Deductions</th>
    </tr>
    <tr>
        <td>Fixed Deductions</td>
        <td align="right">'.number_format($fixed_deductions, 2).'</td>
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
        <td align="right"><strong>'.number_format($net_pay, 2).'</strong></td>
    </tr>
</table>
';

$pdf->writeHTML($html);
$pdf->Output('employee_payslip.pdf', 'I');
?>
