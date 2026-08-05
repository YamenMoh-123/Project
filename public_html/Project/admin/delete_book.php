<?php

require_once __DIR__ . "/../includes/auth.php";

requireAdmin();

require_once __DIR__ . "/../../../config/db.php";

// if we cant find the book id redirect back
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["id"])) {
    header("Location: manage_books.php");
    exit;
}

$bookId = $_POST["id"];

// first we get the image path for this book id from the db
$stmt = $pdo->prepare(
    "SELECT image
     FROM books
     WHERE id = ?"
);

$stmt->execute([$bookId]);
$book = $stmt->fetch();

// next, we delete the all the favorites associated with this book
if ($book) {
    $stmt = $pdo->prepare(
        "DELETE FROM favorites
         WHERE book_id = ?"
    );

    $stmt->execute([
        $bookId
    ]);

    // also delete the reviews for this book
    $stmt = $pdo->prepare(
        "DELETE FROM reviews
         WHERE book_id = ?"
    );

    $stmt->execute([
        $bookId
    ]);

    // next, we delete the books cover image from our folder
    if (!empty($book["image"])) {
        $imagePath = __DIR__ . "/../../" . $book["image"];

        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    // lastly delete the actual book entry from the db
    $stmt = $pdo->prepare(
        "DELETE FROM books
         WHERE id = ?"
    );

    $stmt->execute([
        $bookId
    ]);

}

header("Location: manage_books.php");

exit;

?>
