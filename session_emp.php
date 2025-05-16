<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'conn.php';  // This should define $conn (adjust path if needed)

if (!isset($_SESSION['employee']) || trim($_SESSION['employee']) == '') {
    header('location: ../index.php');
    exit();
}
?>
