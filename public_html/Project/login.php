<?php

session_start();

require_once __DIR__ . "/../../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $pdo->prepare(
        "SELECT 
            id,
            name,
            email,
            password_hash,
            is_admin,
            is_disabled
        FROM users
        WHERE email = ?"
    );

    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = "Invalid email or password";

    } elseif ($user["is_disabled"]) {
        $error = "Your account has been disabled";

    } elseif (!password_verify($password, $user["password_hash"])) {
        $error = "Invalid email or password";

    } else {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["email"] = $user["email"];
        $_SESSION["is_admin"] = $user["is_admin"];

        if ($user["is_admin"] == 1) {
            header("Location: admin/dashboard.php");

        } else {
            header("Location: profile.php");
        }

        exit();
    }
}

$pageTitle = "Login";

require_once __DIR__ . "/../../includes/header.php";

?>

<div class="container">
    <h1> Login </h1>

    <?php if ($error): ?>
        <p class="error">
            <?= htmlspecialchars($error) ?>
        </p>

    <?php endif; ?>

    <form method="POST">
        <label> Email </label>

        <input
            type="email"
            name="email"
            required>

        <label> Password </label>

        <input
            type="password"
            name="password"
            required>

        <button type="submit"> Login </button>
    </form>

    <p>
        Don't have an account?
        <a href="<?= BASE_URL ?>register.php"> Register </a>
    </p>
</div>

<?php

require_once __DIR__ . "/../../includes/footer.php";

?>