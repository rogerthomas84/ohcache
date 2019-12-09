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

use OhCache\Exception\ApcExtensionNotLoaded;

/**
 * Interact with APC
 *
 * AdapterApc
 */
class AdapterApc extends AdapterAbstract
{
    /**
     * Construct, and check the presence of the APC extension
     *
     * @param array $config
     * @throws ApcExtensionNotLoaded
     */
    public function __construct(array $config = array())
    {
        $this->checkExtension();
        if (array_key_exists('prefix', $config)) {
            $this->prefix = $config['prefix'];
        }
    }

    /**
     * Get a value from cache by key name
     *
     * @param mixed $key
     * @return mixed|boolean false
     */
    public function get($key)
    {
        $record = apc_fetch(
            $this->getKeyString($key),
            $found
        );
        if ($found) {
            return $record;
        }

        return false;
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
        return apc_store(
            $this->getKeyString($key),
            $value,
            $ttl
        );
    }

    /**
     * Set a value in cache if it doesn't already exist. Internally, this uses
     * apc_add
     *
     * @param string $key
     * @param mixed $value
     * @param integer $ttl
     * @return boolean
     */
    public function setIfNotExists($key, $value, $ttl = self::DEFAULT_TTL)
    {
        return apc_add(
            $this->getKeyString($key),
            $value,
            $ttl
        );
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
        $key = $this->getKeyString($key);
        $val = apc_fetch($key, $fetched);

        if ($fetched) {
            return apc_store($key, $val, $ttl);
        }

        return false;
    }

    /**
     * Remove a value from cache by $key
     *
     * @param string $key
     * @return boolean
     */
    public function remove($key)
    {
        return apc_delete($this->getKeyString($key));
    }

    /**
     * Flush the entire cache.
     *
     * @return boolean
     */
    public function flush()
    {
        return apc_clear_cache();
    }

    /**
     * Check if apc is enabled
     * @throws ApcExtensionNotLoaded
     */
    private function checkExtension()
    {
        if (false === extension_loaded('apc')) {
            throw new ApcExtensionNotLoaded('Apc Extension Not Loaded');
        }
    }
}
