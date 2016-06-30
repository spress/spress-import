<?php

/*
 * This file is part of the Spress\Import.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spress\Import\Tests;

use Spress\Import\Provider\WxrProvider;
use Spress\Import\ProviderCollection;

class ProviderCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyCollection()
    {
        $collection = new ProviderCollection();

        $this->assertCount(0, $collection);
    }

    public function testIterator()
    {
        $providers = [
            'wordpress' => new WxrProvider(),
        ];
        $collection = new ProviderCollection($providers);

        $this->assertInstanceOf('\ArrayIterator', $collection->getIterator());
        $this->assertSame($providers, $collection->getIterator()->getArrayCopy());
    }

    public function gestGet()
    {
        $provider = new WxrProvider();
        $collection = new ProviderCollection(['wordpress' => $provider]);

        $this->assertSame($provider, $collection->get('wordpress'));
    }

    public function testGet()
    {
        $provider = new WxrProvider();
        $collection = new ProviderCollection();
        $collection->set($provider, 'wordpress');

        $this->assertSame($provider, $collection->get('wordpress'));
    }

    public function testHas()
    {
        $collection = new ProviderCollection();
        $collection->set(new WxrProvider(), 'wordpress');

        $this->assertTrue($collection->has('wordpress'));
        $this->assertFalse($collection->has('foo'));
    }

    public function testAdd()
    {
        $provider = new WxrProvider();
        $collection = new ProviderCollection();
        $collection->add($provider, 'wordpress');

        $this->assertSame([
            'wordpress' => $provider,
        ], $collection->getIterator()->getArrayCopy());
    }

    public function testAll()
    {
        $provider = new WxrProvider();
        $collection = new ProviderCollection();
        $collection->add($provider, 'wordpress');

        $this->assertSame([
            'wordpress' => $provider,
        ], $collection->all());
    }

    public function testCount()
    {
        $collection = new ProviderCollection();
        $collection->add(new WxrProvider(), 'wordpress');

        $this->assertEquals(1, $collection->count());
    }

    public function testRemove()
    {
        $collection = new ProviderCollection();
        $collection->add(new WxrProvider(), 'wordpress');
        $collection->remove('wordpress');

        $this->assertCount(0, $collection);
    }

    public function testClear()
    {
        $collection = new ProviderCollection();
        $collection->add(new WxrProvider(), 'wordpress');
        $collection->clear();

        $this->assertCount(0, $collection);
    }

    /**
     * @expectedException \RuntimeException
     * expectedExceptionMessage A previous provider exists with the same name: "foo".
     */
    public function testAddExistingProviderName()
    {
        $itemSet = new ProviderCollection();
        $itemSet->add(new WxrProvider(), 'wordpress');
        $itemSet->add(new WxrProvider(), 'wordpress');
    }

    /**
     * @expectedException \RuntimeException
     * expectedExceptionMessage Provider with name: "foo" not found.
     */
    public function testProviderNotFound()
    {
        $itemSet = new ProviderCollection();
        $itemSet->get('foo');
    }
}
