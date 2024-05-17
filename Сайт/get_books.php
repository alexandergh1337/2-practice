<?php
include 'config.php';

header('Content-Type: application/json');

$sql = "SELECT Books.book_id, Books.title, Books.publication_year, Authors.name AS author_name, Genres.genre_name
        FROM Books
        JOIN Authors ON Books.author_id = Authors.author_id
        JOIN Genres ON Books.genre_id = Genres.genre_id";
$result = $conn->query($sql);

$books = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}
echo json_encode($books);

$conn->close();
?>
