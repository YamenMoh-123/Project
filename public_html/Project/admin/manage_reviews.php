<?php

session_start();

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../includes/auth.php";

requireAdmin();

// check if we got a delete request and we have an id
if (isset($_GET["action"]) &&
    $_GET["action"] === "delete" &&
    isset($_GET["id"])) {
   
    // delete the review with the specific id
    $stmt = $pdo->prepare(
        "DELETE FROM reviews
         WHERE id = ?"
    );

    $stmt->execute( [$_GET["id"]] );

    header("Location: manage_reviews.php");

    exit();
}

$rating = $_GET["rating"] ?? "";
$search = $_GET["search"] ?? "";
$group = $_GET["group"] ?? "newest";

// create order by clause depending on the option
$order = match($group) {
    "user" => "users.name ASC",
    "book" => "books.title ASC",
    "rating" => "reviews.rating DESC",
    default => "reviews.created_at DESC"
};

// select the review info, with the user who created it and the book its for
// join on users and books for filtering and info
$sql = "
    SELECT
        reviews.id,
        reviews.rating,
        reviews.review_text,
        reviews.created_at,

        users.name AS username,
        books.title AS book_title

    FROM reviews
    JOIN users
    ON reviews.user_id = users.id
    JOIN books
    ON reviews.book_id = books.id

    WHERE
    (
        users.name LIKE ?
        OR
        books.title LIKE ?
    )";

$params = ["%" . $search . "%", "%" . $search . "%"];

if ($rating !== "") {
    $sql .= " AND reviews.rating = ? ";
    $params[] = $rating;
}

$sql .= " ORDER BY $order";

$stmt = $pdo->prepare($sql);

$stmt->execute($params);
$reviews = $stmt->fetchAll();

$pageTitle = "Manage Reviews";

require_once __DIR__ . "/../../includes/header.php";

?>

<div class="container">
    <a class="help-link" href="<?= BASE_URL ?>help/admin_advanced.html">
        Review moderation guide
    </a>

    <h1> Manage Reviews </h1>

    <form method="GET">
        <label> Search </label>

        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="User or book">

        <label> Rating </label>

        <select name="rating">
            <option value=""> All ratings </option>

            <!-- show option to filter by 1,2,3,4, or 5 star ratings -->
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <option
                    value="<?= $i ?>"
                    <?= $rating == $i ? "selected" : "" ?>>
                    <?= $i ?> Stars
                </option>

            <?php endfor; ?>

        </select>

        <label> Group by </label>

        <select name="group">
            <option value="newest" <?= $group=="newest" ? "selected" : "" ?>> Newest </option>
            <option value="user" <?= $group=="user" ? "selected" : "" ?>> User </option>
            <option value="book" <?= $group=="book" ? "selected" : "" ?>> Book </option>
            <option value="rating" <?= $group=="rating" ? "selected" : "" ?>> Rating </option>
        </select>

        <button> Filter </button>

    </form>

    <table>
        <tr>
            <th>User</th>
            <th>Book</th>
            <th>Rating</th>
            <th>Review</th>
            <th>Date</th>
            <th>Action</th>
        </tr>

        <?php foreach ($reviews as $review): ?>
            <tr>
                <td> <?= htmlspecialchars($review["username"]) ?> </td>
                <td> <?= htmlspecialchars($review["book_title"]) ?> </td>
                
                <td>
                    <?= str_repeat("★", $review["rating"]) ?>
                </td>

                <td>
                    <?= htmlspecialchars($review["review_text"]) ?>
                </td>

                <td> <?= $review["created_at"] ?> </td>

                <td>
                    <a
                    href="?action=delete&id=<?= $review["id"] ?>"
                    onclick="return confirm('Delete review?')">
                    Delete
                    </a>
                </td>

            </tr>

        <?php endforeach; ?>

    </table>
</div>

<?php
require_once __DIR__ . "/../../includes/footer.php";
?>
