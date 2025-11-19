<?php
// Simple PHP Router with 404 fallback

$page = $_GET['page'] ?? 'home'; // Default page is home
$file = $page . '.php';

// Check if the file exists
if (file_exists($file)) {
    include $file;
} else {
    include '404.php'; // Show 404 page if not found
}
?>
