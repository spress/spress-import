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

use Spress\Import\Item;

class ItemTest extends \PHPUnit_Framework_TestCase
{
    public function testGetTypeAndPermalink()
    {
        $item = new Item(Item::TYPE_PAGE, '/about');

        $this->assertEquals(Item::TYPE_PAGE, $item->getType());
        $this->assertEquals('/about', $item->getPermalink());
    }

    public function testGetTitle()
    {
        $item = new Item(Item::TYPE_PAGE, '/welcome');
        $item->setTitle('Hi Spress');

        $this->assertEquals('Hi Spress', $item->getTitle());
    }

    public function testGetContent()
    {
        $item = new Item(Item::TYPE_PAGE, '/foo');
        $item->setContent('foo');

        $this->assertEquals('foo', $item->getContent());
    }

    public function testGetDate()
    {
        $datetime = new \DateTime();
        $item = new Item(Item::TYPE_PAGE, '/date');
        $item->setDate($datetime);

        $this->assertEquals($datetime, $item->getDate());
    }

    public function testGetAttributes()
    {
        $attributes = [
            'title' => 'Hi Spress',
        ];
        $item = new Item(Item::TYPE_PAGE, '/attributes');
        $item->setAttributes($attributes);

        $this->assertEquals($attributes, $item->getAttributes());
    }
}
