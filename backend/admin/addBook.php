<?php
require_once "config/config.php";

$title = "What will You do";
$author = "Miguel Osen";
$isbn = "619";
$genre = "Thriller";

try {
    //check if book already exist
    $checkSql = "SELECT isbn FROM book WHERE isbn = ?";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$isbn]);

    $result = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if($result) {
        throw new Exception("Book already exist.");
    }

    //adds book to book table
    $insertSql = "INSERT INTO book (title, author, isbn, genre) 
                  VALUES(:title, :author, :isbn, :genre)";
    $insertStmt = $pdo->prepare($insertSql);
    $insertStmt->execute([
        'title' => $title, 
        'author' => $author, 
        'isbn' => $isbn, 
        'genre' => $genre]);

    $bookId = $pdo->lastInsertId();
    $copySql = "INSERT INTO bookcopy (bookID, status) VALUES (?, 'available')";
    $pdo->prepare($copySql)->execute([$bookId]);
        
    echo "Successfully added " . $title;

} catch (\PDOException $e) {
    echo "failed to add book." . $e->getMessage();
}
