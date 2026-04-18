<?php
require_once "config/config.php";

$isbn = '619';

try {
    $deleteSql = "DELETE FROM book WHERE isbn = ?";
    $deleteSql = $pdo->prepare($deleteSql);
    $deleteSql->execute([$isbn]);

    echo "Successfully removed " . $title;

} catch (\PDOException $e) {
    echo "failed to remove book." . $e->getMessage();
}
