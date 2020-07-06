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
use Xigen\Menu\Api\Data\MenuInterfaceFactory;
use Xigen\Menu\Api\Data\MenuSearchResultsInterfaceFactory;
use Xigen\Menu\Api\MenuRepositoryInterface;
use Xigen\Menu\Model\ResourceModel\Menu as ResourceMenu;
use Xigen\Menu\Model\ResourceModel\Menu\CollectionFactory as MenuCollectionFactory;

class MenuRepository implements MenuRepositoryInterface
{

    protected $extensibleDataObjectConverter;
    protected $menuCollectionFactory;

    protected $dataObjectHelper;

    protected $resource;

    private $storeManager;

    protected $searchResultsFactory;

    protected $menuFactory;

    protected $dataObjectProcessor;

    protected $dataMenuFactory;

    protected $extensionAttributesJoinProcessor;

    private $collectionProcessor;


    /**
     * @param ResourceMenu $resource
     * @param MenuFactory $menuFactory
     * @param MenuInterfaceFactory $dataMenuFactory
     * @param MenuCollectionFactory $menuCollectionFactory
     * @param MenuSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceMenu $resource,
        MenuFactory $menuFactory,
        MenuInterfaceFactory $dataMenuFactory,
        MenuCollectionFactory $menuCollectionFactory,
        MenuSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->menuFactory = $menuFactory;
        $this->menuCollectionFactory = $menuCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataMenuFactory = $dataMenuFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Xigen\Menu\Api\Data\MenuInterface $menu)
    {
        /* if (empty($menu->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $menu->setStoreId($storeId);
        } */
        
        $menuData = $this->extensibleDataObjectConverter->toNestedArray(
            $menu,
            [],
            \Xigen\Menu\Api\Data\MenuInterface::class
        );
        
        $menuModel = $this->menuFactory->create()->setData($menuData);
        
        try {
            $this->resource->save($menuModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the menu: %1',
                $exception->getMessage()
            ));
        }
        return $menuModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($menuId)
    {
        $menu = $this->menuFactory->create();
        $this->resource->load($menu, $menuId);
        if (!$menu->getId()) {
            throw new NoSuchEntityException(__('Menu with id "%1" does not exist.', $menuId));
        }
        return $menu->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->menuCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Xigen\Menu\Api\Data\MenuInterface::class
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
    public function delete(\Xigen\Menu\Api\Data\MenuInterface $menu)
    {
        try {
            $menuModel = $this->menuFactory->create();
            $this->resource->load($menuModel, $menu->getMenuId());
            $this->resource->delete($menuModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Menu: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($menuId)
    {
        return $this->delete($this->get($menuId));
    }
}

