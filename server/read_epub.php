<?php

function findFile($dir, $filename) {
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($rii as $file) {
        if (!$file->isDir() && basename($file) === $filename) {
            return $file->getPathname();
        }
    }
    return false;
}

function readEpub($extractPath) {
    $opfPath = findFile($extractPath, 'content.opf');

    if (!$opfPath) {
        return false;
    }

    $opfContent = file_get_contents($opfPath);
    $xml = simplexml_load_string($opfContent);
    $namespaces = $xml->getNamespaces(true);
    $metadata = $xml->metadata->children($namespaces['dc']);
    $manifest = $xml->manifest->children($namespaces['opf']);

    $title = (string) $metadata->title;
    $author = (string) $metadata->creator;
    $description = isset($metadata->description) ? (string) $metadata->description : 'Описание отсутствует';

    $genre = 'Неизвестный жанр';
    $publication_year = date('Y');

    foreach ($metadata as $meta) {
        $metaName = $meta->getName();
        if ($metaName === 'subject') {
            $genre = (string) $meta;
        } elseif ($metaName === 'date') {
            $publication_year = (string) $meta;
        }
    }

    $pages = [];
    $styles = '';
    foreach ($manifest->item as $item) {
        $attributes = $item->attributes();
        $mediaType = (string) $attributes['media-type'];
        $href = (string) $attributes['href'];

        if ($mediaType === 'application/xhtml+xml') {
            $pageContent = file_get_contents($extractPath . '/' . $href);
            $pages[] = $pageContent;
        } elseif ($mediaType === 'text/css') {
            $styles .= file_get_contents($extractPath . '/' . $href);
        }
    }

    return [
        'title' => $title,
        'author' => $author,
        'description' => $description,
        'genre' => $genre,
        'publication_year' => $publication_year,
        'pages' => $pages,
        'styles' => $styles,
    ];
}
?>
