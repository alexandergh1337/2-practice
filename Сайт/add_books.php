<?php
include 'config.php';

function addBookToDatabase($bookData) {
    global $conn;

    // Проверка и добавление автора
    $author = $conn->real_escape_string($bookData['author']);
    $sql = "SELECT author_id FROM Authors WHERE name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $author);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $author_id = $result->fetch_assoc()['author_id'];
    } else {
        $sql = "INSERT INTO Authors (name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $author);
        if ($stmt->execute()) {
            $author_id = $stmt->insert_id;
        } else {
            echo "Ошибка при добавлении автора: " . $conn->error;
            return false;
        }
    }

    // Проверка и добавление жанра (по умолчанию 'Неизвестный жанр')
    $genre = $conn->real_escape_string($bookData['genre']);
    $sql = "SELECT genre_id FROM Genres WHERE genre_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $genre);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $genre_id = $result->fetch_assoc()['genre_id'];
    } else {
        $sql = "INSERT INTO Genres (genre_name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $genre);
        if ($stmt->execute()) {
            $genre_id = $stmt->insert_id;
        } else {
            echo "Ошибка при добавлении жанра: " . $conn->error;
            return false;
        }
    }

    // Добавление книги
    $title = $conn->real_escape_string($bookData['title']);
    $description = $conn->real_escape_string($bookData['description']);
    $publication_year = intval($bookData['publication_year']);
    $path = $conn->real_escape_string($bookData['path']);

    $sql = "INSERT INTO Books (title, author_id, genre_id, description, publication_year, path) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siisss", $title, $author_id, $genre_id, $description, $publication_year, $path);
    if ($stmt->execute()) {
        return true;
    } else {
        echo "Ошибка при добавлении книги: " . $conn->error;
        return false;
    }
}
?>
