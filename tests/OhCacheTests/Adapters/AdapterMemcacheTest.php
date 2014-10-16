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
namespace OhCacheTests\Adapters;

use OhCache\Adapters\AdapterMemcache;

/**
 * AdapterMemcacheTest
 */
class AdapterMemcacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AdapterMemcache
     */
    private $adapter = null;

    private $name = null;

    public function setUp()
    {
        if (!class_exists('\Memcache')) {
            $this->markTestSkipped(
                'Memcache class is not present. Try installing Memcache (and the PHP driver)'
            );
            return;
        }
        $this->name = md5(__CLASS__);
        $cacheConfig = array(
            array(
                'host' => 'localhost',
                'port' => 11211,
                'timeout' => 60,
                'weight' => 1
            )
        );
        $this->adapter = new AdapterMemcache($cacheConfig);
        $this->adapter->flush();
    }

    public function testSetGet()
    {
        $this->adapter->set($this->name, 'foo', 10);
        $this->assertEquals('foo', $this->adapter->get($this->name));
    }

    public function testSetGetTtlExpired()
    {
        $this->assertTrue($this->adapter->set($this->name, 'slept', 1));
        sleep(2);
        $this->assertFalse($this->adapter->get($this->name));
    }

    public function testSetIfNotExistsGet()
    {
        $str = md5(time());
        $this->assertTrue($this->adapter->setIfNotExists($str, 'foobar', 10));
        $this->assertFalse($this->adapter->setIfNotExists($str, 'barfoo', 10));
    }

    public function testSetRenew()
    {
        $this->assertTrue($this->adapter->set($this->name, 'bar', 10));
        $this->assertTrue($this->adapter->renew($this->name, 10));
        $this->assertFalse($this->adapter->renew(md5(microtime()), 100));
    }

    public function testHas()
    {
        $this->adapter->set($this->name, 'foobar', 10);
        $this->assertTrue($this->adapter->has($this->name));
        $this->assertEquals('foobar', $this->adapter->get($this->name));
        $this->adapter->remove($this->name);
        $this->assertFalse($this->adapter->has($this->name));
    }

    public function testRemove()
    {
        $this->adapter->set($this->name, 'barfoo', 10);
        $this->adapter->remove($this->name);
        $this->assertFalse($this->adapter->has($this->name));
    }
}
