<?php
include 'config.php';

$book_id = $_GET['book_id'];

$sql = "SELECT * FROM Books WHERE book_id = '$book_id'";
$book_result = $conn->query($sql);

if ($book_result->num_rows > 0) {
    $book = $book_result->fetch_assoc();
} else {
    echo "Книга не найдена.";
    exit;
}

$sql = "SELECT * FROM BookPages WHERE book_id = '$book_id' ORDER BY page_number ASC";
$pages_result = $conn->query($sql);

$pages = [];
if ($pages_result->num_rows > 0) {
    while ($row = $pages_result->fetch_assoc()) {
        $pages[] = $row;
    }
} else {
    echo "Страницы не найдены.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($book['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .hidden {
            display: none;
        }
    </style>
</head>
<body class="bg-gray-100">
<header class="bg-blue-600 p-4 text-white text-center">
    <h1><?= htmlspecialchars($book['title']); ?></h1>
</header>
<main class="p-4">
    <h2 class="text-xl font-bold">Автор: <?= htmlspecialchars($book['author_id']); ?></h2>
    <h3 class="text-lg">Жанр: <?= htmlspecialchars($book['genre_id']); ?></h3>
    <p class="mb-4"><?= htmlspecialchars($book['description']); ?></p>

    <div class="pages">
        <?php foreach ($pages as $page): ?>
            <div class="page <?= $page['page_number'] == 0 ? '' : 'hidden'; ?>" id="page-<?= $page['page_number']; ?>">
                <?= htmlspecialchars($page['content']); ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="navigation mt-4">
        <button id="prev" class="bg-blue-600 text-white px-4 py-2 rounded" onclick="navigate(-1)">Назад</button>
        <button id="next" class="bg-blue-600 text-white px-4 py-2 rounded" onclick="navigate(1)">Вперед</button>
    </div>
</main>
<footer class="bg-blue-600 p-4 text-white text-center">
    &copy; 2024 Онлайн Библиотека
</footer>

<script>
    let currentPage = 0;
    const totalPages = <?= count($pages); ?>;

    function navigate(direction) {
        document.getElementById('page-' + currentPage).classList.add('hidden');
        currentPage += direction;
        currentPage = Math.max(0, Math.min(currentPage, totalPages - 1));
        document.getElementById('page-' + currentPage).classList.remove('hidden');
    }
</script>
</body>
</html>

<?php
$conn->close();
?>
