<?php
// =============================================
// DATABASE CONFIGURATION
// =============================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'grocery_db');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("<div style='text-align:center;padding:50px;font-family:sans-serif;color:red;'>
        <h2>Database Connection Failed!</h2>
        <p>" . mysqli_connect_error() . "</p>
        <p>Please check your config.php file and ensure MySQL is running.</p>
    </div>");
}
mysqli_set_charset($conn, "utf8");

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Site Config
define('SITE_NAME', 'FreshMart Grocery');
define('CURRENCY', '₹');
?>
