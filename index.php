<?php

// HitBTC Trading Bot Version 3
// Started in 01/08/2026
// Made By DE https://demile.de
// This trading bot is builT using HitBTC Public API
// API KEY AND SECRET KEY FROM HITBTC ACCOUNT IS REQUIRED

// API CREDENTIALS

$apiKey = '';
$apiSecret = '';

// Coins you want to trade

$symbol = "SHIBUSDT";
$quantity = 3827000;

// Using HitBTC API to get asking price

$url = 'https://api.hitbtc.com/api/3/public/ticker/' . $symbol;
$response = file_get_contents($url);

$data = json_decode($response, true);

$ask = $data['ask'];

// Variables - Calculate Percent to Buy and Sell Crypto Currency
// 2% = 0.02, 5% = 0.05

$ask_price = $ask;

$buy_percent = 0.02; // bot will buy at 0.02 = %2
$sell_percent = 0.02; // bot will sell at %2
$dont_waste_money_percent = 0.9; //if the price doesn't go up and it is going bellow  percent of bought price // I don't get it anymore -------- not use and change 7/19/2024

// Read and Display content in user_bot_paid.txt
function read_user_bot_paid_txt() { // function to read text in flipping.txt
    // File path
    $file = 'user_bot_paid.txt';

    // Open the file in read mode
    $fileHandle = fopen($file, 'r') or die("Unable to open file!");

    // Read the content of the file
    $text = fread($fileHandle, filesize($file));

    // Close the file handle
    fclose($fileHandle);

    return $text;
}

// Counter Function to count every trade

function counter()
{
    // The name of the text file
    $filename = "counter.txt";

    // Read the content of the text file
    if (file_exists($filename)) {
        $counter = (int) file_get_contents($filename);
    } else {
        $counter = 0;
    }

    // Increment the counter by 1
    $counter++;

    // Write the new counter value to the text file
    file_put_contents($filename, $counter);

    // Display the new counter value
    echo "<br><br>";
    echo "Page refreshed " . $counter . " times.";
}


// Read only flipping.txt content  $user_bot_paidx = $flipping_text2
echo "User Bot Paid<br>";
$user_bot_paidx = read_user_bot_paid_txt();
echo "<br>";
echo $user_bot_paidx;
echo "<br>";

$total_buy_percent = floatval($user_bot_paidx - ($user_bot_paidx * $buy_percent));
$total_sell_percent = floatval($user_bot_paidx + ($user_bot_paidx * $sell_percent));

$total_dont_waste_money = floatval($user_bot_paidx - ($user_bot_paidx * $dont_waste_money_percent));//floatval($user_bot_paidx - ($user_bot_paidx * $dont_waste_money)); // -

$display_buy_percent = number_format($total_buy_percent, 10, '.', '');
$display_sell_percent = number_format($total_sell_percent, 10, '.', '');

//$savemoneyx = $user_bot_paidx - ($user_bot_paidx*$dont_waste_money); // new function added 7/19/2024
$display_dont_waste_money = number_format($total_dont_waste_money, 10, '.', '');

echo "BUY: ";
echo $display_buy_percent;
echo "<br>";
echo "SELL: ";
echo $display_sell_percent;
echo "<br>";
echo "SAVE: ";
echo $display_dont_waste_money;
//echo "<br> new don't waste money: ";
//echo $user_bot_paidx;


// Write sell or buy on a text file flipping.txt
// flipping strategy will write sell after selling your coins
// or buy after buying new coins to sell
// This flipping strategy will make sure that the bot stop buying
// after it just bought coins. It must wait now to sell vis versa

function write_sell_buy_flipping_txt($text) {
	// File path
	$file = 'flipping.txt';
	
	// Open the file in write mode
	$fileHandle = fopen($file, 'w') or die("Unable to open file!");

	// Write the text to the file
	fwrite($fileHandle, $text);

	// Close the file handle
	fclose($fileHandle);
}


// Display Some Price Info  from HitBTC
$price = $data['last'];
$bid = $data['bid'];

$low = $data['low'];
$high = $data['high'];

echo "<br><br><h3>Live Price from HitBTC</h3><br>";
echo "<br>Current Price: $price USDT<br>";
echo "Bid Price: $bid USDT<br>";
echo "Ask Price: $ask USDT<br>";
echo "low Price: $low USDT<br>";
echo "high Price: $high USDT<br>";


//write_sell_buy_flipping_txt("sell"); // test for flipping.txt

function read_sell_buy_flipping_txt() { // function to read text in flipping.txt
    // File path
    $file = 'flipping.txt';

    // Open the file in read mode
    $fileHandle = fopen($file, 'r') or die("Unable to open file!");

    // Read the content of the file
    $text = fread($fileHandle, filesize($file));

    // Close the file handle
    fclose($fileHandle);

    return $text;
}


// Read only flipping.txt content 
$flipping_text = read_sell_buy_flipping_txt();
echo $flipping_text;

// this function will write the price user/bot used to pay or buy your coins/tokens
// ******************************************************************************** 
// BEFORE RUNNING THE BOT, YOU MUST ADD THE PRICE YOU WANT THE BOT TO START TRADING
// EVERY TIME THE BOT BUY OR SELL IT WILL AUTOMATICALLY UPDATE THE PRICE IN  "user_paid.text file"
// ******************************************************************************** 

function user_bot_paid_txt() {
    global $ask;
	// File path
	$file = 'user_bot_paid.txt';
	
	$text = strval($ask);
	
	// Open the file in write mode
	$fileHandle = fopen($file, 'w') or die("Unable to open file!");

	// Write the text to the file
	fwrite($fileHandle, $text);

	// Close the file handle
	fclose($fileHandle);
	
}

//user_bot_paid_txt();




// Buy or Sell function from hitbtc trading bot v2

// BUY OR SELL FUNCTION

function buy_or_sell($symbol, $side, $quantity)
{
    global $apiKey, $apiSecret, $ask, $quantity, $symbol;

    $url = 'https://api.hitbtc.com/api/3/spot/order';

    $orderData = [
        'symbol' => $symbol,
        'side' => $side,
        'quantity' => $quantity,
        'price' => $ask,//$price,
    ];

    $payload = http_build_query($orderData);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Basic ' . base64_encode($apiKey . ':' . $apiSecret),
        'Content-Type: application/x-www-form-urlencoded',
    ));

    $response = curl_exec($ch);

    if ($response === false) {
        echo "Error: Unable to place order.\n";
        exit();
    }

    $order = json_decode($response, true);

    print_r($order);
}

// buy_or_sell($symbol, 'buy', $number_coins_buy_sell, $ask); // 'sell' will sell coins and 'buy' will buy coins

// write_sell_buy_flipping_txt("sell")
// Read only flipping.txt content 

// user_bot_paid_txt();
$flipping_text = read_sell_buy_flipping_txt();


$display_buy_percent = number_format($total_buy_percent, 10, '.', '');
$display_sell_percent = number_format($total_sell_percent, 10, '.', '');
$display_dont_waste_money = number_format($total_dont_waste_money, 10, '.', '');

// $savemoney = $user_bot_paidx - ($user_bot_paidx*0.02); // new function added 7/19/2024

if ($ask_price <= $display_dont_waste_money and $flipping_text == "buy") { # $user_bot_paidx used to be $display_dont_waste_money +++++ $savemoney was added later
    echo "<br>bot will sell to avoid loss";
    write_sell_buy_flipping_txt("stop"); // suppose to be "buy" but will add "stop" to break the bot # to avoid the bot from buying more I put buy. It was sell before.
	buy_or_sell($symbol, 'sell', $quantity);
    user_bot_paid_txt();
    counter();
} 

if ($ask_price >= $display_sell_percent and $flipping_text == "buy") { // it's "buy" but put "null" to stop the bot
    echo "<br>bot will sell";
    write_sell_buy_flipping_txt("sell");
	buy_or_sell($symbol, 'sell', $quantity);
    user_bot_paid_txt();
    counter();
	
	//RIGHT AFTER SELLING i ADDED THIS cODE TO BUY AGIN IN CASE THE PRICE WILL GO UP - MIGHT BE A BAD IDEA SINCE I CAN'T USE TIME FUNC TO WAIT FOR A FEW SECOND
	echo "";
	echo "";
	echo "";
	echo "";
	echo "";
	echo "";
	echo "";
	echo "";
	echo "";
	echo "";
	echo "";
	echo "";
	echo "";
	echo "";
    echo "";
	echo "";
	echo "";
	echo "";
	echo "";
	echo "";
	echo "";
	echo "<br>bot will buy again"; // <-- this code is a copy from the bottom
    // write_sell_buy_flipping_txt("buy"); // suppose to be "buy" but
	// buy_or_sell($symbol, 'buy', $quantity);
    // user_bot_paid_txt();
    // counter();
	
	//RIGHT AFTER SELLING i ADDED THIS cODE TO BUY AGAIN IN CASE THE PRICE WILL GO UP - MIGHT BE A BAD IDEA SINCE I CAN'T USE TIME FUNC TO WAIT FOR A FEW SECOND

} elseif ($ask_price <= $display_buy_percent and $flipping_text == "sell") {
    echo "<br>bot will buy";
    write_sell_buy_flipping_txt("buy");
	buy_or_sell($symbol, 'buy', $quantity);
    user_bot_paid_txt();
    counter();
	
} else {
    echo "<br>Bot is waiting for better price to Sell or Buy";
}


?>
