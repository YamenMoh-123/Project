<?php

session_start();

require_once __DIR__ . "/../../../config/db.php";
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../includes/image_upload.php";

requireAdmin();

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
        $stmt = $pdo->prepare(
            "INSERT INTO books
            (
                title,
                author,
                page_count,
                description,
                genre,
                published_year
            )

            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?
            )"
        );

        $stmt->execute([
            $title,
            $author,
            $pages,
            $description,
            $genre,
            $year
        ]);

        $bookId = $pdo->lastInsertId();

        $image = uploadBookImage($bookId);

        if ($image) {
            $stmt = $pdo->prepare(
                "UPDATE books
                 SET image = ?
                 WHERE id = ?"
            );

            $stmt->execute([
                $image,
                $bookId
            ]);
        }

        header( "Location: manage_books.php" );
        exit();
    }
}

$pageTitle = "Add Book";

require_once __DIR__ . "/../includes/header.php";

?>

<div class="container">

    <h1> Add Book </h1>

    <?php if ($error): ?>
        <p class="error"> <?= htmlspecialchars($error) ?> </p>

    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <label> Title </label>
        <input name="title" required>

        <label> Author </label>
        <input name="author" required>

        <label> Pages </label>
        <input type="number" name="page_count" required>

        <label> Genre </label>
        <input name="genre" requierd>

        <label> Published Year </label>
        <input type="number" name="published_year" required>

        <label> Description </label>
        <textarea name="description"></textarea required>

        <label> Book Cover </label>
        <input type="file" name="image" accept="image/*">

        <button> Add Book </button>

    </form>
</div>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>
