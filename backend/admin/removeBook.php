<?php
require_once "../config/config.php";

$isbn = $_GET['isbn'] ?? null;

if (!$isbn) {
    die("Error: No ISBN provided.");
}

try {
    $pdo->beginTransaction();

    // 1. Find the Book ID
    $stmt = $pdo->prepare("SELECT id FROM book WHERE isbn = ?");
    $stmt->execute([$isbn]);
    $book = $stmt->fetch();

    if ($book) {
        // 2. Remove all copies
        $pdo->prepare("DELETE FROM bookcopy WHERE bookID = ?")->execute([$book['id']]);
        
        // 3. Remove the main book
        $pdo->prepare("DELETE FROM book WHERE isbn = ?")->execute([$isbn]);
    }

    $pdo->commit();
    header("Location: ../../frontend/adminDashboard.php?msg=Book+Deleted");
    exit;

} catch (\PDOException $e) {
    $pdo->rollBack();
    echo "Failed to remove book: " . $e->getMessage();
}