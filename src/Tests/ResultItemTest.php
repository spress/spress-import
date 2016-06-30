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

use Spress\Import\ResultItem;

class ResultItemTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSorucePermalink()
    {
        $resultItem = new ResultItem('http://mysite.com/about');

        $this->assertEquals('http://mysite.com/about', $resultItem->getSourcePermalink());
    }

    public function testGetContent()
    {
        $resultItem = new ResultItem('http://mysite.com/', 'foo');

        $this->assertEquals('foo', $resultItem->getContent());

        $resultItem->setContent('hi');

        $this->assertEquals('hi', $resultItem->getContent());
    }

    public function testExistsPrevousFile()
    {
        $resultItem = new ResultItem('http://mysite.com/', '', true);

        $this->assertTrue($resultItem->existsFilePreviously());

        $resultItem = new ResultItem('http://mysite.com/', '', false);

        $this->assertFalse($resultItem->existsFilePreviously());

        $resultItem = new ResultItem('http://mysite.com/', 'foo');
        $resultItem->setExistsFilePreviously(true);

        $this->assertTrue($resultItem->existsFilePreviously());
    }

    public function testGetMessage()
    {
        $resultItem = new ResultItem('http://mysite.com/');

        $this->assertEquals('', $resultItem->getMessage());
    }

    public function testSetMessage()
    {
        $resultItem = new ResultItem('http://mysite.com/');
        $resultItem->setMessage('ok');

        $this->assertEquals('ok', $resultItem->getMessage());
    }
}
