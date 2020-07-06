<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Api\Data;

interface ItemSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Item list.
     * @return \Xigen\Menu\Api\Data\ItemInterface[]
     */
    public function getItems();

    /**
     * Set menu_id list.
     * @param \Xigen\Menu\Api\Data\ItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

