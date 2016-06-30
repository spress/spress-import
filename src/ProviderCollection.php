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

use Spress\Import\Provider\ProviderInterface;

/**
 * Represents an item of content. e.g: post or page.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ProviderCollection implements \IteratorAggregate, \Countable
{
    protected $providers = [];

    /**
     * Constructor.
     *
     * @param array $providers A list of name-provider pairs.
     *                         e.g: ['wordpress' => new WxrProvider()]
     *
     * @throws \RuntimeException If the provider has been registered previously with the same name.
     */
    public function __construct(array $providers = [])
    {
        foreach ($providers as $name => $provider) {
            $this->Add($provider, $name);
        }
    }

    /**
     * Gets the current ProviderCollection as an Iterator that includes all provider.
     * The key of each item is the provider's name.
     *
     * @return \ArrayIterator An \ArrayIterator object for iterating over provider.
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->providers);
    }

    /**
     * Adds an new provider.
     *
     * @param ProviderInterface $provider
     *
     * @throws \RuntimeException If the provider has been registered previously with the same name.
     */
    public function add(ProviderInterface $provider, $name)
    {
        if ($this->has($name) === true) {
            throw new \RuntimeException(sprintf('A previous provider exists with the same name: "%s".', $name));
        }

        $this->set($provider, $name);
    }

    /**
     * Sets a provider.
     *
     * @param ProviderInterface $provider The provider.
     * @param string            $name     The name of the provider.
     */
    public function set(ProviderInterface $provider, $name)
    {
        $this->providers[$name] = $provider;
    }

    /**
     * Counts the providers registered.
     *
     * @return int
     */
    public function count()
    {
        return count($this->providers);
    }

    /**
     * Gets an item.
     *
     * @param string $name The name of the provider.
     *
     * @return ProviderInterface
     *
     * @throws \RuntimeException If the provider was not found.
     */
    public function get($name)
    {
        if ($this->has($name) === false) {
            throw new \RuntimeException(sprintf('Provider with name: "%s" not found.', $name));
        }

        return $this->providers[$name];
    }

    /**
     * Checks if a provider exists.
     *
     * @param string $id The identifier of the item.
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->providers[$name]);
    }

    /**
     * Returns all providers in this collection.
     *
     * @return array
     */
    public function all()
    {
        return $this->providers;
    }

    /**
     * Removes a provider from the collection.
     *
     * @param string $name The provider identifier.
     */
    public function remove($name)
    {
        unset($this->providers[$name]);
    }

    /**
     * Clears all providers in this collection.
     */
    public function clear()
    {
        $this->providers = [];
    }
}
