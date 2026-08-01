<?php

// load environment variabls from .env
$envFile = __DIR__ . "/../.env";

if (file_exists($envFile)) {

    $fileContents = file($envFile, FILE_IGNORE_NEW_LINES);

    foreach ($fileContents as $entry) {
        list($key, $value) = explode("=", $entry, 2);
        $_ENV[$key] = $value;
    }
}

# required variables from env file
$hostname = $_ENV["DB_HOST"];
$db_name   = $_ENV["DB_NAME"];
$user_name = $_ENV["DB_USER"];
$password = $_ENV["DB_PASS"];

$connection_string = "mysql:host=$hostname;dbname=$db_name;charset=utf8mb4";

// connect to database
try {
    $pdo = new PDO($connection_string, $user_name, $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

} catch (PDOException) {
    die(
        "Database connection failed: "
    );
}

?>