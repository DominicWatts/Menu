<?php

namespace Xigen\Menu\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Xigen\Menu\Helper\Data;
use Xigen\Menu\Model\ResourceModel\Menu\CollectionFactory as MenuCollectionFactory;

class Menu implements OptionSourceInterface
{
    private $menuCollectionFactory;
    protected $_menuCollection;

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
            $name = $menu->getName();
            $urlPath = $menu->getUrlPath();

            if (isset($name) && isset($urlPath)) {
                $suffix = '';

                if (!$menu->getIsActive()) {
                    $suffix = __('(Inactive)');
                }

                $menus[] = [
                    'value' => $menu->getId(),
                    'label' => __($menu->getName()) . ' ' . $suffix
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
            $name = $menu->getName();
            $urlPath = $menu->getUrlPath();

            if (isset($name) && isset($urlPath)) {
                $suffix = '';

                if (!$menu->getIsActive()) {
                    $suffix = __('(Inactive)');
                }

                $menus[$menu->getId()] = __($menu->getName()) . ' ' . $suffix;
            }
        }
    }

    /**
     * @return mixed
     */
    protected function _getMenuCollection()
    {
        if (!$this->_menuCollection) {
            $collection = $this->menuCollectionFactory->create();
            $this->_menuCollection = $collection;
        }
        return $this->_menuCollection;
    }
}
