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

use Spress\Import\Item;

/**
 * Provider for content defined by an array.
 * This provider is only for testing purposes.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ArrayProvider implements ProviderInterface
{
    protected $contets;

    /**
     * Constructor.
     *
     * @param     array of content. e.g:
     * [
     *    [
     *      'type' => Item::TYPE_PAGE,
     *      'permalink' => '/acme.html',
     *      'content' => 'test',
     *      'attributes' => ['code' => true],
     *    ],
     *    [...],
     * ]
     */
    public function __construct(array $contents)
    {
        $this->contents = $contents;
    }

    /**
     * {@inheritdoc}
     */
    public function setUp(array $options)
    {
        // Nothing to do here
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        $items = [];

        foreach ($this->contents as $content) {
            if (isset($content['type']) === false) {
                continue;
            }

            if (isset($content['permalink']) === false) {
                continue;
            }

            $item = new Item($content['type'], $content['permalink']);

            if (isset($content['content']) === true) {
                $item->setContent($content['content']);
            }

            if (isset($content['title']) === true) {
                $item->setTitle($content['title']);
            }

            if (isset($content['date']) === true) {
                $item->setDate(new \DateTime($content['date']));
            }

            if (isset($content['attributes']) === true) {
                $item->setAttributes($content['attributes']);
            }

            $items[] = $item;
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
}
