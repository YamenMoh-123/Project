<?php

require_once __DIR__ . "/../../../includes/auth.php";

// make sure only admins can go to this page
requireAdmin();

$pageTitle = "Admin Dashboard";

require_once __DIR__ . "/../../../includes/header.php";

?>


<div class="container">

    <h1> Admin Dashboard </h1>

    <p>
        Welcome,
        <?= htmlspecialchars($_SESSION["name"]) ?>
    </p>

    <div class="card">
        <h2> User Management </h2>
        <p> View, disable, and manage user accounts. </p>

        <a class="button" href="users.php"> Manage Users </a>

    </div>

    <div class="card">
        <h2> Book Management </h2>
        <p> Add, edit, and remove books. </p>

        <a class="button" href="books.php"> Manage Books </a>

    </div>

    <div class="card">
        <h2> Review Moderation </h2>
        <p> Manage reviews and comments. </p>

        <a class="button" href="reviews.php"> Manage Reviews </a>

    </div>

    <div class="card">
        <h2> Website Status </h2>

        <table>
            <tr>
                <th> Service </th>
                <th> Status </th>
            </tr>

            <tr>
                <td> PHP </td>
                <td> Online </td>
            </tr>

            <tr>
                <td> Database </td>
                <td> Online </td>
            </tr>

            <tr>
                <td> Website </td>
                <td> Online </td>
            </tr>
        </table>
    </div>
</div>

<?php

require_once __DIR__ . "/../../../includes/footer.php";

?>