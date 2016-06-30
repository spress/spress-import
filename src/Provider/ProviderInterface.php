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

/**
 * Iterface for a provider.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface ProviderInterface
{
    /**
     * Set up the provider.
     *
     * @param array $options Configuration values.
     */
    public function setUp(array $options);

    /**
     * Gets a set of item.
     *
     * @return Item[]
     */
    public function getItems();

    /**
     * Clean up the objects and connections made by the provider.
     */
    public function tearDown();
}
