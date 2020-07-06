<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Api\Data;

interface MenuSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Menu list.
     * @return \Xigen\Menu\Api\Data\MenuInterface[]
     */
    public function getItems();

    /**
     * Set identifier list.
     * @param \Xigen\Menu\Api\Data\MenuInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
