<?php
include 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$title = $data['title'];
$author_id = intval($data['author_id']);
$genre_id = intval($data['genre_id']);
$publication_year = intval($data['publication_year']);
$description = $data['description'];
$pages = $data['pages'];

$sql = "INSERT INTO Books (title, author_id, genre_id, publication_year, description) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("siiss", $title, $author_id, $genre_id, $publication_year, $description);

if ($stmt->execute()) {
    $book_id = $stmt->insert_id;
    $response = ['status' => 'success', 'book_id' => $book_id];
    foreach ($pages as $page) {
        $sql = "INSERT INTO Pages (book_id, content) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $book_id, $page);
        $stmt->execute();
    }
} else {
    $response = ['status' => 'error', 'message' => 'Error adding book: ' . $conn->error];
}

echo json_encode($response);
?>
