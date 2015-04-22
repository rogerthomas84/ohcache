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

/**
 * Abstract class for OhCache Adapters
 *
 * AdapterAbstract
 */
abstract class AdapterAbstract
{
    /**
     * @var integer
     */
    const DEFAULT_TTL = 86400;

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * Constructor with optional config array
     *
     * @param array $config (optional)
     */
    abstract public function __construct(array $config = array());

    /**
     * Get a value from cache by key name
     *
     * @param mixed $key
     * @return mixed|boolean false
     */
    abstract public function get($key);

    /**
     * Set a value in cache by key name, specifying the TTL
     *
     * @param string $key
     * @param mixed $value
     * @param integer $ttl
     * @return boolean
     */
    abstract public function set($key, $value, $ttl = self::DEFAULT_TTL);

    /**
     * Set a value in cache if it doesn't already exist.
     *
     * @param string $key
     * @param mixed $value
     * @param integer $ttl
     * @return boolean
     */
    abstract public function setIfNotExists($key, $value, $ttl = self::DEFAULT_TTL);

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
    abstract public function has($key);

    /**
     * Alter the TTL for a given key. Essentially, renewing it in
     * the cache.
     *
     * @param string $key
     * @param integer $ttl
     * @return boolean
     */
    abstract public function renew($key, $ttl = self::DEFAULT_TTL);

    /**
     * Remove a value from cache by $key
     *
     * @param string $key
     * @return boolean
     */
    abstract public function remove($key);

    /**
     * Flush the entire cache.
     *
     * @return boolean
     */
    abstract public function flush();

    /**
     * Get a key name for a cache value.
     *
     * @param string $name
     * @return string
     */
    protected function getKeyString($name)
    {
        return $this->prefix . $name;
    }
}
