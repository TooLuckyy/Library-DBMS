<?php
session_start();
require_once "config/config.php";

if (!isset($_SESSION['user_id'])) {
    // If not logged in, send to login
    header("Location: frontend/login.php");
} else {
    // If logged in, send to their specific dashboard
    $path = ($_SESSION['role'] === 'librarian') ? "frontend/adminDashboard.php" : "frontend/studentDashboard.php";
    header("Location: $path");
}
exit;