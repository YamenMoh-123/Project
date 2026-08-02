<?php

require_once __DIR__ . "/../../../config/db.php";

$pageTitle = "Books";

require_once __DIR__ . "/../includes/header.php";

// select all books and calculate average rating
$stmt = $pdo->query(
    "SELECT
        b.id,
        b.title,
        b.author,
        b.page_count,
        b.description,
        b.image,
        b.genre,
        b.published_year,
        ROUND(AVG(r.rating), 1) AS average_rating,
        COUNT(r.id) AS review_count
    FROM books b
    LEFT JOIN reviews r
        ON b.id = r.book_id
    GROUP BY b.id
    ORDER BY b.title ASC"
);

$books = $stmt->fetchAll();

$favorites = [];

// if a user is logged in get their favorites
if (isset($_SESSION["user_id"])) {
    $stmt = $pdo->prepare(
        "SELECT book_id
         FROM favorites
         WHERE user_id = ?"
    );

    $stmt->execute([$_SESSION["user_id"]]);

    $favorites = array_column($stmt->fetchAll(), "book_id");
}

?>

<div class="container">
    <h1>Book Catalogue</h1>
    <p>Browse all books available on BookHub.</p>

    <div class="book-grid">

        <?php foreach ($books as $book): ?>
            <div class="book-card">

                <?php if (!empty($book["image"])): ?>
                    <img
                        src="<?= BASE_URL . htmlspecialchars($book["image"]) ?>"
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
                <p><strong>Pages:</strong> <?= htmlspecialchars($book["page_count"] ?: "N/A") ?></p>
                <p><strong>Year:</strong> <?= htmlspecialchars($book["published_year"] ?: "N/A") ?></p>

                <p class="rating">

                    <?php if ($book["review_count"] > 0): ?>
                        ⭐ <?= htmlspecialchars($book["average_rating"]) ?>
                        (<?= htmlspecialchars($book["review_count"]) ?> review<?= $book["review_count"] == 1 ? "" : "s" ?>)

                    <?php else: ?>
                        No reviews yet

                    <?php endif; ?>

                </p>

                <?php if (isset($_SESSION["user_id"])): ?>
                    <?php if (in_array($book["id"], $favorites)): ?>

                        <a class="button" href="toggle_favorite.php?book_id=<?= urlencode($book["id"]) ?>">
                            ★ Favorited
                        </a>

                    <?php else: ?>
                        <a class="button" href="toggle_favorite.php?book_id=<?= urlencode($book["id"]) ?>">
                            ☆ Add to Favorites
                        </a>

                    <?php endif; ?>

                <?php endif; ?>

                <a class="button" href="book.php?id=<?= urlencode($book["id"]) ?>"> View Details </a>

            </div>

        <?php endforeach; ?>

    </div>
</div>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>
