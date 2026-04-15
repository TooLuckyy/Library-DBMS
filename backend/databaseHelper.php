<?php
require_once "config.php";

function getAvalibility($pdo, $copyId) {
    $sql = "SELECT status FROM bookcopy WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$copyId]);

    $status = $stmt->fetch(PDO::FETCH_ASSOC);

    return $status ? $status['status'] : FALSE;
}

function getStudentName($pdo, $studentId) {
    $sql = "SELECT name FROM student WHERE id = ?";
    $stmt =  $pdo->prepare($sql);
    $stmt->execute([$studentId]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['name'] : FALSE;
}