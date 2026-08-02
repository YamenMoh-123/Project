<?php

session_start();

require_once __DIR__ . "/../../../config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();

}

$bookId = $_POST["book_id"];
$rating = $_POST["rating"];
$text = trim($_POST["review_text"]);

$stmt = $pdo->prepare(
    "INSERT INTO reviews
    (
        book_id,
        user_id,
        rating,
        review_text
    )

    VALUES
    (
        ?,
        ?,
        ?,
        ?
    )

    ON DUPLICATE KEY UPDATE

        rating = VALUES(rating),
        review_text = VALUES(review_text)"
);

$stmt->execute([
    $bookId,
    $_SESSION["user_id"],
    $rating,
    $text
]);

// redirect user back to the book detail page
header( "Location: book.php?id=" . $bookId );

exit();

?>