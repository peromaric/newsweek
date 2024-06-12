<?php
$dbc = mysqli_connect("db", "root", "root", "newsweek", "3306");
$createTableQuery = "CREATE TABLE IF NOT EXISTS users (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        username VARCHAR(50) NOT NULL UNIQUE,
                        password VARCHAR(255) NOT NULL,
                        isAdmin BOOLEAN DEFAULT 0
                    )";
$dbc->query("USE newsweek");
$dbc->query($createTableQuery);
if ($dbc->connect_error) {
    die("Connection failed: " . $dbc->connect_error);
}
?>
