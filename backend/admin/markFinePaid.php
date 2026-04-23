<?php
session_start();
require_once "../config/config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian') {
    header("Location: ../../frontend/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../frontend/adminDashboard.php");
    exit;
}

$fineId = $_POST['fineId'] ?? null;

try {
    if (!$fineId || !is_numeric($fineId)) {
        throw new Exception("Invalid fine ID.");
    }

    $stmt = $pdo->prepare("UPDATE fine SET status = 'paid' WHERE fineId = ? AND status = 'unpaid'");
    $stmt->execute([$fineId]);

    if ($stmt->rowCount() === 0) {
        throw new Exception("Fine not found or already paid.");
    }

    header("Location: ../../frontend/adminDashboard.php?msg=" . urlencode("Fine marked as paid."));
    exit;
} catch (Exception $e) {
    header("Location: ../../frontend/adminDashboard.php?msg=" . urlencode("Error: " . $e->getMessage()));
    exit;
}
