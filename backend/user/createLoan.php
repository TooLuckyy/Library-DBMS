<?php
require_once "../config/config.php";
require_once '../databaseHelper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    

    $bookCopyId = $_POST['bookCopyId'] ?? null;
    $studentId  = $_POST['studentId'] ?? null;
    $dueDate    = $_POST['dueDate'] ?? null;
    $borrowDate = date('Y-m-d'); // Current date

    try {
        if (!$bookCopyId || !$studentId || !$dueDate) {
            throw new Exception("All fields are required.");
        }

        $pdo->beginTransaction();

        $status = getAvalibility($pdo, $bookCopyId);
        
        if ($status === FALSE) {
            throw new Exception("Book copy ID #$bookCopyId does not exist.");
        }
        
        if ($status !== 'available') {
            throw new Exception("This copy is currently $status and cannot be loaned.");
        }

        // 3. Insert the Loan record
        $sql = "INSERT INTO loan (bookCopyId, studentId, borrowDate, dueDate) 
                VALUES (:copyId, :studentId, :borrowDate, :dueDate)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':copyId'    => $bookCopyId,
            ':studentId' => $studentId,
            ':borrowDate'=> $borrowDate,
            ':dueDate'   => $dueDate
        ]);

        $pdo->commit();
        
        // 4. Success Redirect
        header("Location: ../../frontend/adminDashboard.php?msg=Loan+created+successfully");
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // Redirect back with an error message
        header("Location: ../../frontend/adminDashboard.php?msg=" . urlencode("Error: " . $e->getMessage()));
        exit;
    }
} else {
    // If someone tries to access this file directly without POSTing
    header("Location: ../../frontend/adminDashboard.php");
    exit;
}