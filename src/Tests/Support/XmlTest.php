<?php

/*
 * This file is part of the Spress\Import.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spress\Import\Tests\Support;

use Spress\Import\Support\Xml;

class XmlTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadXmlFile()
    {
        $xml = Xml::loadFile(__DIR__.'/../fixtures/wxr.xml');

        $this->assertInstanceOf('SimpleXMLElement', $xml);
    }
}
