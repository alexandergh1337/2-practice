<?php
session_start();
include '../server/config.php';
include '../server/read_epub.php';
include '../server/add_books.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['epub'])) {
    $fileName = $_FILES['epub']['name'];
    $fileTmpName = $_FILES['epub']['tmp_name'];
    $uploadDir = '../assets/uploads/';
    $uploadPath = $uploadDir . $fileName;
    $extractPath = $uploadDir . pathinfo($fileName, PATHINFO_FILENAME);

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($fileTmpName, $uploadPath)) {
        $zip = new ZipArchive;
        if ($zip->open($uploadPath) === TRUE) {
            $zip->extractTo($extractPath);
            $zip->close();

            $bookData = readEpub($extractPath);
            if ($bookData) {
                $coverImagePath = $extractPath . '/cover.jpg';
                $coverImageRelPath = $uploadDir . pathinfo($fileName, PATHINFO_FILENAME) . '_cover.jpg';
                if (file_exists($coverImagePath)) {
                    if (copy($coverImagePath, $coverImageRelPath)) {
                        $bookData['cover_image'] = '../assets/uploads/' . pathinfo($fileName, PATHINFO_FILENAME) . '_cover.jpg';
                    } else {
                        $bookData['cover_image'] = '../assets/default_cover.jpg';
                    }
                } else {
                    $bookData['cover_image'] = '../assets/default_cover.jpg';
                }

                $bookData['path'] = $extractPath;
                if (addBookToDatabase($bookData)) {
                    $message = 'Книга успешно загружена и сохранена.';
                } else {
                    $message = 'Ошибка при добавлении книги в базу данных.';
                }
            } else {
                $message = 'Ошибка при чтении EPUB файла.';
            }
        } else {
            $message = 'Не удалось распаковать EPUB файл.';
        }
    } else {
        $message = 'Ошибка при загрузке файла.';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Загрузка EPUB</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
<div class="max-w-lg w-full mx-auto bg-white shadow-md rounded-lg p-6">
    <h1 class="text-2xl font-bold mb-6">Загрузка EPUB файла</h1>
    <?php if ($message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <strong class="font-bold"><?= $message ?></strong>
        </div>
    <?php endif; ?>
    <form action="upload.php" method="post" enctype="multipart/form-data" class="space-y-4">
        <div>
            <input type="file" name="epub" accept=".epub" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Загрузить</button>
        </div>
    </form>
</div>
</body>
</html>
