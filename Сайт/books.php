<?php
include 'config.php';

function findPages($bookPath) {
    if (!is_dir($bookPath)) {
        throw new UnexpectedValueException("Неверно задано имя папки: $bookPath");
    }

    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($bookPath));
    $pages = [];
    foreach ($rii as $file) {
        if (!$file->isDir() && $file->getExtension() === 'html') {
            $pages[] = file_get_contents($file->getPathname());
        }
    }
    return $pages;
}

$bookId = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;
if ($bookId > 0) {
    $sql = "SELECT * FROM Books WHERE book_id = $bookId";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
        $bookPath = $book['path'];

        try {
            $pages = findPages($bookPath);
            echo "<h1>{$book['title']}</h1>";
            foreach ($pages as $page) {
                echo "<div class='page'>{$page}</div>";
            }
        } catch (UnexpectedValueException $e) {
            echo "Ошибка: " . $e->getMessage();
        }
    } else {
        echo "Книга не найдена.";
    }
} else {
    echo "Неверный идентификатор книги.";
}
?>
