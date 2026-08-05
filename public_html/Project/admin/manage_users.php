<?php

session_start();

require_once __DIR__ . "/../../../config/db.php";
require_once __DIR__ . "/../includes/auth.php";

requireAdmin();

if (isset($_GET["action"]) && isset($_GET["id"])) {
    $id = $_GET["id"];
    $action = $_GET["action"];

    // if admin wants to disable account, update db for that user
    if ($id != $_SESSION["user_id"]) {
        if ($action === "disable") {

            $stmt = $pdo->prepare(
                "UPDATE users
                 SET is_disabled = 1
                 WHERE id = ?"
            );

            $stmt->execute([$id]);
        }

        // for enabling accounts update db
        elseif ($action === "enable") {
            $stmt = $pdo->prepare(
                "UPDATE users
                 SET is_disabled = 0
                 WHERE id = ?"
            );

            $stmt->execute([$id]);
        }

        // grant admin to a specified user
        elseif ($action === "admin") {
            $stmt = $pdo->prepare(
                "UPDATE users
                 SET is_admin = 1
                 WHERE id = ?"
            );

            $stmt->execute([$id]);
        }

        // remove admin from a specified user
        elseif ($action === "remove_admin") {
            $stmt = $pdo->prepare(
                "UPDATE users
                 SET is_admin = 0
                 WHERE id = ?"
            );

            $stmt->execute([$id]);
        }

        // delete the user and all associated rows
        elseif ($action === "delete") {

            // delete favorites for this user
            $stmt = $pdo->prepare(
                "DELETE FROM favorites
                WHERE user_id = ?"
            );
            $stmt->execute([$id]);

            // delete reviews for this user
            $stmt = $pdo->prepare(
                "DELETE FROM reviews
                WHERE user_id = ?"
            );
            $stmt->execute([$id]);

            // delete the row from the users table
            $stmt = $pdo->prepare(
                "DELETE FROM users
                WHERE id = ?"
            );
            $stmt->execute([$id]);
        }

    header("Location: manage_users.php");

    exit();
    }
}

$search = "";

if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
}

$sort = $_GET["sort"] ?? "newest";

// create order by clause for sorting
$order = match($sort) {
    "admins" => "is_admin DESC",
    "disabled" => "is_disabled DESC",
    "oldest" => "created_at ASC",
    default => "created_at DESC"
};

// select important info for users including sorting order
$sql = 
"SELECT
    id,
    name,
    email,
    is_admin,
    is_disabled,
    created_at
 FROM users
 WHERE name LIKE ?
 ORDER BY $order
";

$stmt = $pdo->prepare($sql);

$stmt->execute(["%" . $search . "%"]);
$users = $stmt->fetchAll();

$pageTitle = "Manage Users";

require_once __DIR__ . "/../includes/header.php";

?>

<div class="container">
    <a class="help-link" href="<?= BASE_URL ?>help/admin_advanced.html">
        User management guide
    </a>

    <h1> Manage Users </h1>

    <form method="GET">
        <label> Search name </label>

        <input
        type="text"
        name="search"
        value="<?= htmlspecialchars($search) ?>"
        placeholder="Search users">

        <label> Sort </label>

        <select name="sort">
            <option value="newest"> Newest </option>
            <option value="oldest"> Oldest </option>
            <option value="admins"> Admins first </option>
            <option value="disabled"> Disabled first </option>
        </select>

        <button> Search </button>

    </form>

    <table>

        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>

        <?php foreach ($users as $user): ?>
            <tr>

                <td> <?= htmlspecialchars($user["name"]) ?> </td>
                <td> <?= htmlspecialchars($user["email"]) ?> </td>

                <td>
                    <?= $user["is_admin"] ? "Admin" : "User" ?>
                </td>

                <td>
                    <?= $user["is_disabled"] ? "Disabled" : "Active" ?>
                </td>

                <td>
                    <?= $user["created_at"] ?>
                </td>

                <td>
                    <?php if ($user["is_disabled"]): ?>
                        <a href="?action=enable&id=<?= $user["id"] ?>"> Enable </a>

                    <?php else: ?>
                        <a href="?action=disable&id=<?= $user["id"] ?>"> Disable </a>

                    <?php endif; ?>

                    <br>

                    <?php if ($user["is_admin"]): ?>
                        <a href="?action=remove_admin&id=<?= $user["id"] ?>"> Remove Admin </a>

                    <?php else: ?>
                        <a href="?action=admin&id=<?= $user["id"] ?>"> Make Admin </a>

                    <?php endif; ?>

                    <br>

                    <a
                        href="?action=delete&id=<?= $user["id"] ?>"
                        onclick="return confirm('Delete user?')">
                        Delete 
                    </a>

                </td>
            </tr>

        <?php endforeach; ?>

    </table>
</div>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>
