<?php
function findPages($dir) {
    if (!is_dir($dir)) {
        throw new UnexpectedValueException("Неверно задано имя папки: $dir");
    }

    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    $pages = [];
    foreach ($rii as $file) {
        if (!$file->isDir() && ($file->getExtension() === 'xhtml' || $file->getExtension() === 'html')) {
            $pages[] = $file->getPathname();
        }
    }
    sort($pages);
    return $pages;
}
?>
