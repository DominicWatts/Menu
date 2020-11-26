<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Xigen\Menu\Helper\Data;
use Xigen\Menu\Model\ResourceModel\Menu\CollectionFactory as MenuCollectionFactory;

class Menu implements OptionSourceInterface
{
    /**
     * @var MenuCollectionFactory
     */
    private $menuCollectionFactory;

    /**
     * @var MenuCollection
     */
    protected $_menuCollection;

    /**
     * Menu constructor.
     * @param MenuCollectionFactory $menuCollectionFactory
     */
    public function __construct(
        MenuCollectionFactory $menuCollectionFactory
    ) {
        $this->menuCollectionFactory = $menuCollectionFactory;
    }

    /**
     * Options getter
     * @return array
     */
    public function toOptionArray($addEmptyField = true)
    {
        $collection = $this->_getMenuCollection();
        $menus = [];

        // phpcs:disable
        if ($addEmptyField) {
            $menus[] = [
                'value' => '',
                'label' => __(Data::PLEASE_SELECT_TEXT)
            ];
        }
        // phpcs:enable

        foreach ($collection as $menu) {
            $name = $menu->getMenuId() . ' ' . $menu->getTitle();
            $urlKey = $menu->getIdentifier();

            if (isset($name) && isset($urlKey)) {
                $suffix = '';

                if (!$menu->getIsActive()) {
                    $suffix = __('(Inactive)');
                }

                $menus[] = [
                    'value' => $menu->getMenuId(),
                    'label' => __($name) . ' ' . $suffix
                ];
            }
        }

        return $menus;
    }

    /**
     * Get options in "key-value" format
     * @return array
     */
    public function toArray($addEmptyField = true)
    {
        $collection = $this->_getMenuCollection();
        $menus = [];

        // phpcs:disable
        if ($addEmptyField) {
            $menus[] = __(Data::PLEASE_SELECT_TEXT);
        }
        // phpcs:enable

        foreach ($collection as $menu) {
            $name = $menu->getMenuId() . ' ' . $menu->getTitle();
            $urlKey = $menu->getIdentifier();

            if (isset($name) && isset($urlKey)) {
                $suffix = '';

                if (!$menu->getIsActive()) {
                    $suffix = __('(Inactive)');
                }

                $menus[$menu->getMenuId()] = __($name) . ' ' . $suffix;
            }
        }

        return $menus;
    }

    /**
     * @return mixed
     */
    protected function _getMenuCollection()
    {
        if (!$this->_menuCollection) {
            $collection = $this->menuCollectionFactory
                ->create()
                ->addStatusFilter(Data::ENABLED);
            $this->_menuCollection = $collection;
        }
        return $this->_menuCollection;
    }
}
