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
        array $data = []
    ) {
        $this->itemDataFactory = $itemDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
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
}

