<?php

require_once __DIR__ . "/includes/auth.php";

requireLogin();

$pageTitle = "My Profile";

require_once __DIR__ . "/includes/header.php";

?>

<div class="container">
    <h1> My Profile </h1>

    <div class="card">
        <h2> Welcome, <?= htmlspecialchars($_SESSION["name"]) ?> </h2>

        <p>
            <strong>Name:</strong> <?= htmlspecialchars($_SESSION["name"]) ?>
        </p>

        <p>
            <strong>Email:</strong> <?= htmlspecialchars($_SESSION["email"]) ?>
        </p>

        <p>
            <strong>Account Type:</strong>

            <?php if ($_SESSION["is_admin"] == 1): ?>
                Administrator

            <?php else: ?>
                User

            <?php endif; ?>
        </p>
    </div>

    <div class="card">
        <h2> Account Options </h2>

        <a class="button" href="logout.php"> Logout </a>
    </div>
</div>

<?php
require_once __DIR__ . "/includes/footer.php";
?>
