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
 * Represents a processed item.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ResultItem
{
    private $sourcePermalink;
    private $relativePath;
    private $message;
    private $content;
    private $error = false;
    private $existsFile = false;

    /**
     * Constructor.
     *
     * @param string $sourcePermalink      The source Permalink of the item.
     * @param string $content              The content adapted to Spress.
     * @param bool   $existsFilePreviously Indicates if there is a file previously.
     */
    public function __construct($sourcePermalink, $content = '', $existsFilePreviously = false)
    {
        $this->message = '';
        $this->content = $content;
        $this->existsFile = $existsFilePreviously;
        $this->sourcePermalink = $sourcePermalink;
    }

    public function getSourcePermalink()
    {
        return $this->sourcePermalink;
    }

    public function setRelativePath($path)
    {
        $this->relativePath = $path;
    }

    public function getRelativePath()
    {
        return $this->relativePath;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setExistsFilePreviously($value)
    {
        $this->existsFile = $value;
    }

    public function existsFilePreviously()
    {
        return $this->existsFile;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setHasError($value)
    {
        $this->error = $value;
    }

    public function hasError()
    {
        return $this->error;
    }
}
