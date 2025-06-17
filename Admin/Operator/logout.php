<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the main index page
header("Location: ../../villager/Main/index.html");
exit;
?>