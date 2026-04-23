<?php
session_start();
require_once "../config/config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian') {
    header("Location: ../../frontend/login.php");
    exit;
}

$loanId = $_GET['id'] ?? null;

if (!$loanId) {
    header("Location: ../../frontend/manageLoans.php?msg=" . urlencode("No loan ID provided."));
    exit;
}

try {
    // Set returned status directly, trigger can still handle copy/fine side effects.
    $stmt = $pdo->prepare("UPDATE loan SET returnDate = NOW(), loanStatus = 'returned' WHERE id = ? AND loanStatus = 'active' AND returnDate IS NULL");
    $stmt->execute([$loanId]);

    if ($stmt->rowCount() === 0) {
        throw new Exception("Loan not found or not eligible for return.");
    }

    header("Location: ../../frontend/manageLoans.php?msg=" . urlencode("Book return processed successfully."));
    exit;

} catch (Exception $e) {
    header("Location: ../../frontend/manageLoans.php?msg=" . urlencode("Error: " . $e->getMessage()));
    exit;
}