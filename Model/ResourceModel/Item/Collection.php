<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Model\ResourceModel\Item;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Xigen\Menu\Api\Data\ItemInterface;
use Xigen\Menu\Api\Data\MenuInterface;
use Xigen\Menu\Model\Item;
use Xigen\Menu\Model\Menu;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'item_id';

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * Undocumented function
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        EntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        TimezoneInterface $localeDate,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->_storeManager = $storeManager;
        $this->localeDate = $localeDate;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Xigen\Menu\Model\Item::class,
            \Xigen\Menu\Model\ResourceModel\Item::class
        );
    }

    /**
     * Join title for parent items
     */
    public function joinParentNames()
    {
        $select = $this->getSelect();

        $select->joinLeft(
            ['item_table' => $this->getTable('xigen_menu_item')],
            'main_table.parent_id = item_table.item_id',
            ['parent_title' => 'title']
        )->order('main_table.item_id ASC');

        return $this;
    }

    /**
     * Add menu filter to item collection
     * @param int | \Xigen\Menu\Model\Menu $menu
     * @return $this
     */
    public function addMenuFilter($menu)
    {
        if ($menu) {
            if ($menu instanceof Menu) {
                $menu = $menu->getMenuId();
            }
            if ($menu instanceof Item) {
                $menu = $menu->getMenuId();
            }

            $this->addFilter(MenuInterface::MENU_ID, $menu);
        }

        return $this;
    }

    /**
     * Filter collection by status
     * @param string $status
     * @return $this
     */
    public function addStatusFilter($status = null)
    {
        if (empty($status)) {
            $this->addFieldToFilter(ItemInterface::IS_ACTIVE, ['null' => true]);
        } else {
            $this->addFieldToFilter(ItemInterface::IS_ACTIVE, $status);
        }
        return $this;
    }

    /**
     * Set order to item collection
     * @return $this
     */
    public function setPositionOrder()
    {
        $this->setOrder(ItemInterface::POSITION, 'asc');
        return $this;
    }

    /**
     * set order by parent id
     * @return $this
     */
    public function setParentIdOrder()
    {
        $this->setOrder(ItemInterface::PARENT_ID, 'asc');
        return $this;
    }

    /**
     * @param $item
     * @return $this
     */
    public function excludeCurrentItem($item)
    {
        if ($item) {
            if ($item instanceof Item) {
                $item = $item->getItemId();
            }

            $this->addFieldToFilter(ItemInterface::ITEM_ID, ['nin' => $item]);
        }
        return $this;
    }
}
