<?php
// includes/auth.php - guarded session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header('Location: /index.php');
    exit;
}