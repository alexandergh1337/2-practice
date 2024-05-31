<?php
session_start();
require_once 'user_functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = connectToDatabase();
    if (registerUser($conn, $_POST["username"], $_POST["password"], $_POST["email"])) {
        header("Location: index.php");
        exit();
    } else {
        echo "Ошибка при регистрации пользователя.";
    }
    $conn->close();
} else {
    die('This script should be accessed via POST request');
}
?>