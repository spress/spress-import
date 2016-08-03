<?php

/*
 * This file is part of the Spress\Import.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spress\Import\Provider;

use League\Csv\Reader;
use Spress\Import\Item;

/**
 * Provider for importing posts from CSV files.
 * The CSV file will be read with the following columns:
 * 1. title
 * 2. permalink
 * 3. content
 * 4. published_at
 * 5. categories (optional): a list of terms separated by semicolon. e.g: "news;events".
 * 6. tags (optional): a list of terms separated by semicolon.
 * 7. markup (optional) markup language used in content. e.g: "md", "html".
 *    "md" by default. This value will be used
 *    as filename's extension of the imported item.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class CsvProvider implements ProviderInterface
{
    private $options = [];

    public function __construct()
    {
        if (!ini_get('auto_detect_line_endings')) {
            ini_set('auto_detect_line_endings', '1');
        }
    }

    /**
     * {@inheritdoc}
     *
     * Options:
     *  - file (string): path to CSV file.
     *  - content (string): content in CSV format. If this option is set,
     *      'file' option will be ignored.
     *  - delimiter_character (string): Sets the deliminter character. ',' by
     *      default.
     *  - enclosure_character (string): Sets the enclousure character. '"' by
     *      default.
     *  - escape_character (string): Sets the escape character. '\' by default.
     *    Warning: str_getcsv function stills with a bug related with the escape character
     *      after more than four years. @link https://bugs.php.net/bug.php?id=55413
     *  - terms_delimiter_character (string): Sets the terms delimiter character.
     *      ';' by default.
     *  - not_header (bool): Indicates if the first row is considered as header.
     *      false by default.
     *
     *  e.g: ['file' => '/tmp/file.csv']
     */
    public function setUp(array $options)
    {
        $options = $this->resolveOptions($options);

        if (is_null($options['content']) && file_exists($options['file']) === false) {
            throw new \RuntimeException(sprintf('File "%s" not found.', $options['file']));
        }

        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        $line = 1;
        $items = [];
        $isFirstRow = true;
        $reader = $this->buildCsvReader();

        $rows = $reader->fetchAssoc([
            'title',
            'permalink',
            'content',
            'published_at',
            'categories',
            'tags',
            'markup',
        ]);

        foreach ($rows as $row) {
            if ($this->options['not_header'] == false && $isFirstRow == true) {
                ++$line;
                $isFirstRow = false;
                continue;
            }

            $data = $this->resolveCsvRow($row, $line);

            $item = new Item(Item::TYPE_POST, $data['permalink']);
            $item->setDate($data['published_at']);
            $item->setContent($data['content']);
            $item->setTitle($data['title']);
            $item->setContentExtension($data['markup']);

            $attributes = [];

            if (count($data['categories']) > 0) {
                $attributes['categories'] = $data['categories'];
            }

            if (count($data['tags']) > 0) {
                $attributes['tags'] = $data['tags'];
            }

            $item->setAttributes($attributes);

            $items[] = $item;

            ++$line;
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        // Nothing to do here
    }

    private function resolveCsvRow(array $data, $line)
    {
        if (empty($data['title'])) {
            throw new \RuntimeException(sprintf('Error at line %d, column 1: title cannot be empty.', $line));
        }

        $data['title'] = $this->normalize($data['title']);

        if (empty($data['permalink'])) {
            throw new \RuntimeException(sprintf('Error at line %d, column 2: permalink cannot be empty.', $line));
        }

        $data['permalink'] = $this->normalize($data['permalink']);

        if (empty($data['content'])) {
            throw new \RuntimeException(sprintf('Error at line %d, column 3: content cannot be empty.', $line));
        }

        $data['content'] = $this->normalize($data['content']);

        if (empty($data['published_at'])) {
            throw new \RuntimeException(sprintf('Error at line %d, column 4: published_at cannot be empty.', $line));
        }

        try {
            $data['published_at'] = new \DateTime($data['published_at']);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Error at line %d, column 4: published_at is not a valid date.', $line));
        }

        $delimiter = $this->options['terms_delimiter_character'];

        if (empty($data['categories'])) {
            $data['categories'] = [];
        } else {
            $data['categories'] = explode($delimiter, $this->normalize($data['categories']));
        }

        if (empty($data['tags'])) {
            $data['tags'] = [];
        } else {
            $data['tags'] = explode($delimiter, $this->normalize($data['tags']));
        }

        if (empty($data['markup'])) {
            $data['markup'] = 'md';
        }

        $data['markup'] = $this->normalize($data['markup']);

        return $data;
    }

    private function resolveOptions(array $options)
    {
        $resolved = array_replace([
            'file' => null,
            'content' => null,
            'delimiter_character' => ',',
            'enclosure_character' => '"',
            'escape_character' => '\\',
            'terms_delimiter_character' => ';',
            'not_header' => false,
        ], $options);

        if (is_string($resolved['delimiter_character']) == false) {
            throw new InvalidArgumentException('Expected string at "delimiter_character" option.');
        }

        if (strlen($resolved['delimiter_character']) > 1) {
            throw new InvalidArgumentException('Expected a single character at "delimiter_character" option.');
        }

        if (is_string($resolved['enclosure_character']) == false) {
            throw new InvalidArgumentException('Expected string at "enclosure_character" option.');
        }

        if (strlen($resolved['enclosure_character']) > 1) {
            throw new InvalidArgumentException('Expected a single character at "enclosure_character" option.');
        }

        if (is_string($resolved['escape_character']) == false) {
            throw new InvalidArgumentException('Expected string at "escape_character" option.');
        }

        if (strlen($resolved['escape_character']) > 1) {
            throw new InvalidArgumentException('Expected a single character at "escape_character" option.');
        }

        if (is_string($resolved['terms_delimiter_character']) == false) {
            throw new InvalidArgumentException('Expected string at "terms_delimiter_character" option.');
        }

        if (strlen($resolved['terms_delimiter_character']) > 1) {
            throw new InvalidArgumentException('Expected a single character at "terms_delimiter_character" option.');
        }

        if (is_bool($resolved['not_header']) == false) {
            throw new InvalidArgumentException('Expected boolean at "not_header" option.');
        }

        return $resolved;
    }

    private function buildCsvReader()
    {
        if (is_null($this->options['content']) == true) {
            $reader = Reader::createFromPath($this->options['file']);
        } else {
            $reader = Reader::createFromString($this->options['content']);
        }

        $reader->setDelimiter($this->options['delimiter_character']);
        $reader->setEnclosure($this->options['enclosure_character']);
        $reader->setEscape($this->options['escape_character']);

        return $reader;
    }

    private function normalize($data)
    {
        return preg_replace('/ {2,}/', ' ', trim($data));
    }
}
