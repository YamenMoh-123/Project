<?php

session_start();

require_once __DIR__ . "/../../../config/db.php";
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../includes/image_upload.php";

requireAdmin();

$id = $_GET["id"] ?? null;

if (!$id) {
    die("Missing book id.");
}

$stmt = $pdo->prepare(
    "SELECT *
     FROM books
     WHERE id = ?"
);

$stmt->execute([$id]);

$book = $stmt->fetch();

if (!$book) {
    die("Book not found.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"]);
    $author = trim($_POST["author"]);
    $pages = $_POST["page_count"];
    $description = trim($_POST["description"]);
    $genre = trim($_POST["genre"]);
    $year = $_POST["published_year"];

    if (!$title || !$author) {
        $error = "Title and author are required.";
    }

    else {
        $newImage = uploadBookImage($id);

        if ($newImage) {
            $image = $newImage;
        }
        else {
            $image = $book["image"];
        }

        $stmt = $pdo->prepare(
            "UPDATE books

            SET
                title=?,
                author=?,
                page_count=?,
                description=?,
                image=?,
                genre=?,
                published_year=?

            WHERE id=?"
        );

        $stmt->execute([
            $title,
            $author,
            $pages,
            $description,
            $image,
            $genre,
            $year,
            $id
        ]);

        header( "Location: manage_books.php" );

        exit();
    }
}

$pageTitle = "Edit Book";
require_once __DIR__ . "/../includes/header.php";

?>

<div class="container">

    <h1> Edit Book </h1>

    <form  method="POST" enctype="multipart/form-data">

        <label> Title </label>
        <input
        name="title"
        value="<?= htmlspecialchars($book["title"]) ?>">

        <label> Author </label>
        <input
        name="author"
        value="<?= htmlspecialchars($book["author"]) ?>">

        <label> Pages </label>
        <input
        type="number"
        name="page_count"
        value="<?= $book["page_count"] ?>">

        <label> Genre </label>
        <input
        name="genre"
        value="<?= htmlspecialchars($book["genre"] ?? "") ?>">

        <label> Published Year </label>
        <input
        type="number"
        name="published_year"
        value="<?= $book["published_year"] ?>">

        <label> Description </label>
        <textarea name="description"><?= htmlspecialchars($book["description"] ?? "") ?></textarea>

        <label> Replace Cover Image </label>
        <input type="file" name="image" accept="image/*">

        <button> Save Changes </button>

    </form>
</div>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>
