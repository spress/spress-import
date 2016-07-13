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

use Spress\Import\Support\Str;
use Spress\Import\Support\File;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Represents an item of content. e.g: post or page.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ProviderManager
{
    protected $dryRun = false;
    protected $fetchResources = false;
    protected $replaceResources = true;
    protected $layoutPage;
    protected $layoutPost;
    protected $assetsPath;
    protected $srcPath;
    protected $providerCollection;
    protected $impotedItems = [];
    protected $resourceItems = [];
    protected $postAndPageItems = [];

    /**
     * Constructor.
     *
     * @param ProviderCollection $collection Collection of providers.
     * @param string             $srcPath    Path to the src folder. e.g: "/site/src".
     * @param string             $assetsPath Relative path to `$srcPath/content` for storing the assets. e.g: "assets"
     */
    public function __construct(ProviderCollection $collection, $srcPath, $assetsPath = null)
    {
        $this->srcPath = $srcPath;
        $this->assetsPath = $this->sanitizePath('content/'.$assetsPath);
        $this->providerCollection = $collection;
    }

    /**
     * Indicates that it's not necessary to do changes
     * in the `src` path.
     */
    public function enableDryRun()
    {
        $this->dryRun = true;
    }

    public function enableFetchResources()
    {
        if (function_exists('curl_version') == false) {
            throw new \RuntimeException('CURL library was not found.');
        }

        if (empty($this->assetsPath) == true) {
            throw new \RuntimeException('The assets path is necessary for fetching resources.');
        }

        $this->fetchResources = true;
    }

    /**
     * Avoids to replace the URLs matched the fetched resources by local resources.
     * This option only has effect if fetch resources feature is enabled.
     *
     * @See fetchResources
     */
    public function DoNotReplaceResources()
    {
        $this->replaceResources = false;
    }

    /**
     * Sets the layout for page items.
     *
     * @param string $layoutName The layout. e.g: "default" or "pages/default".
     */
    public function setPageLayout($layoutName)
    {
        $this->layoutPage = $layoutName;
    }

    /**
     * Sets the layout for post items.
     *
     * @param string $layoutName The layout. e.g: "default" or "blog/post".
     */
    public function setPostLayout($layoutName)
    {
        $this->layoutPost = $layoutName;
    }

    /**
     * Import a site from a provider.
     *
     * @param string $providerName The name of the provider.
     * @param array  $options      Options passed to provider.
     *
     * @return ResultItem[]
     */
    public function import($providerName, array $options)
    {
        $provider = $this->providerCollection->get($providerName);
        $provider->setUp($options);

        $this->processItems($provider->getItems());

        $provider->tearDown();

        return $this->impotedItems;
    }

    protected function processItems(array $items)
    {
        $this->impotedItems = [];
        $this->resourceItems = [];
        $this->postAndPageItems = [];

        foreach ($items as $item) {
            try {
                $resultItem = $this->processItem($item);
            } catch (\Exception $e) {
                $resultItem = new ResultItem($item->getPermalink());
                $resultItem->setHasError(true);
                $resultItem->setMessage($e->getMessage());
            }

            if ($this->replaceResources == true) {
                $this->replaceUrlResourcePostAndPages();
            }

            if (is_null($resultItem) == false) {
                $this->impotedItems[] = $resultItem;
                $this->dumpResultItem($resultItem);
            }
        }
    }

    protected function processItem(Item $item)
    {
        switch ($item->getType()) {
            case Item::TYPE_POST:
                return $this->processPostItem($item);
                break;
            case Item::TYPE_RESOURCE:
                return $this->processResourceItem($item);
                break;
            default:
                return $this->processPageItem($item);
                break;
        }
    }

    protected function processPageItem(Item $item)
    {
        $urlPath = parse_url($item->getPermalink(), PHP_URL_PATH);
        $urlPath = $this->normalizePath($urlPath);
        $baseName = basename($urlPath);
        $baseNameLength = strlen($baseName.'/');
        $pathWithoutBase = substr_replace($urlPath, '', -$baseNameLength, $baseNameLength);

        if ($baseName == '') {
            $baseName = 'index.html';
        }

        if (strpos($baseName, '.') === false) {
            $baseName .= '.html';
        }

        $relativePath = $this->sanitizePath($pathWithoutBase.'/'.$baseName);
        $fileExists = file_exists($this->getSrcPath($relativePath));
        $spressContent = $this->getSpressContent($item);

        $resultItem = new ResultItem($item->getPermalink(), $spressContent, $fileExists);
        $resultItem->setRelativePath($relativePath);
        $this->postAndPageItems[] = $resultItem;

        return $resultItem;
    }

    protected function processPostItem(Item $item)
    {
        if (is_null($item->getDate())) {
            throw new \RuntimeException(sprintf('Date in post item: "%s" is required.', $item->getPermalink()));
        }

        if (empty($item->getTitle())) {
            throw new \RuntimeException(sprintf('A title in post item: "%s" is required.', $item->getPermalink()));
        }

        $slugedTitle = Str::slug($item->getTitle());
        $filename = sprintf('%s-%s.html', $item->getDate()->format('Y-m-d'), $slugedTitle);

        $relativePath = $this->sanitizePath('content/posts/'.$filename);
        $fileExists = file_exists($this->getSrcPath($relativePath));
        $spressContent = $this->getSpressContent($item);

        $resultItem = new ResultItem($item->getPermalink(), $spressContent, $fileExists);
        $resultItem->setRelativePath($relativePath);
        $this->postAndPageItems[] = $resultItem;

        return $resultItem;
    }

    protected function processResourceItem(Item $item)
    {
        if ($this->fetchResources == false) {
            return;
        }

        $urlPath = parse_url($item->getPermalink(), PHP_URL_PATH);
        $urlPath = $this->normalizePath($urlPath);

        $baseName = basename($urlPath);
        $baseNameLength = strlen($baseName.'/');
        $pathWithoutBase = substr_replace($urlPath, '', -$baseNameLength, $baseNameLength);
        $relativePath = $this->assetsPath.'/'.$this->sanitizePath($pathWithoutBase.'/'.$baseName);

        $fileExists = file_exists($this->getSrcPath($relativePath));
        $binaryContent = $item->getContent();

        if ($item->getFetchPermalink() == true) {
            $binaryContent = $this->downloadResource($item->getPermalink());
        }

        $resultItem = new ResultItem($item->getPermalink(), $binaryContent, $fileExists);
        $resultItem->setRelativePath($relativePath);
        $this->resourceItems[] = $resultItem;

        return $resultItem;
    }

    protected function replaceUrlResourcePostAndPages()
    {
        $urlsSourcePermalinks = [];
        $urlLocal = [];

        foreach ($this->resourceItems as $resultItem) {
            $urlsSourcePermalinks[] = $resultItem->getSourcePermalink();
            $urlLocal[] = Str::deletePrefix($resultItem->getRelativePath(), 'content');
        }

        foreach ($this->postAndPageItems as $resultItem) {
            $content = $resultItem->getContent();
            $resultItem->setContent(str_replace($urlsSourcePermalinks, $urlLocal, $content));
        }
    }

    protected function normalizePath($url)
    {
        return $this->sanitizePath(strtolower($url).'/');
    }

    protected function sanitizePath($url)
    {
        return preg_replace('/\/\/+/', '/', ltrim($url, '/'));
    }

    protected function getSrcPath($relativePath)
    {
        return $this->srcPath.'/'.$relativePath;
    }

    protected function dumpResultItem(ResultItem $resultItem)
    {
        if ($this->dryRun == true || $resultItem->hasError() == true) {
            return;
        }

        $fs = new Filesystem();
        $fs->dumpFile($this->getSrcPath($resultItem->getRelativePath()), $resultItem->getContent());
    }

    protected function getSpressContent(Item $item)
    {
        $attributes = $item->getAttributes();
        $attributes['source_permalink'] = $item->getPermalink();

        switch ($item->getType()) {
            case Item::TYPE_POST:
                if (empty($this->layoutPost) == false) {
                    $attributes['layout'] = $this->layoutPost;
                }
                break;
            case Item::TYPE_PAGE:
                if (empty($this->layoutPage) == false) {
                    $attributes['layout'] = $this->layoutPage;
                }
                break;
        }

        if (empty($item->getTitle()) == false) {
            $attributes['title'] = $item->getTitle();
        }

        $yamlContent = Yaml::dump($attributes);
        $content = sprintf("---\n%s\n---\n%s", $yamlContent, $item->getContent());

        return $content;
    }

    protected function downloadResource($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 Spress-import plugin');
        $result = curl_exec($ch);
        $resultcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($resultcode != 200) {
            throw new \RuntimeException(sprintf('Requested resource responded with a code: %d.', $resultcode));
        }

        return $result;
    }
}
