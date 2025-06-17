<?php
session_start();

// Function to check if user is logged in
function checkLogin($redirect = true) {
    if (!isset($_SESSION['user_id'])) {
        if ($redirect) {
            // Redirect to login page with a return URL
            $currentPage = urlencode($_SERVER['REQUEST_URI']);
            header("Location: ../login/login.php?return_to=$currentPage");
            exit();
        }
        return false;
    }
    return true;
}

// Function to get logged-in user ID
function getUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}
?>