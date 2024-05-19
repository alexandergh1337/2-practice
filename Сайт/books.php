<?php
include 'config.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Number of books per page
$offset = ($page - 1) * $limit;

// Get total number of books
$sql = "SELECT COUNT(*) as total FROM Books";
$total_result = $conn->query($sql);
$total_books = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_books / $limit);

// Fetch limited books for the current page
$sql = "SELECT Books.book_id, Books.title, Books.publication_year, Authors.name as author_name, Genres.genre_name, Books.description
        FROM Books
        JOIN Authors ON Books.author_id = Authors.author_id
        JOIN Genres ON Books.genre_id = Genres.genre_id
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список книг</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<header class="bg-blue-600 p-4 text-white text-center">
    <h1>Список книг</h1>
</header>
<main class="p-4">
    <table class="table-auto w-full bg-white shadow-md rounded">
        <thead>
        <tr>
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Название</th>
            <th class="px-4 py-2">Автор</th>
            <th class="px-4 py-2">Жанр</th>
            <th class="px-4 py-2">Год издания</th>
            <th class="px-4 py-2">Описание</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                                <td class='border px-4 py-2'>{$row['book_id']}</td>
                                <td class='border px-4 py-2'><a href='view_book.php?book_id={$row['book_id']}' class='text-blue-600 hover:underline'>{$row['title']}</a></td>
                                <td class='border px-4 py-2'>{$row['author_name']}</td>
                                <td class='border px-4 py-2'>{$row['genre_name']}</td>
                                <td class='border px-4 py-2'>{$row['publication_year']}</td>
                                <td class='border px-4 py-2'>{$row['description']}</td>
                              </tr>";
            }
        } else {
            echo "<tr><td colspan='6' class='text-center py-4'>Нет доступных книг</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="mt-4">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>" class="bg-blue-600 text-white px-4 py-2 rounded"><?= $i ?></a>
        <?php endfor; ?>
    </div>
</main>
<footer class="bg-blue-600 p-4 text-white text-center">
    &copy; 2024 Онлайн Библиотека
</footer>
</body>
</html>

<?php
$conn->close();
?>
