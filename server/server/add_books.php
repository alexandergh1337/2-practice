<?php
include 'config.php';

function getAuthorId($authorName) {
    global $conn;

    $sql = "SELECT author_id FROM Authors WHERE name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $authorName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $author = $result->fetch_assoc();
        return $author['author_id'];
    } else {
        $sql = "INSERT INTO Authors (name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $authorName);
        $stmt->execute();
        return $stmt->insert_id;
    }
}

function getGenreId($genreName) {
    global $conn;

    $sql = "SELECT genre_id FROM Genres WHERE genre_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $genreName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $genre = $result->fetch_assoc();
        return $genre['genre_id'];
    } else {
        $sql = "INSERT INTO Genres (genre_name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $genreName);
        $stmt->execute();
        return $stmt->insert_id;
    }
}

function addBookToDatabase($bookData) {
    global $conn;

    $sql = "INSERT INTO Books (title, author_id, genre_id, publication_year, description, cover_image, path)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        return false;
    }

    $authorId = getAuthorId($bookData['author']);
    $genreId = getGenreId($bookData['genre']);
    $stmt->bind_param('siissss', $bookData['title'], $authorId, $genreId, $bookData['publication_year'], $bookData['description'], $bookData['cover_image'], $bookData['path']);

    $result = $stmt->execute();
    $stmt->close();

    return $result;
}
?>
