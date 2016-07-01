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
use Spress\Import\Support\Xml;

/**
 * Provider for WXR files generated by Wordpres.
 * This provider requires SimpleXML extension.
 *
 * @see https://devtidbits.com/2011/03/16/the-wordpress-extended-rss-wxr-exportimport-xml-document-format-decoded-and-explained/
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class WxrProvider implements ProviderInterface
{
    private $file;
    private $namespaces = [];

    /**
     * {@inheritdoc}
     *
     * Options:
     *  - file (string): path to wxr file (a XML file generated by Wordpress).
     *
     *  e.g: ['file' => /tmp/myblog.xml]
     *
     * @throw RuntimeException If SimpleXML extension was not found.
     */
    public function setUp(array $options)
    {
        if (extension_loaded('simplexml') === false) {
            throw new \RuntimeException('SimpleXML extension not found.');
        }

        $this->file = $options['file'];
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        $items = [];
        $xml = Xml::loadFile($this->file);

        $this->processVersion($xml);
        $this->processNamespaces($xml);

        $base_url = $xml->xpath('/rss/channel/wp:base_site_url');
        $base_url = (string) trim($base_url[0]);

        foreach ($xml->channel->item as $item) {
            $attributes = [];
            $wp = $item->children($this->namespaces['wp']);
            $content = $item->children('http://purl.org/rss/1.0/modules/content/');
            $excerpt = $item->children($this->namespaces['excerpt']);

            $importedItem = $this->makeItem($wp, (string) $item->link);
            $importedItem->setTitle((string) $item->title);
            $importedItem->setContent((string) $content->encoded);
            $importedItem->setDate(new \DateTime((string) $wp->post_date_gmt));

            $dc = $item->children('http://purl.org/dc/elements/1.1/');

            $attributes['author'] = (string) $dc->creator;
            $attributes['excerpt'] = (string) $excerpt->encoded;
            $attributes['categories'] = [];
            $attributes['tags'] = [];

            foreach ($item->category as $category) {
                $att = $category->attributes();

                if (isset($att['nicename']) === true) {
                    if (isset($att['domain']) === true && (string) $att['domain'] == 'post_tag') {
                        $attributes['tags'][] = (string) $category;
                        continue;
                    }

                    $attributes['categories'][] = (string) $category;
                }
            }

            $importedItem->setAttributes($attributes);

            $items[] = $importedItem;
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
    }

    private function processVersion(\SimpleXMLElement $xml)
    {
        $version = $xml->xpath('/rss/channel/wp:wxr_version');

        if ($version === false
        ) {
            throw new \RuntimeException('This does not appear to be a WXR file, missing or invalid WXR version number.');
        }

        $version = (string) trim($version[0]);

        if (preg_match('/^\d+\.\d+$/', $version) === false) {
            throw new \RuntimeException('This does not appear to be a WXR file, missing or invalid WXR version number.');
        }
    }

    private function processNamespaces(\SimpleXMLElement $xml)
    {
        $this->namespaces = $xml->getDocNamespaces();

        if (isset($this->namespaces['wp']) === false) {
            $this->namespaces['wp'] = 'http://wordpress.org/export/1.1/';
        }

        if (isset($this->namespaces['excerpt']) === false) {
            $this->namespaces['excerpt'] = 'http://wordpress.org/export/1.1/excerpt/';
        }
    }

    private function makeItem(\SimpleXMLElement $xml, $permalink)
    {
        $type = (string) $xml->post_type;

        switch ($type) {
            case 'post':
                return new Item(Item::TYPE_POST, $permalink);
            case 'attachment':
                return new Item(Item::TYPE_RESOURCE, $permalink);
            default:
                return new Item(Item::TYPE_PAGE, $permalink);
        }
    }
}