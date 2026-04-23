<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Login</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: Arial, sans-serif;
            background: linear-gradient(160deg, #2e1065 0%, #6d28d9 55%, #a78bfa 100%);
        }
        .login-card {
            width: min(420px, 92vw);
            background: #ffffff;
            border-radius: 14px;
            padding: 28px;
            box-shadow: 0 12px 35px rgba(20, 10, 40, 0.28);
        }
        h1 { margin: 0 0 8px; color: #4c1d95; }
        p { margin: 0 0 18px; color: #5b6170; }
        label { display: block; margin: 10px 0 6px; color: #3b1f77; font-weight: 700; }
        input, select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d9d0ee;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 14px;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15);
        }
        button {
            margin-top: 16px;
            width: 100%;
            border: none;
            border-radius: 8px;
            background: #6d28d9;
            color: #fff;
            font-weight: 700;
            padding: 11px;
            cursor: pointer;
        }
        button:hover { background: #5b21b6; }
        .hint {
            margin-top: 12px;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>
<body>
    <form class="login-card" action="../backend/loginAction.php" method="POST">
        <h1>Library Login</h1>
        <p>Sign in to access your student or librarian dashboard.</p>

        <label for="email">Email</label>
        <input id="email" type="email" name="email" placeholder="you@example.com" required>

        <label for="password">Password</label>
        <input id="password" type="password" name="password" placeholder="Enter your password" required>

        <label for="role">Role</label>
        <select id="role" name="role" required>
            <option value="student">Student</option>
            <option value="librarian">Librarian</option>
        </select>

        <button type="submit">Sign In</button>
        <div class="hint">Use the role dropdown for the correct account type.</div>
    </form>
</body>
</html>