<?php
require_once 'config.php';

$bookCopyId = 1;
$studentId  = 101;
$dueDate    = date('Y-m-d', strtotime('+14 days'));

try {
    $pdo->beginTransaction();

    // 1. Check availability
    $checkSql = "SELECT status FROM bookCopy WHERE id = ? FOR UPDATE";
    $stmt = $pdo->prepare($checkSql);
    $stmt->execute([$bookCopyId]); 
    $copy = $stmt->fetch();

    if (!$copy || $copy['status'] !== 'available') {
        throw new Exception("Book not available for loan.");
    }

    // 2. Insert Loan
    $loanSql = "INSERT INTO loan (bookCopyId, studentId, returnDate)
                VALUES (:copyId, :studentId, :dueDate)";
    $loanStmt = $pdo->prepare($loanSql);
    $loanStmt->execute([
        ':copyId'    => $bookCopyId,
        ':studentId' => $studentId,
        ':dueDate'   => $dueDate
    ]);

    // 3. Update status
    $updateSql = "UPDATE bookCopy SET status = 'checked_out' WHERE id = ?";
    $updateStmt = $pdo->prepare($updateSql); 
    $updateStmt->execute([$bookCopyId]);

    $pdo->commit();
    echo "Loan successful. Due date: $dueDate";

} catch (Exception $e) {
    // Check if we are in a transaction before rolling back
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Failed to create loan: " . $e->getMessage();
}