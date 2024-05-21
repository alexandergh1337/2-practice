<?php

function readEpub($filePath) {
    $zip = new ZipArchive;
    if ($zip->open($filePath) === TRUE) {
        $content = $zip->getFromName('content.opf');
        $xml = new SimpleXMLElement($content);

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
?>
