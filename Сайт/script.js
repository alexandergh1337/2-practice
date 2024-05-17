document.addEventListener('DOMContentLoaded', function() {
    const bookList = document.getElementById('book-list');
    const bookContent = document.getElementById('book-content');

    if (bookList) {
        fetch('get_books.php')
            .then(response => response.json())
            .then(books => {
                if (books.length > 0) {
                    let html = '<table class="table-auto w-full bg-white shadow-md rounded">';
                    html += '<thead><tr><th class="px-4 py-2">ID</th><th class="px-4 py-2">Название</th><th class="px-4 py-2">Автор</th><th class="px-4 py-2">Жанр</th><th class="px-4 py-2">Год издания</th></tr></thead>';
                    html += '<tbody>';
                    books.forEach(book => {
                        html += `<tr>
                                    <td class="border px-4 py-2">${book.book_id}</td>
                                    <td class="border px-4 py-2">${book.title}</td>
                                    <td class="border px-4 py-2">${book.author_name}</td>
                                    <td class="border px-4 py-2">${book.genre_name}</td>
                                    <td class="border px-4 py-2">${book.publication_year}</td>
                                    <td class="border px-4 py-2"><button class="bg-blue-600 text-white px-4 py-2 rounded" onclick="fetchPages(${book.book_id})">Просмотреть</button></td>
                                </tr>`;
                    });
                    html += '</tbody></table>';
                    bookList.innerHTML = html;
                } else {
                    bookList.innerHTML = '<p>Нет доступных книг</p>';
                }
            })
            .catch(error => console.error('Ошибка:', error));
    }
});

function fetchPages(bookId) {
    fetch(`get_pages.php?book_id=${bookId}`)
        .then(response => response.json())
        .then(pages => {
            if (pages.length > 0) {
                let html = '<div class="bg-white p-4 shadow-md rounded">';
                pages.forEach(page => {
                    html += `<div class="mb-4"><h3 class="text-xl font-bold">Страница ${page.page_number}</h3><p>${page.content}</p></div>`;
                });
                html += '</div>';
                document.getElementById('book-content').innerHTML = html;
            } else {
                document.getElementById('book-content').innerHTML = '<p>Страницы не найдены</p>';
            }
        })
        .catch(error => console.error('Ошибка:', error));
}
