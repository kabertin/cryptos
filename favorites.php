<?php
// Enable caching for this file
header("Cache-Control: max-age=86400, public"); // Cache for 1 day
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Favorites</title>
    <style>
        /* General Body Styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #000; /* Black background */
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        /* Main title styling */
        h1 {
            color: #4CAF50; /* Green color */
            margin: 40px 20px;
            text-align: center;
            font-size: 2rem;
        }

        /* Navigation bar styling */
        #navBar {
            width: 100%;
            background-color: #333; /* Dark background */
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 10px 20px;
        }

        /* Styling for navigation links */
        #navBar a {
            color: #fff;
            padding: 10px 15px;
            text-decoration: none;
            text-align: center;
            font-size: 1rem;
            border-right: 1px solid #444;
        }

        /* Remove border from last nav link */
        #navBar a:last-child {
            border-right: none;
        }

        /* Hover effect for navigation links */
        #navBar a:hover {
            background-color: #ddd;
            color: #000;
        }

        /* Favorites List Grid Styling */
        #favoritesList {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
            max-width: 1200px;
            margin-top: 50px;
            width: 100%;
            box-sizing: border-box;
        }

        /* Individual Favorite Card Styling */
        .favorite-card {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }

        /* Hover Effect on Cards */
        .favorite-card:hover {
            transform: scale(1.05);
        }

        /* Title Styling for Each Card */
        .favorite-card h3 {
            margin: 0 0 10px;
            color: #4CAF50;
            font-size: 1.5em;
        }

        /* Description Styling in Cards */
        .favorite-card p {
            margin: 8px 0;
            color: #ccc;
        }

        /* Button Styling */
        .favorite-card button {
            padding: 12px 18px;
            font-size: 14px;
            color: #412424;
            background-color: #a13908;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        /* Remove Button Styling */
        .favorite-card button:last-child {
            background-color: #e97161;
        }

        /* Button Hover Effect */
        .favorite-card button:hover {
            background-color: #ff6f61;
        }

        /* "No Favorites" Message Styling */
        .no-favorites-message {
            color: #ff6f61;
            font-size: 18px;
            text-align: center;
            margin-top: 50px;
        }

        /* Responsive Design: Adjust for smaller screens */
        @media (max-width: 768px) {
            /* Make the navigation bar more compact */
            #navBar {
                flex-direction: row;
                gap: 5px;
            }

            h1 {
                font-size: 1.5rem;
            }

            #navBar a {
                font-size: 0.9rem;
                padding: 8px 12px;
            }

            /* Adjust the grid layout for smaller screens */
            #favoritesList {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                padding: 15px;
            }

            /* Adjust font size in cards */
            .favorite-card h3 {
                font-size: 1.3em;
            }

            /* Adjust button size */
            .favorite-card button {
                font-size: 12px;
                padding: 10px 14px;
            }
        }

        @media (max-width: 480px) {
            /* Adjust the heading size for smaller screens */
            h1 {
                font-size: 1.2rem;
                margin: 20px 10px;
            }

            #navBar a {
                font-size: 0.8rem;
                padding: 6px 10px;
            }

            /* Further reduce padding for cards on very small screens */
            .favorite-card {
                padding: 15px;
            }

            /* Further adjust font size for card titles */
            .favorite-card h3 {
                font-size: 0.9rem;
            }
            .favorite-card p {
                font-size: 0.7rem;
            }
            .favorite-card button {
                font-size: 0.7rem;
                padding: 5px 8px;
            }

            /* Make the "No favorites" message slightly smaller */
            .no-favorites-message {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <!-- Main Heading -->
    <h1>Favorites Coins</h1>
    
    <!-- Navigation Bar -->
    <div id="navBar">
        <!-- Navigation links -->
        <a href="dashboard.html">All Coins</a>
        <a href="favorites.php">Favorites</a>
        <a href="charts.html">Charts</a>
        <a href="set_notification.php">Notifications</a>
        <a href="logout.php" style="background-color: #963d3d; border-radius: 5px;">Logout</a>
    </div>

    <!-- List of Favorite Coins (dynamically populated) -->
    <div id="favoritesList"></div>

    <!-- Message to show if no favorites are selected -->
    <p id="noFavoritesMessage" class="no-favorites-message" style="display: none;">No favorites selected</p>

    <script>
        // Function to fetch and display favorite coins
        async function fetchFavorites() {
            const response = await fetch('fetch_favorites.php'); // Fetch favorite coins from the server
            const favorites = await response.json(); // Parse the response to JSON
            const container = document.getElementById('favoritesList'); // Get the container to display the list
            const noFavoritesMessage = document.getElementById('noFavoritesMessage'); // Get the no favorites message element

            container.innerHTML = ''; // Clear any existing content

            if (favorites.length === 0) {
                // If no favorites are found, show the "No favorites" message
                noFavoritesMessage.style.display = 'block';
            } else {
                // Hide the "No favorites" message if there are favorites
                noFavoritesMessage.style.display = 'none';

                // Loop through each favorite cryptocurrency
                for (let cryptoId of favorites) {
                    const cryptoDetails = await fetchCryptoDetails(cryptoId); // Fetch details for the favorite coin
                    const div = document.createElement('div'); // Create a new div for the coin card
                    div.className = 'favorite-card'; // Apply the styling class for each favorite card
                    div.innerHTML = `
                        <h3>${cryptoDetails.name} (${cryptoDetails.symbol.toUpperCase()})</h3>
                        <p>Price: $${cryptoDetails.current_price.toFixed(2)}</p>
                        <p>Market Cap: $${cryptoDetails.market_cap.toFixed(2)}</p>
                        <p>24h High: $${cryptoDetails.high_24h.toFixed(2)}</p>
                        <p>24h Low: $${cryptoDetails.low_24h.toFixed(2)}</p>
                        <button onclick="removeFromFavorites('${cryptoId}')">Remove</button>
                    `;
                    container.appendChild(div); // Append the new div to the list
                }
            }
        }

        // Function to fetch details of a specific cryptocurrency
        async function fetchCryptoDetails(cryptoId) {
            const response = await fetch(`https://api.coingecko.com/api/v3/coins/${cryptoId}`); // Fetch data from CoinGecko API
            const data = await response.json(); // Parse the response to JSON

            // Extract and return the necessary details for the coin
            return {
                name: data.name,
                symbol: data.symbol,
                current_price: data.market_data.current_price.usd,
                market_cap: data.market_data.market_cap.usd,
                high_24h: data.market_data.high_24h.usd,
                low_24h: data.market_data.low_24h.usd
            };
        }

        // Function to remove a cryptocurrency from favorites
        async function removeFromFavorites(cryptoId) {
            const response = await fetch('remove_favorite.php', {
                method: 'POST',
                body: new URLSearchParams({ crypto_id: cryptoId }), // Sending the crypto_id to the server
            });
            const result = await response.text(); // Get the result of the removal request
            alert(result); // Show the result in an alert box
            fetchFavorites(); // Refresh the favorites list
        }

        fetchFavorites(); // Initial call to load favorites when the page loads
    </script>
</body>
</html>
