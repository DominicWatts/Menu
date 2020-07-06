<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Api;

interface MenuRepositoryInterface
{
    /**
     * Save Menu
     * @param \Xigen\Menu\Api\Data\MenuInterface $menu
     * @return \Xigen\Menu\Api\Data\MenuInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Xigen\Menu\Api\Data\MenuInterface $menu);

    /**
     * Retrieve Menu
     * @param string $menuId
     * @return \Xigen\Menu\Api\Data\MenuInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($menuId);

    /**
     * Retrieve Menu matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Xigen\Menu\Api\Data\MenuSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Menu
     * @param \Xigen\Menu\Api\Data\MenuInterface $menu
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Xigen\Menu\Api\Data\MenuInterface $menu);

    /**
     * Delete Menu by ID
     * @param string $menuId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($menuId);
}
