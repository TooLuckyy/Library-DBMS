<?php
require_once 'config.php';
require_once 'databaseHelper.php';

$bookCopyId = 1;
$studentId  = 101;
$dueDate    = date('Y-m-d', strtotime('+14 days'));

try {
    $pdo->beginTransaction();

    //Check availability
    $status = getAvalibility($pdo, $copyId);
    if ($status !== 'available') {
        throw new Exception("Book not available for loan.");
    }

    //Insert Loan
    $loanSql = "INSERT INTO loan (bookCopyId, studentId, dueDate)
                VALUES (:copyId, :studentId, :dueDate)";
    $loanStmt = $pdo->prepare($loanSql);
    $loanStmt->execute([
        ':copyId'    => $bookCopyId,
        ':studentId' => $studentId,
        ':dueDate'   => $dueDate
    ]);

    //Update status
    $updateSql = "UPDATE bookcopy SET status = 'checked_out' WHERE id = ?";
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