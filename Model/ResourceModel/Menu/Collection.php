<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Model\ResourceModel\Menu;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\Store;
use Xigen\Menu\Api\Data\MenuInterface;

class Collection extends AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'menu_id';

    /**
     * @var int
     */
    protected $_storeId;

    /**
     * Define resource model
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

    /**
     * Add store availability filter. Include availability product for store website.
     *
     * @param null|string|bool|int|Store $store
     * @return $this
     */
    public function addStoreFilter($store = null)
    {
        if ($store === null) {
            $store = $this->getStoreId();
        }

        try {
            $store = $this->_storeManager->getStore($store);
        } catch (NoSuchEntityException $e) {
            return $this;
        }

        if ($store->getId()) {
            $this->addFieldToFilter(
                MenuInterface::STORE_ID,
                [
                    'or' => [
                        0 => ['finset' => Store::DEFAULT_STORE_ID],
                        1 => ['finset' => $store->getId()],
                    ]
                ],
                'left'
            );
            return $this;
        }
        return $this;
    }

    /**
     * Return current store id
     * @return int
     */
    public function getStoreId()
    {
        if ($this->_storeId === null) {
            $this->setStoreId($this->_storeManager->getStore()->getId());
        }
        return $this->_storeId;
    }

    /**
     * Set store scope ID
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }
}
