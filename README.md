# Project: Cryptos

## Overview
Cryptos is a web application designed to help users track and manage their favorite cryptocurrencies. It provides real-time data visualization, a user-friendly interface, and customizable notification settings to keep users informed about market trends.

---

## Features
- **User Authentication:**
  - Secure user registration and login functionality.
  - Passwords are securely hashed for safe storage.

- **Cryptocurrency Management:**
  - Add or remove cryptocurrencies from your favorites.
  - View a list of your favorite cryptocurrencies.

- **Data Visualization:**
  - Interactive charts displaying cryptocurrency trends using `chart.js`.

- **Notifications:**
  - Set preferences for cryptocurrency updates and receive alerts.

---

## File Structure

### **PHP Files**
- **`add_favorite.php`**: Adds a cryptocurrency to the user's favorites list.
- **`db_connection.php`**: Establishes a secure connection to the database.
- **`fetch_cryptos.php`**: Fetches cryptocurrency data from the database or external APIs.
- **`fetch_favorites.php`**: Retrieves the user's favorite cryptocurrencies.
- **`favorites.php`**: Displays a page showing the user's favorite cryptocurrencies.
- **`login.php`**: Handles user login and session management.
- **`logout.php`**: Logs out the user and terminates their session.
- **`notify.php`**: Sends notifications to users based on their preferences.
- **`register.php`**: Handles user registration.
- **`remove_favorite.php`**: Removes a cryptocurrency from the user's favorites list.
- **`set_notification.php`**: Configures notification preferences for users.

### **HTML Files**
- **`charts.html`**: Displays interactive charts for cryptocurrency trends.
- **`dashboard.html`**: Serves as the main user interface after login.
- **`login_form.html`**: Provides the login page interface.
- **`register.html`**: Provides the registration page interface.

### **CSS Files**
- **`styles.css`**: Contains styling for all pages to ensure consistent and responsive design.

### **JavaScript Files**
- **`chart.js`**: Manages chart rendering for `charts.html` using cryptocurrency data.

### **Database Files**
- **`cryptos.sql`**: SQL script to set up the database schema and initial data.

### **Dependency Files**
- **`composer.json`**: Manages PHP dependencies.
- **`composer.lock`**: Locks dependency versions.

---

## Installation

### Prerequisites
- **PHP**: Version 7.4 or later.
- **MySQL**: To store user and cryptocurrency data.
- **Composer**: For dependency management.
- **Web Server**: Apache or Nginx.

### Steps
1. **Clone the Repository**
   ```bash
   git clone <repository_url>
   cd cryptocurrency-dashboard
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Set Up the Database**
   - Import `cryptos.sql` into your MySQL database.
   - Update `db_connection.php` with your database credentials.

4. **Configure the Application**
   - Create an `.env` file to store sensitive information (e.g., database credentials).
   - Example `.env` file:
     ```env
     DB_HOST=localhost
     DB_USER=root
     DB_PASSWORD=your_password
     DB_NAME=cryptos_db
     ```

5. **Start the Web Server**
   - Use a local server (e.g., XAMPP, WAMP) or deploy to a hosting service.

6. **Access the Application**
   - Open a web browser and navigate to `http://localhost/cryptocurrency-dashboard`.

---

## Usage

1. **Register an Account:**
   - Go to the registration page (`register.html`) and create a new account.

2. **Log In:**
   - Use your credentials on the login page (`login_form.html`) to access the dashboard.

3. **Manage Favorites:**
   - Add or remove cryptocurrencies to/from your favorites list.

4. **View Charts:**
   - Navigate to `charts.html` to explore interactive charts.

5. **Set Notifications:**
   - Configure your notification preferences for cryptocurrency updates.

---

## Security Best Practices
- **Sanitize Inputs:** Ensure all user inputs are sanitized to prevent SQL injection and XSS attacks.
- **Secure Sessions:** Use HTTPS to secure data transmission.
- **Environment Variables:** Store sensitive credentials in an `.env` file.

---

## Future Enhancements
- Real-time price updates using WebSockets.
- Integration with external APIs for live cryptocurrency data.
- Enhanced user notifications with email or SMS alerts.

---

## Author
Developed by **KARINDA Bertin** e-mail **karindabertin35@gmail.com**.
