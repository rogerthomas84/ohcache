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

/**
 * Interact with Memcached
 *
 * AdapterMemcached
 */
class AdapterMemcached extends AdapterAbstract
{
    /**
     * @var \Memcached
     */
    private $memcached = null;

    /**
     * Construct the adapter, giving an array of servers.
     * @example
     *     array(
     *         'prefix' => '',
     *         'persistent_id' => '',
     *         'servers' => array(
     *             array (
     *                 'host' => 'cache1.example.com',
     *                 'port' => 11211,
     *                 'weight' => 1
     *             ),
     *             array(
     *                 'host' => 'cache2.example.com',
     *                 'port' => 11211,
     *                 'weight' => 2
     *             )
     *         )
     *     )
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        try {
            if (array_key_exists('prefix', $config)) {
                $this->prefix = $config['prefix'];
            }
            if (array_key_exists('persistent_id', $config) && !empty($config['persistent_id'])) {
                // @codeCoverageIgnoreStart
                $this->memcached = new \Memcached($config['persistent_id']);
            } else {
                // @codeCoverageIgnoreEnd
                $this->memcached = new \Memcached();
            }
            foreach ($config['servers'] as $server) {
                $this->memcached->addserver(
                    $server['host'],
                    $server['port'],
                    $server['weight']
                );
            }
            if (array_key_exists('options', $config)) {
                foreach ($config['options'] as $optionKey => $optionValue) {
                    $this->memcached->setOption($optionKey, $optionValue);
                }
            }
        } catch (\Exception $e) {
            // @codeCoverageIgnoreStart
            $this->memcached = null;
            // @codeCoverageIgnoreEnd
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
        if (!$this->hasConnection()) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        try {
            $result = $this->memcached->get(
                $this->getKeyString($key)
            );
            if ($result === \Memcached::RES_NOTFOUND) {
                return false;
            }

            return $result;
            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
        }

        return false;
        // @codeCoverageIgnoreEnd
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
        if (!$this->hasConnection()) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        try {
            return $this->memcached->set(
                $this->getKeyString($key),
                $value,
                $ttl
            );
            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
        }

        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Set a value in cache if it doesn't already exist. Internally, this uses
     * Memcache::add
     *
     * @param string $key
     * @param mixed $value
     * @param integer $ttl
     * @return boolean
     */
    public function setIfNotExists($key, $value, $ttl = self::DEFAULT_TTL)
    {
        if (!$this->hasConnection()) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        try {
            return $this->memcached->add(
                $this->getKeyString($key),
                $value,
                $ttl
            );
            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
        }

        return false;
        // @codeCoverageIgnoreEnd
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
        if (!$this->hasConnection()) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        $value = $this->get($key);
        if ($value !== false) {
            try {
                return $this->memcached->replace(
                    $this->getKeyString($key),
                    $value,
                    $ttl
                );
                // @codeCoverageIgnoreStart
            } catch (\Exception $e) {
            }
        }
        // @codeCoverageIgnoreEnd

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
        if (!$this->hasConnection()) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        try {
            return $this->memcached->delete($this->getKeyString($key), 0);
            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
        }

        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Flush the entire cache.
     *
     * @return boolean
     */
    public function flush()
    {
        if (!$this->hasConnection()) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        try {
            return $this->memcached->flush();
            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
        }

        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Check if instance of \Memcached has been assigned
     * @return boolean
     */
    private function hasConnection()
    {
        return ($this->memcached instanceof \Memcached);
    }
}
