<?php
function connectToDatabase() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "OnlineLibrary";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function registerUser($conn, $username, $password, $email) {
    $password = password_hash($password, PASSWORD_DEFAULT);
    $registration_date = date('Y-m-d');
    $sql = "INSERT INTO Users (username, password, email, registration_date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $password, $email, $registration_date);
    if ($stmt->execute() === TRUE) {
        return true;
    } else {
        error_log("Ошибка: " . $stmt->error);
        return false;
    }
}

function getUserByUsername($conn, $username) {
    $sql = "SELECT * FROM Users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
?>