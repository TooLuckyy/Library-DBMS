<?php
require_once 'config.php';

try {
    // 1. The JOIN Query grabs the copy ID, the Book Title, and the Status
    $sql = "SELECT 
                bookcopy.id AS copy_id, 
                book.title, 
                bookcopy.status 
            FROM bookcopy 
            JOIN book ON bookcopy.bookID = book.id
            ORDER BY book.title ASC";
            
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
    <title>Library Inventory</title>
    <style>
        /* A little basic styling to make it readable */
        table { border-collapse: collapse; width: 100%; max-width: 800px; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .status-available { color: green; font-weight: bold; }
        .status-checked-out { color: orange; font-weight: bold; }
    </style>
</head>
<body>

    <h2>Library Inventory</h2>

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
                        
                        <td class="<?= $copy['status'] === 'available' ? 'status-available' : 'status-checked-out' ?>">
                            <?= ucfirst(str_replace('_', ' ', htmlspecialchars($copy['status']))) ?>
                        </td>
                        
                        <td>
                            <?php if ($copy['status'] === 'available'): ?>
                                <a href="remove_copy.php?copy_id=<?= $copy['copy_id'] ?>">Remove Copy</a>
                            <?php else: ?>
                                <span style="color: gray;">Cannot Remove (Loaned)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>