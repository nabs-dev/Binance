<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $currency = $_POST['currency'];
    $amount = $_POST['amount'];
    $type = $_POST['type'];
    $address = bin2hex(random_bytes(16)); // Mock wallet address
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, currency, amount, type) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $currency, $amount, $type]);
    $stmt = $conn->prepare("INSERT INTO wallets (user_id, currency, balance, address) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE balance = balance + ?");
    $stmt->execute([$user_id, $currency, $amount, $address, $amount]);
    echo "<script>alert('Transaction processed'); window.location.href='wallet.php';</script>";
}
$wallets = $conn->query("SELECT * FROM wallets WHERE user_id = $user_id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet - Crypto Exchange</title>
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
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
        }
        input, select {
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
        }
        button:hover {
            background: #d4a00a;
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
            <h2>Wallet</h2>
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
            <h2>Deposit/Withdraw</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="currency">Currency</label>
                    <select id="currency" name="currency">
                        <option value="BTC">BTC</option>
                        <option value="ETH">ETH</option>
                        <option value="BNB">BNB</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" id="amount" name="amount" step="0.00000001" required>
                </div>
                <div class="form-group">
                    <label for="type">Type</label>
                    <select id="type" name="type">
                        <option value="deposit">Deposit</option>
                        <option value="withdrawal">Withdrawal</option>
                    </select>
                </div>
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>
</body>
</html>
