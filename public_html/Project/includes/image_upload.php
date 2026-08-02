<?php

function uploadBookImage($bookId)
{
    if (!isset($_FILES["image"]) ||
        $_FILES["image"]["error"] === UPLOAD_ERR_NO_FILE ) {
        return null;
    }

    $file = $_FILES["image"];

    if ($file["error"] !== UPLOAD_ERR_OK ) {
        die("Image upload failed.");
    }

    if ($file["size"] > 2 * 1024 * 1024) {
        die("Image must be smaller than 2MB.");
    }

    if (getimagesize($file["tmp_name"]) === false) {
        die("File is not a valid image.");
    }

    $extension = strtolower(
        pathinfo(
            $file["name"],
            PATHINFO_EXTENSION
        )
    );

    $allowed = [
        "jpg",
        "jpeg",
        "png",
        "webp"
    ];

    if (!in_array($extension, $allowed)) {
        die("Invalid image type.");
    }

    $uploadDirectory =
    __DIR__ . "/../public_html/Project/assets/images/books/";

    // create the directory for images if it dosent exist
    if (!is_dir($uploadDirectory)) {
        mkdir( $uploadDirectory, 0755, true );
    }

    $filename = "book_" . $bookId . "." . $extension;

    $destination = $uploadDirectory . $filename;

    // delete old version of the image for the same book
    foreach ($allowed as $oldExtension) {
        $oldFile = $uploadDirectory . "book_" . $bookId . "." . $oldExtension;

        if (file_exists($oldFile)) {
            unlink($oldFile);
        }
    }

    if (!move_uploaded_file( $file["tmp_name"], $destination)) {
        die(
            "Could not save image: " . $destination
        );
    }

    return "assets/images/books/" . $filename;
}

?>
