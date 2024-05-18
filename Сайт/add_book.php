<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить книгу</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<header class="bg-blue-600 p-4 text-white text-center">
    <h1>Добавить книгу</h1>
</header>
<main class="p-4">
    <form action="create_book.php" method="post" class="bg-white p-4 shadow-md rounded" id="bookForm">
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700">Название книги</label>
            <input type="text" id="title" name="title" class="mt-1 block w-full border-gray-300 rounded-md" required>
        </div>
        <div class="mb-4">
            <label for="author_id" class="block text-sm font-medium text-gray-700">ID автора</label>
            <input type="text" id="author_id" name="author_id" class="mt-1 block w-full border-gray-300 rounded-md" required>
        </div>
        <div class="mb-4">
            <label for="genre_id" class="block text-sm font-medium text-gray-700">ID жанра</label>
            <input type="text" id="genre_id" name="genre_id" class="mt-1 block w-full border-gray-300 rounded-md" required>
        </div>
        <div class="mb-4">
            <label for="publication_year" class="block text-sm font-medium text-gray-700">Год издания</label>
            <input type="text" id="publication_year" name="publication_year" class="mt-1 block w-full border-gray-300 rounded-md" required>
        </div>
        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700">Описание</label>
            <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 rounded-md" required></textarea>
        </div>
        <div class="mb-4">
            <label for="pages" class="block text-sm font-medium text-gray-700">Страницы</label>
            <textarea id="pages" name="pages" class="mt-1 block w-full border-gray-300 rounded-md" placeholder="Введите содержание страниц через | (например: Page 1 Content | Page 2 Content)" required></textarea>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Добавить книгу</button>
    </form>
</main>
<footer class="bg-blue-600 p-4 text-white text-center">
    &copy; 2024 Онлайн Библиотека
</footer>
<script>
    document.getElementById('bookForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        data.pages = data.pages.split('|').map(page => page.trim());

        fetch('create_book.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.book_id) {
                    window.location.href = 'view_book.php?book_id=' + data.book_id;
                }
            })
            .catch(error => console.error('Error:', error));
    });
</script>
</body>
</html>
