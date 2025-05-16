<?php
session_start();
if (isset($_SESSION['employee_id'])) {
    header("Location: employee_home.php"); // Redirect to home if already logged in
    exit();
}
// Include database connection
include('admin/includes/conn.php');

// Get employee ID from session
$employee_id = $_SESSION['employee_id'];

// Fetch employee data from the database
$sql_employee = "SELECT * FROM employees WHERE employee_id = '$employee_id'";
$result_employee = mysqli_query($conn, $sql_employee);
$employee = mysqli_fetch_assoc($result_employee);

// Fetch schedule for the employee
$sql_schedule = "SELECT * FROM schedules WHERE employee_id = '$employee_id'";
$result_schedule = mysqli_query($conn, $sql_schedule);
$schedule = mysqli_fetch_assoc($result_schedule);

// Fetch recent attendance for the employee
$sql_attendance = "SELECT * FROM attendance WHERE employee_id = '$employee_id' ORDER BY date DESC LIMIT 5";
$attendance_result = mysqli_query($conn, $sql_attendance);


?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>Employee Dashboard</h1>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-4">
        <!-- Employee Profile -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Profile</h3>
          </div>
          <div class="box-body">
            <img src="../images/<?php echo $employee['photo']; ?>" class="img-circle" alt="User Image" style="width: 100px; height: 100px;">
            <p>Name: <?php echo $employee['firstname'] . ' ' . $employee['lastname']; ?></p>
            <p>Position: <?php echo $employee['position_id']; ?></p>
            <p>Schedule: <?php echo $schedule['time_in'] . ' - ' . $schedule['time_out']; ?></p>
          </div>
        </div>
      </div>

      <div class="col-md-8">
        <!-- Recent Attendance -->
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title">Recent Attendance</h3>
          </div>
          <div class="box-body">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Time In</th>
                  <th>Time Out</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($attendance = mysqli_fetch_assoc($attendance_result)) { ?>
                <tr>
                  <td><?php echo $attendance['date']; ?></td>
                  <td><?php echo $attendance['status'] == 1 ? 'Present' : 'Absent'; ?></td>
                  <td><?php echo $attendance['time_in']; ?></td>
                  <td><?php echo $attendance['time_out']; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Employee Overtime (if applicable) -->
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">Overtime</h3>
          </div>
          <div class="box-body">
            <p>No overtime recorded yet.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php
include('includes/footer.php'); // Include footer
?>
