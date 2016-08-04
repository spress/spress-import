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

use Spress\Import\Support\Str;

class StrTest extends \PHPUnit_Framework_TestCase
{
    public function testSlug()
    {
        $this->assertEquals('welcome-to-spress', Str::slug('Welcome to Spress'));
        $this->assertEquals('bienvenido-a-espana', Str::slug('Bienvenido a España'));
        $this->assertEquals('version-2-0-0', Str::slug('version 2.0.0'));
        $this->assertEquals('hello-spress', Str::slug('hello  spress'));
        $this->assertEquals('hello-spress', Str::slug('-hello-spress-'));
        $this->assertEquals('12-cheese', Str::slug('1\2 cheese'));
        $this->assertEquals('2-step', Str::slug('.,;{}+¨¿?=()/&%$·#@|!ºª2 step     ^[]'));
    }

    public function testStartWith()
    {
        $this->assertTrue(Str::startWith('Welcome to Spress', 'Wel'));
        $this->assertFalse(Str::startWith('Welcome to Spress', 'Well'));
    }

    public function testEndWith()
    {
        $this->assertTrue(Str::endWith('Welcome to Spress', 'press'));
        $this->assertFalse(Str::endWith('Welcome to Spress', 'to'));
    }

    public function testDeletePrefix()
    {
        $this->assertEquals('to Spress', Str::deletePrefix('Welcome to Spress', 'Welcome '));
        $this->assertEquals('Welcome to Spress', Str::deletePrefix('Welcome to Spress', 'Hi'));
        $this->assertEquals('Welcome to Spress', Str::deletePrefix('Welcome to Spress', ''));
    }

    public function testDeleteSufix()
    {
        $this->assertEquals('Welcome to', Str::deleteSufix('Welcome to Spress', ' Spress'));
        $this->assertEquals('Welcome to Spress', Str::deleteSufix('Welcome to Spress', 'Hi'));
        $this->assertEquals('Welcome to Spress', Str::deleteSufix('Welcome to Spress', ''));
    }

    public function testToAscii()
    {
        $this->assertEquals('camion', Str::toAscii('camión'));
        $this->assertEquals('espana', Str::toAscii('españa'));
        $this->assertEquals('bash', Str::toAscii('баш'));
        $this->assertEquals('baSH', Str::toAscii('баШ'));
    }
}
