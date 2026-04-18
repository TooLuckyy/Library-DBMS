<?php
session_start();
require_once "../backend/config/config.php";
require_once "../backend/databaseHelper.php";

// Security Check: Only allow Librarians
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian') {
    header("Location: login.php");
    exit;
}

$staffName = $_SESSION['name'];
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
            background: #f4f7f6; 
        }
        nav { 
            background: #333; 
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
            background: #007bff; 
            color: white; 
            padding: 10px 20px; 
            text-decoration: none; 
            border-radius: 5px; 
            margin-top: 15px; 
        }
        .btn:hover {
            background: #0056b3;
        }
        .welcome-header { 
            margin-bottom: 30px; 
        }
        .alert-success {
            background-color: #d4edda; 
            color: #155724; 
            padding: 15px; 
            margin-bottom: 20px; 
            border: 1px solid #c3e6cb; 
            border-radius: 5px; 
            text-align: center;
        }
    </style>
</head>
<body>

    <nav>
        <span><strong>Library Admin</strong> | <?php echo htmlspecialchars($staffName); ?></span>
        <a href="../backend/logout.php" style="color: #ff4d4d; text-decoration: none;">Logout</a>
    </nav>

    <div class="container">
        <div class="welcome-header">
            <h1>Administrative Dashboard</h1>
            <p>Select a management tool below to begin.</p>
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
                    <p>Register a brand new book title and its first copy into the system.</p>
                </div>
                <a href="addBookForm.php" class="btn">Add New Book</a>
            </div>

            <div class="card">
                <div>
                    <h3>Loans & Returns</h3>
                    <p>Process student returns and check the status of active loans.</p>
                </div>
                <a href="manageLoans.php" class="btn">View All Loans</a>
            </div>
        </div>
    </div>

</body>
</html>