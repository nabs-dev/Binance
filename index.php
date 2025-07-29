<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crypto Exchange - Home</title>
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
        .market-table {
            width: 100%;
            border-collapse: collapse;
            background: #2a2a4a;
            border-radius: 10px;
            overflow: hidden;
        }
        .market-table th, .market-table td {
            padding: 1rem;
            text-align: left;
        }
        .market-table th {
            background: #3a3a5a;
        }
        .market-table tr:hover {
            background: #3a3a5a;
        }
        @media (max-width: 600px) {
            .navbar {
                flex-direction: column;
            }
            .market-table th, .market-table td {
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
        <h2>Market Prices</h2>
        <table class="market-table">
            <thead>
                <tr>
                    <th>Pair</th>
                    <th>Price (USD)</th>
                    <th>24h Change</th>
                </tr>
            </thead>
            <tbody id="market-data"></tbody>
        </table>
    </div>
    <script>
        async function fetchMarketData() {
            const apiKey = 'CG-okNuw6BnPDRHVKmdyGB2BQP4';
            const url = 'https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&ids=bitcoin,ethereum,binancecoin&order=market_cap_desc&per_page=3&page=1&sparkline=false&price_change_percentage=24h';
            try {
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'x-cg-api-key': apiKey
                    }
                });
                const data = await response.json();
                const tbody = document.getElementById('market-data');
                tbody.innerHTML = '';
                data.forEach(coin => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${coin.symbol.toUpperCase()}/USD</td>
                        <td>$${coin.current_price.toFixed(2)}</td>
                        <td style="color: ${coin.price_change_percentage_24h >= 0 ? '#00cc00' : '#ff4d4d'}">${coin.price_change_percentage_24h.toFixed(2)}%</td>
                    `;
                    tbody.appendChild(row);
                });
            } catch (error) {
                console.error('Error fetching market data:', error);
            }
        }
        fetchMarketData();
        setInterval(fetchMarketData, 60000); // Update every minute
    </script>
</body>
</html>
