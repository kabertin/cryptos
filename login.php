<?php
// Include the database connection file to interact with the database
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Check if the form was submitted via POST
    $email = $_POST['email'];  // Get the user's email from the POST request
    $password = $_POST['password'];  // Get the user's password from the POST request

    // Prepare and execute the SQL query to fetch user ID and password for the provided email
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);  // Bind the email parameter to prevent SQL injection
    $stmt->execute();  // Execute the SQL query
    $stmt->store_result();  // Store the result to check if the user exists

    // Check if the user exists in the database
    if ($stmt->num_rows > 0) {
        // Fetch the user data (user ID and hashed password)
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();

        // Verify the password using password_verify() (compares the provided password to the hashed one)
        if (password_verify($password, $hashed_password)) {
            session_start();  // Start a new session or resume the existing one
            $_SESSION['user_email'] = $email;  // Store the user's email in the session for later use
            $_SESSION['user_id'] = $user_id;  // Store the user's ID in the session for later use

            // Generate a CSRF token if it doesn't already exist in the session
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            // Output a success message and redirect to the welcome page
            echo "<script>
                    alert('Login successful. Welcome, $email!');
                    window.location.href = 'dashboard.html';
                </script>";
        } else {
            // If the password is incorrect, show an error message and redirect to the login page
            echo "<script>
                alert('Invalid password. Please try again.');
                window.location.href = 'login_form.html';
            </script>";
        }
    } else {
        // If no user is found with the provided email, show an error message and redirect to the registration page
        echo "<script>
            alert('User not found. Please register first.');
            window.location.href = 'register.html';
        </script>";
    }

    $stmt->close();  // Close the prepared statement after use
} else {
    // If the request method is not POST, show an error message and redirect to the login page
    echo "<script>
        alert('Invalid request method.');
        window.location.href = 'login_form.html';
    </script>";
}

$conn->close();  // Close the database connection after use
?>
