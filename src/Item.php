<?php

/*
 * This file is part of the Spress\Import.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spress\Import;

/**
 * Represents an item of content. e.g: post or page.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Item
{
    const TYPE_PAGE = 'page';
    const TYPE_POST = 'post';
    const TYPE_RESOURCE = 'resource';

    private $date;
    private $title;
    private $permalink;
    private $attributes = [];
    private $content;

    /**
     * Constructor.
     *
     * @param string $type      Type of item (page, post or resource).
     * @param string $permalink Permalink of the item. This value acts as identifier
     *                          of the content. e.g: http://acme.com/about
     */
    public function __construct($type, $permalink)
    {
        $this->title = '';
        $this->content = '';
        $this->type = $type;
        $this->permalink = $permalink;
    }

    /**
     * Gets the type of the item.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the title.
     *
     * @param string $title The title.
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Gets the title of the item.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Gets the permalink of the item.
     *
     * @return string
     */
    public function getPermalink()
    {
        return $this->permalink;
    }

    /**
     * Sets the content.
     *
     * @param string $value The content of the item.
     */
    public function setContent($value)
    {
        $this->content = $value;
    }

    /**
     * Gets the content of the item.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the date when the item was published.
     *
     * @param DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * Gets the date when the item was published. This value is mandatory
     * in case of post item.
     *
     * @return DateTime|null Null if this value was not set up.
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Sets the attributes associated with the item.
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Gets a set of attributes associated with the item.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    public function __toString()
    {
        return $this->getPermalink();
    }
}
