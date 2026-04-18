<?php
require_once "../config/config.php";

// 1. Get the ID from the URL
$copyId = $_GET['id'] ?? null;

if (!$copyId) {
    die("Error: No copy ID provided.");
}

try {
    $deleteSql = "DELETE FROM bookcopy WHERE id = ?";
    $deleteStmt = $pdo->prepare($deleteSql);
    $deleteStmt->execute([$copyId]);

    // 3. Redirect back to the inventory 
    header("Location: manageBookCopy.php?msg=Copy+Removed");
    exit;

} catch (\PDOException $e) {
    echo "Failed to remove book copy: " . $e->getMessage();
}