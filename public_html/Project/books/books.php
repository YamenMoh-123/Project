<?php

session_start();

require_once __DIR__ . "/../../../config/db.php";

$pageTitle = "Books";

require_once __DIR__ . "/../includes/header.php";

// Get book layout from settings (3, 4, 5 books per row)
$stmt = $pdo->query(
    "SELECT book_layout
     FROM settings
     WHERE id = 1"
);

$layout = $stmt->fetchColumn();

// default to 4 books per row
if (!$layout) {
    $layout = 4;
}

// search and filter values
$q = trim($_GET["q"] ?? "");
$author = trim($_GET["author"] ?? "");
$genre = trim($_GET["genre"] ?? "");
$minRating = $_GET["rating"] ?? "";
$sort = $_GET["sort"] ?? "title";

// get all genres for the dropdown
$genreStmt = $pdo->query(
    "SELECT DISTINCT genre
     FROM books
     WHERE genre IS NOT NULL
       AND genre != ''
     ORDER BY genre ASC"
);

$genres = $genreStmt->fetchAll(PDO::FETCH_COLUMN);

// create order by options for query based on the available sorting options
$orderBy = match($sort) {
    "author" => "books.author ASC",
    "rating" => "avg_rating DESC",
    "newest" => "books.published_year DESC",
    "oldest" => "books.published_year ASC",
    default => "books.title ASC"
};

$sql = "
SELECT
    books.*,
    ROUND(AVG(reviews.rating), 1) AS avg_rating,
    COUNT(reviews.id) AS review_count
FROM books
LEFT JOIN reviews
    ON books.id = reviews.book_id
WHERE 1=1
";

$params = [];

// quick search (title OR author)
if ($q !== "") {
    $sql .= "
    AND (
        books.title LIKE ?
        OR
        books.author LIKE ?
    )
    ";

    $params[] = "%{$q}%";
    $params[] = "%{$q}%";
}

// create parts of the where clause for each filter option
if ($author !== "") {
    $sql .= "
    AND books.author LIKE ?
    ";

    $params[] = "%{$author}%";
}

if ($genre !== "") {
    $sql .= "
    AND books.genre = ?
    ";

    $params[] = $genre;
}

$sql .= "
GROUP BY books.id
";

// calculate average rating
if ($minRating !== "") {
    $sql .= "
    HAVING COALESCE(AVG(reviews.rating), 0) >= ?
    ";

    $params[] = (int)$minRating;
}

$sql .= "
ORDER BY {$orderBy}
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$books = $stmt->fetchAll();

$favorites = [];

// get favorites for the current user
if (isset($_SESSION["user_id"])) {
    $stmt = $pdo->prepare(
        "SELECT book_id
         FROM favorites
         WHERE user_id = ?"
    );

    $stmt->execute([
        $_SESSION["user_id"]
    ]);

    $favorites = array_column(
        $stmt->fetchAll(),
        "book_id"
    );
}

?>

<div class="container">
    <h1> Book Catalogue </h1>

    <p> Browse and search all books available on TITLE. </p>

    <div class="card">
        <a class="help-link" href="<?= BASE_URL ?>help/user_advanced.html">
            Need help searching books?
        </a>        

        <form method="GET">
            <label> Search </label>

            <input
            type="text"
            name="q"
            value="<?= htmlspecialchars($q) ?>"
            placeholder="Search by title or author">

            <details>
                <summary> Advanced Filters </summary>

                <label> Author </label>
                <input
                type="text"
                name="author"
                value="<?= htmlspecialchars($author) ?>"
                placeholder="Filter by author">

                <label> Genre </label>
                <select name="genre">
                <option value=""> All Genres </option>

                <!-- for each genre option, display the name and mark as selected when it is selected -->
                <?php foreach ($genres as $g): ?>
                    <option value="<?= htmlspecialchars($g) ?>" <?= $genre === $g ? "selected" : "" ?>>
                        <?= htmlspecialchars($g) ?>
                    </option>

                <?php endforeach; ?>

                </select>

                <label> Minimum Average Rating </label>

                <select name="rating">
                <option value=""> Any Rating </option>

                <?php for ($i = 1; $i <= 5; $i++): ?>

                    <option value="<?= $i ?>" <?= $minRating == $i ? "selected" : "" ?>>
                        <?= $i ?> Stars &amp; Up
                    </option>

                <?php endfor; ?>

                </select>

                <label> Sort By </label>

                <select name="sort">
                    <option value="title" <?= $sort === "title" ? "selected" : "" ?>> Title </option>
                    <option value="author" <?= $sort === "author" ? "selected" : "" ?>> Author </option>
                    <option value="rating" <?= $sort === "rating" ? "selected" : "" ?>> Highest Rated </option>
                    <option value="newest" <?= $sort === "newest" ? "selected" : "" ?>> Newest </option>
                    <option value="oldest" <?= $sort === "oldest" ? "selected" : "" ?>> Oldest </option>
                </select>

            </details>

            <button type="submit"> Search </button>

        </form>
    </div>

    <h2>
        <?= $q !== "" || $author !== "" || $genre !== "" || $minRating !== ""
            ? "Search Results (" . count($books) . ")"
            : "All Books (" . count($books) . ")"
        ?>

    </h2>

    <?php if (empty($books)): ?>
        <p> No books matched your search. </p>

    <?php else: ?>
        <div class="book-grid-<?= htmlspecialchars($layout) ?>">

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

            <h2>
                <?= htmlspecialchars($book["title"]) ?>
            </h2>

            <p>

            <strong>Author:</strong>
                <?= htmlspecialchars($book["author"]) ?>
            </p>

            <p>
                <strong>Genre:</strong>
                ?= htmlspecialchars($book["genre"] ?: "Unknown") ?>
            </p>

            <p>
                <strong>Pages:</strong>
                <?= htmlspecialchars($book["page_count"] ?: "N/A") ?>
            </p>

            <p>
                <strong>Year:</strong>
                <?= htmlspecialchars($book["published_year"] ?: "N/A") ?>
            </p>

            <p class="rating">

            <?php if ($book["review_count"] > 0): ?>
                ⭐ <?= htmlspecialchars($book["avg_rating"]) ?>
                (<?= htmlspecialchars($book["review_count"]) ?>
                review<?= $book["review_count"] == 1 ? "" : "s" ?>)

            <?php else: ?>
                No reviews yet

            <?php endif; ?>

            </p>

            <?php if (isset($_SESSION["user_id"])): ?>
                <?php if (in_array($book["id"], $favorites)): ?>

                    <a
                        class="button"
                        href="toggle_favorite.php?book_id=<?= urlencode($book["id"]) ?>">
                        ★ Favorited
                    </a>

                <?php else: ?>
                    <a
                        class="button"
                        href="toggle_favorite.php?book_id=<?= urlencode($book["id"]) ?>">
                        ☆ Add to Favorites
                    </a>

                <?php endif; ?>
            <?php endif; ?>

            <a
                class="button"
                href="book.php?id=<?= urlencode($book["id"]) ?>">
                View Details
            </a>
        </div>

        <?php endforeach; ?>
    </div>

    <?php endif; ?>

</div>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>
