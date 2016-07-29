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

use Spress\Import\Provider\CsvProvider;

class CsvProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testsGetItemsCsvFile()
    {
        $provider = new CsvProvider();
        $provider->setUp(['file' => __DIR__.'/../fixtures/posts.csv']);
        $items = $provider->getItems();

        $this->assertContainsOnlyInstancesOf('Spress\Import\Item', $items);
        $this->assertCount(2, $items);
        $this->assertEquals('Hello', $items[0]->getTitle());
        $this->assertEquals('http://mysite.com/posts/hello', $items[0]->getPermalink());
        $this->assertEquals('The content', $items[0]->getContent());
        $this->assertEquals(new \DateTime('2016-07-27'), $items[0]->getDate());
        $this->assertEquals('md', $items[0]->getContentExtension());

        $this->assertEquals('Welcome', $items[1]->getTitle());
        $this->assertEquals('http://mysite.com/posts/welcome', $items[1]->getPermalink());
        $this->assertEquals('Welcome to Spress', $items[1]->getContent());
        $this->assertEquals(new \DateTime('2016-07-26'), $items[1]->getDate());
        $this->assertEquals('md', $items[1]->getContentExtension());
    }

    public function testGetItemsCsvString()
    {
        $csv = <<<EOF
        title,permalink,content,published_at
        Hello,"http://mysite.com/posts/hello",The content,2016-07-27
        Welcome,"http://mysite.com/posts/welcome",Welcome to Spress,2016-07-26
EOF;
        $provider = new CsvProvider();
        $provider->setUp(['content' => $csv]);
        $items = $provider->getItems();

        $this->assertContainsOnlyInstancesOf('Spress\Import\Item', $items);
        $this->assertCount(2, $items);
        $this->assertEquals('Hello', $items[0]->getTitle());
        $this->assertEquals('http://mysite.com/posts/hello', $items[0]->getPermalink());
        $this->assertEquals('The content', $items[0]->getContent());
        $this->assertEquals(new \DateTime('2016-07-27'), $items[0]->getDate());
        $this->assertEquals('md', $items[0]->getContentExtension());

        $this->assertEquals('Welcome', $items[1]->getTitle());
        $this->assertEquals('http://mysite.com/posts/welcome', $items[1]->getPermalink());
        $this->assertEquals('Welcome to Spress', $items[1]->getContent());
        $this->assertEquals(new \DateTime('2016-07-26'), $items[1]->getDate());
        $this->assertEquals('md', $items[1]->getContentExtension());
    }

    public function testNoHeader()
    {
        $csv = <<<EOF
        Hello,"http://mysite.com/posts/hello",The content,2016-07-27
        Welcome,"http://mysite.com/posts/welcome",Welcome to Spress,2016-07-26
EOF;
        $provider = new CsvProvider();
        $provider->setUp(['content' => $csv, 'no_header' => true]);
        $items = $provider->getItems();

        $this->assertCount(2, $items);
        $this->assertEquals('Hello', $items[0]->getTitle());
        $this->assertEquals('Welcome', $items[1]->getTitle());
    }

    public function testHtmlMarkup()
    {
        $csv = <<<EOF
        Hello,"http://mysite.com/posts/hello",The content,2016-07-27
        Welcome,"http://mysite.com/posts/welcome",Welcome to Spress,2016-07-26,html
EOF;
        $provider = new CsvProvider();
        $provider->setUp(['content' => $csv, 'no_header' => true]);
        $items = $provider->getItems();

        $this->assertCount(2, $items);
        $this->assertEquals('md', $items[0]->getContentExtension());
        $this->assertEquals('html', $items[1]->getContentExtension());
    }

    public function testChangeDelimiterCharacter()
    {
        $csv = <<<EOF
        Hello;"http://mysite.com/posts/hello";The content;2016-07-27
        Welcome;"http://mysite.com/posts/welcome";Welcome to Spress;2016-07-26
EOF;
        $provider = new CsvProvider();
        $provider->setUp([
            'content' => $csv,
            'no_header' => true,
            'delimiter_character' => ';',
        ]);
        $items = $provider->getItems();

        $this->assertCount(2, $items);
        $this->assertEquals('Hello', $items[0]->getTitle());
        $this->assertEquals('Welcome', $items[1]->getTitle());
        $this->assertEquals('md', $items[0]->getContentExtension());
        $this->assertEquals('md', $items[1]->getContentExtension());
    }

    public function testChangeEnclousureCharacter()
    {
        $csv = <<<EOF
        Hello,'http://mysite.com/posts/hello',The content,2016-07-27
        Welcome,'http://mysite.com/posts/welcome',Welcome to Spress,2016-07-26
EOF;
        $provider = new CsvProvider();
        $provider->setUp([
            'content' => $csv,
            'no_header' => true,
            'enclosure_character' => "'",
        ]);
        $items = $provider->getItems();

        $this->assertCount(2, $items);
        $this->assertEquals('Hello', $items[0]->getTitle());
        $this->assertEquals('Welcome', $items[1]->getTitle());
        $this->assertEquals('md', $items[0]->getContentExtension());
        $this->assertEquals('md', $items[1]->getContentExtension());
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Error at line 1, column 1: title cannot be empty.
     */
    public function testNoTitleValue()
    {
        $csv = <<<EOF
        "","http://mysite.com/posts/hello",The content,2016-07-27
EOF;
        $provider = new CsvProvider();
        $provider->setUp(['content' => $csv, 'no_header' => true]);
        $items = $provider->getItems();
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Error at line 1, column 2: permalink cannot be empty.
     */
    public function testNoPermalinkValue()
    {
        $csv = <<<EOF
        Hello,,The content,2016-07-27
EOF;
        $provider = new CsvProvider();
        $provider->setUp(['content' => $csv, 'no_header' => true]);
        $items = $provider->getItems();
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Error at line 1, column 3: content cannot be empty.
     */
    public function testNoContentValue()
    {
        $csv = <<<EOF
        Hello,"http://mysite.com/posts/hello",,2016-07-27
EOF;
        $provider = new CsvProvider();
        $provider->setUp(['content' => $csv, 'no_header' => true]);
        $items = $provider->getItems();
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Error at line 1, column 4: published_at is not a valid date.
     */
    public function testBadPublishedAtValue()
    {
        $csv = <<<EOF
        Hello,"http://mysite.com/posts/hello",The content,no-date
EOF;
        $provider = new CsvProvider();
        $provider->setUp(['content' => $csv, 'no_header' => true]);
        $items = $provider->getItems();
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessageRegExp /not found./
     */
    public function testCsvFileNotFound()
    {
        $provider = new CsvProvider();
        $provider->setUp(['file' => __DIR__.'/../fixtures/not-found-file.csv']);
    }
}
