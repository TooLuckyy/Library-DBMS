<?php

require_once 'config.php';

try {
    $sql = "INSERT INTO book (title, author, isbn, genre) 
            VALUES (:title, :author, :isbn, :genre)";
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        ':title'    => 'The Great Gatsby',
        ':author'   => 'F. Scott Fitzgerald',
        ':isbn'     => '9780743273565',
        ':genre' => 'Classic'
    ]);

    echo "Successfully added a book to the library!";

} catch (\PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>