<?php

// Database connection
$host = "127.0.0.1";  
$user = "root";  
$pass = "admin";  
$db   = "system_db";  
$port = 3306;

$conn = new mysqli($host, $user, $pass, $db, $port);


if ($conn->connect_error) {
    die(" Connection failed: " . $conn->connect_error);
}
?>
