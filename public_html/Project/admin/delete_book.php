<?php

session_start();

require_once __DIR__ . "/../../../config/db.php";
require_once __DIR__ . "/../includes/auth.php";

requireAdmin();

$id = $_POST["id"] ?? null;

if (!$id) {
    die("Missing book id.");
}

// delete the row containing the book id
$stmt = $pdo->prepare(
    "DELETE FROM books
     WHERE id = ?"
);

$stmt->execute([$id]);

header( "Location: manage_books.php" );

exit();

?>