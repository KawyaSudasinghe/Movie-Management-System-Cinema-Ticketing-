<?php

// Database connection settings for the local XAMPP MySQL server.
$host = 'localhost';
$dbname = 'movie_management';
$username = 'root';
$password = '';

// Create a PDO connection with error reporting enabled.
try {

    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    // Stop the application if the database cannot be reached.
    die('Database connection failed. Please check XAMPP and database settings.');

}
?>
