<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'hms';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('DB failed: ' . $conn->connect_error);
}

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Escape output safely
if (!function_exists('e')) {
    function e($s) {
        return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
    }
}

// Require user to be logged in
if (!function_exists('require_login')) {
    function require_login() {
        if (empty($_SESSION['user'])) {
            header('Location: /hms-pro/index.php');
            exit;
        }
    }
}

// Require user to have a specific role
if (!function_exists('require_role')) {
    function require_role($role) {
        require_login();
        if ($_SESSION['user']['role'] !== $role) {
            header('Location: /hms-pro/index.php');
            exit;
        }
    }
}
?>
