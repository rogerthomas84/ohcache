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
 * An adapter for use in Tests, or development environments
 *
 * AdapterNull
 */
class AdapterNull extends AdapterAbstract
{
    /**
     * Empty constructor
     *
     * @param array $config (optional)
     */
    public function __construct(array $config = array())
    {
    }

    /**
     * Emulate the get of a cached value
     *
     * @return boolean false
     */
    public function get($key)
    {
        return false;
    }

    /**
     * Emulate the set of a cached key, value pair.
     *
     * @param string $key
     * @param mixed $value
     * @param integer $ttl (optional) default 86400
     * @return boolean false
     */
    public function set($key, $value, $ttl = self::DEFAULT_TTL)
    {
        return false;
    }

    /**
     * Emulate the set of a cached key, value pair if it doesn't already exist.
     *
     * @param string $key
     * @param mixed $value
     * @param integer $ttl (optional) default 86400
     * @return boolean false
     */
    public function setIfNotExists($key, $value, $ttl = self::DEFAULT_TTL)
    {
        return false;
    }

    /**
     * Emulate the has of a cached value
     *
     * @param string $key
     * @return boolean false
     */
    public function has($key)
    {
        return false;
    }

    /**
     * Emulate the renew of a cached value
     *
     * @param string $key
     * @param integer $ttl (optional) default 86400
     * @return boolean false
     */
    public function renew($key, $ttl = self::DEFAULT_TTL)
    {
        return false;
    }

    /**
     * Emulate the remove of a cached value
     *
     * @param string $key
     * @return boolean false
     */
    public function remove($key)
    {
        return false;
    }

    /**
     * Emulate the flush of the cache
     *
     * @return boolean false
     */
    public function flush()
    {
        return false;
    }
}
