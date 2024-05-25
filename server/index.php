<?php
session_start();
include 'config.php';

$sql = "SELECT Books.book_id, Books.title, Books.publication_year, Authors.name AS author, Genres.genre_name, Books.description
        FROM Books
        JOIN Authors ON Books.author_id = Authors.author_id
        JOIN Genres ON Books.genre_id = Genres.genre_id";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$books = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
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
    </style>
    <link rel="stylesheet" href="/assets/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="icon" href="https://img.icons8.com/ios-filled/50/000000/book.png" type="image/png">
</head>
<body class="bg-gray-100">
<header class="bg-blue-600 p-4 text-white text-center">
    <h1>Онлайн Библиотека</h1>
</header>
<main class="p-4">
    <h2 class="text-2xl font-bold mb-6">Добро пожаловать в нашу библиотеку!</h2>

    <?php if (isset($_SESSION['username'])): ?>
        <div class="welcome-section">
            <p class="text-lg">Привет, <?= htmlspecialchars($_SESSION['username']); ?>!</p>
            <a href="logout.php" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Выйти</a>
        </div>

        <h1 class="text-xl font-bold mb-4 mt-8">Список книг</h1>
        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead class="bg-gray-200">
            <tr>
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
                    <td><?= htmlspecialchars($book['title']); ?></td>
                    <td><?= htmlspecialchars($book['author']); ?></td>
                    <td><?= htmlspecialchars($book['publication_year']); ?></td>
                    <td><?= htmlspecialchars($book['genre_name']); ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars(str_replace('\n', ' ', $book['description'])); ?></td>
                    <td class="py-2 px-4"><a href="books.php?id=<?= $book['book_id']; ?>" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Читать</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <section class="my-8">
            <h3 class="text-xl font-bold">Регистрация</h3>
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
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Зарегистрироваться</button>
            </form>
        </section>

        <section class="my-8">
            <h3 class="text-xl font-bold">Вход</h3>
            <form action="login.php" method="post" class="bg-white p-4 shadow-md rounded">
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700">Имя пользователя</label>
                    <input type="text" id="username" name="username" class="mt-1 block w-full border-gray-300 rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Пароль</label>
                    <input type="password" id="password" name="password" class="mt-1 block w-full border-gray-300 rounded-md" required>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Войти</button>
            </form>
        </section>
    <?php endif; ?>

</main>
<footer class="bg-blue-600 p-4 text-white text-center">
    &copy; 2024 Онлайн Библиотека
</footer>
<script src="/assets/script.js"></script>
</body>
</html>
