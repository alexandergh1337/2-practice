<?php
include 'config.php';
include 'read_epub.php';
include 'add_books.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['epub'])) {
    $fileName = $_FILES['epub']['name'];
    $fileTmpName = $_FILES['epub']['tmp_name'];
    $uploadDir = 'uploads/';
    $uploadPath = $uploadDir . $fileName;
    $extractPath = $uploadDir . pathinfo($fileName, PATHINFO_FILENAME);

    if (move_uploaded_file($fileTmpName, $uploadPath)) {
        $zip = new ZipArchive;
        if ($zip->open($uploadPath) === TRUE) {
            $zip->extractTo($extractPath);
            $zip->close();

            $bookData = readEpub($extractPath);
            if ($bookData) {
                $bookData['path'] = $extractPath;
                if (addBookToDatabase($bookData)) {
                    echo 'Книга успешно загружена и сохранена.';
                } else {
                    echo 'Ошибка при добавлении книги в базу данных.';
                }
            } else {
                echo 'Ошибка при чтении EPUB файла.';
            }
        } else {
            echo 'Не удалось распаковать EPUB файл.';
        }
    } else {
        echo 'Ошибка при загрузке файла.';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Загрузка EPUB</title>
</head>
<body>
<h1>Загрузка EPUB файла</h1>
<form action="upload.php" method="post" enctype="multipart/form-data">
    <input type="file" name="epub" accept=".epub">
    <button type="submit">Загрузить</button>
</form>
</body>
</html>
