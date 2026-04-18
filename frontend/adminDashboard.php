<?php
session_start();
require_once "../backend/config/config.php";
require_once "../backend/databaseHelper.php";

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian') {
    header("Location: login.php");
    exit;
}
?>

<div class="button-group">
    <a href="../backend/admin/addBook.php" class="btn">Add New Book</a>
    <a href="../backend/admin/manageBookCopy.php" class="btn">Inventory Management</a>
</div>

<!DOCTYPE html>
<html>
<head>
    <title>Librarian Admin Panel</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Librarian Dashboard</h1>
        <p>Welcome, Admin <?php echo htmlspecialchars($staffName); ?> | <a href="../backend/logout.php">Logout</a></p>
    </header>

    <main>
        <section class="stats-grid">
            <div class="stat-box">
                <h3>Total Books</h3>
                <p><?php echo $totalBooks; ?></p>
            </div>
            <div class="stat-box">
                <h3>Active Loans</h3>
                <p><?php echo $activeLoans; ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Owed Fines</h3>
                <p>$<?php echo number_format($unpaidFines, 2); ?></p>
            </div>
        </section>

        <section class="admin-actions">
            <h2>Management Tools</h2>
            <div class="button-group">
                <a href="addBook.php" class="btn">Add New Book</a>
                <a href="manageLoans.php" class="btn">Process Returns</a>
                <a href="viewStudents.php" class="btn">Student Directory</a>
            </div>
        </section>

        <section class="recent-loans">
            <h2>Recent Loans</h2>
            <table>
                <tr>
                    <th>Student ID</th>
                    <th>Book Copy ID</th>
                    <th>Date Borrowed</th>
                </tr>
                <?php
                // Fetching the last 5 loans from the loan table
                $recent = $pdo->query("SELECT * FROM loan ORDER BY borrowDate DESC LIMIT 5")->fetchAll();
                foreach ($recent as $row) {
                    echo "<tr>
                            <td>{$row['studentId']}</td>
                            <td>{$row['bookCopyId']}</td>
                            <td>{$row['borrowDate']}</td>
                          </tr>";
                }
                ?>
            </table>
        </section>
    </main>
</body>
</html>