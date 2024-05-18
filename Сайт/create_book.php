<?php
include 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$title = $data['title'];
$author_id = $data['author_id'];
$genre_id = $data['genre_id'];
$publication_year = $data['publication_year'];
$description = $data['description'];
$pages = $data['pages'];

$sql = "INSERT INTO Books (title, author_id, genre_id, publication_year, description) VALUES ('$title', '$author_id', '$genre_id', '$publication_year', '$description')";

if ($conn->query($sql) === TRUE) {
    $book_id = $conn->insert_id;
    foreach ($pages as $page_number => $content) {
        $sql = "INSERT INTO BookPages (book_id, page_number, content) VALUES ('$book_id', '$page_number', '$content')";
        if ($conn->query($sql) !== TRUE) {
            echo json_encode(["message" => "Ошибка: " . $sql . "<br>" . $conn->error]);
            $conn->close();
            exit;
        }
    }
    echo json_encode(["message" => "Книга добавлена", "book_id" => $book_id]);
} else {
    echo json_encode(["message" => "Ошибка: " . $sql . "<br>" . $conn->error]);
}

$conn->close();
?>
