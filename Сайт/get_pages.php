<?php
include 'config.php';

header('Content-Type: application/json');

$book_id = $_GET['book_id'];

$sql = "SELECT * FROM BookPages WHERE book_id = ? ORDER BY page_number ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

$pages = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pages[] = $row;
    }
    echo json_encode($pages);
} else {
    echo json_encode(["message" => "Страницы не найдены"]);
}

$stmt->close();
$conn->close();
?>
