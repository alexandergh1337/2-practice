<?php
include 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$book_id = $data['book_id'];
$pages = $data['pages'];

foreach ($pages as $page_number => $content) {
    $sql = "INSERT INTO BookPages (book_id, page_number, content) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $book_id, $page_number, $content);

    if ($stmt->execute() !== TRUE) {
        echo json_encode(["message" => "Ошибка: " . $stmt->error]);
        $stmt->close();
        $conn->close();
        exit;
    }
}

echo json_encode(["message" => "Страницы добавлены"]);

$stmt->close();
$conn->close();
?>
