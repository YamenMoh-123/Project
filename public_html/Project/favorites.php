<?php

require_once __DIR__ . "/includes/auth.php";
requireLogin();

require_once __DIR__ . "/../../config/db.php";

$pageTitle = "My Favorites";

require_once __DIR__ . "/includes/header.php";

// get users favorites list by selecting from the db
$stmt = $pdo->prepare(
    "SELECT
        b.id,
        b.title,
        b.author,
        b.page_count,
        b.description,
        b.image,
        b.genre,
        b.published_year
    FROM favorites f
    INNER JOIN books b
        ON f.book_id = b.id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC"
);

$stmt->execute([$_SESSION["user_id"]]);

$books = $stmt->fetchAll();

?>

<div class="container">

<h1>My Favorite Books</h1>

<?php if (empty($books)): ?>
    <p>You have not added any books to your favorites yet.</p>

<?php else: ?>
    <div class="book-grid">

        <?php foreach ($books as $book): ?>
            <div class="book-card">

                <?php if (!empty($book["image"])): ?>
                    <img
                        src="<?= htmlspecialchars($book["image"]) ?>"
                        alt="<?= htmlspecialchars($book["title"]) ?> cover"
                        class="book-cover">

                <?php else: ?>
                    <img
                        src="https://placehold.co/150x220?text=Book"
                        alt="Book cover placeholder"
                        class="book-cover">

                <?php endif; ?>

                <h2><?= htmlspecialchars($book["title"]) ?></h2>

                <p><strong>Author:</strong> <?= htmlspecialchars($book["author"]) ?></p>

                <p><strong>Genre:</strong> <?= htmlspecialchars($book["genre"] ?: "Unknown") ?></p>

                <a class="button" href="books/book.php?id=<?= urlencode($book["id"]) ?>">
                    View Details
                </a>
            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>

</div>

<?php
require_once __DIR__ . "/includes/footer.php";
?>
