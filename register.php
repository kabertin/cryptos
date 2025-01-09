<?php
session_start();
require 'db_connection.php';

// CSRF token generation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['csrf_token'])) {
        echo "<script>
                alert('Invalid request. Please try again.');
                window.location.href = 'register.html';
            </script>";
    }

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
                alert('Invalid email format.');
                window.location.href = 'register.html';
            </script>";
    }

    // Validate password strength
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&.])[A-Za-z\d@$!%*?&.]{8,}$/', $password)) {
        echo "<script>
            alert('Password must meet the strength criteria.');
            window.location.href = 'register.html';
        </script>";
    }

    $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>
                    alert('Already Registered.');
                    window.location.href = 'index.php';
            </script>";
    }
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $hashedPassword);

    if ($stmt->execute()) {
        echo "<script>
                alert('User registered successfully.');
                window.location.href = 'index.php';
            </script>";
    } else {
        error_log("Database error: " . $stmt->error);
        echo "<script>
                alert('An error occurred. Please try again later.');
                window.location.href = 'register.html';
            </script>";
    }

    $stmt->close();
    $conn->close();
}
