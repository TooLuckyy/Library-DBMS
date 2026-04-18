<?php
require_once "config.php";

$copyId = '1';

try {
    $deleteSql = "DELETE FROM bookCopy WHERE id = ?";
    $deleteStmt = $pdo->prepare($deleteSql);
    $deleteStmt->execute([$copyId]);

    echo "Successfully removed #$copyId book copy";

} catch (\PDOException $e) {
    echo "failed to remove book." . $e->getMessage();
}
