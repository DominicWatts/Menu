<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Model\ResourceModel\Menu;

use Xigen\Menu\Api\Data\MenuInterface;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'menu_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Xigen\Menu\Model\Menu::class,
            \Xigen\Menu\Model\ResourceModel\Menu::class
        );
    }

    /**
     * Filter collection by status
     * @param string $status
     * @return $this
     */
    public function addStatusFilter($status = null)
    {
        if (empty($status)) {
            $this->addFieldToFilter(MenuInterface::IS_ACTIVE, ['null' => true]);
        } else {
            $this->addFieldToFilter(MenuInterface::IS_ACTIVE, $status);
        }
        return $this;
    }
}
