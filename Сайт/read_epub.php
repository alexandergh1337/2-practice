<?php

function readEpub($filePath) {
    $zip = new ZipArchive;
    if ($zip->open($filePath) === TRUE) {
        $content = $zip->getFromName('content.opf'); // путь к файлу метаданных
        $xml = new SimpleXMLElement($content);

        $namespaces = $xml->getNamespaces(true);
        $metadata = $xml->metadata->children($namespaces['dc']);
        $manifest = $xml->manifest->children($namespaces['opf']);

        $title = (string) $metadata->title;
        $author = (string) $metadata->creator;
        $description = isset($metadata->description) ? (string) $metadata->description : 'Описание отсутствует';

        $genre = 'Неизвестный жанр';
        $publication_year = '2024';

        // Поиск жанра и года публикации
        foreach ($metadata as $meta) {
            $metaName = $meta->getName();
            if ($metaName === 'subject') {
                $genre = (string) $meta;
            } elseif ($metaName === 'date') {
                $publication_year = (string) $meta;
            }
        }

        // Получение страниц книги и стилей
        $pages = [];
        $styles = '';
        foreach ($manifest->item as $item) {
            $attributes = $item->attributes();
            $mediaType = (string) $attributes['media-type'];
            $href = (string) $attributes['href'];

            if ($mediaType === 'application/xhtml+xml') {
                $pageContent = $zip->getFromName($href);
                $pages[] = $pageContent;
            } elseif ($mediaType === 'text/css') {
                $styles .= $zip->getFromName($href);
            }
        }

        $zip->close();

        return [
            'title' => $title,
            'author' => $author,
            'description' => $description,
            'genre' => $genre,
            'publication_year' => $publication_year,
            'pages' => $pages,
            'styles' => $styles,
        ];
    } else {
        return false;
    }
}

$book = readEpub('books/*.epub');

if ($book) {
    echo "Title: {$book['title']}<br>";
    echo "Author: {$book['author']}<br>";
    echo "Description: {$book['description']}<br>";
    echo "Genre: {$book['genre']}<br>";
    echo "Publication Year: {$book['publication_year']}<br>";

    echo "<style>{$book['styles']}</style>";

    foreach ($book['pages'] as $page) {
        echo "<div class='page'>{$page}</div>";
    }
} else {
    echo "Failed to read EPUB file.";
}
?>
