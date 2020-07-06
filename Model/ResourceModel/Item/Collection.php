<?php

namespace Xigen\Menu\Model\ResourceModel\Item;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Xigen\Menu\Api\Data\ItemInterface;
use Xigen\Menu\Api\Data\MenuInterface;
use Xigen\Menu\Model\Item;
use Xigen\Menu\Model\Menu;

class Collection extends AbstractCollection
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
