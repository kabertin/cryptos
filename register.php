
<?php
session_start();
require 'db_connection.php';

// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo "<script>
                alert('Invalid CSRF token. Please try again.');
                window.location.href = 'register.php';
            </script>";
        exit;
    }

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
                alert('Invalid email format.');
                window.location.href = 'register.php';
            </script>";
        exit;
    }

    // Validate password strength
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&.])[A-Za-z\d@$!%*?&.]{8,}$/', $password)) {
        echo "<script>
                alert('Password must meet the strength criteria.');
                window.location.href = 'register.php';
            </script>";
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>
                alert('This email is already registered.');
                window.location.href = 'index.php';
            </script>";
        exit;
    }

    $stmt->close();

    // Insert the new user into the database
    $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $hashedPassword);

    if ($stmt->execute()) {
        echo "<script>
                alert('User registered successfully!');
                window.location.href = 'index.php';
            </script>";
    } else {
        error_log("Database error: " . $stmt->error);
        echo "<script>
                alert('An error occurred. Please try again later.');
                window.location.href = 'register.php';
            </script>";
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #1b1b1b;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background-color: #000000;
            background-image: linear-gradient(180deg, #000000 0%, #d4cece 100%);
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            margin-bottom: 20px;
            text-align: center;
            font-size: 24px;
            color: #4CAF50;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input {
            margin-bottom: 15px;
            background-color: transparent;
            padding: 12px;
            font-size: 16px;
            color: rgb(0, 0, 0);
            border: 1px solid #464040;
            border-radius: 8px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        input:focus {
            border-color: #99ec9b;
            box-shadow: 0 0 5px rgba(27, 27, 27, 0.5);
            outline: none;
        }
        input::placeholder {
            color: #161212;
            font-size: 16px;
        }
        input:focus::placeholder {
            color: #84c986;
        }
        button {
            padding: 12px;
            font-size: 16px;
            background-color: #4CAF50;
            color: rgb(24, 0, 0);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }
        button:hover {
            background-color: #45a049;
            transform: translateY(-2px);
        }
        @media (max-width: 480px) {
            .form-container {
                padding: 15px;
            }
            h2 {
                font-size: 20px;
            }
            input, button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Sign Up</h2>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
