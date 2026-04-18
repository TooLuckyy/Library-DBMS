<?php
session_start();
require_once "config/config.php";

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

try {
    // 1. Determine which table to search
    $table = ($role === 'librarian') ? 'librarian' : 'student';
    $idField = ($role === 'librarian') ? 'staffId' : 'studentId';

    // 2. Fetch the user
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // 3. Verify Password
    if ($user && ($password === $user['password'] || password_verify($password, $user['password']))) {
        
        // 4. Set Session Data
        $_SESSION['user_id'] = $user[$idField];
        $_SESSION['role'] = $role;
        $_SESSION['name'] = $user['name'];
        
        // Redirect based on role
        header("Location: ../frontend/" . ($role === 'librarian' ? "adminDashboard.php" : "studentDashboard.php"));
        exit;
    } else {
        echo "Invalid email or password.";
    }
} catch (PDOException $e) {
    echo "Login error: " . $e->getMessage();
}