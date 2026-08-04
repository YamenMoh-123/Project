<?php

session_start();

require_once __DIR__ . "/../../../config/db.php";
require_once __DIR__ . "/../includes/auth.php";

requireAdmin();

$stmt = $pdo->query(
    "SELECT
        id,
        title,
        author,
        genre,
        page_count,
        published_year

    FROM books

    ORDER BY id DESC"
);

$books = $stmt->fetchAll();

$pageTitle = "Manage Books";

require_once __DIR__ . "/../includes/header.php";

?>

<div class="container">

    <a class="help-link" href="<?= BASE_URL ?>help/admin_basics.html">
        Managing books help
    </a>

    <h1> Manage Books </h1>
    <a class="button" href="add_book.php"> Add New Book </a>

    <table>
        <tr>
            <th> ID </th>
            <th> Title </th>
            <th> Author </th>
            <th> Genre </th>
            <th> Actions </th>
        </tr>

        <?php foreach ($books as $book): ?>
            <tr>
                <td> <?= $book["id"] ?> </td>
                <td> <?= htmlspecialchars($book["title"]) ?> </td>
                <td> <?= htmlspecialchars($book["author"]) ?> </td>
                <td> <?= htmlspecialchars($book["genre"] ?? "Unknown") ?> </td>
                <td>
                    <a class="button" href="edit_book.php?id=<?= $book["id"] ?>"> Edit </a>

                    <form method="POST" action="delete_book.php" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $book["id"] ?>">
                        <button type="submit"> Delete </button>
                    </form>
                </td>
            </tr>

        <?php endforeach; ?>

    </table>
</div>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>
