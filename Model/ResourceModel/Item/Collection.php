<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Model\ResourceModel\Item;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'item_id';

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
        if ($menu instanceof \Xigen\Menu\Model\Menu) {
            $menu = $menu->getId();
        }

        $this->addFilter('menu_id', $menu);

        return $this;
    }

    /**
     * Add status filter to item collection
     * @return $this
     */
    public function addStatusFilter()
    {
        $this->addFilter('is_active', 1);
        return $this;
    }

    /**
     * Set order to item collection
     * @return $this
     */
    public function setPositionOrder()
    {
        $this->setOrder('position', 'asc');
        return $this;
    }

    /**
     * set order by parent id
     * @return $this
     */
    public function setParentIdOrder()
    {
        $this->setOrder('parent_id', 'asc');
        return $this;
    }

    /**
     * @param $itemId
     * @return $this
     */
    public function excludeCurrentItem($itemId)
    {
        if ($itemId) {
            $this->addFieldToFilter('item_id', ['nin' => $itemId]);
        }
        return $this;
    }
}
