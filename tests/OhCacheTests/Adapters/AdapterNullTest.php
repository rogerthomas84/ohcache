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

use OhCache\Adapters\AdapterNull;

class AdapterNullTest extends \PHPUnit_Framework_TestCase
{
    public function testSet()
    {
        $instance = new AdapterNull();
        $this->assertFalse($instance->set('foo', 'bar'));
    }

    public function testGet()
    {
        $instance = new AdapterNull();
        $this->assertFalse($instance->get('foo'));
    }

    public function testHas()
    {
        $instance = new AdapterNull();
        $this->assertFalse($instance->has('foo'));
    }

    public function testRenew()
    {
        $instance = new AdapterNull();
        $this->assertFalse($instance->renew('foo', 3600));
    }

    public function testRemove()
    {
        $instance = new AdapterNull();
        $this->assertFalse($instance->remove('foo'));
    }

    public function testFlush()
    {
        $instance = new AdapterNull();
        $this->assertFalse($instance->flush());
    }
}

