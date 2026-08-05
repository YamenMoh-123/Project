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
    $format = $_POST["format"] ?? "Paperback";
    $language = $_POST["language"] ?? "English";

    if (!$title || !$author) {
        $error = "Title and author are required.";
    }
    if (empty($format)) {
        $errors[] = "Format is required.";
    }
    if (empty($language)) {
        $errors[] = "Language is required.";
    }
    else {
        $stmt = $pdo->prepare(
            "INSERT INTO books
            (
                title,
                author,
                genre,
                page_count,
                published_year,
                description,
                image,
                format,
                language
            )
            VALUES
            (
                ?, ?, ?, ?, ?, ?, ?, ?, ?
            )"
        );

        $stmt->execute([
            $title,
            $author,
            $genre,
            $pages,
            $year,
            $description,
            $image,
            $format,
            $language
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

        <label>Format</label>
        <select name="format" required>
            <option value="Hardcover">Hardcover</option>
            <option value="Paperback" selected>Paperback</option>
            <option value="eBook">eBook</option>
        </select>

        <label>Language</label>
        <select name="language" required>
            <option value="English" selected>English</option>
            <option value="French">French</option>
            <option value="Arabic">Arabic</option>
            <option value="Spanish">Spanish</option>
        </select>

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
