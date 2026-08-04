<?php

require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../../../config/db.php";

// make sure only admins can access
requireAdmin();

$pageTitle = "Admin Dashboard";

require_once __DIR__ . "/../includes/header.php";

?>

<?php

// status check for admin dashboard
// check database connection, if we can issue a select statement, it is up

try {
    $pdo->query("SELECT 1");
    $databaseStatus = "Online";
}

catch (Exception $e) {
    $databaseStatus = "Offline";
}

// list php information
$phpVersion = PHP_VERSION;
$phpStatus = "Running";

// check that the image folder exists and is writable
$imageFolder = __DIR__ . "/../assets/images/books";

if (is_dir($imageFolder) && is_writable($imageFolder)) {
    $imageStatus = "Online";
}

else {
    $imageStatus = "Offline";
}

// get info on users, books, reviews from db
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$bookCount = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$reviewCount = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();

?>

<div class="container">
    <a class="help-link" href="<?= BASE_URL ?>help/admin_basics.html">
        Admin dashboard guide
    </a>

    <h1> Admin Dashboard </h1>

    <div class="card">
        <h2> Welcome </h2>
        <p> Welcome, <?= htmlspecialchars($_SESSION["name"]) ?> </p>
    </div>

    <div class="card">
        <h2> User Management </h2>
        <p> View, disable, delete, and manage user accounts. </p>

        <a class="button" href="manage_users.php"> Manage Users </a>
    </div>

    <div class="card">
        <h2> Book Management </h2>
        <p> Add, edit, and remove books from the catalogue. </p>

        <a class="button" href="add_book.php"> Add Book </a>

        <br><br>
        <a class="button" href="manage_books.php"> Manage Books </a>
    </div>

    <div class="card">
        <h2> Review Moderation </h2>
        <p> View and remove user reviews. </p>

        <a class="button" href="manage_reviews.php"> Manage Reviews </a>
    </div>

    <div class="card">
        <h2> Website Appearance </h2>
        <p> Change the active site theme. </p>

        <a class="button" href="themes.php"> Website Settings </a>
    </div>

    <div class="card">
        <h2> Website Status </h2>

        <table>
            <tr>
                <th> Service </th>
                <th> Status </th>
            </tr>

            <tr>
                <td> PHP </td>
                <td> 
                    <?= $phpStatus ?>
                    <br>
                    Version: <?= $phpVersion ?>
                </td>
            </tr>

            <tr>
                <td> Database </td>
                <td> <?= $databaseStatus ?> </td>
            </tr>

            <tr>
                <td> Image Storage </td>
                <td> <?= $imageStatus ?> </td>
            </tr>

        </table>

        <br>

        <h3> Website Statistics </h3>

        <table>
            <tr>
                <th> Item </th>
                <th> Count </th>
            </tr>

            <tr>
                <td> Users </td>
                <td> <?= $userCount ?> </td>
            </tr>

            <tr>
                <td> Books </td>
                <td> <?= $bookCount ?> </td>
            </tr>

            <tr>
                <td> Reviews </td>
                <td> <?= $reviewCount ?> </td>
            </tr>

        </table>
    </div>
</div>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>
