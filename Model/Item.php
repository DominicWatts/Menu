<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Model;

use Magento\Catalog\Model\CategoryRepository;
use Magento\Cms\Helper\Page;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Xigen\Menu\Api\Data\ItemInterface;
use Xigen\Menu\Api\Data\ItemInterfaceFactory;
use Xigen\Menu\Helper\Data;

class Item extends AbstractModel
{
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var string
     */
    protected $_eventPrefix = 'xigen_menu_item';

    /**
     * @var ItemInterfaceFactory
     */
    protected $itemDataFactory;

    /**
     * @var \Xigen\Menu\Model\Menu
     */
    protected $menu;

    /**
     * @var \Xigen\Menu\Model\Item
     */
    protected $parent;

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
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var string
     */
    protected $fullUrl;

    /**
     * Item constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ItemInterfaceFactory $itemDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ResourceModel\Item $resource
     * @param ResourceModel\Item\Collection $resourceCollection
     * @param \Xigen\Menu\Model\MenuFactory $menuFactory
     * @param \Xigen\Menu\Model\ItemFactory $itemFactory
     * @param UrlInterface $urlBuilder
     * @param CategoryRepository $categoryRepository
     * @param Page $cmsPageHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ItemInterfaceFactory $itemDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Xigen\Menu\Model\ResourceModel\Item $resource,
        \Xigen\Menu\Model\ResourceModel\Item\Collection $resourceCollection,
        MenuFactory $menuFactory,
        ItemFactory $itemFactory,
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
     * Before save
     * @return $this
     */
    public function beforeSave()
    {
        if ($category = $this->getData(ItemInterface::CATEGORY_ID)) {
            $cleanCategory = null;
            if (is_array($category)) {
                $cleanCategory = [];
                foreach ($category as $item) {
                    if (!empty($item) && $item != ',') {
                        $cleanCategory[$item] = $item;
                    }
                }
            } elseif (is_string($category)) {
                $cleanCategory = explode(',', $category);
            }
            $this->setCategoryId(implode(',', array_filter($cleanCategory)));
        }
        return parent::beforeSave();
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
     * Get Category
     * @return bool|\Magento\Catalog\Api\Data\CategoryInterface|mixed|null
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
     * Get final URL
     * @return string|null
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
