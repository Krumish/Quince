<?php
session_start();
include 'conn.php'; // Update path based on your actual structure

if (!isset($_SESSION['employee']) || trim($_SESSION['employee']) == '') {
    header('location: ../employee_login.php');
    exit();
}

$sql = "SELECT * FROM employees WHERE id = '" . $_SESSION['employee'] . "'";
$query = $conn->query($sql);
$user = $query->fetch_assoc();
?>
