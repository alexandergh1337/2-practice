<?php
include 'config.php';
include 'read_epub.php';
include 'add_books.php';

$dir = 'books/'; // Директория с ePub файлами
$files = glob($dir . '*.epub');

foreach ($files as $file_path) {
    $bookData = readEpub($file_path);
    if ($bookData && addBookToDatabase($bookData)) {
        echo "Книга {$bookData['title']} успешно добавлена.<br>";
    } else {
        echo "Ошибка при добавлении книги {$bookData['title']}.<br>";
    }
}

$conn->close();
?>
