<?php
include 'config.php';

function addPagesToBook($bookId, $pages) {
    global $conn;
    $bookId = intval($bookId);

    foreach ($pages as $page) {
        $content = $conn->real_escape_string($page['content']);
        $sql = "INSERT INTO Pages (book_id, content) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $bookId, $content);
        if (!$stmt->execute()) {
            echo "Ошибка при добавлении страницы: " . $conn->error;
            return false;
        }
    }

    return true;
}
?>
