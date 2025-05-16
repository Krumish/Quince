<?php
session_start();

// Include database connection file
include('admin/includes/conn.php');

// Check if the form is submitted
if (isset($_POST['login'])) {
    $employee_id = $_POST['employee_id'];
    $password = $_POST['password'];

    // Sanitize inputs
    $employee_id = mysqli_real_escape_string($conn, $employee_id);
    $password = mysqli_real_escape_string($conn, $password);

    // Fetch employee data from the database
    $sql = "SELECT * FROM employees WHERE employee_id = '$employee_id'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $employee = mysqli_fetch_assoc($result);

        // Verify the password
        if (password_verify($password, $employee['password'])) {
            // Correct credentials, create session
            $_SESSION['employee_id'] = $employee['employee_id'];
            $_SESSION['employee_name'] = $employee['firstname'] . ' ' . $employee['lastname'];

            // Redirect to the employee home page
            header("Location: employee_home.php");
            exit();
        } else {
            // Incorrect password
            $_SESSION['login_error'] = "Invalid password. Please try again.";
            header("Location: employee_login.php");
            exit();
        }
    } else {
        // Employee not found
        $_SESSION['login_error'] = "No employee found with that ID.";
        header("Location: employee_login.php");
        exit();
    }
}
?>
