<?php
require_once "../config/config.php";

$loanId = $_GET['id'] ?? null;

if (!$loanId) {
    die("No loan ID provided.");
}

try {
    $pdo->beginTransaction();

    // 1. Fetch loan details to check for fines
    $stmt = $pdo->prepare("SELECT bookCopyId, dueDate FROM loan WHERE id = ?");
    $stmt->execute([$loanId]);
    $loan = $stmt->fetch();

    if (!$loan) throw new Exception("Loan not found.");

    // 2. Update the loan with today's date
    $today = date('Y-m-d');
    $updateLoan = $pdo->prepare("UPDATE loan SET returnDate = ? WHERE id = ?");
    $updateLoan->execute([$today, $loanId]);

    // 3. Set the book copy back to 'available'
    $updateCopy = $pdo->prepare("UPDATE bookcopy SET status = 'available' WHERE id = ?");
    $updateCopy->execute([$loan['bookCopyId']]);

    // 4. Fine Calculation
    //     $dueDate = strtotime($loan['dueDate']);
    $returnDate = strtotime($today);

    if ($returnDate > $dueDate) {
        $daysLate = floor(($returnDate - $dueDate) / (60 * 60 * 24));
        $fineAmount = $daysLate * 0.50;

        $insertFine = $pdo->prepare("INSERT INTO fine (loanId, amount, status) VALUES (?, ?, 'unpaid')");
        $insertFine->execute([$loanId, $fineAmount]);
        $msg = "Book returned. Late fee of $$fineAmount applied.";
    } else {
        $msg = "Book returned successfully. No fines.";
    }

    $pdo->commit();
    header("Location: ../../frontend/manageLoans.php?msg=" . urlencode($msg));
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error processing return: " . $e->getMessage();
}