<?php

$host    = 'localhost';
$db      = 'library';
$user    = 'root';
$pass    = '';

$dsn = "mysql:host=$host;dbname=$db";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throws errors so catch can grab them
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Returns data as a clean array
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Uses real prepared statements for security
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // If connection fails sends message and kills program
    die("Database connection failed: " . $e->getMessage());
}