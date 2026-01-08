<?php
// total of usd in account


$apiKey = '';
$apiSecret = '';

$baseURL = 'https://api.hitbtc.com';

// Get account balance
$balanceURL = $baseURL . '/api/2/trading/balance';

$ch = curl_init($balanceURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Set authorization header
$headers = [
    'Authorization: Basic ' . base64_encode($apiKey . ':' . $apiSecret),
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($httpCode == 200) {
    $data = json_decode($response, true);

    // Extract USDT balance
    $usdtBalance = null;
    foreach ($data as $balance) {
        if ($balance['currency'] === 'USD') {
            $usdtBalance = $balance['available'];
            break;
        }
    }

    if ($usdtBalance !== null) {
        echo ' ' . $usdtBalance;
    } else {
        echo 'USDT balance not found in the account.';
    }
} else {
    echo 'Error: ' . $httpCode . ', ' . $response;
}

?>
