<?php

/*
 * This file is part of the Spress\Import.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spress\Import\Tests\Provider;

use Spress\Import\Item;
use Spress\Import\Provider\WxrProvider;

class WxrProviderTest extends \PHPUnit_Framework_TestCase
{
    protected $provider;

    public function setUp()
    {
        $this->provider = new WxrProvider();
        $this->provider->setUp(['file' => __DIR__.'/../fixtures/wxr.xml']);
    }

    public function testGetItems()
    {
        $items = $this->provider->getItems();

        $this->assertContainsOnlyInstancesOf('Spress\Import\Item', $items);
    }

    public function testPostItems()
    {
        $items = $this->provider->getItems();

        $this->assertEquals(Item::TYPE_POST, $items[0]->getType());
        $this->assertEquals('Spress import plugin', $items[0]->getTitle());
        $this->assertEquals('This is a test post used by Spress import plugin. Import is a tool for importing blog from platform such as Wordpress or Tumblr.', $items[0]->getContent());
        $this->assertEquals('https://spressimport.wordpress.com/2016/06/21/spress-import-plugin/', $items[0]->getPermalink());

        $this->assertEquals([
            'author' => 'lennyvpg',
            'excerpt' => '',
            'categories' => ['Tech'],
            'tags' => ['plugins', 'spress', 'static site generator'],
        ], $items[0]->getAttributes());

        $this->assertEquals(Item::TYPE_POST, $items[1]->getType());
        $this->assertEquals('Hello with photo', $items[1]->getTitle());
        $this->assertEquals('[caption id="attachment_7" align="alignnone" width="800"]<img class="alignnone size-full wp-image-7" src="https://spressimport.files.wordpress.com/2016/06/14004361452_b952deddeb_o.jpg" alt="Colorado Farming" width="800" height="533" /> Early morning springtime scenic landscape view of a red barn and tractor with the majestic Rocky Mountains front range towering in the background and a view of the Twin Peaks Mt Meeker 13,911 feet and Longs Peak 14,256 feet. Colorado is home to towering mountains and flat plains. [/caption]', $items[1]->getContent());
        $this->assertEquals('https://spressimport.wordpress.com/2016/06/21/hello-with-photo/', $items[1]->getPermalink());

        $this->assertEquals([
            'author' => 'lennyvpg',
            'excerpt' => '',
            'categories' => ['No category'],
            'tags' => [],
        ], $items[1]->getAttributes());
    }

    public function testGetResourceItems()
    {
        $items = $this->provider->getItems();

        $this->assertEquals(Item::TYPE_RESOURCE, $items[2]->getType());
        $this->assertEquals('Colorado Farming', $items[2]->getTitle());
        $this->assertEquals('', $items[2]->getContent());
        $this->assertEquals('https://spressimport.files.wordpress.com/2016/06/14004361452_b952deddeb_o.jpg', $items[2]->getPermalink());
        $this->assertNotEmpty($items[2]->getAttributes()['excerpt']);
    }
}
