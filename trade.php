<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

// Verify user exists in database
$user_id = $_SESSION['user_id'];
try {
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    if (!$stmt->fetch()) {
        session_destroy();
        echo "<script>window.location.href='login.php';</script>";
        exit;
    }
} catch (PDOException $e) {
    file_put_contents('error.log', date('Y-m-d H:i:s') . ' - User validation error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pair = $_POST['pair'];
        $type = $_POST['type'];
        $side = $_POST['side'];
        $amount = floatval($_POST['amount']);
        $price = isset($_POST['price']) && $_POST['price'] !== '' ? floatval($_POST['price']) : null;

        // Validate inputs
        if ($amount <= 0) {
            throw new Exception('Amount must be greater than 0');
        }
        if ($type !== 'market' && $price === null) {
            throw new Exception('Price is required for limit and stop-loss orders');
        }

        $stmt = $conn->prepare("INSERT INTO orders (user_id, pair, type, side, amount, price) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $pair, $type, $side, $amount, $price]);
        echo "<script>alert('Order placed successfully'); window.location.href='trade.php';</script>";
    } catch (Exception $e) {
        // Log error to file
        file_put_contents('error.log', date('Y-m-d H:i:s') . ' - Order placement error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
        echo "<script>alert('Error placing order: " . htmlspecialchars($e->getMessage()) . "'); window.location.href='trade.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade - Crypto Exchange</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            display: flex;
            gap: 2rem;
        }
        .chart-section, .order-section {
            background: #2a2a4a;
            padding: 1.5rem;
            border-radius: 10px;
            flex: 1;
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
        canvas {
            max-width: 100%;
        }
        @media (max-width: 800px) {
            .container {
                flex-direction: column;
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
        <div class="chart-section">
            <h2>Price Chart</h2>
            <canvas id="priceChart"></canvas>
        </div>
        <div class="order-section">
            <h2>Place Order</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="pair">Trading Pair</label>
                    <select id="pair" name="pair">
                        <option value="BTC/USD">BTC/USD</option>
                        <option value="ETH/USD">ETH/USD</option>
                        <option value="BNB/USD">BNB/USD</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="type">Order Type</label>
                    <select id="type" name="type">
                        <option value="market">Market</option>
                        <option value="limit">Limit</option>
                        <option value="stop_loss">Stop-Loss</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="side">Side</label>
                    <select id="side" name="side">
                        <option value="buy">Buy</option>
                        <option value="sell">Sell</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" id="amount" name="amount" step="0.00000001" min="0.00000001" required>
                </div>
                <div class="form-group">
                    <label for="price">Price (USD, optional for Market)</label>
                    <input type="number" id="price" name="price" step="0.01" min="0">
                </div>
                <button type="submit">Place Order</button>
            </form>
        </div>
    </div>
    <script>
        const ctx = document.getElementById('priceChart').getContext('2d');
        const priceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'BTC/USD Price',
                    data: [],
                    borderColor: '#f0b90b',
                    fill: false
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });

        async function fetchChartData() {
            const apiKey = 'CG-okNuw6BnPDRHVKmdyGB2BQP4';
            const url = 'https://api.coingecko.com/api/v3/coins/bitcoin/market_chart?vs_currency=usd&days=1&interval=hourly';
            try {
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'x-cg-api-key': apiKey
                    }
                });
                const data = await response.json();
                const prices = data.prices.slice(-5); // Last 5 hours
                priceChart.data.labels = prices.map(p => new Date(p[0]).toLocaleTimeString());
                priceChart.data.datasets[0].data = prices.map(p => p[1]);
                priceChart.update();
            } catch (error) {
                console.error('Error fetching chart data:', error);
            }
        }
        fetchChartData();
        setInterval(fetchChartData, 60000); // Update every minute
    </script>
</body>
</html>
