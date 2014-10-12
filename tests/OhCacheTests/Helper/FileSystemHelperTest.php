<?php
/**
 * This file is part of the OhCache library
 *
 * @author      Roger Thomas <rogere84@gmail.com>
 * @copyright   2014 Roger Thomas <rogere84@gmail.com>
 * @package     OhCache
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace OhCache\Tests\Helper;

use OhCache\Helper\FileSystemHelper;

/**
 * FileSystemHelperTest
 */
class FileSystemHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testMakeInvalids()
    {
        $fullPath = sys_get_temp_dir() . FileSystemHelper::DS . 'OhCacheTests';
        @mkdir($fullPath);
        $helper = new FileSystemHelper();
        $makeFake = $helper->createPath(
            FileSystemHelper::DS . 'no' . FileSystemHelper::DS . 'such' . FileSystemHelper::DS . 'path',
            'ok'
        );
        $this->assertFalse($makeFake);
        $makeEmptyPath = $helper->createPath($fullPath, '');
        $this->assertFalse($makeEmptyPath);
        // cover continue for empty paths.
        $directoryCreate = $helper->createPath($fullPath, 'tests' . FileSystemHelper::DS . 'directory');
        $this->assertTrue($directoryCreate);

        $helper->recursivelyDeleteFromDirectory($fullPath);
    }

    public function testRecusiveDelete()
    {
        $fullPath = sys_get_temp_dir() . FileSystemHelper::DS . 'OhCacheTests';
        mkdir($fullPath);
        touch($fullPath . FileSystemHelper::DS . '1');
        $this->assertFileExists($fullPath . FileSystemHelper::DS . '1');
        @mkdir($fullPath . FileSystemHelper::DS . 'dir');
        touch($fullPath . FileSystemHelper::DS . 'dir' . FileSystemHelper::DS . '2');
        $this->assertFileExists($fullPath . FileSystemHelper::DS . 'dir' . FileSystemHelper::DS . '2');
        @mkdir($fullPath . FileSystemHelper::DS . 'dir');
        $dir = new FileSystemHelper();
        $dir->recursivelyDeleteFromDirectory($fullPath . FileSystemHelper::DS . '1');
        $dir->recursivelyDeleteFromDirectory($fullPath);
        $this->assertFileNotExists($fullPath . FileSystemHelper::DS . '1');
        $this->assertFileNotExists($fullPath . FileSystemHelper::DS . 'dir' . FileSystemHelper::DS . '2');
    }
}
