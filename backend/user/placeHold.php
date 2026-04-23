<?php
session_start();
require_once "../config/config.php";
require_once "../databaseHelper.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../../frontend/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/studentDashboard.php");
    exit;
}

$studentId = $_SESSION['user_id'];
$bookId = $_POST['bookId'] ?? null;

try {
    if (!$bookId) {
        throw new Exception("Book is required.");
    }

    placeHoldForStudent($pdo, $studentId, $bookId);
    header("Location: ../../frontend/studentDashboard.php?msg=" . urlencode("Hold placed successfully."));
    exit;
} catch (Exception $e) {
    header("Location: ../../frontend/studentDashboard.php?msg=" . urlencode("Error: " . $e->getMessage()));
    exit;
}
