<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Block;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Cms\Helper\Page;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManager;
use Xigen\Menu\Helper\Data;
use Xigen\Menu\Model\ResourceModel\Item\CollectionFactory as ItemCollectionFactory;
use Xigen\Menu\Model\ResourceModel\Menu\CollectionFactory as MenuCollectionFactory;

class Menu extends Template implements IdentityInterface
{
    /**
     * @var array
     */
    protected $identities = [];

    /**
     * @var \Magento\Framework\Data\Tree\Node
     */
    protected $_menu;

    /**
     * @var Page
     */
    protected $_cmsPageHelper;

    /**
     * @var array
     */
    protected $_categoryItemIds = [];

    /**
     * @var NodeFactory
     */
    protected $_nodeFactory;

    /**
     * @var $registry
     */
    protected $registry;

    /**
     * @var bool|string
     */
    protected $_currentUrlPath;

    /**
     * @var bool|string
     */
    protected $_baseUrlPath;

    /**
     * @var bool|string
     */
    protected $_currentCategoryUrlPath;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Xigen\Menu\Model\Menu
     */
    protected $_menuModel;

    /**
     * @var ItemCollectionFactory
     */
    protected $itemCollectionFactory;

    /**
     * @var MenuCollectionFactory
     */
    protected $menuCollectionFactory;

    /**
     * @var ItemCollectionFactory
     */
    protected $_menuItemCollection;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Menu constructor.
     * @param Template\Context $context
     * @param NodeFactory $nodeFactory
     * @param TreeFactory $treeFactory
     * @param Page $cmsPageHelper
     * @param StoreManager $storeManager
     * @param Registry $registry
     * @param CollectionFactory $categoryCollectionFactory
     * @param ItemCollectionFactory $itemCollectionFactory
     * @param MenuCollectionFactory $menuCollectionFactory
     * @param CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        Page $cmsPageHelper,
        StoreManager $storeManager,
        Registry $registry,
        CollectionFactory $categoryCollectionFactory,
        ItemCollectionFactory $itemCollectionFactory,
        MenuCollectionFactory $menuCollectionFactory,
        CategoryFactory $categoryFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_menu = $nodeFactory->create(
            [
                'data' => [],
                'idField' => 'root',
                'tree' => $treeFactory->create()
            ]
        );

        $this->_nodeFactory = $nodeFactory;
        $this->_storeManager = $storeManager;
        $this->_cmsPageHelper = $cmsPageHelper;
        $this->_coreRegistry = $registry;
        $this->_currentUrlPath = $this->_getTrimmedPath($this->_urlBuilder->getCurrentUrl());
        $this->_baseUrlPath = $this->_getTrimmedPath($this->_urlBuilder->getBaseUrl());
        $this->_collectionFactory = $categoryCollectionFactory;

        if ($currentCategory = $this->_getCurrentCategory()) {
            $this->_currentCategoryUrlPath = $this->_getTrimmedPath($currentCategory->getUrl());
        }

        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->menuCollectionFactory = $menuCollectionFactory;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @return mixed
     */
    protected function _getCurrentCategory()
    {
        return $this->_coreRegistry->registry('current_category');
    }

    /**
     * Get top menu html
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     * @return string
     */
    public function getHtml($outermostClass = '', $childrenWrapClass = '', $limit = 0)
    {
        $this->_eventManager->dispatch(
            'page_block_html_menumanager_gethtml_before',
            ['menu' => $this->_menu, 'block' => $this]
        );

        $this->initMenu();
        $this->_fillMenuTree();

        $this->_menu->setOutermostClass($outermostClass);
        $this->_menu->setChildrenWrapClass($childrenWrapClass);

        $html = $this->_getHtml($this->_menu, $childrenWrapClass, $limit);

        $transportObject = new DataObject(['html' => $html]);

        $this->_eventManager->dispatch(
            'page_block_html_menumanager_gethtml_after',
            ['menu' => $this->_menu, 'transportObject' => $transportObject]
        );

        $html = $transportObject->getHtml();

        return $html;
    }

    /**
     * Recursively generates top menu html from data that is specified in $menuTree
     *
     * @param \Magento\Framework\Data\Tree\Node $menuTree
     * @param string $childrenWrapClass
     * @param int $limit
     * @param array $colBrakes
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getHtml(Node $menuTree, $childrenWrapClass, $limit, $colBrakes = [])
    {
        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = $parentLevel === null ? 0 : $parentLevel + 1;

        $counter = 1;
        $itemPosition = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        foreach ($children as $child) {
            if (!$this->_isMenuItemActive($child)) {
                continue;
            }

            $this->_generateFinalUrl($child);
            $this->_generateFinalTitle($child);

            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);
            $child->setIsActiveUrl($this->_hasCurrentUrl($child));
            $child->setType($child->getOpenType() == Data::NEW_WINDOW ? 'target="_blank"' : '');

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $child->setClass($outermostClass);
            }

            if (is_object($colBrakes) || is_array($colBrakes)) {
                if (count($colBrakes) && $colBrakes[$counter]['colbrake']) {
                    $html .= '</ul></li><li class="column"><ul>';
                }
            }

            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
            $html .= '<a href="' . $child->getFullUrl() . '" ' . $outermostClassCode . $child->getType() . '><span>'
                . $this->escapeHtml($child->getFinalTitle()) . '</span></a>'
                . $this->_addSubMenu(
                    $child,
                    $childLevel,
                    $childrenWrapClass,
                    $limit
                ) . '</li>';
            $itemPosition++;
            $counter++;
        }

        if (is_object($colBrakes) || is_array($colBrakes)) {
            if (count($colBrakes) && $limit) {
                $html = '<li class="column"><ul>' . $html . '</ul></li>';
            }
        }

        return $html;
    }

    /**
     * Generates string with all attributes that should be present in menu item element
     * @param \Magento\Framework\Data\Tree\Node $item
     * @return string
     */
    protected function _getRenderedMenuItemAttributes(Node $item)
    {
        $html = '';
        $attributes = $this->_getMenuItemAttributes($item);
        foreach ($attributes as $attributeName => $attributeValue) {
            $html .= ' ' . $attributeName . '="' . str_replace('"', '\"', $attributeValue) . '"';
        }

        return $html;
    }

    /**
     * Returns array of menu item's attributes
     * @param \Magento\Framework\Data\Tree\Node $item
     * @return array
     */
    protected function _getMenuItemAttributes(Node $item)
    {
        $menuItemClasses = $this->_getMenuItemClasses($item);

        return ['class' => implode(' ', $menuItemClasses)];
    }

    /**
     * Returns array of menu item's classes
     * @param \Magento\Framework\Data\Tree\Node $item
     * @return array
     */
    protected function _getMenuItemClasses(Node $item)
    {
        $classes = [];

        $classes[] = 'level' . $item->getLevel();

        if ($item->getIsFirst()) {
            $classes[] = 'first';
        }

        if ($item->getIsActiveUrl()) {
            $classes[] = 'active';
        } elseif ($item->getHasActiveUrl()) {
            $classes[] = 'has-active';
        }

        if ($item->getIsLast()) {
            $classes[] = 'last';
        }

        if ($item->getClass()) {
            $classes[] = $item->getClass();
        }

        if ($item->hasChildren()) {
            $classes[] = 'parent';
        }

        return $classes;
    }

    /**
     * Checks if menu item's or child's url is same as current url
     * @param Node $item
     * @return bool
     */
    protected function _hasCurrentUrl(Node $item)
    {
        $homeFlag = $item->getUrl() == '/' ? true : false;
        $itemUrl = $this->_getTrimmedPath($item->getFullUrl());

        if ($this->_baseUrlPath == $this->_currentUrlPath && $homeFlag) {
            return true;
        }

        if ($itemUrl) {
            if ($this->_currentUrlPath == $itemUrl && !$homeFlag) {
                return true;
            }

            if ($this->_currentCategoryUrlPath == $itemUrl) {
                return true;
            }
        }

        if ($item->hasChildren()) {
            foreach ($item->getChildren() as $child) {
                if (!$this->_isMenuItemActive($child)) {
                    continue;
                }

                if ($this->_hasCurrentUrl($child)) {
                    $item->setHasActiveUrl(true);
                }
            }
        }

        return false;
    }

    /**
     * Retrieve parsed and trimmed url path
     *
     * @param $url
     * @return string
     */
    protected function _getTrimmedPath($url)
    {
        if ($url) {
            $url = parse_url($url); //phpcs:ignore

            if (isset($url['path'])) {
                return rtrim($url['path'], '/');
            }
        }
        return false;
    }

    /**
     * Building Array with Column Brake Stops
     *
     * @param \Magento\Backend\Model\Menu $items
     * @param int $limit
     *
     * @return array|void
     *
     * @todo: Add Depth Level limit, and better logic for columns
     */
    protected function _columnBrake($items, $limit)
    {
        $result = [];
        $total = $this->_countItems($items);

        if ($total <= $limit) {
            return $result;
        }

        $result[] = ['total' => $total, 'max' => (int)ceil($total / ceil($total / $limit))];

        $count = 0;
        $firstCol = true;

        foreach ($items as $item) {
            $place = $this->_countItems($item->getChildren()) + 1;
            $count += $place;

            if ($place >= $limit) {
                $colbrake = !$firstCol;
                $count = 0;
            } elseif ($count >= $limit) {
                $colbrake = !$firstCol;
                $count = $place;
            } else {
                $colbrake = false;
            }

            $result[] = ['place' => $place, 'colbrake' => $colbrake];

            $firstCol = false;
        }

        return $result;
    }

    /**
     * Count All Subnavigation Items
     *
     * @param \Magento\Backend\Model\Menu $items
     *
     * @return int
     */
    protected function _countItems($items)
    {
        $total = $items->count();
        foreach ($items as $item) {
            /** @var $item \Magento\Backend\Model\Menu\Item */
            if ($item->hasChildren()) {
                $total += $this->_countItems($item->getChildren());
            }
        }

        return $total;
    }

    /**
     * Add sub menu HTML code for current menu item
     *
     * @param \Magento\Framework\Data\Tree\Node $child
     * @param string $childLevel
     * @param string $childrenWrapClass
     * @param int $limit
     *
     * @return string HTML code
     */
    protected function _addSubMenu($child, $childLevel, $childrenWrapClass, $limit)
    {
        $html = '';
        if (!$child->hasChildren()) {
            return $html;
        }

        $colStops = null;
        if ($childLevel == 0 && $limit) {
            $colStops = $this->_columnBrake($child->getChildren(), $limit);
        }

        $html .= '<ul class="level' . $childLevel . ' submenu">';
        $html .= $this->_getHtml($child, $childrenWrapClass, $limit, $colStops);
        $html .= '</ul>';

        return $html;
    }

    /**
     * Add identity
     * @param array $identity
     * @return void
     */
    public function addIdentity($identity)
    {
        $this->identities[] = $identity;
    }

    /**
     * Get identities
     * @return array
     */
    public function getIdentities()
    {
        return $this->identities;
    }

    /**
     * @return bool
     */
    public function _fillMenuTree()
    {
        $collection = $this->_getMenuItemCollection()
            ->setParentIdOrder()
            ->setPositionOrder();

        if (!$collection->count()) {
            return false;
        }

        $nodes = [];
        $nodes[0] = $this->_menu;

        foreach ($collection as $item) {
            if (!isset($nodes[$item->getParentId()])) {
                continue;
            }

            /**
             * @var $parentItemNode Node
             */
            $parentItemNode = $nodes[$item->getParentId()];

            $itemNode = $this->_nodeFactory->create(
                [
                    'data' => $item->getData(),
                    'idField' => 'item_id',
                    'tree' => $parentItemNode->getTree(),
                    'parent' => $parentItemNode
                ]
            );

            $nodes[$item->getId()] = $itemNode;
            $parentItemNode->addChild($itemNode);

            if ($categoryId = $item->getCategoryId()) {
                $this->_categoryItemIds[$item->getId()] = $categoryId;
            }
        }

        $this->_fillCategoryData($nodes);

        return true;
    }

    /**
     * @param array $nodes
     */
    protected function _fillCategoryData(array $nodes)
    {
        $categoryItemIds = $this->_categoryItemIds;

        $collection = $this->_collectionFactory->create();
        $collection->addAttributeToSelect('url_key')
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('is_active', 1)
            ->addIdFilter($categoryItemIds)
            ->addUrlRewriteToResult();

        /**
         * [$categoryId => $categoryData]
         */
        $categoryArray = $this->_prepareCategoryArray($collection);

        foreach ($categoryItemIds as $itemId => $categoryId) {
            $item = $nodes[$itemId];
            $itemCategoryId = $item->getCategoryId();

            if ($itemCategoryId && isset($categoryArray[$itemCategoryId])) {
                $item->setCategory($categoryArray[$itemCategoryId]);
            }
        }
    }

    /**
     * @param $categoryCollection
     * @return array
     */
    protected function _prepareCategoryArray($categoryCollection)
    {
        $result = [];

        foreach ($categoryCollection as $category) {
            $result[$category->getId()] = $category;
        }

        return $result;
    }

    /**
     * @return \Xigen\Menu\Model\ResourceModel\Item\Collection
     */
    protected function _getMenuItemCollection()
    {
        if (!$this->_menuItemCollection) {
            $collection = $this->itemCollectionFactory
                ->create()
                ->addMenuFilter($this->_menuModel)
                ->setPositionOrder()
                ->addStatusFilter(Data::ENABLED);

            $this->_menuItemCollection = $collection;
        }

        return $this->_menuItemCollection;
    }

    /**
     * @return \Xigen\Menu\Model\ResourceModel\Item\Collection
     */
    public function getMenuItemCollectionByMenu($menu = null)
    {
        if (!$this->_menuItemCollection) {
            $collection = $this->itemCollectionFactory
                ->create()
                ->addMenuFilter($menu)
                ->setPositionOrder()
                ->addStatusFilter(Data::ENABLED);

            $this->_menuItemCollection = $collection;
        }

        return $this->_menuItemCollection;
    }

    /**
     * @return bool | \Xigen\Menu\Model\Menu
     */
    public function initMenu()
    {
        if ($this->_menuModel) {
            return $this->_menuModel;
        }

        if ($identifier = $this->getIdentifier()) {
            $collection = $this->menuCollectionFactory->create()
                ->addFieldToFilter('identifier', $identifier)
                ->addStatusFilter(Data::ENABLED)
                ->addStoreFilter();

            $this->_menuModel = $collection->getFirstItem();
        }

        return $this->_menuModel;
    }

    /**
     * @param $item
     * @return boolean
     */
    protected function _isMenuItemActive($item)
    {
        $showCategory = true;

        // Check category status and menu visibility
        // @todo Confirm
        /*
        if ($categoryId = $item->getCategoryId()) {
            $category = $this->categoryFactory
                ->create()
                ->load($categoryId);
            if (!$category->getIncludeInMenu() || !$category->getIsActive()) {
                $showCategory = false;
            }
        }
        */

        if ($item->getUrlType() == Data::CATEGORY && !$item->getCategoryId() && $showCategory) {
            return false;
        }
        return true;
    }

    /**
     * Get final name
     * @return string|null
     */
    protected function _generateFinalTitle($item)
    {
        $item->setFinalTitle(null);
        switch ($item->getUrlType()) {
            case Data::CUSTOM_URL:
            case Data::CMS_PAGE:
            default:
                $item->setFinalTitle($item->getTitle());
                break;
            case Data::CATEGORY:
                if ($category = $item->getCategory()) {
                    $item->setFinalTitle($category->getName());
                }
                break;
        }
        return $item->getFinalTitle();
    }

    /**
     * Get final URL
     * @return string|null
     */
    protected function _generateFinalUrl($item)
    {
        $item->setFullUrl(null);
        switch ($item->getUrlType()) {
            case Data::CUSTOM_URL:
            default:
                if ($itemUrl = $item->getUrl()) {
                    if (strpos($itemUrl, '://') === false) {
                        $itemUrl = $this->_urlBuilder->getDirectUrl($itemUrl != '/' ? $itemUrl : '');
                    }
                    $item->setFullUrl($itemUrl);
                }
                break;
            case Data::CMS_PAGE:
                $item->setFullUrl($this->_cmsPageHelper->getPageUrl($item->getCmsPageIdentifier()));
                break;
            case Data::CATEGORY:
                if ($category = $item->getCategory()) {
                    $item->setFullUrl($category->getUrl());
                }
                break;
        }
        return $item->getFullUrl();
    }
}
