<?php
// Initialize the session
session_start();

$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
$fullname = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : null;

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("../location: auth-signin-basic.php");
    exit;
}
?>
