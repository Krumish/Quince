<?php include 'includes/session.php'; ?>
<?php
  include '../timezone.php';
  $range_to = date('m/d/Y');
  $range_from = date('m/d/Y', strtotime('-30 day', strtotime($range_to)));
?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Payroll
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Payroll</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <?php
        if(isset($_SESSION['error'])){
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              ".$_SESSION['error']."
            </div>
          ";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              ".$_SESSION['success']."
            </div>
          ";
          unset($_SESSION['success']);
        }
      ?>
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <div class="pull-right">
                <form method="POST" class="form-inline" id="payForm">
                  <div class="input-group">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control pull-right col-sm-8" id="reservation" name="date_range" value="<?php echo (isset($_GET['range'])) ? $_GET['range'] : $range_from.' - '.$range_to; ?>">
                  </div>
                  <button type="button" class="btn btn-success btn-sm btn-flat" id="payroll"><span class="glyphicon glyphicon-print"></span> Payroll</button>
                  <button type="button" class="btn btn-primary btn-sm btn-flat" id="payslip"><span class="glyphicon glyphicon-print"></span> Payslip</button>
                </form>
              </div>
            </div>
            <div class="box-body">
              <table id="example1" class="table table-bordered">
               <thead>
  <th>Employee Name</th>
  <th>Employee ID</th>
  <th>Gross</th>
  <th>Deductions</th>
  <th>Tax</th>
  <th>Cash Advance</th>
  <th>Net Pay</th>
</thead>
                <tbody>
                  <?php
                    $sql = "SELECT *, SUM(amount) as total_amount FROM deductions";
                    $query = $conn->query($sql);
                    $drow = $query->fetch_assoc();
                    $deduction = $drow['total_amount'];
                    $to = date('Y-m-d');
                    $from = date('Y-m-d', strtotime('-30 day', strtotime($to)));

                    if(isset($_GET['range'])){
                      $range = $_GET['range'];
                      $ex = explode(' - ', $range);
                      $from = date('Y-m-d', strtotime($ex[0]));
                      $to = date('Y-m-d', strtotime($ex[1]));
                    }

$sql = "SELECT attendance.*, employees.firstname, employees.middlename, employees.lastname, employees.employee_id, position.rate, position.description, SUM(num_hr) AS total_hr, attendance.employee_id AS empid 
FROM attendance 
LEFT JOIN employees ON employees.id = attendance.employee_id 
LEFT JOIN position ON position.id = employees.position_id 
WHERE date BETWEEN '$from' AND '$to' 
GROUP BY attendance.employee_id 
ORDER BY employees.lastname ASC, employees.firstname ASC";
                    $query = $conn->query($sql);
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

                    $total = 0;
                    while($row = $query->fetch_assoc()){
                      $empid = $row['empid'];
                      
                      $casql = "SELECT *, SUM(amount) AS cashamount FROM cashadvance WHERE employee_id='$empid' AND date_advance BETWEEN '$from' AND '$to'";
                      
                      $caquery = $conn->query($casql);
                      $carow = $caquery->fetch_assoc();
                      $cashadvance = $carow['cashamount'];
                      
                  $otsql = "SELECT SUM(hours * rate) AS total_overtime 
          FROM overtime 
          WHERE employee_id = '$empid' 
          AND date_overtime BETWEEN '$from' AND '$to'";
$otquery = $conn->query($otsql);
$otrow = $otquery->fetch_assoc();
$overtime_pay = $otrow['total_overtime'] ? $otrow['total_overtime'] : 0;

// Add overtime to gross salary
$gross = ($row['rate'] * $row['total_hr']) + $overtime_pay;
// Approximate monthly salary (22 working days × 8 hours)
$monthly_salary = $row['rate'] * 22 * 8;

// Calculate tax based on TRAIN law
$tax = computePhilippinesTax($monthly_salary);

// Total deduction includes fixed deduction, cash advance, and tax
$total_deduction = $deduction + $cashadvance + $tax;

// Net pay
$net = $gross - $total_deduction;

echo "
  <tr>
    <td>".$row['lastname'].", ".$row['firstname']." ".$row['middlename']."</td>
    <td>".$row['employee_id']."</td>
    <td>".number_format($gross, 2)."</td>
    <td>".number_format($deduction, 2)."</td>
    <td>".number_format($tax, 2)."</td>
    <td>".number_format($cashadvance, 2)."</td>
    <td>".number_format($net, 2)."</td>
  </tr>
";

                    }

                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>   
  </div>
    
  <?php include 'includes/footer.php'; ?>
</div>
<?php include 'includes/scripts.php'; ?> 
<script>
$(function(){
  $('.edit').click(function(e){
    e.preventDefault();
    $('#edit').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $('.delete').click(function(e){
    e.preventDefault();
    $('#delete').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $("#reservation").on('change', function(){
    var range = encodeURI($(this).val());
    window.location = 'payroll.php?range='+range;
  });

  $('#payroll').click(function(e){
    e.preventDefault();
    $('#payForm').attr('action', 'payroll_generate.php');
    $('#payForm').submit();
  });

  $('#payslip').click(function(e){
    e.preventDefault();
    $('#payForm').attr('action', 'payslip_generate.php');
    $('#payForm').submit();
  });

});

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'position_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('#posid').val(response.id);
      $('#edit_title').val(response.description);
      $('#edit_rate').val(response.rate);
      $('#del_posid').val(response.id);
      $('#del_position').html(response.description);
    }
  });
}


</script>
</body>
</html>
