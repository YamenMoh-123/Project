<?php

session_start();

require_once __DIR__ . "/../../../config/db.php";

$bookId = $_GET["id"] ?? null;

if (!$bookId) {
    die("Book not found.");
}

// select the book information and calculate the average rating
$stmt = $pdo->prepare(
    "SELECT
        books.*,
        AVG(reviews.rating) AS average_rating,
        COUNT(reviews.id) AS review_count

    FROM books

    LEFT JOIN reviews
        ON books.id = reviews.book_id

    WHERE books.id = ?

    GROUP BY books.id"
);

$stmt->execute([$bookId]);

$book = $stmt->fetch();

if (!$book) {
    die("Book not found.");
}

// select all reviews associated with the book
$stmt = $pdo->prepare(
    "SELECT
        reviews.*,
        users.name

    FROM reviews

    JOIN users
        ON reviews.user_id = users.id

    WHERE book_id = ?

    ORDER BY created_at DESC"
);

$stmt->execute([$bookId]);

$reviews = $stmt->fetchAll();

// check if current user has left a review
$userReview = null;

if (isset($_SESSION["user_id"])) {

    $stmt = $pdo->prepare(
        "SELECT *
         FROM reviews
         WHERE book_id = ?
         AND user_id = ?"
    );

    $stmt->execute([
        $bookId,
        $_SESSION["user_id"]
    ]);

    $userReview = $stmt->fetch();
}

$pageTitle = $book["title"];

require_once __DIR__ . "/../includes/header.php";

?>

<div class="container">
    <div class="card">

        <img
            class="book-cover"
            src="<?= BASE_URL . htmlspecialchars($book["image"]) ?>"
            alt="Book cover">
        
        <h1> <?=htmlspecialchars($book["title"]) ?> </h1>
        <h3> <?= htmlspecialchars($book["author"]) ?> </h3>

        <p> Genre: <?= htmlspecialchars($book["genre"] ?? "") ?> </p>
        <p> Pages: <?= htmlspecialchars($book["page_count"] ?? "") ?> </p>
        <p> Published: <?= htmlspecialchars($book["published_year"] ?? "") ?> </p>

        <p> <?= htmlspecialchars($book["description"] ?? "") ?> </p>

        <div class="rating">
            <?= number_format($book["average_rating"] ?? 0, 1) ?>
            ⭐
            (<?= $book["review_count"] ?> reviews)

        </div>
    </div>

    <div class="card">
        <?php if (!isset($_SESSION["user_id"])): ?>
            <p> Login to write a review. </p>

        <?php else: ?>
            <h2>
                <?php if ($userReview): ?>
                    Edit Your Review

                <?php else: ?>
                    Write a Review

                <?php endif; ?>

            </h2>

            <form method="POST" action="submit_review.php">

                <input
                    type="hidden"
                    name="book_id"
                    value="<?= $bookId ?>">

                <label> Rating </label>

                <select name="rating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>

                        <option
                            value="<?= $i ?>"
                            <?= ($userReview && $userReview["rating"] == $i)
                                ? "selected"
                                : "" ?>>

                            <?= $i ?> Stars
                        </option>

                    <?php endfor; ?>

                </select>

                <label> Review </label>

                <textarea
                    name="review_text"
                    required><?= $userReview["review_text"] ?? "" ?></textarea>

                <button>
                    <?= $userReview
                        ? "Update Review"
                        : "Post Review" ?>
                </button>

            </form>

            <?php if ($userReview): ?>
                <form method="POST"
                      action="delete_review.php">

                    <input
                        type="hidden"
                        name="review_id"
                        value="<?= $userReview["id"] ?>">

                    <input
                    type="hidden"
                    name="book_id"
                    value="<?= $bookId ?>">

                    <button>  Delete Review </button>

                </form>

            <?php endif; ?>
        <?php endif; ?>

    </div>

    <div class="card">
        <h2> Reviews </h2>

        <?php if (!$reviews): ?>
            <p> No reviews yet. </p>

        <?php endif; ?>

        <?php foreach ($reviews as $review): ?>
            <div class="card">

                <h3> <?= htmlspecialchars($review["name"]) ?> </h3>

                <div class="rating">
                    <?= str_repeat(
                        "⭐",
                        $review["rating"]
                    ) ?>

                </div>

                <p> <?= htmlspecialchars($review["review_text"]) ?> </p>

                <small> <?= $review["created_at"] ?> </small>

            </div>

        <?php endforeach; ?>

    </div>
</div>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>
