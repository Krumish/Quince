<?php 
session_start();

include 'admin/includes/conn.php';

if (isset($_SESSION['employee_id']) && isset($_SESSION['id'])) {
    header('Location: employee_dashboard.php');
    exit();
}
?>


<?php include 'header.php'; ?>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <b>Employee</b> Login
  </div>
  
  <div class="login-box-body">
    <p class="login-box-msg">Sign in to start your session</p>

    <?php
 if (isset($_SESSION['login_error'])) {
    echo "<div class='alert alert-danger text-center'>" . $_SESSION['login_error'] . "</div>";
    unset($_SESSION['login_error']);
}
  ?>

    <form action="employee_login_process.php" method="POST">
      <div class="form-group has-feedback">
<input type="text" class="form-control" name="employee_id" placeholder="Employee ID" required>
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" name="password" placeholder="Password" required>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <!-- Optional: Remember me checkbox -->
        </div>
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat" name="login">Login</button>
        </div>
      </div>
    </form>

    <br>
    <a href="index.php"><i class="fa fa-arrow-left"></i> Back to Main Page</a>

  </div>
</div>

<?php include 'scripts.php' ?>
<script type="text/javascript">
$('#employeeLoginBtn').click(function() {
  window.location.href = 'employee_login.php';
    
});
</script>
</body>
</html>
