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
 * Iterface for a provider. The goal of a provider is to bring items to
 * provider manager. Items contain information such as permalink or content.
 * The provider manager doesn't apply any transformation over the content.
 * Therefore, a provider should apply transformations as many time as necessary
 * in order to adapt the content. Stuff such as replaces permalinks in content
 * or sets the item's layout are responsibility  of provider manager.
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
