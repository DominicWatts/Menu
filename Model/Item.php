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
use Magento\Framework\UrlInterface;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Cms\Helper\Page;
use Xigen\Menu\Helper\Data;

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
     * @var \Xigen\Menu\Model\ItemFactory
     */
    protected $itemFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var Magento\Cms\Helper\Page
     */
    protected $_cmsPageHelper;

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
        \Xigen\Menu\Model\ItemFactory $itemFactory,
        UrlInterface $urlBuilder,
        CategoryRepository $categoryRepository,
        Page $cmsPageHelper,
        array $data = []
    ) {
        $this->itemDataFactory = $itemDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->menuFactory = $menuFactory;
        $this->itemFactory = $itemFactory;
        $this->_urlBuilder = $urlBuilder;
        $this->categoryRepository = $categoryRepository;
        $this->_cmsPageHelper = $cmsPageHelper;
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
     * @return \Xigen\Menu\Model\Menu
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
    /**
     * Load parent ID
     * @return \Xigen\Menu\Model\Item
     */
    public function getParent()
    {
        if (!$this->parent) {
            $this->parent = $this->itemFactory
                ->create()
                ->load($this->getParentId());
        }
        return $this->parent;
    }

    /**
     * Is Child function
     * @return bool
     */
    public function hasParent()
    {
        return (bool) $this->getParent();
    }
    
    /**
     * Get category
     * @return \Magento\Category\Module\Category
     */
    public function getCategory()
    {
        if (!$this->getCategoryId()) {
            return false;
        }
        try {
            return $this->categoryRepository->get($this->getCategoryId());
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get final URL string
     * @return void
     */
    public function getFinalUrl()
    {
        if (!$this->fullUrl) {
            switch ($this->getUrlType()) {
                case Data::CUSTOM_URL:
                default:
                    if ($itemUrl = $this->getUrl()) {
                        if (strpos($itemUrl, '://') === false) {
                            $itemUrl = $this->_urlBuilder->getDirectUrl($itemUrl != '/' ? $itemUrl : '');
                        }
                        $this->fullUrl = $itemUrl;
                    }
                    break;
                case Data::CMS_PAGE:
                    $this->fullUrl = $this->_cmsPageHelper->getPageUrl($this->getCmsPageIdentifier());
                    break;
                case Data::CATEGORY:
                    if ($category = $this->getCategory()) {
                        $this->fullUrl = $category->getUrl();
                    }
                    break;
            }
        }
        return $this->fullUrl;
    }
}
