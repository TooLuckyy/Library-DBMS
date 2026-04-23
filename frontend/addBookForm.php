<?php
session_start();
// Security check to ensure only librarians can access the form
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Book Title</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f3ff; padding: 40px; }
        .form-container { background: white; padding: 30px; border-radius: 8px; max-width: 500px; margin: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #4c1d95; }
        input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn-submit { background-color: #6d28d9; color: white; padding: 12px; border: none; width: 100%; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn-submit:hover { background-color: #5b21b6; }
        .back-link { display: block; margin-top: 15px; text-align: center; color: #6d28d9; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Catalog New Book</h2>
    <form action="../backend/admin/addBook.php" method="POST">
        <div class="form-group">
            <label>Book Title</label>
            <input type="text" name="title" placeholder="e.g. The Great Gatsby" required>
        </div>
        <div class="form-group">
            <label>Author</label>
            <input type="text" name="author" placeholder="e.g. F. Scott Fitzgerald" required>
        </div>
        <div class="form-group">
            <label>ISBN</label>
            <input type="text" name="isbn" placeholder="Unique 13-digit code" required>
        </div>
        <div class="form-group">
            <label>Genre</label>
            <input type="text" name="genre" placeholder="e.g. Fiction" required>
        </div>
        <button type="submit" class="btn-submit">Add Book & Create Copy</button>
    </form>
    <a href="adminDashboard.php" class="back-link">&larr; Back to Dashboard</a>
</div>

</body>
</html>