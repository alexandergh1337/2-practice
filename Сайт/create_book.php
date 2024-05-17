<?php
include 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$title = $data['title'];
$author_id = $data['author_id'];
$genre_id = $data['genre_id'];
$publication_year = $data['publication_year'];

// Используем подготовленные выражения для предотвращения SQL-инъекций
$sql = "INSERT INTO Books (title, author_id, genre_id, publication_year) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("siii", $title, $author_id, $genre_id, $publication_year);

if ($stmt->execute()) {
    echo json_encode(["message" => "Книга добавлена", "book_id" => $conn->insert_id]);
} else {
    echo json_encode(["message" => "Ошибка: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
