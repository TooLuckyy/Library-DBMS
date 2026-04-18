<?php
require_once "../config/config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $genre = $_POST['genre'];

    try {
        // 1. Check if ISBN exists
        $checkStmt = $pdo->prepare("SELECT isbn FROM book WHERE isbn = ?");
        $checkStmt->execute([$isbn]);

        if ($checkStmt->fetch()) {
             die("Error: A book with this ISBN already exists. <a href='../../frontend/addBookForm.php'>Go back</a>");
        }

        // 2. Insert
        $insertSql = "INSERT INTO book (title, author, isbn, genre) VALUES (?, ?, ?, ?)";
        $pdo->prepare($insertSql)->execute([$title, $author, $isbn, $genre]);

        // 3. Create the first physical copy
        $bookId = $pdo->lastInsertId();
        $pdo->prepare("INSERT INTO bookcopy (bookID, status) VALUES (?, 'available')")->execute([$bookId]);
        
        // 4. Redirect back to dashboard
        header("Location: ../../frontend/adminDashboard.php?msg=Book+Added+Successfully");
        exit;

    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
}