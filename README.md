# Cryptos App

A simple web application that allows users to explore cryptocurrency prices, manage a favorites list, and track their preferences with notifications.

## Features

- View live cryptocurrency prices.
- Add or remove cryptocurrencies from your favorites list.
- Track favorite cryptos and receive updates.
- User authentication with login and registration functionality.
- Notification system to alert users of price changes.
- Responsive design for a better mobile experience.

## Files Overview

Here is a brief description of the key files in this project:

- **`index.php`**: The landing page of the app.
- **`login.php`**: Handles user login logic.
- **`register.php`**: Allows users to register a new account.
- **`login_form.html`**: The HTML form for user login.
- **`dashboard.html`**: Displays the dashboard, including cryptocurrency data.
- **`charts.html`**: Displays cryptocurrency charts using Chart.js.
- **`favorites.html`**: Displays a list of the user's favorite cryptocurrencies.
- **`add_favorite.php`**: Adds a cryptocurrency to the user's favorites.
- **`remove_favorite.php`**: Removes a cryptocurrency from the user's favorites.
- **`fetch_cryptos.php`**: Fetches the current cryptocurrency prices.
- **`fetch_favorites.php`**: Fetches the user's favorite cryptocurrencies.
- **`notify.php`**: Sends email notifications to users when a cryptocurrency's price reaches a set alert level. This is powered by PHPMailer and Monolog for logging and email tracking.

## Database Structure

The application uses a MySQL database named `cryptos`, which contains the following tables:

1. **`users`**: Stores user details including their email addresses.
2. **`price_alerts`**: Stores price alert preferences for users, such as the cryptocurrency symbol, price level, and email sent status.
3. **`favorites`**: Stores the cryptocurrencies added to each user's favorites list.

### `price_alerts` Table Example

| alert_id | user_id | crypto_symbol | price_level | email_sent | alert_status |
|----------|---------|---------------|-------------|------------|--------------|
| 1        | 2       | btc           | 50000       | no         | triggered    |
| 2        | 3       | eth           | 3000        | yes        | triggered    |

### `users` Table Example

| id | email               |
|----|---------------------|
| 2  | user1@example.com   |
| 3  | user2@example.com   |

## How It Works

### Price Alerts

- Users can set price alerts for cryptocurrencies, specifying a target price.
- The `notify.php` script checks whether the current price of the cryptocurrency meets the user's set price level.
- If a user's alert condition is met, an email notification is sent using PHPMailer.
- Once the email is sent, the status is updated in the database to indicate that the alert has been triggered and the email was sent.

### Email Notification Process

The `notify.php` script:
- Fetches all the price alerts that have not yet triggered an email.
- Fetches the latest cryptocurrency prices from the CoinGecko API.
- Compares the current price of each cryptocurrency to the price level set by the user.
- If the current price exceeds the set price level, it sends an email using PHPMailer and logs the event with Monolog.
- Updates the `price_alerts` table to mark the alert as triggered.

### Logging

- Monolog is used to log the success or failure of email notifications in a log file called `email_logs.log`.
- Successful emails are logged with details of the sent alert, while failures include error messages.

## Requirements

- PHP 7.4 or higher.
- Composer for dependency management (PHPMailer and Monolog).
- A Mail server or SMTP setup for sending emails.

### To install dependencies:

1. Clone the repository:

    ```bash
    git clone https://github.com/kabertin/cryptos.git
    ```

2. Install dependencies with Composer:

    ```bash
    composer install
    ```

3. Set up your mail server or use a service like Gmail (SMTP settings in `notify.php`).

4. Create a `.env` file or configure your mail settings directly in the `notify.php` file (SMTP server, email, password, etc.).

## Setup Instructions

1. Ensure your MySQL database is set up with the `cryptos` database and the relevant tables (`users`, `price_alerts`, `favorites`).
2. Update your database connection details in `db_connection.php`.
3. Add your email settings to `notify.php` for email notifications.

### Example configuration for PHPMailer in `notify.php`:

```php
$mail->Host       = 'smtp.yourmailserver.com';
$mail->Username   = 'youremail@example.com';
$mail->Password   = 'yourpassword';
```

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Credits

- [PHPMailer](https://github.com/PHPMailer/PHPMailer)
- [Monolog](https://github.com/Seldaek/monolog)
- [CoinGecko API](https://www.coingecko.com/en/api)
