<?php
session_start();
require_once "../backend/config/config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian') {
    header("Location: login.php");
    exit;
}

// Fetch all active loans ready for return processing.
$sql = "SELECT 
            l.id AS loan_id, 
            s.name AS student_name, 
            b.title AS book_title, 
            l.borrowDate, 
            l.dueDate 
        FROM loan l
        JOIN student s ON l.studentId = s.studentId
        JOIN bookcopy bc ON l.bookCopyId = bc.id
        JOIN book b ON bc.bookID = b.id
        WHERE l.loanStatus = 'active' AND l.returnDate IS NULL
        ORDER BY l.dueDate ASC";

$loans = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Active Loans</title>
    <style>
        body { font-family: sans-serif; background: #f5f3ff; padding: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #6d28d9; color: white; }
        .overdue { color: #dc3545; font-weight: bold; }
        .btn-return { background: #7c3aed; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; }
        .btn-return:hover { background: #6d28d9; }
    </style>
</head>
<body>
    <a href="adminDashboard.php" style="color:#6d28d9; font-weight:bold;">&larr; Back to Dashboard</a>
    <h2>Active Loans</h2>

    <?php if (isset($_GET['msg'])): ?>
        <p style="color: #4c1d95; font-weight: bold;"><?= htmlspecialchars($_GET['msg']) ?></p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Book Title</th>
                <th>Due Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($loans as $loan): ?>
            <?php 
                $isOverdue = strtotime($loan['dueDate']) < time(); 
            ?>
            <tr>
                <td><?= htmlspecialchars($loan['student_name']) ?></td>
                <td><?= htmlspecialchars($loan['book_title']) ?></td>
                <td class="<?= $isOverdue ? 'overdue' : '' ?>">
                    <?= $loan['dueDate'] ?> <?= $isOverdue ? '(OVERDUE)' : '' ?>
                </td>
                <td>
                    <a href="../backend/admin/processReturn.php?id=<?= $loan['loan_id'] ?>" 
                       class="btn-return" 
                       onclick="return confirm('Confirm return for this book?')">
                       Mark as Returned
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>