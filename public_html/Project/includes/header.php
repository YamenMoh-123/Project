<?php

// load config.php for base_url so we can use it across all files
require_once __DIR__ . "/../../../config/config.php";
require_once __DIR__ . "/../../../config/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$stmt = $pdo->query(
    "SELECT site_theme FROM settings LIMIT 1"
);

$theme = $stmt->fetchColumn();

if (!$theme) {
    $theme = "classic";
}

?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>
            <?= isset($pageTitle) ? htmlspecialchars($pageTitle) : "Book Archive" ?>
        </title>

        <meta name="description" content="Book Archive - Discover books, write reviews, and discuss books with the community.">
        <meta name="keywords" content="books, reviews, reading, community">

        <link rel="icon" type="image/jpeg" href="<?= BASE_URL ?>assets/images/icon.jpg">

        <!-- load css using with base_url as the root path !-->
        <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
        <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/themes.css">

    </head>

    <body class="theme-<?= htmlspecialchars($theme) ?>">

    <nav class="navbar">

        <div class="logo">
            <a href="<?= BASE_URL ?>index.php"> Book Archive </a>
        </div>

        <div class="nav-links">
            <a href="<?= BASE_URL ?>index.php"> Home </a>
            <a href="<?= BASE_URL ?>books/books.php"> Books </a>

            <!-- only show profile button if a user is logged in -->
            <?php if (isset($_SESSION["user_id"])): ?>
                <a href="<?= BASE_URL ?>favorites.php">Favorites</a>
                <a href="<?= BASE_URL ?>profile.php"> Profile </a>

                <!-- only show admin dashboard if user is logged in and is an admin -->
                <?php if ($_SESSION["is_admin"] == 1): ?>
                    <a href="<?= BASE_URL ?>admin/dashboard.php"> Admin </a>
                <?php endif; ?>

                <a href="<?= BASE_URL ?>logout.php"> Logout </a>

            <!-- show login and register buttons if user is not logged in -->
            <?php else: ?>
                <a href="<?= BASE_URL ?>login.php"> Login </a>
                <a href="<?= BASE_URL ?>register.php"> Register </a>
            <?php endif; ?>

            <a href="<?= BASE_URL ?>help/index.html"> Help </a>

        </div>
    </nav>

<main>
