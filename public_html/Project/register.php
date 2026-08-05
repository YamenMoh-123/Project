<?php

session_start();
require_once __DIR__ . "/../../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];

    if ( empty($name) || empty($email) || empty($password) ) {
        $error = "All fields are required.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    }

    elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    }
    else {
        // check if the email already exists by selecting user from the db with the same email (duplicate)
        $stmt = $pdo->prepare(
            "SELECT id
             FROM users
             WHERE email = ?"
        );

        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = "An account with this email already exists.";
        }
        else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // hash the password and insert a new users row
            $stmt = $pdo->prepare(
                "INSERT INTO users
                (
                    name,
                    email,
                    password_hash,
                    is_admin,
                    is_disabled
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    0,
                    0
                )"
            );

            $stmt->execute([
                $name,
                $email,
                $passwordHash
            ]);

            // Redirect to login page with success flag
            header("Location: login.php?registered=1");
            exit();
        }
    }
}

$pageTitle = "Register";
require_once __DIR__ . "/includes/header.php";
?>

<div class="container">
    <h1>Create Account</h1>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Name</label>
        <input type="text" name="name" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>

        <button type="submit">Register</button>
    </form>

    <a class="help-link" href="<?= BASE_URL ?>help/user_basics.html">
        New to Book Archive?
    </a>

    <p>
        Already have an account?
        <a href="login.php">Login</a>
    </p>

</div>

<?php
require_once __DIR__ . "/includes/footer.php";
?>
