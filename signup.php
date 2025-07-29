<?php
require 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    try {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password]);
        echo "<script>window.location.href='login.php';</script>";
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Crypto Exchange</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1e1e2f 0%, #2a2a4a 100%);
            color: #fff;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #2a2a4a;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            color: #f0b90b;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
        }
        input {
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 5px;
            background: #3a3a5a;
            color: #fff;
        }
        button {
            width: 100%;
            padding: 0.75rem;
            background: #f0b90b;
            border: none;
            border-radius: 5px;
            color: #1e1e2f;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #d4a00a;
        }
        .error {
            color: #ff4d4d;
            text-align: center;
        }
        a {
            color: #f0b90b;
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 1rem;
        }
        @media (max-width: 600px) {
            .container {
                margin: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Sign Up</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Sign Up</button>
        </form>
        <a href="javascript:window.location.href='login.php'">Already have an account? Log In</a>
    </div>
</body>
</html>
