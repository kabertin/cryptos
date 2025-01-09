<?php

// URL of the API to fetch data from (CoinGecko API for cryptocurrency market data)
$url = "https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd";

// Set the HTTP headers to mimic a user agent (this helps avoid issues with request being blocked by the API)
$options = [
    "http" => [
        "header" => "User-Agent: PHP-Script\r\n" // Custom user agent header
    ]
];

// Create a stream context using the defined HTTP options
$context = stream_context_create($options);

// Fetch the API response using the file_get_contents function with the specified context
$response = file_get_contents($url, false, $context);

// Check if the response was successfully fetched
if ($response === false) {
    die('Error fetching data.'); // Exit the script and show an error message if the API call fails
}

// Set the header to indicate the response content type is JSON
header('Content-Type: application/json');

// Output the raw JSON response from the API
echo $response;

?>

