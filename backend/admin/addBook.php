<?php
require_once "../config/config.php"; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $genre = $_POST['genre'];

    try {
        // Check if book exists
        $checkStmt = $pdo->prepare("SELECT isbn FROM book WHERE isbn = ?");
        $checkStmt->execute([$isbn]);
        
        if ($checkStmt->fetch()) {
            die("Error: This ISBN already exists in the system.");
        }

        // Insert new book
        $sql = "INSERT INTO book (title, author, isbn, genre) VALUES (?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$title, $author, $isbn, $genre]);

        // Automatically create the first copy
        $bookId = $pdo->lastInsertId();
        $pdo->prepare("INSERT INTO bookcopy (bookID, status) VALUES (?, 'available')")
            ->execute([$bookId]);

        header("Location: ../../frontend/adminDashboard.php?success=1");
        exit;
    } catch (PDOException $e) {
        echo "Failed: " . $e->getMessage();
    }
}