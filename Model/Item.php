<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Model;

use Magento\Framework\Api\DataObjectHelper;
use Xigen\Menu\Api\Data\ItemInterface;
use Xigen\Menu\Api\Data\ItemInterfaceFactory;

class Item extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $_eventPrefix = 'xigen_menu_item';
    protected $itemDataFactory;

    /**
     * @var \Xigen\Menu\Model\Menu
     */
    protected $menu;

    /**
     * @var \Xigen\Menu\Model\MenuFactory
     */
    protected $menuFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ItemInterfaceFactory $itemDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Xigen\Menu\Model\ResourceModel\Item $resource
     * @param \Xigen\Menu\Model\ResourceModel\Item\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ItemInterfaceFactory $itemDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Xigen\Menu\Model\ResourceModel\Item $resource,
        \Xigen\Menu\Model\ResourceModel\Item\Collection $resourceCollection,
        \Xigen\Menu\Model\MenuFactory $menuFactory,
        array $data = []
    ) {
        $this->itemDataFactory = $itemDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->menuFactory = $menuFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve item model with item data
     * @return ItemInterface
     */
    public function getDataModel()
    {
        $itemData = $this->getData();
        
        $itemDataObject = $this->itemDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $itemDataObject,
            $itemData,
            ItemInterface::class
        );
        
        return $itemDataObject;
    }

    /**
     * Set menu
     * @param \Xigen\Menu\Model\Menu $group
     * @return $this
     */
    public function setMenu(\Xigen\Menu\Model\Menu $menu)
    {
        $this->menu = $menu;
        return $this;
    }

    /**
     * Get group
     * @return \Xigen\Announce\Model\Group
     */
    public function getMenu()
    {
        if (!$this->menu) {
            $this->menu = $this->menuFactory
                ->create()
                ->load($this->getMenuId());
        }
        return $this->menu;
    }
}
