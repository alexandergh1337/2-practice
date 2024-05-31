<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../server/read_epub.php';

class FindFileTest extends TestCase
{
    public function testFindFile()
    {
        $testDir = __DIR__ . '/test_files';
        mkdir($testDir, 0777, true);
        file_put_contents($testDir . '/testfile.txt', 'Test content');

        $result = findFile($testDir, 'testfile.txt');

        $this->assertNotFalse($result);
        $expected = $testDir . DIRECTORY_SEPARATOR . 'testfile.txt';
        $this->assertEquals($expected, $result);

        unlink($testDir . '/testfile.txt');
        rmdir($testDir);
    }
}
?>