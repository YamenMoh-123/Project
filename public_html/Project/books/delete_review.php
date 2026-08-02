<?php

session_start();

require_once __DIR__ . "/../../../config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();

}

$bookId = $_POST["book_id"];
$reviewId = $_POST["review_id"];

// query to delete the row for the review associated with a user and a book
$stmt = $pdo->prepare(
    "DELETE FROM reviews
     WHERE id = ?
     AND user_id = ?"
);

$stmt->execute([
    $reviewId,
    $_SESSION["user_id"]
]);

// redirect user to the book detail page
header( "Location: book.php?id=" . $bookId );
exit();

?>