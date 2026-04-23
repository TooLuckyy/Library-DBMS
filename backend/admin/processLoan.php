<?php
session_start();
require_once "../config/config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian') {
    header("Location: ../../frontend/login.php");
    exit;
}

$loanId = $_GET['id'] ?? null;
$staffId = $_SESSION['user_id'];

if (!$loanId) {
    header("Location: ../../frontend/adminDashboard.php?msg=" . urlencode("Loan ID is required."));
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE loan SET loanStatus = 'active', processedBy = ? WHERE id = ? AND loanStatus = 'pending'");
    $stmt->execute([$staffId, $loanId]);

    if ($stmt->rowCount() === 0) {
        throw new Exception("Loan not found or already processed.");
    }

    header("Location: ../../frontend/adminDashboard.php?msg=" . urlencode("Loan processed successfully."));
    exit;
} catch (Exception $e) {
    header("Location: ../../frontend/adminDashboard.php?msg=" . urlencode("Error: " . $e->getMessage()));
    exit;
}
