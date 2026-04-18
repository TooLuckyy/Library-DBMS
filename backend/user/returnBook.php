<?php
require_once "config.php";

$bookCopyId = 1;

try {
    //check if book not checked out
    $status = getAvalibility($pdo, $bookCopyId);
    if ($status === FALSE){
        throw new Exeption("Erorr: Book Copy Does Not Exits");
    }

    if ($status === 'available') {
        throw new Exception("Book already avalible.");
    }

    //Update loan
    $pdo->beginTransaction();

    
    
} catch (\PDOException $e) {
    echo "Failed to return book";
}