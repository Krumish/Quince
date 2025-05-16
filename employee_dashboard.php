<?php
session_start();
include('admin/includes/conn.php');
include 'timezone.php';

// Redirect if not logged in
if (!isset($_SESSION['employee_id']) || !isset($_SESSION['id'])) {
    header('Location: employee_login.php');
    exit();
}

$emp_id = $_SESSION['id'];          // DB employee internal ID
$employee_code = $_SESSION['employee_id']; // Employee code/id

// Fetch employee info with position and schedule
$sql = "SELECT e.*, p.description AS position_name, p.rate, s.time_in AS sched_in, s.time_out AS sched_out
        FROM employees e
        LEFT JOIN position p ON p.id = e.position_id
        LEFT JOIN schedules s ON s.id = e.schedule_id
        WHERE e.id = '$emp_id'";
$res = mysqli_query($conn, $sql);
$emp = mysqli_fetch_assoc($res);
if (!$emp) die('Employee record not found');

// Get attendance summary
$attendance_sql = "SELECT COUNT(*) AS total_days, SUM(num_hr) AS total_hours 
                   FROM attendance WHERE employee_id = '$emp_id'";
$attendance_res = mysqli_query($conn, $attendance_sql);
$attendance = mysqli_fetch_assoc($attendance_res);

// Get cash advance total
$advance_sql = "SELECT SUM(amount) AS total_advance FROM cashadvance WHERE employee_id = '$emp_id'";
$advance_res = mysqli_query($conn, $advance_sql);
$advance = mysqli_fetch_assoc($advance_res);

// Get overtime total (by employee code)
$overtime_sql = "SELECT SUM(hours) AS total_ot_hours FROM overtime WHERE employee_id = '$employee_code'";
$overtime_res = mysqli_query($conn, $overtime_sql);
$overtime = mysqli_fetch_assoc($overtime_res);

$year = date('Y');
if (isset($_GET['year'])) {
    $year = intval($_GET['year']);
}

// Attendance stats for charts
$total_sql = "SELECT COUNT(*) AS total FROM attendance WHERE employee_id = '$emp_id'";
$total_res = mysqli_query($conn, $total_sql);
$total_attendance = mysqli_fetch_assoc($total_res)['total'] ?? 0;

$ontime_sql = "SELECT COUNT(*) AS ontime FROM attendance WHERE employee_id = '$emp_id' AND status = 1";
$ontime_res = mysqli_query($conn, $ontime_sql);
$ontime_count = mysqli_fetch_assoc($ontime_res)['ontime'] ?? 0;

$late_sql = "SELECT COUNT(*) AS late FROM attendance WHERE employee_id = '$emp_id' AND status = 0";
$late_res = mysqli_query($conn, $late_sql);
$late_count = mysqli_fetch_assoc($late_res)['late'] ?? 0;

$ontime_percent = ($total_attendance > 0) ? ($ontime_count / $total_attendance) * 100 : 0;

// Monthly data for chart
$months = [];
$ontime_data = [];
$late_data = [];
$and_year = "AND YEAR(date) = $year";

for ($m = 1; $m <= 12; $m++) {
    $month_name = date('M', mktime(0, 0, 0, $m, 1));
    $months[] = $month_name;

    $sql_ontime_month = "SELECT COUNT(*) AS count FROM attendance WHERE employee_id = '$emp_id' AND MONTH(date) = $m AND status = 1 $and_year";
    $res_ontime_month = mysqli_query($conn, $sql_ontime_month);
    $ontime_data[] = intval(mysqli_fetch_assoc($res_ontime_month)['count']);

    $sql_late_month = "SELECT COUNT(*) AS count FROM attendance WHERE employee_id = '$emp_id' AND MONTH(date) = $m AND status = 0 $and_year";
    $res_late_month = mysqli_query($conn, $sql_late_month);
    $late_data[] = intval(mysqli_fetch_assoc($res_late_month)['count']);
}

$months_json = json_encode($months);
$ontime_json = json_encode($ontime_data);
$late_json = json_encode($late_data);
$imagePath = 'images/';
$profilePhoto = (!empty($emp['photo']) && file_exists($imagePath . $emp['photo'])) 
    ? $imagePath . htmlspecialchars($emp['photo']) 
    : $imagePath . 'profile.jpg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Employee Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  body { background: #f8f9fa; }
  .navbar {
      background-color: #3c8dbc !important;
      border-color: #3c8dbc !important;
    }

    .navbar .navbar-brand,
    .navbar .nav-link,
    .navbar .navbar-text {
      color: #fff !important;
    }
  .profile-img {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 50%;
    border: 3px solid #3c8dbc;
  }
  .card-header {
    background-color: #3c8dbc;
    color: white;
  }
  .card-body text-center {
    background-color: #222d32;
  }

  .stat-box {
    border-radius: 8px;
    padding: 20px;
    color: white;
    text-align: center;
    font-weight: 600;
  }
  .stat-total { background-color: #17a2b8; }
  .stat-ontime { background-color: #28a745; }
  .stat-late { background-color: #dc3545; }
</style>
</head>
<body>

<nav class="navbar navbar-expand navbar-dark bg-primary">
  <a class="navbar-brand" href="#"> Q U I N C È</a>
  <div class="ml-auto">
    <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
  </div>
</nav>

<div class="container my-4">
  <div class="row">
    <!-- Profile -->
    <div class="col-md-4 mb-4">
      <div class="card shadow-sm">
        <div class="card-header text-center">My Profile</div>
        <div class="card-body text-center">
          <img src="<?php echo !empty($emp['photo']) ? 'images/' . htmlspecialchars($emp['photo']) : 'images/profile.jpg'; ?>" alt="Profile" class="profile-img mb-3" />
          <h4><?php echo htmlspecialchars($emp['firstname'] . ' ' . $emp['lastname']); ?></h4>
          <p class="text-muted"><?php echo htmlspecialchars($emp['position_name']); ?></p>
         <ul class="list-group text-left">
    <li class="list-group-item"><strong>Employee ID:</strong> <?php echo htmlspecialchars($employee_code); ?></li>
    <li class="list-group-item"><strong>Member Since:</strong> <?php echo date('M d, Y', strtotime($emp['created_on'])); ?></li>
    <li class="list-group-item"><strong>Contact:</strong> <?php echo htmlspecialchars($emp['contact_info']); ?></li>
    <li class="list-group-item"><strong>Schedule:</strong> <?php echo htmlspecialchars($emp['sched_in'] . ' - ' . $emp['sched_out']); ?></li>
    <li class="list-group-item"><strong>Hourly Rate:</strong> ₱<?php echo number_format($emp['rate'], 2); ?></li>
</ul>

<div class="mt-3">
    <a href="payslip_print_employee.php" target="_blank" class="btn btn-primary btn-block">Print Payslip</a>
    <a href="schedule_print_employee.php" target="_blank" class="btn btn-secondary btn-block">Print Schedule</a>
</div>
        </div>
      </div>
    </div>

    <!-- Stats & Chart -->
    <div class="col-md-8">
      <div class="row mb-3">
        <div class="col-sm-4">
          <div class="stat-box stat-total">
            <h3><?php echo intval($attendance['total_days'] ?? 0); ?></h3>
            <div>Days Present</div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="stat-box stat-ontime">
            <h3><?php echo number_format($ontime_percent, 2); ?>%</h3>
            <div>On Time</div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="stat-box stat-late">
            <h3><?php echo intval($late_count); ?></h3>
            <div>Late Days</div>
          </div>
        </div>
      </div>

      <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span>Monthly Attendance - <?php echo $year; ?></span>
          <select id="selectYear" class="form-control form-control-sm w-auto">
            <?php
            for ($i = 2015; $i <= 2065; $i++) {
                $selected = ($i == $year) ? 'selected' : '';
                echo "<option value='$i' $selected>$i</option>";
            }
            ?>
          </select>
        </div>
        <div class="card-body">
          <canvas id="attendanceChart" style="height: 300px;"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(function() {
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo $months_json; ?>,
            datasets: [
                {
                    label: 'Late',
                    backgroundColor: '#dc3545',
                    data: <?php echo $late_json; ?>
                },
                {
                    label: 'On Time',
                    backgroundColor: '#28a745',
                    data: <?php echo $ontime_json; ?>
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    $('#selectYear').change(function() {
        const year = $(this).val();
        window.location.href = '?year=' + year;
    });
});
</script>

</body>
</html>
