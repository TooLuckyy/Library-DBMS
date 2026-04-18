<form action="loginAction.php" method="POST">
    <h2>Library Login</h2>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    
    <select name="role">
        <option value="student">Student</option>
        <option value="librarian">Librarian</option>
    </select>
    
    <button type="submit">Login</button>
</form>