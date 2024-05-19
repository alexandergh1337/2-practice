<?php
include 'config.php';

function addBookToDatabase($bookData) {
    global $conn;

    // Проверка и добавление автора
    $author = $conn->real_escape_string($bookData['author']);
    $sql = "SELECT author_id FROM Authors WHERE name = '$author'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $author_id = $result->fetch_assoc()['author_id'];
    } else {
        $sql = "INSERT INTO Authors (name) VALUES ('$author')";
        if ($conn->query($sql) === TRUE) {
            $author_id = $conn->insert_id;
        } else {
            echo "Ошибка при добавлении автора: " . $conn->error;
            return false;
        }
    }

    // Проверка и добавление жанра (по умолчанию 'Неизвестный жанр')
    $genre = $conn->real_escape_string($bookData['genre']);
    $sql = "SELECT genre_id FROM Genres WHERE genre_name = '$genre'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $genre_id = $result->fetch_assoc()['genre_id'];
    } else {
        $sql = "INSERT INTO Genres (genre_name) VALUES ('$genre')";
        if ($conn->query($sql) === TRUE) {
            $genre_id = $conn->insert_id;
        } else {
            echo "Ошибка при добавлении жанра: " . $conn->error;
            return false;
        }
    }

    // Добавление книги
    $title = $conn->real_escape_string($bookData['title']);
    $publication_year = $conn->real_escape_string($bookData['publication_year']);
    $description = $conn->real_escape_string($bookData['description']);

    $sql = "INSERT INTO Books (title, author_id, genre_id, publication_year, description) 
            VALUES ('$title', '$author_id', '$genre_id', '$publication_year', '$description')";
    if ($conn->query($sql) === TRUE) {
        $book_id = $conn->insert_id;

        // Добавление страниц книги
        foreach ($bookData['pages'] as $page_number => $page_content) {
            $page_content = $conn->real_escape_string(trim($page_content));
            $sql = "INSERT INTO BookPages (book_id, page_number, content) VALUES ('$book_id', '$page_number', '$page_content')";
            if ($conn->query($sql) !== TRUE) {
                echo "Ошибка при добавлении страницы: " . $conn->error;
                return false;
            }
        }
        return true;
    } else {
        echo "Ошибка при добавлении книги: " . $conn->error;
        return false;
    }
}
?>
