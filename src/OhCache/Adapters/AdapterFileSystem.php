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
namespace OhCache\Adapters;

use OhCache\Adapters\AdapterAbstract;
use OhCache\Helper\FileSystemHelper;

/**
 * Provides a file system based caching solution.
 *
 * AdapterFileSystem
 */
class AdapterFileSystem extends AdapterAbstract
{
    /**
     * Shorter name for DIRECTORY_SEPARATOR
     *
     * @var string
     */
    const DS = DIRECTORY_SEPARATOR;

    /**
     * Prefix for cache keys
     *
     * @var string
     */
    private $prefix = 'OHAFS_';

    /**
     * Instance of FileSystemHelper
     *
     * @var FileSystemHelper|null
     */
    private $helper = null;

    /**
     * The path to the writable folder
     *
     * @var string|null
     */
    private $path = null;

    /**
     * Construct requires an array with the key of 'path', which should point
     * to a writable folder.
     *
     * @param array $config
     * @throws \Exception
     */
    public function __construct(array $config = array())
    {
        if (!array_key_exists('path', $config) || !is_dir($config['path']) || !is_writable($config['path'])) {
            throw new \Exception('"path" key must be specified and be a valid location');
        }
        $this->path = rtrim($config['path'], '/\\');
        $this->helper = new FileSystemHelper();
    }

    /**
     * Get a value from cache by key name
     *
     * @param mixed $key
     * @return mixed|boolean false
     */
    public function get($key)
    {
        $md5 = md5($key);
        $folderPath = $this->getFolderPathFromMd5($md5);
        $path = $this->path . self::DS . $folderPath . self::DS . $this->prefix . $md5;
        if (!file_exists($path) || !is_readable($path)) {
            return false;
        }

        $content = file_get_contents($path);
        if (!$content) {
            return false;
        }

        $pieces = explode(PHP_EOL, $content);
        if (!is_array($pieces) || !is_numeric($pieces[0])) {
            unlink($path);
            return false;
        }

        $val = @unserialize($pieces[1]);
        if ($val === false || $pieces[0] <= time()) {
            $this->remove($key);
            return false;
        }

        return $val;
    }

    /**
     * Set a value in cache by key name, specifying the TTL
     *
     * @param string $key
     * @param mixed $value
     * @param integer $ttl
     * @return boolean
     */
    public function set($key, $value, $ttl = self::DEFAULT_TTL)
    {
        $md5 = md5($key);
        $folderPath = $this->getFolderPathFromMd5($md5);
        $create = $this->helper->createPath($this->path, $folderPath);
        if (!$create) {
            return false;
        }
        $basePaths = $this->path . self::DS . $folderPath;
        $path = $basePaths . self::DS . $this->prefix . $md5;

        $handle = fopen($path, 'w');
        if ($handle) {
            $data = time() + $ttl . PHP_EOL . serialize($value);
            $success = fwrite($handle, $data);
            @fclose($handle);
            return ($success !== false);
        }

        // @codeCoverageIgnoreStart
        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Set a value in cache if it doesn't already exist. Internally, this uses the self::has() and
     * self::set() methods.
     *
     * @param string $key
     * @param mixed $value
     * @param integer $ttl
     * @return boolean
     */
    public function setIfNotExists($key, $value, $ttl = self::DEFAULT_TTL)
    {
        if (false === $this->has($key)) {
            return $this->set($key, $value, $ttl);
        }

        return false;
    }

    /**
     * Establish whether the cache contains a value with key of $key. Internally,
     * this method performs a get() on the key, so it's worth using get() instead
     * if you require a value.
     * Don't use:
     *   if (has('x')) { $a = get('x'); }
     * Use:
     *   if (false != ($a = get('x')) { // ok. }
     *
     * @param string $key
     * @return boolean
     */
    public function has($key)
    {
        return ($this->get($key) !== false);
    }

    /**
     * Alter the TTL for a given key. Essentially, renewing it in
     * the cache.
     *
     * @param string $key
     * @param integer $ttl
     * @return boolean
     */
    public function renew($key, $ttl = self::DEFAULT_TTL)
    {
        $md5 = md5($key);
        $folderPath = $this->getFolderPathFromMd5($md5);
        $path = $this->path . self::DS . $folderPath . self::DS . $this->prefix . $md5;
        if (!file_exists($path) || !is_readable($path)) {
            return false;
        }

        $content = file_get_contents($path);
        if (!$content) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        $pieces = explode(PHP_EOL, $content);
        if (!is_array($pieces) || !is_numeric($pieces[0])) {
            unlink($path);
            return false;
        }

        $val = @unserialize($pieces[1]);
        if (!$val) {
            $this->remove($key);
            return false;
        }

        return $this->set($key, $val, $ttl);
    }

    /**
     * Remove a value from cache by $key
     *
     * @param string $key
     * @return boolean
     */
    public function remove($key)
    {
        $md5 = md5($key);
        $folderPath = $this->getFolderPathFromMd5($md5);
        $path = $this->path . self::DS . $folderPath . self::DS . $this->prefix . $md5;
        if (!file_exists($path) || !is_readable($path)) {
            return false;
        }

        return unlink($path);
    }

    /**
     * Flush the entire cache.
     *
     * @return boolean
     */
    public function flush()
    {
        try {
            $this->helper->recursivelyDeleteFromDirectory($this->path);
            return @mkdir($this->path);

            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
        }

        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Get a folder path from a given MD5
     *
     * @param string $md5
     * @return string
     */
    private function getFolderPathFromMd5($md5)
    {
        $folderOne = substr($md5, 0, 2);
        $folderTwo = substr($md5, 2, 2);

        return $folderOne . self::DS . $folderTwo;
    }
}
