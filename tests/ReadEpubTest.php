<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../server/read_epub.php';

class ReadEpubTest extends TestCase
{
    public function testReadEpub()
    {
        $extractPath = __DIR__ . '/test_files';
        mkdir($extractPath, 0777, true);

        $opfContent = <<<XML
<package xmlns="http://www.idpf.org/2007/opf" version="2.0">
  <metadata xmlns:dc="http://purl.org/dc/elements/1.1/">
    <dc:title>Test Title</dc:title>
    <dc:creator>Test Author</dc:creator>
    <dc:description>Test Description</dc:description>
    <dc:subject>Test Genre</dc:subject>
    <dc:date>2024</dc:date>
  </metadata>
  <manifest>
    <item id="content" href="content.xhtml" media-type="application/xhtml+xml"/>
    <item id="style" href="style.css" media-type="text/css"/>
  </manifest>
</package>
XML;

        file_put_contents($extractPath . '/content.opf', $opfContent);
        file_put_contents($extractPath . '/content.xhtml', '<p>Test content</p>');
        file_put_contents($extractPath . '/style.css', 'body { color: red; }');

        $result = readEpub($extractPath);

        $this->assertNotFalse($result);
        $this->assertEquals('Test Title', $result['title']);
        $this->assertEquals('Test Author', $result['author']);
        $this->assertEquals('Test Description', $result['description']);
        $this->assertEquals('Test Genre', $result['genre']);
        $this->assertEquals('2024', $result['publication_year']);
        $this->assertContains('<p>Test content</p>', $result['pages']);
        $this->assertEquals('body { color: red; }', $result['styles']);

        unlink($extractPath . '/content.opf');
        unlink($extractPath . '/content.xhtml');
        unlink($extractPath . '/style.css');
        rmdir($extractPath);
    }
}
?>