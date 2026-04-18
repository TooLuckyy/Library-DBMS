<?php
require_once "config.php";

function getAvalibility($pdo, $copyId) {
    $sql = "SELECT status FROM bookcopy WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$copyId]);

    $status = $stmt->fetch(PDO::FETCH_ASSOC);

    return $status ? $status['status'] : FALSE;
}

function getStudentName($pdo, $studentId) {
    $sql = "SELECT name FROM student WHERE id = ?";
    $stmt =  $pdo->prepare($sql);
    $stmt->execute([$studentId]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['name'] : FALSE;
}

function getLoanHistory($pdo, $studentId) {
    $sql = "SELECT b.title, l.borrowDate, l.dueDate, l.returnDate
            FROM loan l
            JOIN bookcopy bc ON l.bookCopyId = bc.id
            JOIN book b ON bc.bookID = b.id
            WHERE l.studentId = ?
            ORDER BY l.borrowDate DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$studentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getStudentFines($pdo, $studentId) {
    $sql = "SELECT b.title, f.amount, f.status, l.returnDate
            FROM fine f
            JOIN loan l ON f.loanId = l.id
            JOIN bookcopy bc ON l.bookCopyId = bc.id
            JOIN book b ON bc.bookID = b.id
            WHERE l.studentId = ? AND f.status = 'unpaid'";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$studentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}