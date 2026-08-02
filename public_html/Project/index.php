<?php

$pageTitle = "Home";
require_once __DIR__ . "/includes/header.php";

?>

<div class="container">

<h1> Welcome to TITLE </h1>

    <p>
        A community platform where users discover books,
        write reviews, and discuss their favourite stories.
    </p>

    <div class="card">
        <h2> Start Reading </h2>
        <p> Browse books, create reviews, and join discussions. </p>

        <a class="button" href="<?= BASE_URL ?>books/books.php"> Browse Books </a>
    </div>
</div>

<?php
require_once __DIR__ . "/includes/footer.php";
?>
