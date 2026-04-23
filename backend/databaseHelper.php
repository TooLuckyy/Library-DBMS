<?php
require_once __DIR__ . "/config/config.php";

function getAvalibility($pdo, $copyId) {
    $sql = "SELECT status FROM bookcopy WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$copyId]);

    $status = $stmt->fetch(PDO::FETCH_ASSOC);

    return $status ? $status['status'] : FALSE;
}

function getStudentName($pdo, $studentId) {
    $sql = "SELECT name FROM student WHERE studentId = ?";
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

function getStudentLoanHistory($pdo, $studentId) {
    $sql = "SELECT b.title, l.dueDate, l.returnDate 
            FROM loan l
            JOIN bookcopy bc ON l.bookCopyId = bc.id
            JOIN book b ON bc.bookID = b.id
            WHERE l.studentId = ? 
            ORDER BY l.borrowDate DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$studentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getStudentTotalFines($pdo, $studentId) {
    $sql = "SELECT SUM(f.amount) as total 
            FROM fine f
            JOIN loan l ON f.loanId = l.id
            WHERE l.studentId = ? AND f.status = 'unpaid'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$studentId]);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

function searchCatalog($pdo, $query = "") {
    $sql = "SELECT
                b.id,
                b.title,
                b.author,
                b.genre,
                COALESCE(SUM(CASE WHEN bc.status = 'available' THEN 1 ELSE 0 END), 0) AS availableCopies
            FROM book b
            LEFT JOIN bookcopy bc ON bc.bookID = b.id";

    $params = [];
    if (!empty($query)) {
        $like = "%" . $query . "%";
        $sql .= " WHERE b.title LIKE ? OR b.author LIKE ? OR b.genre LIKE ? OR b.isbn LIKE ?";
        $params = [$like, $like, $like, $like];
    }

    // Group all selected non-aggregated columns for strict SQL mode compatibility.
    $sql .= " GROUP BY b.id, b.title, b.author, b.genre ORDER BY b.title ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPopularBooksByMajor($pdo, $studentId) {
    $sql = "SELECT b.title, b.author, COUNT(l.id) AS borrowCount
            FROM loan l
            JOIN student s ON l.studentId = s.studentId
            JOIN bookcopy bc ON l.bookCopyId = bc.id
            JOIN book b ON bc.bookID = b.id
            WHERE s.major = (SELECT major FROM student WHERE studentId = :studentId)
              AND l.loanStatus IN ('active', 'returned')
            GROUP BY b.id, b.title, b.author
            ORDER BY borrowCount DESC, b.title ASC
            LIMIT 5";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':studentId' => $studentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createPendingLoanForStudent($pdo, $studentId, $bookId, $dueDate) {
    $pdo->beginTransaction();
    try {
        $totalUnpaidFines = (float) getStudentTotalFines($pdo, $studentId);
        if ($totalUnpaidFines >= 10.00) {
            throw new Exception("Loan restricted: outstanding fines are $" . number_format($totalUnpaidFines, 2) . ". Please pay fines below $10.00 to borrow.");
        }

        $copyStmt = $pdo->prepare("SELECT id FROM bookcopy WHERE bookID = ? AND status = 'available' ORDER BY id ASC LIMIT 1");
        $copyStmt->execute([$bookId]);
        $copy = $copyStmt->fetch(PDO::FETCH_ASSOC);

        if (!$copy) {
            throw new Exception("No available copy for the selected book.");
        }

        $loanStmt = $pdo->prepare("INSERT INTO loan (bookCopyId, studentId, dueDate, loanStatus) VALUES (?, ?, ?, 'pending')");
        $loanStmt->execute([$copy['id'], $studentId, $dueDate]);

        $pdo->commit();
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

function placeHoldForStudent($pdo, $studentId, $bookId) {
    $checkStmt = $pdo->prepare("SELECT id FROM hold WHERE studentId = ? AND bookId = ? AND status IN ('waiting', 'notified')");
    $checkStmt->execute([$studentId, $bookId]);
    if ($checkStmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("You already have an active hold on this book.");
    }

    $stmt = $pdo->prepare("INSERT INTO hold (studentId, bookId, status) VALUES (?, ?, 'waiting')");
    $stmt->execute([$studentId, $bookId]);
}

function getPendingLoans($pdo) {
    $sql = "SELECT
                l.id,
                s.name AS studentName,
                s.studentId,
                b.title AS bookTitle,
                l.borrowDate,
                l.dueDate
            FROM loan l
            JOIN student s ON s.studentId = l.studentId
            JOIN bookcopy bc ON bc.id = l.bookCopyId
            JOIN book b ON b.id = bc.bookID
            WHERE l.loanStatus = 'pending'
            ORDER BY l.borrowDate ASC";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

function getActiveLoans($pdo) {
    $sql = "SELECT
                l.id,
                s.name AS studentName,
                b.title AS bookTitle,
                l.borrowDate,
                l.dueDate
            FROM loan l
            JOIN student s ON s.studentId = l.studentId
            JOIN bookcopy bc ON bc.id = l.bookCopyId
            JOIN book b ON b.id = bc.bookID
            WHERE l.loanStatus = 'active'
            ORDER BY l.dueDate ASC";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

function getAllUnpaidFines($pdo) {
    $sql = "SELECT
                f.fineId,
                s.name AS studentName,
                b.title AS bookTitle,
                f.amount,
                f.status
            FROM fine f
            JOIN loan l ON l.id = f.loanId
            JOIN student s ON s.studentId = l.studentId
            JOIN bookcopy bc ON bc.id = l.bookCopyId
            JOIN book b ON b.id = bc.bookID
            WHERE f.status = 'unpaid'
            ORDER BY f.amount DESC";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}