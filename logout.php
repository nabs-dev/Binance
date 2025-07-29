<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - Crypto Exchange</title>
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
            text-align: center;
        }
        .container {
            background: #2a2a4a;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        h2 {
            color: #f0b90b;
        }
        a {
            color: #f0b90b;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Logged Out</h2>
        <p>You have been logged out successfully.</p>
        <a href="javascript:window.location.href='login.php'">Login Again</a>
    </div>
    <script>
        setTimeout(() => {
            window.location.href = 'login.php';
        }, 3000);
    </script>
</body>
</html>
