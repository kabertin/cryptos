<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Metadata for character encoding and responsiveness -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Page title -->
    <title>User Registration</title>
    
    <!-- Inline CSS styles -->
    <style>
        /* General body styling */
        body {
            font-family: Arial, sans-serif; /* Font family */
            background-color: #000; /* Black background */
            color: #1b1b1b; /* Dark text color */
            margin: 0;
            padding: 0;
            display: flex; /* Flexbox for centering */
            justify-content: center; /* Centers content horizontally */
            align-items: center; /* Centers content vertically */
            height: 100vh; /* Full viewport height */
        }

        /* Styling for the form container */
        .form-container {
            background-color: transparent; /* Transparent background */
            padding: 20px 30px; /* Padding for spacing */
            border-radius: 12px; /* Rounded corners */
            background-color: #000000; /* Solid black background */
            background-image: linear-gradient(180deg, #000000 0%, #d4cece 100%); /* Gradient background */
            border: none; /* No border */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Shadow effect */
            width: 100%; /* Full width */
            max-width: 400px; /* Limits the maximum width */
        }

        /* Header styling */
        h2 {
            margin-bottom: 20px; /* Space below the header */
            text-align: center; /* Centers the text */
            font-size: 24px; /* Font size */
            color: #4CAF50; /* Green text color */
        }

        /* Form styling */
        form {
            display: flex; /* Flexbox for layout */
            flex-direction: column; /* Stacks elements vertically */
        }

        /* Input field styling */
        input {
            margin-bottom: 15px; /* Space below each input */
            background-color: transparent; /* Transparent background */
            padding: 12px; /* Padding inside input fields */
            font-size: 16px; /* Font size */
            color: rgb(0, 0, 0); /* Black text color */
            border: 1px solid #464040; /* Border color */
            border-radius: 8px; /* Rounded corners */
            transition: border-color 0.3s, box-shadow 0.3s; /* Smooth transition on focus */
        }

        /* Input field focus styling */
        input:focus {
            border-color: #99ec9b; /* Green border on focus */
            box-shadow: 0 0 5px rgba(27, 27, 27, 0.5); /* Shadow effect */
            outline: none; /* Removes default outline */
        }

        /* Placeholder styling */
        input::placeholder {
            color: #161212; /* Default placeholder text color */
            font-size: 16px; /* Placeholder font size */
            opacity: 1; /* Ensures full visibility */
        }

        /* Placeholder color change on focus */
        input:focus::placeholder {
            color: #84c986; /* Greenish placeholder color */
            opacity: 1;
        }

        /* Button styling */
        button {
            padding: 12px; /* Padding inside button */
            font-size: 16px; /* Font size */
            background-color: #4CAF50; /* Green background */
            color: rgb(24, 0, 0); /* Text color */
            border: none; /* No border */
            border-radius: 8px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor */
            transition: background-color 0.3s, transform 0.2s; /* Smooth transitions */
        }

        /* Button hover effect */
        button:hover {
            background-color: #45a049; /* Darker green on hover */
            transform: translateY(-2px); /* Slight upward movement */
        }

        /* Message section styling */
        .message {
            text-align: center; /* Centers text */
            margin-top: 15px; /* Space above messages */
        }

        /* Error message styling */
        .error {
            color: red; /* Red color for error messages */
        }

        /* Success message styling */
        .success {
            color: green; /* Green color for success messages */
        }

        /* Responsive styling for small screens */
        @media (max-width: 480px) {
            .form-container {
                padding: 15px; /* Reduced padding for small screens */
            }

            h2 {
                font-size: 20px; /* Smaller font size for headers */
            }

            input, button {
                font-size: 14px; /* Reduced font size for inputs and buttons */
            }
        }
    </style>
</head>
<body>
    <!-- Registration form container -->
    <div class="form-container">
        <h2>Sign Up</h2>

        <!-- PHP logic for handling form submission -->
        <?php
        require 'db_connection.php'; // Includes the database connection file

        // Checks if the form was submitted
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email']; // Retrieves the email from the form
            $password = $_POST['password']; // Retrieves the password from the form

            // Validates the email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "<p class='message error'>Invalid email format.</p>";
            } 
            // Checks if the password meets the minimum length requirement
            elseif (strlen($password) < 6) {
                echo "<p class='message error'>Password must be at least 6 characters long.</p>";
            } 
            // Proceeds with user registration
            else {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT); // Hashes the password

                // Prepares the SQL query to insert user data into the database
                $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
                $stmt->bind_param("ss", $email, $hashedPassword); // Binds parameters to the SQL query

                // Executes the query and checks for success or errors
                if ($stmt->execute()) {
                    echo "<p class='message success'>User registered successfully.</p>";
                } else {
                    echo "<p class='message error'>Error: " . $conn->error . "</p>";
                }

                // Closes the statement and database connection
                $stmt->close();
                $conn->close();
            }
        }
        ?>

        <!-- Registration form -->
        <form method="POST" action="">
            <!-- Email input field -->
            <input type="email" name="email" placeholder="Email" required>
            
            <!-- Password input field -->
            <input type="password" name="password" placeholder="Password" required>
            
            <!-- Submit button -->
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>

