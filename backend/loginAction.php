<?php
// 1. Enable error reporting to diagnose the 500 error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . "/config/config.php";

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

try {
    // 3. search student or librarian table
    $table = ($role === 'librarian') ? 'librarian' : 'student';
    $idField = ($role === 'librarian') ? 'staffId' : 'studentId';

    // 4. Fetch the user
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // 5. Verify Password
    if ($user && (
        $password === $user['password'] || 
        md5($password) === $user['password'] || 
        password_verify($password, $user['password'])
    )) {
        
        // 6. Set Session Data
        $_SESSION['user_id'] = $user[$idField];
        $_SESSION['role'] = $role;
        $_SESSION['name'] = $user['name'];
        
        // 7. Redirect to the FRONTEND folder
        if ($role === 'librarian') {
            header("Location: ../frontend/adminDashboard.php");
        } else {
            header("Location: ../frontend/studentDashboard.php");
        }
        exit;
    } else {
        echo "Invalid email or password. <a href='../frontend/login.php'>Try again</a>";
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}