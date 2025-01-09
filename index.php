<?php
session_start();
// CSRF token generation (assuming you have a session-based CSRF token mechanism)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // generates a secure token
}
$csrf_token = $_SESSION['csrf_token'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Specifies the document type and declares the language as English -->
    <meta charset="UTF-8">
    <!-- Sets the viewport to ensure responsive design for mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Page title displayed in the browser tab -->
    <title>Cryptos - Track Prices & Charts</title>

    <style>
        /* General reset for margin, padding, and box-sizing for all elements */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Base styling for the body */
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to bottom, #1e1e2f, #121212); /* Dark gradient background */
            color: #ffffff; /* White text */
            line-height: 1.6; /* Increases line spacing for better readability */
            display: flex; /* Flexbox layout for centering content */
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Full viewport height */
            overflow: hidden; /* Prevents scrolling */
        }

        /* Header styles */
        header {
            text-align: center; /* Centers text in the header */
            margin-bottom: 20px; /* Adds space below the header */
        }

        header h1 {
            font-size: 60px; /* Large font size for the title */
            color: #00e676; /* Green color for the title */
            text-shadow: 2px 2px 10px #00e676; /* Glowing text effect */
        }

        header p {
            font-size: 20px; /* Smaller font for subtitle */
            color: #cccccc; /* Light gray color */
            margin-top: 10px; /* Space above subtitle */
        }

        /* Main content section styles */
        section {
            max-width: 700px; /* Limits width for better readability */
            text-align: center; /* Centers text */
            margin: 20px 20px 40px; /* Adds spacing around the section */
        }

        section h2 {
            font-size: 32px; /* Heading font size */
            color: #00b0ff; /* Blue color for emphasis */
            margin-bottom: 15px; /* Space below the heading */
        }

        section p {
            font-size: 18px; /* Paragraph font size */
            color: #e0e0e0; /* Light gray text */
            margin-bottom: 20px; /* Adds space below the paragraph */
        }

        /* Button styles */
        .button-container {
            display: flex; /* Flexbox for aligning buttons */
            justify-content: center; /* Centers buttons horizontally */
            gap: 20px; /* Adds space between buttons */
        }

        button {
            padding: 15px 30px; /* Button padding for size */
            font-size: 18px; /* Button text size */
            font-weight: bold;
            color: #532a2a; /* Dark text color */
            background: #00e676; /* Green button background */
            border: none;
            border-radius: 10px; /* Rounded button corners */
            box-shadow: 0px 4px 10px rgba(0, 255, 127, 0.4); /* Button shadow */
            cursor: pointer; /* Changes cursor to pointer on hover */
            transition: all 0.3s ease-in-out; /* Smooth hover effects */
        }

        /* Button hover and active effects */
        button:hover {
            background: #00c853; /* Darker green on hover */
            transform: scale(1.05); /* Slightly enlarges the button */
        }

        button:active {
            transform: scale(1); /* Restores size on click */
        }

        /* Background animation container */
        .background-animation {
            position: absolute; /* Positioned relative to the viewport */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden; /* Ensures animations don’t overflow */
            z-index: -1; /* Places it behind other elements */
        }

        /* Styles for animated circles */
        .circle {
            position: absolute;
            border-radius: 50%; /* Ensures the elements are circular */
            background: rgba(0, 230, 118, 0.1); /* Transparent green circles */
            animation: float 10s infinite; /* Floating animation */
        }

        /* Different circle positions and sizes */
        .circle:nth-child(1) {
            width: 150px;
            height: 150px;
            top: 10%;
            left: 15%;
        }

        .circle:nth-child(2) {
            width: 200px;
            height: 200px;
            top: 50%;
            left: 70%;
            animation-delay: -5s; /* Delays animation for variation */
        }

        .circle:nth-child(3) {
            width: 250px;
            height: 250px;
            top: 80%;
            left: 10%;
        }

        /* Floating animation keyframes */
        @keyframes float {
            0% {
                transform: translateY(0px); /* Initial position */
            }
            50% {
                transform: translateY(-30px); /* Moves up */
            }
            100% {
                transform: translateY(0px); /* Returns to initial position */
            }
        }

        /* Responsive design adjustments for smaller screens */
        @media (max-width: 480px) {
            header h1 {
                font-size: 40px; /* Reduces title size */
            }

            section h2 {
                font-size: 24px; /* Reduces section heading size */
            }

            button {
                font-size: 14px; /* Smaller text for buttons */
                padding: 10px 20px; /* Adjusts button size */
            }
        }
    </style>
</head>
<body>
    <!-- Background animation with circles -->
    <div class="background-animation">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
    </div>

    <!-- Page header with title and subtitle -->
    <header>
        <h1>Cryptos</h1>
        <p>Track live prices, charts, and trends of cryptocurrencies with ease.</p>
    </header>

    <!-- Main content section -->
    <section>
        <h2>Stay Ahead of the Market</h2>
        <p>
            With Cryptos, you can monitor live cryptocurrency prices, analyze charts, and make informed decisions. 
            Your ultimate tool for keeping up with the fast-moving crypto world.
        </p>
    </section>

    <!-- Button container for navigation -->
    <div class="button-container">
        <button onclick="window.location.href='login_form.html'">Sign In</button>
        <button onclick="window.location.href='register.html'">Sign Up</button>
    </div>
</body>
</html>
