<?php
session_start();
require_once "../backend/config/config.php";
require_once "../backend/databaseHelper.php";

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian') {
    header("Location: login.php");
    exit;
}

// 1. Fetch Students for the dropdown
$studentsStmt = $pdo->query("SELECT studentId, name FROM student ORDER BY name ASC");
$students = $studentsStmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Fetch only AVAILABLE book copies
$booksStmt = $pdo->query("
    SELECT bc.id, b.title 
    FROM bookcopy bc 
    JOIN book b ON bc.bookID = b.id 
    WHERE bc.status = 'available' 
    ORDER BY b.title ASC
");
$availableBooks = $booksStmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Set a default due date (14 days from now)
$defaultDueDate = date('Y-m-d', strtotime('+14 days'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Issue New Loan</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 40px; }
        .form-container { background: white; padding: 30px; border-radius: 8px; max-width: 500px; margin: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        select, input { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 14px; }
        .btn-submit { background-color: #28a745; color: white; padding: 12px; border: none; width: 100%; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn-submit:hover { background-color: #218838; }
        .header { text-align: center; margin-bottom: 25px; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #007bff; text-decoration: none; }
    </style>
</head>
<body>

<div class="form-container">
    <div class="header">
        <h2>Issue New Loan</h2>
        <p>Assign a book copy to a student.</p>
    </div>

    <form action="../backend/admin/createloan.php" method="POST">
        
        <div class="form-group">
            <label>Student Name</label>
            <select name="studentId" required>
                <option value="">-- Select a Student --</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?= $student['studentId'] ?>">
                        <?= htmlspecialchars($student['name']) ?> (ID: <?= $student['studentId'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Available Book Copy</label>
            <select name="bookCopyId" required>
                <option value="">-- Select Available Book --</option>
                <?php foreach ($availableBooks as $book): ?>
                    <option value="<?= $book['id'] ?>">
                        #<?= $book['id'] ?> - <?= htmlspecialchars($book['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small style="color: #666;">Only copies currently in stock are listed.</small>
        </div>

        <div class="form-group">
            <label>Return Due Date</label>
            <input type="date" name="dueDate" value="<?= $defaultDueDate ?>" required>
        </div>

        <button type="submit" class="btn-submit">Complete Check-Out</button>
    </form>

    <a href="adminDashboard.php" class="back-link">Return to Dashboard</a>
</div>

</body>
</html>