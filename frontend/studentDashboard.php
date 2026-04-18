<?php
session_start();
require_once "../backend/config/config.php";
require_once "../backend/databaseHelper.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$studentId = $_SESSION['user_id'];
$studentName = $_SESSION['name'];


$loans = getStudentLoanHistory($pdo, $studentId);
$totalDebt = getStudentTotalFines($pdo, $studentId);
?>

<h1>Welcome back, <?php echo htmlspecialchars($studentName); ?>!</h1>
<div class="status-card">
    <p>Current Account Balance: <strong>$<?php echo number_format($totalDebt, 2); ?></strong></p>
    <?php if ($totalDebt > 0): ?>
        <span class="warning">Please settle your fines to keep borrowing privileges.</span>
    <?php endif; ?>
</div>

<h3>Your Book History</h3>
<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Due Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($loans as $loan): ?>
            <tr>
                <td><?php echo htmlspecialchars($loan['title']); ?></td>
                <td><?php echo $loan['dueDate']; ?></td>
                <td>
                    <?php echo $loan['returnDate'] ? "Returned" : "<strong>On Loan</strong>"; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>