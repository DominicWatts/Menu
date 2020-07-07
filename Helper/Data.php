<?php

declare(strict_types=1);

namespace Xigen\Menu\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Xigen\Menu\Model\ResourceModel\Item\CollectionFactory as ItemCollectionFactory;

class Data extends AbstractHelper
{
    const PLEASE_SELECT_TEXT = "-- Please Select --";

    const ROOT_ID = 0;
    const ROOT_TEXT = "Root";

    const ENABLED = 1;
    const DISABLED = 0;
    const ENABLED_TEXT = "Enabled";
    const DISABLED_TEXT = "Disabled";

    const SAME_WINDOW = 0;
    const SAME_WINDOW_TEXT = "Same Window";
    const NEW_WINDOW = 1;
    const NEW_WINDOW_TEXT = "New Window";

    const CUSTOM_URL = 0;
    const CUSTOM_URL_TEXT = "Custom URL";
    const CMS_PAGE = 1;
    const CMS_PAGE_TEXT = "CMS Page";
    const CATEGORY = 2;
    const CATEGORY_TEXT = "Category";

    /**
     * Helper
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Xigen\Menu\Model\ResourceModel\Item\CollectionFactory $itemCollectionFactory
     */
    public function __construct(
        Context $context,
        ItemCollectionFactory $itemCollectionFactory
    ) {
        $this->itemCollectionFactory = $itemCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Get item by menu and category
     * @param int $menuId
     * @param int $categoryId
     * @return Xigen\Menu\Model\Item
     */
    public function getByMenuAndCategory($menuId, $categoryId)
    {
        if (!$menuId || !$categoryId) {
            return false;
        }
        $collection = $this->itemCollectionFactory->create()
            ->addStatusFilter(self::ENABLED)
            ->addMenuFilter($menuId)
            ->addCategoryFilter($categoryId);

        if ($collection->getSize() > 0) {
            return $collection->getFirstItem();
        }
        return false;
    }
}
