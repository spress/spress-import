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

use Spress\Import\Provider\ArrayProvider;
use Spress\Import\ProviderCollection;
use Spress\Import\ProviderManager;
use Symfony\Component\Filesystem\Filesystem;

class ProviderManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $srcPath;

    public function setUp()
    {
        $this->srcPath = sys_get_temp_dir().'/spress-import';
    }

    public function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove($this->srcPath);
    }

    public function testContent()
    {
        $providerCollection = new ProviderCollection([
            'array' => new ArrayProvider([
                [
                    'type' => 'page',
                    'permalink' => 'http://mysite.com/about',
                ],
                [
                    'type' => 'page',
                    'permalink' => 'http://mysite.com/about/license',
                ],
            ]),
        ]);
        $providerManager = new ProviderManager($providerCollection, $this->srcPath);
        $providerManager->enableDryRun();
        $itemResults = $providerManager->import('array', []);

        $this->assertCount(2, $itemResults);

        $itemResult = $itemResults[0];

        $this->assertEquals('about.html', $itemResult->getRelativePath());
        $this->assertEquals("---\nsource_permalink: 'http://mysite.com/about'\n\n---\n", $itemResult->getContent());
    }

    public function testPermalinkWithExtension()
    {
        $providerCollection = new ProviderCollection([
            'array' => new ArrayProvider([
                [
                    'type' => 'page',
                    'permalink' => 'http://mysite.com/about/license.html',
                ],
            ]),
        ]);
        $providerManager = new ProviderManager($providerCollection, $this->srcPath);
        $providerManager->enableDryRun();
        $itemResults = $providerManager->import('array', []);

        $this->assertCount(1, $itemResults);

        $itemResult = $itemResults[0];

        $this->assertEquals('about/license.html', $itemResult->getRelativePath());
    }

    public function testWritePageFile()
    {
        $providerCollection = new ProviderCollection([
            'array' => new ArrayProvider([
                [
                    'type' => 'page',
                    'permalink' => 'http://mysite.com/about',
                ],
            ]),
        ]);
        $providerManager = new ProviderManager($providerCollection, $this->srcPath);
        $itemResults = $providerManager->import('array', []);

        $this->assertFileExists($this->srcPath.'/about.html');
    }

    public function testImportPost()
    {
        $providerCollection = new ProviderCollection([
            'array' => new ArrayProvider([
                [
                    'type' => 'post',
                    'permalink' => 'http://mysite.com/posts/hello-world',
                    'date' => '2016-06-29',
                    'title' => 'Hello world',
                ],
            ]),
        ]);
        $providerManager = new ProviderManager($providerCollection, $this->srcPath);
        $providerManager->enableDryRun();
        $itemResults = $providerManager->import('array', []);

        $this->assertCount(1, $itemResults);

        $itemResult = $itemResults[0];

        $this->assertEquals('content/posts/2016-06-29-hello-world.html', $itemResult->getRelativePath());
    }

    public function testWritePostFile()
    {
        $providerCollection = new ProviderCollection([
            'array' => new ArrayProvider([
                [
                    'type' => 'post',
                    'permalink' => 'http://mysite.com/posts/hello-world',
                    'date' => '2016-06-29',
                    'title' => 'Hello world',
                ],
            ]),
        ]);
        $providerManager = new ProviderManager($providerCollection, $this->srcPath);
        $itemResults = $providerManager->import('array', []);

        $this->assertCount(1, $itemResults);
        $this->assertFileExists($this->srcPath.'/content/posts/2016-06-29-hello-world.html');
    }

    public function testLayoutPost()
    {
        $providerCollection = new ProviderCollection([
            'array' => new ArrayProvider([
                [
                    'type' => 'post',
                    'permalink' => 'http://mysite.com/posts/hello-world',
                    'date' => '2016-06-29',
                    'title' => 'Hello world',
                ],
            ]),
        ]);
        $providerManager = new ProviderManager($providerCollection, $this->srcPath);
        $providerManager->enableDryRun();
        $providerManager->setPostLayout('default');
        $itemResults = $providerManager->import('array', []);

        $itemResult = $itemResults[0];
        $content = <<<EOC
---
source_permalink: 'http://mysite.com/posts/hello-world'
layout: default
title: 'Hello world'

---

EOC;
        $this->assertEquals($content, $itemResult->getContent());
    }

    public function testLayoutPage()
    {
        $providerCollection = new ProviderCollection([
            'array' => new ArrayProvider([
                [
                    'type' => 'page',
                    'permalink' => 'http://mysite.com/about',
                ],
            ]),
        ]);
        $providerManager = new ProviderManager($providerCollection, $this->srcPath);
        $providerManager->enableDryRun();
        $providerManager->setPageLayout('page');
        $itemResults = $providerManager->import('array', []);

        $itemResult = $itemResults[0];
        $content = <<<EOC
---
source_permalink: 'http://mysite.com/about'
layout: page

---

EOC;
        $this->assertEquals($content, $itemResult->getContent());
    }

    public function testFetchImagen()
    {
        $providerCollection = new ProviderCollection([
            'array' => new ArrayProvider([
                [
                    'type' => 'resource',
                    'permalink' => 'https://spressimport.files.wordpress.com/2016/06/14004361452_b952deddeb_o.jpg',
                ],
            ]),
        ]);
        $assetsPath = 'img';
        $providerManager = new ProviderManager($providerCollection, $this->srcPath, $assetsPath);
        $providerManager->enableDryRun();
        $providerManager->fetchResources();
        $itemResults = $providerManager->import('array', []);

        $this->assertCount(1, $itemResults);
        $this->assertEquals('content/img/2016/06/14004361452_b952deddeb_o.jpg', $itemResults[0]->getRelativePath());
    }

    public function testWriteImagen()
    {
        $providerCollection = new ProviderCollection([
            'array' => new ArrayProvider([
                [
                    'type' => 'resource',
                    'permalink' => 'https://spressimport.files.wordpress.com/2016/06/14004361452_b952deddeb_o.jpg',
                ],
            ]),
        ]);
        $assetsPath = '/img';
        $providerManager = new ProviderManager($providerCollection, $this->srcPath, $assetsPath);
        $providerManager->fetchResources();
        $itemResults = $providerManager->import('array', []);

        $this->assertCount(1, $itemResults);
        $this->assertFileExists($this->srcPath.'/content/img/2016/06/14004361452_b952deddeb_o.jpg');
    }

    public function testImagenNotFound()
    {
        $providerCollection = new ProviderCollection([
            'array' => new ArrayProvider([
                [
                    'type' => 'resource',
                    'permalink' => 'https://spressimport.files.wordpress.com/image-not-found.png',
                ],
            ]),
        ]);
        $assetsPath = '/img';
        $providerManager = new ProviderManager($providerCollection, $this->srcPath, $assetsPath);
        $providerManager->fetchResources();
        $itemResults = $providerManager->import('array', []);

        $this->assertCount(1, $itemResults);
        $this->assertTrue($itemResults[0]->hasError());
    }

    public function testReplaceResource()
    {
        $providerCollection = new ProviderCollection([
            'array' => new ArrayProvider([
                [
                    'type' => 'resource',
                    'permalink' => 'https://spressimport.files.wordpress.com/2016/06/14004361452_b952deddeb_o.jpg',
                ],
                [
                    'type' => 'page',
                    'permalink' => 'http://mysite.com/about',
                    'content' => '<img src="https://spressimport.files.wordpress.com/2016/06/14004361452_b952deddeb_o.jpg" />',
                ],
            ]),
        ]);
        $assetsPath = '/img';
        $providerManager = new ProviderManager($providerCollection, $this->srcPath, $assetsPath);
        $providerManager->fetchResources();
        $itemResults = $providerManager->import('array', []);

        $this->assertCount(2, $itemResults);

        $content = <<<EOC
---
source_permalink: 'http://mysite.com/about'

---
<img src="/img/2016/06/14004361452_b952deddeb_o.jpg" />
EOC;
        $this->assertEquals($content, $itemResults[1]->getContent());
    }
}
