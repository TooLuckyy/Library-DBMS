<?php
session_start();
require_once "../backend/config/config.php";
require_once "../backend/databaseHelper.php";

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Security Check: Only allow Librarians
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian') {
    header("Location: login.php");
    exit;
}

$staffName = $_SESSION['name'];
$pendingLoans = getPendingLoans($pdo);
$activeLoans = getActiveLoans($pdo);
$unpaidFines = getAllUnpaidFines($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Librarian Admin Panel</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            background: #f5f3ff; 
        }
        nav { 
            background: #4c1d95; 
            color: #fff; 
            padding: 1rem; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .container { 
            padding: 2rem; 
            max-width: 1000px; 
            margin: auto; 
        }
        .grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 20px; 
            margin-top: 20px; 
        }
        .card { 
            background: white; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
            text-align: center; 
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .btn { 
            display: inline-block; 
            background: #7c3aed; 
            color: white; 
            padding: 10px 20px; 
            text-decoration: none; 
            border-radius: 5px; 
            margin-top: 15px; 
        }
        .btn:hover {
            background: #6d28d9;
        }
        .welcome-header { 
            margin-bottom: 30px; 
        }
        .alert-success {
            background-color: #ede9fe; 
            color: #4c1d95; 
            padding: 15px; 
            margin-bottom: 20px; 
            border: 1px solid #c4b5fd; 
            border-radius: 5px; 
            text-align: center;
        }
    </style>
</head>
<body>

    <nav>
        <span><strong>Library Admin</strong> | <?php echo htmlspecialchars($staffName); ?></span>
        <a href="../backend/logout.php" style="color: #f5d0fe; text-decoration: none;">Logout</a>
    </nav>

    <div class="container">
        <div class="welcome-header">
            <h1>Administrative Dashboard</h1>
            <p>Manage pending loans, active checkouts, returns, fines, and inventory.</p>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert-success">
                <strong>Notice:</strong> <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>

        <div class="grid">
            <div class="card">
                <div>
                    <h3>Book Inventory</h3>
                    <p>View, update, or remove specific book copies from the library.</p>
                </div>
                <a href="../backend/admin/manageBookCopy.php" class="btn">Manage Copies</a>
            </div>

            <div class="card">
                <div>
                    <h3>Catalog New Titles</h3>
                    <p>Register a new title in the catalog.</p>
                </div>
                <a href="addBookForm.php" class="btn">Add New Book</a>
            </div>

            <div class="card">
                <div>
                    <h3>Process Returns</h3>
                    <p>View active loans and mark check-ins.</p>
                </div>
                <a href="manageLoans.php" class="btn">Open Returns</a>
            </div>
        </div>

        <h2 style="margin-top: 30px;">Pending Loans (Pickup Queue)</h2>
        <table style="width:100%; border-collapse: collapse; background: #fff;">
            <tr>
                <th style="padding: 10px; border:1px solid #ddd; background:#ede9fe; color:#3b0764;">Loan ID</th>
                <th style="padding: 10px; border:1px solid #ddd; background:#ede9fe; color:#3b0764;">Student</th>
                <th style="padding: 10px; border:1px solid #ddd; background:#ede9fe; color:#3b0764;">Book</th>
                <th style="padding: 10px; border:1px solid #ddd; background:#ede9fe; color:#3b0764;">Requested</th>
                <th style="padding: 10px; border:1px solid #ddd; background:#ede9fe; color:#3b0764;">Action</th>
            </tr>
            <?php foreach ($pendingLoans as $loan): ?>
                <tr>
                    <td style="padding: 10px; border:1px solid #ddd;">#<?php echo (int)$loan['id']; ?></td>
                    <td style="padding: 10px; border:1px solid #ddd;"><?php echo htmlspecialchars($loan['studentName']); ?></td>
                    <td style="padding: 10px; border:1px solid #ddd;"><?php echo htmlspecialchars($loan['bookTitle']); ?></td>
                    <td style="padding: 10px; border:1px solid #ddd;"><?php echo htmlspecialchars($loan['borrowDate']); ?></td>
                    <td style="padding: 10px; border:1px solid #ddd;">
                        <a class="btn" href="../backend/admin/processLoan.php?id=<?php echo (int)$loan['id']; ?>">Process Loan</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h2 style="margin-top: 30px;">Active Loans</h2>
        <table style="width:100%; border-collapse: collapse; background: #fff;">
            <tr>
                <th style="padding: 10px; border:1px solid #ddd; background:#ede9fe; color:#3b0764;">Loan ID</th>
                <th style="padding: 10px; border:1px solid #ddd; background:#ede9fe; color:#3b0764;">Student</th>
                <th style="padding: 10px; border:1px solid #ddd; background:#ede9fe; color:#3b0764;">Book</th>
                <th style="padding: 10px; border:1px solid #ddd; background:#ede9fe; color:#3b0764;">Due Date</th>
            </tr>
            <?php foreach ($activeLoans as $loan): ?>
                <tr>
                    <td style="padding: 10px; border:1px solid #ddd;">#<?php echo (int)$loan['id']; ?></td>
                    <td style="padding: 10px; border:1px solid #ddd;"><?php echo htmlspecialchars($loan['studentName']); ?></td>
                    <td style="padding: 10px; border:1px solid #ddd;"><?php echo htmlspecialchars($loan['bookTitle']); ?></td>
                    <td style="padding: 10px; border:1px solid #ddd;"><?php echo htmlspecialchars($loan['dueDate']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h2 style="margin-top: 30px;">All Unpaid Fines</h2>
        <table style="width:100%; border-collapse: collapse; background: #fff;">
            <tr>
                <th style="padding: 10px; border:1px solid #ddd; background:#ede9fe; color:#3b0764;">Fine ID</th>
                <th style="padding: 10px; border:1px solid #ddd; background:#ede9fe; color:#3b0764;">Student</th>
                <th style="padding: 10px; border:1px solid #ddd; background:#ede9fe; color:#3b0764;">Book</th>
                <th style="padding: 10px; border:1px solid #ddd; background:#ede9fe; color:#3b0764;">Amount</th>
                <th style="padding: 10px; border:1px solid #ddd; background:#ede9fe; color:#3b0764;">Action</th>
            </tr>
            <?php foreach ($unpaidFines as $fine): ?>
                <tr>
                    <td style="padding: 10px; border:1px solid #ddd;">#<?php echo (int)$fine['fineId']; ?></td>
                    <td style="padding: 10px; border:1px solid #ddd;"><?php echo htmlspecialchars($fine['studentName']); ?></td>
                    <td style="padding: 10px; border:1px solid #ddd;"><?php echo htmlspecialchars($fine['bookTitle']); ?></td>
                    <td style="padding: 10px; border:1px solid #ddd;">$<?php echo number_format((float)$fine['amount'], 2); ?></td>
                    <td style="padding: 10px; border:1px solid #ddd;">
                        <form action="../backend/admin/markFinePaid.php" method="POST" style="margin:0;">
                            <input type="hidden" name="fineId" value="<?php echo (int)$fine['fineId']; ?>">
                            <button class="btn" type="submit" style="margin-top:0;">Mark Paid</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

</body>
</html>