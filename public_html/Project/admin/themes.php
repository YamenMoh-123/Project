<?php

require_once __DIR__ . "/../includes/auth.php";

requireAdmin();

require_once __DIR__ . "/../../../config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $theme = $_POST["theme"];
    $layout = $_POST["layout"];

    $allowedThemes = [
        "classic",
        "dark",
        "modern"
    ];

    $allowedLayouts = [3, 4, 5];

    // ensure the requested theme and layout are possible options
    if (in_array($theme, $allowedThemes) && in_array((int)$layout, $allowedLayouts)) {

        // update the settings table with then new theme and layout
        $stmt = $pdo->prepare(
            "UPDATE settings
             SET site_theme = ?,
                 book_layout = ?
             WHERE id = 1"
        );

        $stmt->execute([
            $theme,
            $layout
        ]);

        $message = "Website settings updated successfully.";
    }
}

$stmt = $pdo->query(
    // fetch the theme and layout values from the settings table to display
    "SELECT 
        site_theme,
        book_layout
     FROM settings
     WHERE id = 1"
);

$settings = $stmt->fetch();
$pageTitle = "Website Settings";

require_once __DIR__ . "/../includes/header.php";

?>

<div class="container">
    <h1> Website Settings </h1>

    <?php if ($message): ?>
        <p class="success"> <?= htmlspecialchars($message) ?> </p>

    <?php endif; ?>

    <div class="card">
        <h2> Theme </h2>

        <form method="POST">
            <label>
                <input 
                type="radio"
                name="theme"
                value="classic"
                <?= $settings["site_theme"] == "classic" ? "checked" : "" ?>>
                Classic Library
            </label>

            <label>
                <input 
                type="radio"
                name="theme"
                value="dark"
                <?= $settings["site_theme"] == "dark" ? "checked" : "" ?>>
                Dark Mode
            </label>

            <label>
                <input 
                type="radio"
                name="theme"
                value="modern"
                <?= $settings["site_theme"] == "modern" ? "checked" : "" ?>>
                Modern Blue
            </label>

            <br><br>

            <h2> Book Layout </h2>

            <label>
                <input
                type="radio"
                name="layout"
                value="3"
                <?= $settings["book_layout"] == 3 ? "checked" : "" ?>>
                3 Books Per Row
            </label>

            <label>
                <input
                type="radio"
                name="layout"
                value="4"
                <?= $settings["book_layout"] == 4 ? "checked" : "" ?>>
                4 Books Per Row
            </label>

            <label>
                <input
                type="radio"
                name="layout"
                value="5"
                <?= $settings["book_layout"] == 5 ? "checked" : "" ?>>
                5 Books Per Row
            </label>

            <br><br>

            <button type="submit">
                Save Settings
            </button>

        </form>
    </div>
</div>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>
