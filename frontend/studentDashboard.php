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

$search = trim($_GET['search'] ?? "");
$catalog = [];
$popularBooks = [];
$loanHistory = [];
$outstandingFines = [];
$totalDebt = 0;
$loadError = null;

try {
    $catalog = searchCatalog($pdo, $search);
    $popularBooks = getPopularBooksByMajor($pdo, $studentId);
    $loanHistory = getStudentLoanHistory($pdo, $studentId);
    $outstandingFines = getStudentFines($pdo, $studentId);
    $totalDebt = getStudentTotalFines($pdo, $studentId);
} catch (Exception $e) {
    $loadError = "Unable to load dashboard data right now.";
}

$defaultDueDate = date('Y-m-d', strtotime('+14 days'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f3ff; margin: 0; }
        nav { background: #4c1d95; color: #fff; padding: 14px 24px; display: flex; justify-content: space-between; }
        main { max-width: 1100px; margin: 20px auto; padding: 0 16px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px; }
        .card { background: #fff; padding: 16px; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #ede9fe; color: #3b0764; }
        .msg { background: #e8f5e9; color: #1b5e20; padding: 10px; border-radius: 6px; margin-bottom: 12px; }
        .error { background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; margin-bottom: 12px; }
        .warning { color: #a33; font-weight: bold; }
        .btn { background: #7c3aed; border: none; color: #fff; padding: 8px 12px; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #6d28d9; }
        input, select { width: 100%; padding: 8px; margin: 6px 0; box-sizing: border-box; }
        h2, h3 { color: #4c1d95; }
    </style>
</head>
<body>
    <nav>
        <div><strong>Student Portal</strong> | <?php echo htmlspecialchars($studentName); ?></div>
        <a href="../backend/logout.php" style="color:#fff;">Logout</a>
    </nav>
    <main>
        <?php if (isset($_GET['msg'])): ?>
            <div class="msg"><?php echo htmlspecialchars($_GET['msg']); ?></div>
        <?php endif; ?>
        <?php if ($loadError): ?>
            <div class="error"><?php echo htmlspecialchars($loadError); ?></div>
        <?php endif; ?>

        <div class="card">
            <h2>Account Overview</h2>
            <p>Outstanding fines: <strong>$<?php echo number_format((float)$totalDebt, 2); ?></strong></p>
            <?php if ($totalDebt > 0): ?><p class="warning">Please settle fines to avoid borrowing restrictions.</p><?php endif; ?>
        </div>

        <div class="grid">
            <div class="card">
                <h3>Search Catalog</h3>
                <form method="GET">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Title, author, genre, or ISBN">
                    <button class="btn" type="submit">Search</button>
                </form>
                <table>
                    <tr><th>Book</th><th>Author</th><th>Genre</th><th>Available</th></tr>
                    <?php foreach ($catalog as $book): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <td><?php echo htmlspecialchars($book['genre']); ?></td>
                            <td><?php echo (int)$book['availableCopies']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <div class="card">
                <h3>Popular Books In Your Major</h3>
                <table>
                    <tr><th>Title</th><th>Author</th><th>Borrowed</th></tr>
                    <?php foreach ($popularBooks as $book): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <td><?php echo (int)$book['borrowCount']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <div class="card">
                <h3>Create Loan Request (Pending)</h3>
                <form action="../backend/user/createLoan.php" method="POST">
                    <select name="bookId" required>
                        <option value="">-- Select a Book --</option>
                        <?php foreach ($catalog as $book): ?>
                            <?php if ((int)$book['availableCopies'] > 0): ?>
                                <option value="<?php echo (int)$book['id']; ?>">
                                    <?php echo htmlspecialchars($book['title']); ?> (<?php echo (int)$book['availableCopies']; ?> available)
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <input type="date" name="dueDate" value="<?php echo $defaultDueDate; ?>" required>
                    <button class="btn" type="submit">Create Pending Loan</button>
                </form>
            </div>

            <div class="card">
                <h3>Place Hold</h3>
                <form action="../backend/user/placeHold.php" method="POST">
                    <select name="bookId" required>
                        <option value="">-- Select a Book --</option>
                        <?php foreach ($catalog as $book): ?>
                            <option value="<?php echo (int)$book['id']; ?>">
                                <?php echo htmlspecialchars($book['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn" type="submit">Place Hold</button>
                </form>
            </div>
        </div>

        <div class="grid" style="margin-top:16px;">
            <div class="card">
                <h3>Outstanding Fines</h3>
                <table>
                    <tr><th>Book</th><th>Amount</th><th>Status</th></tr>
                    <?php foreach ($outstandingFines as $fine): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fine['title']); ?></td>
                            <td>$<?php echo number_format((float)$fine['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($fine['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <div class="card">
                <h3>Loan History</h3>
                <table>
                    <tr><th>Title</th><th>Due</th><th>Status</th></tr>
                    <?php foreach ($loanHistory as $loan): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($loan['title']); ?></td>
                            <td><?php echo htmlspecialchars($loan['dueDate']); ?></td>
                            <td><?php echo $loan['returnDate'] ? "Returned" : "On Loan"; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </main>
</body>
</html>