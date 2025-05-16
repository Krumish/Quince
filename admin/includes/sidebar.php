<aside class="main-sidebar">
  <section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="pull-left image">
        <img src="../images/<?php echo $employee['photo']; ?>" class="img-circle" alt="User Image">
      </div>
      <div class="pull-left info">
        <p><?php echo $employee['firstname'].' '.$employee['lastname']; ?></p>
        <a><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>

    <ul class="sidebar-menu" data-widget="tree">
      <li class="header">EMPLOYEE DASHBOARD</li>
      <li><a href="employee_home.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
      <li><a href="attendance.php"><i class="fa fa-calendar"></i> <span>Attendance</span></a></li>
      <li><a href="profile.php"><i class="fa fa-user"></i> <span>Profile</span></a></li>
      <li><a href="logout.php"><i class="fa fa-sign-out"></i> <span>Logout</span></a></li>
    </ul>
  </section>
</aside>
