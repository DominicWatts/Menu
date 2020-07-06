<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Xigen\Menu\Api\Data\ItemInterfaceFactory;
use Xigen\Menu\Api\Data\ItemSearchResultsInterfaceFactory;
use Xigen\Menu\Api\ItemRepositoryInterface;
use Xigen\Menu\Model\ResourceModel\Item as ResourceItem;
use Xigen\Menu\Model\ResourceModel\Item\CollectionFactory as ItemCollectionFactory;

class ItemRepository implements ItemRepositoryInterface
{
    protected $extensibleDataObjectConverter;
    protected $itemFactory;

    protected $itemCollectionFactory;

    protected $dataItemFactory;

    protected $dataObjectHelper;

    protected $resource;

    private $storeManager;

    protected $searchResultsFactory;

    protected $dataObjectProcessor;

    protected $extensionAttributesJoinProcessor;

    private $collectionProcessor;

    /**
     * @param ResourceItem $resource
     * @param ItemFactory $itemFactory
     * @param ItemInterfaceFactory $dataItemFactory
     * @param ItemCollectionFactory $itemCollectionFactory
     * @param ItemSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceItem $resource,
        ItemFactory $itemFactory,
        ItemInterfaceFactory $dataItemFactory,
        ItemCollectionFactory $itemCollectionFactory,
        ItemSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->itemFactory = $itemFactory;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataItemFactory = $dataItemFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Xigen\Menu\Api\Data\ItemInterface $item)
    {
        /* if (empty($item->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $item->setStoreId($storeId);
        } */

        $itemData = $this->extensibleDataObjectConverter->toNestedArray(
            $item,
            [],
            \Xigen\Menu\Api\Data\ItemInterface::class
        );

        $itemModel = $this->itemFactory->create()->setData($itemData);

        try {
            $this->resource->save($itemModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the item: %1',
                $exception->getMessage()
            ));
        }
        return $itemModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($itemId)
    {
        $item = $this->itemFactory->create();
        $this->resource->load($item, $itemId);
        if (!$item->getId()) {
            throw new NoSuchEntityException(__('Item with id "%1" does not exist.', $itemId));
        }
        return $item->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->itemCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Xigen\Menu\Api\Data\ItemInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Xigen\Menu\Api\Data\ItemInterface $item)
    {
        try {
            $itemModel = $this->itemFactory->create();
            $this->resource->load($itemModel, $item->getItemId());
            $this->resource->delete($itemModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Item: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($itemId)
    {
        return $this->delete($this->get($itemId));
    }
}
