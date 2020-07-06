<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Model;

use Magento\Framework\Api\DataObjectHelper;
use Xigen\Menu\Api\Data\MenuInterface;
use Xigen\Menu\Api\Data\MenuInterfaceFactory;
use Xigen\Menu\Model\ResourceModel\Item\CollectionFactory;

class Menu extends \Magento\Framework\Model\AbstractModel
{

    protected $menuDataFactory;

    protected $dataObjectHelper;

    protected $_eventPrefix = 'xigen_menu_menu';

    /**
     * @var CollectionFactory
     */
    protected $itemCollectionFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param MenuInterfaceFactory $menuDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Xigen\Menu\Model\ResourceModel\Menu $resource
     * @param \Xigen\Menu\Model\ResourceModel\Menu\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        MenuInterfaceFactory $menuDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Xigen\Menu\Model\ResourceModel\Menu $resource,
        \Xigen\Menu\Model\ResourceModel\Menu\Collection $resourceCollection,
        CollectionFactory $itemCollectionFactory,
        array $data = []
    ) {
        $this->menuDataFactory = $menuDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->itemCollectionFactory = $itemCollectionFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve menu model with menu data
     * @return MenuInterface
     */
    public function getDataModel()
    {
        $menuData = $this->getData();
        
        $menuDataObject = $this->menuDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $menuDataObject,
            $menuData,
            MenuInterface::class
        );
        
        return $menuDataObject;
    }

    /**
     * Get messages collection
     * @return CollectionFactory
     */
    public function getItemCollection()
    {
        $collection = $this->itemCollectionFactory->create()
            ->addMenuFilter($this->getMenuId());
        if ($this->getId()) {
            foreach ($collection as $item) {
                $item->setMenu($this);
            }
        }
        return $collection;
    }
}
