<?php
require_once "../config/config.php";

try {
    // 2. The JOIN Query grabs the copy ID, the Book Title, and the Status
    $sql = "SELECT 
                bc.id AS copy_id, 
                b.title, 
                bc.status 
            FROM bookcopy bc
            JOIN book b ON bc.bookID = b.id
            ORDER BY b.title ASC";
            
    $stmt = $pdo->query($sql);
    $copies = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Inventory Management</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f5f3ff; }
        table { border-collapse: collapse; width: 100%; max-width: 900px; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #6d28d9; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .status-available { color: #28a745; font-weight: bold; }
        .status-on-loan { color: #fd7e14; font-weight: bold; }
        .btn-remove { color: #dc3545; text-decoration: none; font-weight: bold; }
        .btn-remove:hover { text-decoration: underline; }
        .disabled { color: #6c757d; cursor: not-allowed; }
        .nav-link { display: inline-block; margin-bottom: 20px; color: #6d28d9; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

    <a href="../../frontend/adminDashboard.php" class="nav-link">&larr; Back to Dashboard</a>

    <h2>Library Inventory Management</h2>

    <?php if (isset($_GET['msg'])): ?>
        <p style="color: #4c1d95; border: 1px solid #c4b5fd; padding: 10px; background: #ede9fe;">
            <?= htmlspecialchars($_GET['msg']) ?>
        </p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Copy ID</th>
                <th>Book Title</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($copies)): ?>
                <tr>
                    <td colspan="4">No book copies found in the inventory.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($copies as $copy): ?>
                    <tr>
                        <td>#<?= htmlspecialchars($copy['copy_id']) ?></td>
                        <td><?= htmlspecialchars($copy['title']) ?></td>
                        <td class="<?= $copy['status'] === 'available' ? 'status-available' : 'status-on-loan' ?>">
                            <?= ucfirst(str_replace('_', ' ', htmlspecialchars($copy['status']))) ?>
                        </td>
                        <td>
                            <?php if ($copy['status'] === 'available'): ?>
                                <a href="removeBookCopy.php?id=<?= $copy['copy_id'] ?>" 
                                   class="btn-remove" 
                                   onclick="return confirm('Delete Copy #<?= $copy['copy_id'] ?> (<?= htmlspecialchars($copy['title']) ?>)?')">
                                   Remove Copy
                                </a>
                            <?php else: ?>
                                <span class="disabled">Cannot Remove (On Loan)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>