<?php
require_once "config.php";

$title = "What will You do";
$author = "Miguel Osen";
$isbn = "619";
$genre = "Thriller";

try {
    $insertSql = "INSERT INTO book(title, author, isbn, genre)
                  VALUES (:title, :author, :isbn, :genre)";
    $insertStmt = $pdo->prepare($insertSql);
    $insertStmt->execute([
        'title' => $title,
        'author' => $author,
        'isbn' => $isbn,
        'genre' => $genre
    ]);

    echo "Successfully added " . $title;

} catch (\PDOException $e) {
    echo "failed to add book." . $e->getMessage();
}
