<?php

// create a session if one does not already exist
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// check if user is logged in by checking if user_id is set, this is created / set when user logs in
function isLoggedIn()
{
    return isset($_SESSION["user_id"]);
}

// same as above but for admin privs
function isAdmin()
{
    return isset($_SESSION["is_admin"])
        && $_SESSION["is_admin"] == 1;
}

//change?
// redirect to login.php if a log in is required
function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// also change (general error page?)
// redirect to login.php if admin privs are required
function requireAdmin()
{
    if (!isAdmin()) {
        header("Location: login.php");
        exit();
    }
}

?>