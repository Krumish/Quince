<?php
session_start();
include 'admin/includes/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Get and sanitize inputs
    $employee_id = trim($_POST['employee_id']);
    $password = $_POST['password'];

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM employees WHERE employee_id = ?");
    $stmt->bind_param("s", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $employee = $result->fetch_assoc();

        // Verify the password hash stored in the database
        if (password_verify($password, $employee['password'])) {
            // Set session variables on successful login
            $_SESSION['employee_id'] = $employee['employee_id'];
            $_SESSION['id'] = $employee['id'];

            // Redirect to employee dashboard
            header('Location: employee_dashboard.php');
            exit();
        } else {
            // Wrong password
            $_SESSION['login_error'] = "Incorrect password.";
            header('Location: employee_login.php');
            exit();
        }
    } else {
        // Employee ID not found
        $_SESSION['login_error'] = "Employee ID not found.";
        header('Location: employee_login.php');
        exit();
    }
} else {
    // Invalid access, redirect to login page
    header('Location: employee_login.php');
    exit();
}
