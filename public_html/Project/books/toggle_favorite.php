<?php

require_once __DIR__ . "/../includes/auth.php";
requireLogin();

require_once __DIR__ . "/../../../config/db.php";

if (!isset($_GET["book_id"])) {
    header("Location: books.php");
    exit();
}

$bookId = (int) $_GET["book_id"];
$userId = $_SESSION["user_id"];

// check if the current book is favorited
$stmt = $pdo->prepare(
    "SELECT 1
     FROM favorites
     WHERE user_id = ?
       AND book_id = ?"
);

$stmt->execute([$userId, $bookId]);

if ($stmt->fetch()) {

    // if query has results, then book is a favorite, so remove it
    $stmt = $pdo->prepare(
        "DELETE FROM favorites
         WHERE user_id = ?
           AND book_id = ?"
    );

    $stmt->execute([$userId, $bookId]);

} else {

    // if query has no result, then row does not exist in favorite table, add it
    $stmt = $pdo->prepare(
        "INSERT INTO favorites
        (
            user_id,
            book_id
        )
        VALUES
        (
            ?,
            ?
        )"
    );

    $stmt->execute([$userId, $bookId]);
}

// redirect back to the page the user came from
$return = $_SERVER["HTTP_REFERER"] ?? "books.php";

header("Location: $return");
exit();

?>
