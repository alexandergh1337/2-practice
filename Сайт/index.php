<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Онлайн Библиотека</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="icon" href="https://img.icons8.com/ios-filled/50/000000/book.png" type="image/png">
</head>
<body class="bg-gray-100">
<header class="bg-blue-600 p-4 text-white text-center">
    <h1>Онлайн Библиотека</h1>
</header>
<main class="p-4">
    <h2 class="text-2xl font-bold">Добро пожаловать в нашу библиотеку!</h2>

    <?php
    session_start();
    if (isset($_SESSION['username'])): ?>
        <p>Привет, <?= htmlspecialchars($_SESSION['username']); ?>!</p>
        <a href="logout.php" class="bg-blue-600 text-white px-4 py-2 rounded">Выйти</a>
        <div id="book-list" class="my-8"></div>
        <div id="book-content" class="my-8"></div>
    <?php else: ?>
        <!-- Форма регистрации -->
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

        <!-- Форма входа -->
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
<script src="script.js"></script>
</body>
</html>
