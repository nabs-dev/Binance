<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
$user_id = $_SESSION['user_id'];
$wallets = $conn->query("SELECT * FROM wallets WHERE user_id = $user_id")->fetchAll();
$transactions = $conn->query("SELECT * FROM transactions WHERE user_id = $user_id ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio - Crypto Exchange</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #1e1e2f;
            color: #fff;
            margin: 0;
        }
        .navbar {
            background: #2a2a4a;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: #f0b90b;
            text-decoration: none;
            margin: 0 1rem;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        .container {
            padding: 2rem;
            max-width: 1200px;
            margin: auto;
        }
        .section {
            background: #2a2a4a;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        h2 {
            color: #f0b90b;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 1rem;
            text-align: left;
        }
        .table th {
            background: #3a3a5a;
        }
        .table tr:hover {
            background: #3a3a5a;
        }
        @media (max-width: 600px) {
            .navbar {
                flex-direction: column;
            }
            .table th, .table td {
                font-size: 0.9rem;
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div>
            <a href="javascript:window.location.href='index.php'">Home</a>
            <a href="javascript:window.location.href='trade.php'">Trade</a>
            <a href="javascript:window.location.href='wallet.php'">Wallet</a>
            <a href="javascript:window.location.href='portfolio.php'">Portfolio</a>
        </div>
        <a href="javascript:window.location.href='logout.php'">Logout</a>
    </div>
    <div class="container">
        <div class="section">
            <h2>Portfolio</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Currency</th>
                        <th>Balance</th>
                        <th>Wallet Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($wallets as $wallet): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($wallet['currency']); ?></td>
                            <td><?php echo number_format($wallet['balance'], 8); ?></td>
                            <td><?php echo htmlspecialchars($wallet['address']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="section">
            <h2>Transaction History</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Currency</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $tx): ?>
                        <tr>
                            <td><?php echo $tx['created_at']; ?></td>
                            <td><?php echo ucfirst($tx['type']); ?></td>
                            <td><?php echo htmlspecialchars($tx['currency']); ?></td>
                            <td><?php echo number_format($tx['amount'], 8); ?></td>
                            <td><?php echo ucfirst($tx['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
