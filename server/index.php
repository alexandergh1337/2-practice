<?php
session_start();
require_once 'config.php';

$genreId = isset($_GET['genre']) ? $_GET['genre'] : '';
$books = getBooks($genreId);
$plannedBooks = getUserBooksByStatus($_SESSION['user_id'], 'planned');
$readBooks = getUserBooksByStatus($_SESSION['user_id'], 'read');
$readingBooks = getUserBooksByStatus($_SESSION['user_id'], 'reading');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = $_POST['book_id'];
    $action = $_POST['action'];

    if ($action === 'planned' || $action === 'read' || $action === 'reading') {
        addUserBook($_SESSION['user_id'], $book_id, $action);
    } elseif ($action === 'remove') {
        removeUserBook($_SESSION['user_id'], $book_id);
    }

    header('Location: index.php');
    exit();
}

function getBooks($genreId = '', $limit = 10, $offset = 0) {
    global $conn;
    $sql = "SELECT Books.*, Genres.genre_name, Authors.name AS author
            FROM Books
            LEFT JOIN Genres ON Books.genre_id = Genres.genre_id
            LEFT JOIN Authors ON Books.author_id = Authors.author_id";
    if ($genreId) {
        $sql .= " WHERE Books.genre_id = $genreId";
    }
    $sql .= " LIMIT $limit OFFSET $offset";
    $result = $conn->query($sql);
    $books = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
    }
    return $books;
}


function getUserBooksByStatus($user_id, $status) {
    global $conn;
    $sql = "SELECT Books.*, Authors.name AS author, Genres.genre_name 
            FROM UserBooks
            JOIN Books ON UserBooks.book_id = Books.book_id
            JOIN Authors ON Books.author_id = Authors.author_id
            JOIN Genres ON Books.genre_id = Genres.genre_id
            WHERE UserBooks.user_id = $user_id AND UserBooks.status = '$status'";
    $result = $conn->query($sql);
    $books = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
    }
    return $books;
}


function addUserBook($user_id, $book_id, $status) {
    global $conn;
    $sql = "INSERT INTO UserBooks (user_id, book_id, status) VALUES ($user_id, $book_id, '$status')
            ON DUPLICATE KEY UPDATE status = '$status'";
    $conn->query($sql);
}

function removeUserBook($user_id, $book_id) {
    global $conn;
    $sql = "DELETE FROM UserBooks WHERE user_id = $user_id AND book_id = $book_id";
    $conn->query($sql);
}

function countTotalBooks($genreId = '') {
    global $conn;
    $sql = "SELECT COUNT(*) FROM Books";
    if ($genreId) {
        $sql .= " WHERE genre_id = $genreId";
    }
    $result = $conn->query($sql);
    $row = $result->fetch_row();
    return $row[0];
}

function getGenres() {
    global $conn;
    $sql = "SELECT * FROM Genres";
    $result = $conn->query($sql);
    $genres = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $genres[] = $row;
        }
    }
    return $genres;
}


$genres = getGenres();

// Пагинация
$limit = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$books = getBooks($genreId, $limit, $offset);
$totalBooks = countTotalBooks($genreId);
$totalPages = ceil($totalBooks / $limit);

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Онлайн Библиотека</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .cover-image {
            width: 50px;
            height: auto;
        }
        .form-container {
            width: 300px;
            margin: 0 auto;
        }
        .hidden {
            display: none;
        }
        .block {
            display: block;
        }
        .filter-container {
            float: right;
        }
        .nav-links {
            margin-left: auto;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
        }
    </style>
    <link rel="stylesheet" href="/assets/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="icon" href="https://img.icons8.com/ios-filled/50/000000/book.png" type="image/png">
</head>
<body class="bg-gray-100">
<header class="bg-blue-600 p-4 text-white flex items-center">
    <img src="https://img.icons8.com/ios-filled/50/000000/book.png" alt="Логотип" class="w-10 h-10 mr-4">
    <h1 class="text-2xl font-bold">Онлайн Библиотека</h1>
    <nav class="nav-links flex ml-auto">
        <a href="http://localhost/phpmyadmin/" class="ml-6 text-white hover:underline">Информация БД</a>
        <a href="upload.php" class="ml-6 text-white hover:underline">Загрузить книгу</a>
    </nav>
</header>
<main class="p-4">
    <h2 class="text-2xl font-bold mb-6">Добро пожаловать в нашу библиотеку!</h2>

    <?php if (isset($_SESSION['username'])): ?>
        <div class="welcome-section text-center">
            <p class="text-lg">Привет, <?= htmlspecialchars($_SESSION['username']); ?>!</p>
            <a href="logout.php" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Выйти</a>
        </div>

        <div class="filter-container mb-4">
            <h1 class="text-xl font-bold mb-4 mt-8">Фильтр по жанрам</h1>
            <form action="index.php" method="get">
                <select name="genre" onchange="this.form.submit()" class="block text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-64">
                    <option value="">Все жанры</option>
                    <?php foreach ($genres as $genre): ?>
                        <option value="<?= $genre['genre_id']; ?>" <?= ($genreId == $genre['genre_id']) ? 'selected' : ''; ?>><?= htmlspecialchars($genre['genre_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <h1 class="text-xl font-bold mb-4 mt-8">Список книг</h1>
        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead class="bg-gray-200">
            <tr>
                <th>Обложка</th>
                <th>Название</th>
                <th>Автор</th>
                <th>Год публикации</th>
                <th>Жанр</th>
                <th>Описание</th>
                <th>Действие</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($books as $book): ?>
                <tr class="border-b">
                    <td><img src="<?= htmlspecialchars($book['cover_image']); ?>" alt="Обложка книги" class="cover-image"></td>
                    <td><?= htmlspecialchars($book['title']); ?></td>
                    <td><?= htmlspecialchars($book['author']); ?></td>
                    <td><?= htmlspecialchars($book['publication_year']); ?></td>
                    <td><?= htmlspecialchars($book['genre_name']); ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars(str_replace('\n', ' ', $book['description'])); ?></td>
                    <td class="py-2 px-4">
                        <div class="action-buttons">
                            <form action="index.php" method="post">
                                <input type="hidden" name="book_id" value="<?= $book['book_id']; ?>">
                                <button type="submit" name="action" value="planned" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Планируется</button>
                            </form>
                            <form action="index.php" method="post">
                                <input type="hidden" name="book_id" value="<?= $book['book_id']; ?>">
                                <button type="submit" name="action" value="read" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Прочитано</button>
                            </form>
                            <form action="index.php" method="post">
                                <input type="hidden" name="book_id" value="<?= $book['book_id']; ?>">
                                <button type="submit" name="action" value="reading" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">Читаю</button>
                            </form>
                            <form action="books.php" method="get">
                                <input type="hidden" name="id" value="<?= $book['book_id']; ?>">
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Читать</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <h1 class="text-xl font-bold mb-4 mt-8">Запланировано</h1>
        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead class="bg-gray-200">
            <tr>
                <th>Обложка</th>
                <th>Название</th>
                <th>Автор</th>
                <th>Год публикации</th>
                <th>Жанр</th>
                <th>Описание</th>
                <th>Действие</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($plannedBooks as $book): ?>
                <tr class="border-b">
                    <td><img src="<?= htmlspecialchars($book['cover_image']); ?>" alt="Обложка книги" class="cover-image"></td>
                    <td><?= htmlspecialchars($book['title']); ?></td>
                    <td><?= htmlspecialchars($book['author']); ?></td>
                    <td><?= htmlspecialchars($book['publication_year']); ?></td>
                    <td><?= htmlspecialchars($book['genre_name']); ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars(str_replace('\n', ' ', $book['description'])); ?></td>
                    <td class="py-2 px-4">
                        <div class="action-buttons">
                            <form action="index.php" method="post">
                                <input type="hidden" name="book_id" value="<?= $book['book_id']; ?>">
                                <button type="submit" name="action" value="remove" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Удалить</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <h1 class="text-xl font-bold mb-4 mt-8">Прочитано</h1>
        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead class="bg-gray-200">
            <tr>
                <th>Обложка</th>
                <th>Название</th>
                <th>Автор</th>
                <th>Год публикации</th>
                <th>Жанр</th>
                <th>Описание</th>
                <th>Действие</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($readBooks as $book): ?>
                <tr class="border-b">
                    <td><img src="<?= htmlspecialchars($book['cover_image']); ?>" alt="Обложка книги" class="cover-image"></td>
                    <td><?= htmlspecialchars($book['title']); ?></td>
                    <td><?= htmlspecialchars($book['author']); ?></td>
                    <td><?= htmlspecialchars($book['publication_year']); ?></td>
                    <td><?= htmlspecialchars($book['genre_name']); ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars(str_replace('\n', ' ', $book['description'])); ?></td>
                    <td class="py-2 px-4">
                        <div class="action-buttons">
                            <form action="index.php" method="post">
                                <input type="hidden" name="book_id" value="<?= $book['book_id']; ?>">
                                <button type="submit" name="action" value="remove" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Удалить</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <h1 class="text-xl font-bold mb-4 mt-8">Читаю</h1>
        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead class="bg-gray-200">
            <tr>
                <th>Обложка</th>
                <th>Название</th>
                <th>Автор</th>
                <th>Год публикации</th>
                <th>Жанр</th>
                <th>Описание</th>
                <th>Действие</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($readingBooks as $book): ?>
                <tr class="border-b">
                    <td><img src="<?= htmlspecialchars($book['cover_image']); ?>" alt="Обложка книги" class="cover-image"></td>
                    <td><?= htmlspecialchars($book['title']); ?></td>
                    <td><?= htmlspecialchars($book['author']); ?></td>
                    <td><?= htmlspecialchars($book['publication_year']); ?></td>
                    <td><?= htmlspecialchars($book['genre_name']); ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars(str_replace('\n', ' ', $book['description'])); ?></td>
                    <td class="py-2 px-4">
                        <div class="action-buttons">
                            <form action="index.php" method="post">
                                <input type="hidden" name="book_id" value="<?= $book['book_id']; ?>">
                                <button type="submit" name="action" value="remove" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Удалить</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <div class="form-container">
            <div id="registration-form" class="block">
                <h3 class="text-xl font-bold mb-4 text-center">Регистрация</h3>
                <form action="register.php" method="post" class="bg-white p-4 shadow-md rounded">
                    <div class="mb-4">
                        <label for="username" class="block text-sm font-medium text-gray-700">Имя пользователя</label>
                        <input type="text" id="username" name="username" class="mt-1 block w-full border-gray-300 rounded-md" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Пароль</label>
                        <input type="password" id="password" name="password" class="mt-1 block w-full border-gray-300 rounded-md" required>
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Электронная почта</label>
                        <input type="email" id="email" name="email" class="mt-1 block w-full border-gray-300 rounded-md" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Регистрация</button>
                    </div>
                </form>
                <p class="mt-4 text-center">Уже есть аккаунт? <button onclick="toggleForms()" class="text-blue-600 hover:underline">Войти</button></p>
            </div>

            <div id="login-form" class="hidden">
                <h3 class="text-xl font-bold mb-4 text-center">Вход</h3>
                <form action="login.php" method="post" class="bg-white p-4 shadow-md rounded">
                    <div class="mb-4">
                        <label for="username" class="block text-sm font-medium text-gray-700">Имя пользователя</label>
                        <input type="text" id="username" name="username" class="mt-1 block w-full border-gray-300 rounded-md" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Пароль</label>
                        <input type="password" id="password" name="password" class="mt-1 block w-full border-gray-300 rounded-md" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Войти</button>
                    </div>
                </form>
                <p class="mt-4 text-center">Нет аккаунта? <button onclick="toggleForms()" class="text-blue-600 hover:underline">Зарегистрироваться</button></p>
            </div>
        </div>
    <?php endif; ?>
</main>

<nav class="mt-8">
    <ul class="flex justify-center">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="mx-1">
                <a href="?page=<?= $i; ?>&genre=<?= $genreId; ?>" class="px-3 py-1 border <?= ($i == $page) ? 'bg-blue-500 text-white' : 'bg-white text-blue-500'; ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>


<footer class="mt-4 bg-gray-200 p-4 text-center">
    <p>Онлайн Библиотека &copy; <?= date('Y'); ?></p>
</footer>

<script src="/assets/script.js"></script>
</body>
</html>
